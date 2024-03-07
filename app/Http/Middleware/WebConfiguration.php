<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jevan Tang
 * Released under the Apache-2.0 License.
 */

namespace Fresns\WebEngine\Http\Middleware;

use App\Helpers\CacheHelper;
use App\Helpers\ConfigHelper;
use App\Helpers\PrimaryHelper;
use Closure;
use hisorange\BrowserDetect\Parser as Browser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Str;

class WebConfiguration
{
    public function handle(Request $request, Closure $next)
    {
        $themeFskey = fs_theme('fskey');

        $cacheKey = 'fresns_web_middleware';
        $cacheTags = ['fresnsWeb', 'fresnsWebConfigs'];

        $checkMiddleware = CacheHelper::get($cacheKey, $cacheTags);

        if (empty($checkMiddleware)) {
            if (! fs_status('activate')) {
                $langTag = fs_theme('lang');

                $deactivateDescribe = fs_status('deactivateDescribe')[$langTag] ?? fs_status('deactivateDescribe')['default'] ?? '';

                return Response::view('error', [
                    'message' => "<p>{$deactivateDescribe}</p>",
                    'code' => 503,
                ], 503);
            }

            if (! $themeFskey) {
                $errorMessage = Browser::isMobile() ? '<p>'.__('WebEngine::tips.errorMobileFskey').'</p>' : '<p>'.__('WebEngine::tips.errorDesktopFskey').'</p>';

                return Response::view('error', [
                    'message' => $errorMessage.'<p>'.__('WebEngine::tips.settingTip').'</p>',
                    'code' => 400,
                ], 400);
            }

            if (is_local_api()) {
                $keyId = ConfigHelper::fresnsConfigByItemKey('webengine_key_id');
                $keyInfo = PrimaryHelper::fresnsModelById('key', $keyId);

                if (! $keyInfo) {
                    return Response::view('error', [
                        'message' => '<p>'.__('WebEngine::tips.errorKey').'</p><p>'.__('WebEngine::tips.settingTip').'</p>',
                        'code' => 403,
                    ], 403);
                }
            }

            if (is_remote_api()) {
                $apiConfigs = ConfigHelper::fresnsConfigByItemKeys([
                    'webengine_api_host',
                    'webengine_api_app_id',
                    'webengine_api_app_key',
                ]);

                if (! $apiConfigs['webengine_api_host'] || ! $apiConfigs['webengine_api_app_id'] || ! $apiConfigs['webengine_api_app_key']) {
                    return Response::view('error', [
                        'message' => '<p>'.__('WebEngine::tips.errorApi').'</p><p>'.__('WebEngine::tips.settingTip').'</p>',
                        'code' => 403,
                    ], 403);
                }
            }

            $ulid = Str::ulid();

            CacheHelper::put($ulid, $cacheKey, $cacheTags);
        }

        $cookiePrefix = ConfigHelper::fresnsConfigByItemKey('website_cookie_prefix') ?? 'fresns_';

        $cookieUlid = Cookie::get("{$cookiePrefix}ulid");
        if (empty($cookieUlid)) {
            Cookie::queue("{$cookiePrefix}ulid", Str::ulid());
        }

        $finder = app('view')->getFinder();
        $finder->prependLocation(base_path("themes/{$themeFskey}"));
        $this->loadLanguages();
        $this->webLangTag();

        return $next($request);
    }

    public function loadLanguages()
    {
        $cacheKey = 'fresns_web_languages';
        $cacheTags = ['fresnsWeb', 'fresnsWebConfigs'];

        $supportedLocales = CacheHelper::get($cacheKey, $cacheTags);

        if (empty($supportedLocales)) {
            $menus = fs_config('language_menus') ?? [];

            $supportedLocales = [];
            foreach ($menus as $menu) {
                if (! $menu['isEnabled']) {
                    continue;
                }
                $supportedLocales[$menu['langTag']] = ['name' => $menu['langName']];
            }

            CacheHelper::put($supportedLocales, $cacheKey, $cacheTags);
        }

        app()->get('laravellocalization')->setSupportedLocales($supportedLocales);
    }

    public function webLangTag()
    {
        $params = explode('/', request()->getPathInfo());
        array_shift($params);

        $langTag = ConfigHelper::fresnsConfigByItemKey('default_language');
        if (count($params) > 0) {
            $locale = $params[0];

            if (app('laravellocalization')->checkLocaleInSupportedLocales($locale)) {
                $langTag = $locale;
            }
        }

        $cookiePrefix = ConfigHelper::fresnsConfigByItemKey('website_cookie_prefix') ?? 'fresns_';
        Cookie::queue("{$cookiePrefix}lang_tag", $langTag);
    }
}
