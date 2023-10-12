<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jevan Tang
 * Released under the Apache-2.0 License.
 */

namespace Fresns\WebEngine\Http\Middleware;

use App\Helpers\AppHelper;
use App\Helpers\CacheHelper;
use App\Helpers\ConfigHelper;
use App\Helpers\PluginHelper;
use App\Models\SessionKey;
use Browser;
use Closure;
use Fresns\PluginManager\Plugin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Str;

class WebConfiguration
{
    public function handle(Request $request, Closure $next)
    {
        if (! fs_status('activate')) {
            $langTag = current_lang_tag();

            $deactivateDescribe = fs_status('deactivateDescribe')[$langTag] ?? fs_status('deactivateDescribe')['default'] ?? '';

            return Response::view('error', [
                'message' => "<p>{$deactivateDescribe}</p>",
                'code' => 503,
            ], 503);
        }

        $clientFskey = Browser::isMobile() ? fs_db_config('webengine_view_mobile') : fs_db_config('webengine_view_desktop');

        $errorMessage = Browser::isMobile() ? '<p>'.__('WebEngine::tips.errorMobileFskey').'</p>' : '<p>'.__('WebEngine::tips.errorDesktopFskey').'</p>';

        if (! $clientFskey) {
            return Response::view('error', [
                'message' => $errorMessage.'<p>'.__('WebEngine::tips.settingTip').'</p>',
                'code' => 400,
            ], 400);
        } else {
            $plugin = new Plugin($clientFskey);

            if (! $plugin->isAvailablePlugin() || ! $plugin->isActivate()) {
                return Response::view('error', [
                    'message' => $errorMessage.'<p>'.__('WebEngine::tips.settingTip').'</p>',
                    'code' => 405,
                ], 405);
            }
        }

        if (is_local_api()) {
            if (! fs_db_config('webengine_key_id')) {
                return Response::view('error', [
                    'message' => '<p>'.__('WebEngine::tips.errorKey').'</p><p>'.__('WebEngine::tips.settingTip').'</p>',
                    'code' => 403,
                ], 403);
            }

            $keyId = fs_db_config('webengine_key_id');
            $cacheKey = "fresns_web_key_{$keyId}";
            $cacheTags = ['fresnsWeb', 'fresnsWebConfigs'];

            $keyInfo = CacheHelper::get($cacheKey, $cacheTags);

            if (empty($keyInfo)) {
                $keyInfo = SessionKey::find($keyId);

                CacheHelper::put($keyInfo, $cacheKey, $cacheTags);
            }

            if (! $keyInfo) {
                return Response::view('error', [
                    'message' => '<p>'.__('WebEngine::tips.errorKey').'</p><p>'.__('WebEngine::tips.settingTip').'</p>',
                    'code' => 403,
                ], 403);
            }
        }

        if (! is_local_api()) {
            if (! fs_db_config('webengine_api_host') || ! fs_db_config('webengine_api_app_id') || ! fs_db_config('webengine_api_app_secret')) {
                return Response::view('error', [
                    'message' => '<p>'.__('WebEngine::tips.errorApi').'</p><p>'.__('WebEngine::tips.settingTip').'</p>',
                    'code' => 403,
                ], 403);
            }
        }

        $finder = app('view')->getFinder();
        $finder->prependLocation(base_path("plugins/{$clientFskey}/resources/views"));
        $this->loadLanguages();
        $this->webLangTag();

        $clientVersion = PluginHelper::fresnsPluginVersionByFskey($clientFskey);

        View::share('fresnsVersion', AppHelper::VERSION_MD5_16BIT);
        View::share('clientFskey', $clientFskey);
        View::share('clientVersion', $clientVersion);

        return $next($request);
    }

    public function loadLanguages()
    {
        $cacheKey = 'fresns_web_languages';
        $cacheTags = ['fresnsWeb', 'fresnsWebConfigs'];

        $supportedLocales = CacheHelper::get($cacheKey, $cacheTags);

        if (empty($supportedLocales)) {
            $menus = fs_api_config('language_menus') ?? [];

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
        $params = explode('/', \request()->getPathInfo());
        array_shift($params);

        $langTag = ConfigHelper::fresnsConfigByItemKey('default_language');
        if (\count($params) > 0) {
            $locale = $params[0];

            if (app('laravellocalization')->checkLocaleInSupportedLocales($locale)) {
                $langTag = $locale;
            }
        }

        $cookiePrefix = fs_db_config('website_cookie_prefix', 'fresns_');
        Cookie::queue("{$cookiePrefix}lang_tag", $langTag);

        // ulid
        $ulid = Cookie::get("{$cookiePrefix}ulid");
        if (empty($ulid)) {
            Cookie::queue("{$cookiePrefix}ulid", Str::ulid());
        }
    }
}
