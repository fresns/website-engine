<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jevan Tang
 * Released under the Apache-2.0 License.
 */

namespace Fresns\WebsiteEngine\Http\Middleware;

use App\Helpers\AppHelper;
use App\Helpers\CacheHelper;
use App\Helpers\ConfigHelper;
use App\Helpers\PrimaryHelper;
use App\Helpers\SignHelper;
use Closure;
use hisorange\BrowserDetect\Parser as Browser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Str;

class WebConfiguration
{
    public function handle(Request $request, Closure $next)
    {
        // check lang tag
        $langTag = $this->checkLangTag($request);

        // check config
        $themeFskey = fs_theme('fskey');

        $cacheKey = 'fresns_web_middleware';
        $cacheTags = ['fresnsWeb', 'fresnsWebConfigs'];

        $checkMiddleware = CacheHelper::get($cacheKey, $cacheTags);

        if (empty($checkMiddleware)) {
            if (! fs_status('activate')) {
                $deactivateDescribe = fs_status('deactivateDescribe')[$langTag] ?? fs_status('deactivateDescribe')['default'] ?? '';

                return Response::view('error', [
                    'message' => "<p>{$deactivateDescribe}</p>",
                    'code' => 503,
                ], 503);
            }

            if (! $themeFskey) {
                $errorMessage = Browser::isMobile() ? '<p>'.__('WebsiteEngine::tips.errorMobileFskey').'</p>' : '<p>'.__('WebsiteEngine::tips.errorDesktopFskey').'</p>';

                return Response::view('error', [
                    'message' => $errorMessage.'<p>'.__('WebsiteEngine::tips.settingTip').'</p>',
                    'code' => 400,
                ], 400);
            }

            if (is_local_api()) {
                $keyId = ConfigHelper::fresnsConfigByItemKey('website_engine_key_id');
                $keyInfo = PrimaryHelper::fresnsModelById('key', $keyId);

                if (! $keyInfo) {
                    return Response::view('error', [
                        'message' => '<p>'.__('WebsiteEngine::tips.errorKey').'</p><p>'.__('WebsiteEngine::tips.settingTip').'</p>',
                        'code' => 403,
                    ], 403);
                }
            }

            if (is_remote_api()) {
                $apiConfigs = ConfigHelper::fresnsConfigByItemKeys([
                    'website_engine_api_host',
                    'website_engine_api_app_id',
                    'website_engine_api_app_key',
                ]);

                if (! $apiConfigs['website_engine_api_host'] || ! $apiConfigs['website_engine_api_app_id'] || ! $apiConfigs['website_engine_api_app_key']) {
                    return Response::view('error', [
                        'message' => '<p>'.__('WebsiteEngine::tips.errorApi').'</p><p>'.__('WebsiteEngine::tips.settingTip').'</p>',
                        'code' => 403,
                    ], 403);
                }
            }

            $ulid = Str::ulid();

            CacheHelper::put($ulid, $cacheKey, $cacheTags);
        }

        // set view
        $themeFskey = fs_theme('fskey');
        if ($themeFskey) {
            View::addLocation(base_path("themes/{$themeFskey}"));

            $currentPaths = View::getFinder()->getPaths();

            $currentPaths = array_unique($currentPaths);

            array_unshift($currentPaths, base_path("themes/{$themeFskey}"));

            View::getFinder()->setPaths($currentPaths);
        }

        // set headers
        if (is_local_api()) {
            $this->setHeaders($request, $langTag);
        }

        return $next($request);
    }

    public function checkLangTag(Request $request)
    {
        $defaultLangTag = ConfigHelper::fresnsConfigDefaultLangTag();
        $languageStatus = ConfigHelper::fresnsConfigByItemKey('language_status');

        if (! $languageStatus) {
            App::setLocale($defaultLangTag);

            return $defaultLangTag;
        }

        $supportedLanguages = ConfigHelper::fresnsConfigLangTags();

        // switch language
        $switchLang = $request->language;

        if ($switchLang && in_array($switchLang, $supportedLanguages)) {
            $this->setLangTag($switchLang);

            return $switchLang;
        }

        // cookie locale
        $cookieLangTag = Cookie::get('fresns_lang_tag');

        if ($cookieLangTag) {
            App::setLocale($cookieLangTag);

            return $cookieLangTag;
        }

        // browser locale
        $acceptLang = $request->server('HTTP_ACCEPT_LANGUAGE');

        $firstLang = null;
        $substrLang = null;
        if ($acceptLang) {
            $parts = explode(',', $acceptLang);

            $firstLang = $parts[0] ?? null; // example: en-US
            $substrLang = substr($acceptLang, 0, 2); // example: en
        }

        if (! $firstLang) {
            $this->setLangTag($defaultLangTag);

            return $defaultLangTag;
        }

        // The language tag are identical
        if (in_array($firstLang, $supportedLanguages)) {
            $this->setLangTag($firstLang);

            return $firstLang;
        }

        // zh locale
        if (Str::startsWith($firstLang, 'zh-') || $substrLang == 'zh') {
            $newFirstLang = Str::replace('zh-CN', 'zh-Hans', $firstLang);
            $newFirstLang = Str::replace('zh-SG', 'zh-Hans', $newFirstLang);
            $newFirstLang = Str::replace('zh-TW', 'zh-Hant', $newFirstLang);
            $newFirstLang = Str::replace('zh-HK', 'zh-Hant', $newFirstLang);

            if (in_array($newFirstLang, $supportedLanguages)) {
                $this->setLangTag($newFirstLang);

                return $newFirstLang;
            }

            // example: zh-CN, zh-HK, zh-TW
            $zhLangTagArr[] = Str::replace('zh-', 'zh-Hans-', $firstLang);
            $zhLangTagArr[] = Str::replace('zh-', 'zh-Hant-', $firstLang);
            $zhLangTagArr = array_unique($zhLangTagArr);

            // example: ["zh-Hans-CN", "zh-Hans-HK", "zh-Hans-TW", "zh-Hant-CN", "zh-Hant-HK", "zh-Hant-TW"]
            $allIntersect = array_intersect($zhLangTagArr, $supportedLanguages);

            if ($allIntersect) {
                $zhLocale = reset($allIntersect);
                $this->setLangTag($zhLocale);

                return $zhLocale;
            }
        }

        // Not identical, matches the start of the string
        foreach ($supportedLanguages as $supportedLangTag) {
            if (Str::startsWith($supportedLangTag, $substrLang)) {
                $this->setLangTag($supportedLangTag);

                return $supportedLangTag;
            }
        }

        // default locale
        $this->setLangTag($defaultLangTag);

        return $defaultLangTag;
    }

    public function setLangTag(string $langTag)
    {
        App::setLocale($langTag);

        Cookie::queue(Cookie::forever('fresns_lang_tag', $langTag, '/', null, false, false));
    }

    public function setHeaders(Request $request, string $langTag)
    {
        $keyId = ConfigHelper::fresnsConfigByItemKey('website_engine_key_id');
        $keyInfo = PrimaryHelper::fresnsModelById('key', $keyId);

        if (empty($keyInfo)) {
            return Response::view('error', [
                'message' => '<p>'.__('WebsiteEngine::tips.errorKey').'</p><p>'.__('WebsiteEngine::tips.settingTip').'</p>',
                'code' => 403,
            ], 403);
        }

        $headers = [
            'X-Fresns-App-Id' => $keyInfo->app_id,
            'X-Fresns-Client-Platform-Id' => $keyInfo->platform_id,
            'X-Fresns-Client-Version' => fs_theme('version'),
            'X-Fresns-Client-Device-Info' => base64_encode(json_encode(AppHelper::getDeviceInfo())),
            'X-Fresns-Client-Timezone' => Cookie::get('fresns_timezone'),
            'X-Fresns-Client-Lang-Tag' => $langTag,
            'X-Fresns-Client-Content-Format' => null,
            'X-Fresns-Aid' => Cookie::get('fresns_aid'),
            'X-Fresns-Aid-Token' => Cookie::get('fresns_aid_token'),
            'X-Fresns-Uid' => Cookie::get('fresns_uid'),
            'X-Fresns-Uid-Token' => Cookie::get('fresns_uid_token'),
            'X-Fresns-Signature' => null,
            'X-Fresns-Signature-Timestamp' => time(),
        ];
        $headers['X-Fresns-Signature'] = SignHelper::makeSign($headers, $keyInfo->app_key);

        $request->headers->set('X-Fresns-App-Id', $headers['X-Fresns-App-Id']);
        $request->headers->set('X-Fresns-Client-Platform-Id', $headers['X-Fresns-Client-Platform-Id']);
        $request->headers->set('X-Fresns-Client-Version', $headers['X-Fresns-Client-Version']);
        $request->headers->set('X-Fresns-Client-Device-Info', $headers['X-Fresns-Client-Device-Info']);
        $request->headers->set('X-Fresns-Client-Lang-Tag', $headers['X-Fresns-Client-Lang-Tag']);
        $request->headers->set('X-Fresns-Client-Timezone', $headers['X-Fresns-Client-Timezone']);
        $request->headers->set('X-Fresns-Client-Content-Format', null);
        $request->headers->set('X-Fresns-Aid', $headers['X-Fresns-Aid']);
        $request->headers->set('X-Fresns-Aid-Token', $headers['X-Fresns-Aid-Token']);
        $request->headers->set('X-Fresns-Uid', $headers['X-Fresns-Uid']);
        $request->headers->set('X-Fresns-Uid-Token', $headers['X-Fresns-Uid-Token']);
        $request->headers->set('X-Fresns-Signature', $headers['X-Fresns-Signature']);
        $request->headers->set('X-Fresns-Signature-Timestamp', $headers['X-Fresns-Signature-Timestamp']);
    }
}
