<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jarvis Tang
 * Released under the Apache-2.0 License.
 */

namespace Plugins\FresnsEngine\Http\Middleware;

use App\Helpers\CacheHelper;
use App\Helpers\ConfigHelper;
use App\Models\SessionKey;
use Browser;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\View;

class WebConfiguration
{
    public function handle(Request $request, Closure $next)
    {
        $path = Browser::isMobile() ? fs_db_config('FresnsEngine_Mobile') : fs_db_config('FresnsEngine_Desktop');

        if (! $path) {
            return Response::view('error', [
                'message' => Browser::isMobile() ? '<p>'.__('FsWeb::tips.errorMobileTheme').'</p><p>'.__('FsWeb::tips.settingThemeTip').'</p>' : '<p>'.__('FsWeb::tips.errorDesktopTheme').'</p><p>'.__('FsWeb::tips.settingThemeTip').'</p>',
                'code' => 500,
            ], 500);
        }

        if (fs_db_config('engine_api_type') == 'local') {
            if (! fs_db_config('engine_key_id')) {
                return Response::view('error', [
                    'message' => '<p>'.__('FsWeb::tips.errorKey').'</p><p>'.__('FsWeb::tips.settingApiTip').'</p>',
                    'code' => 500,
                ], 500);
            }

            $keyId = fs_db_config('engine_key_id');
            $cacheKey = "fresns_web_key_{$keyId}";

            $keyInfo = Cache::get($cacheKey);

            if (empty($keyInfo)) {
                $keyInfo = SessionKey::find($keyId);

                CacheHelper::put($keyInfo, $cacheKey, ['fresnsWeb', 'fresnsWebConfigs']);
            }

            if (! $keyInfo) {
                return Response::view('error', [
                    'message' => '<p>'.__('FsWeb::tips.errorKey').'</p><p>'.__('FsWeb::tips.settingApiTip').'</p>',
                    'code' => 500,
                ], 500);
            }
        }

        if (fs_db_config('engine_api_type') == 'remote') {
            if (! fs_db_config('engine_api_host') || ! fs_db_config('engine_api_app_id') || ! fs_db_config('engine_api_app_secret')) {
                return Response::view('error', [
                    'message' => '<p>'.__('FsWeb::tips.errorApi').'</p><p>'.__('FsWeb::tips.settingApiTip').'</p>',
                    'code' => 500,
                ], 500);
            }
        }

        $this->loadLanguages();
        $finder = app('view')->getFinder();
        $finder->prependLocation(base_path("extensions/themes/{$path}"));
        $this->webLangTag();

        View::share('engineUnikey', 'FresnsEngine');

        $timezone = fs_user('detail.timezone') ?: ConfigHelper::fresnsConfigByItemKey('default_timezone');
        $timezoneCookieName = fs_db_config('engine_cookie_prefix', 'fresns_').'timezone';
        Cookie::queue($timezoneCookieName, $timezone);

        return $next($request);
    }

    public function loadLanguages()
    {
        $supportedLocales = Cache::get('fresns_web_languages');

        if (empty($supportedLocales)) {
            $menus = fs_api_config('language_menus') ?? [];

            $supportedLocales = [];
            foreach ($menus as $menu) {
                if (! $menu['isEnable']) {
                    continue;
                }
                $supportedLocales[$menu['langTag']] = ['name' => $menu['langName']];
            }

            CacheHelper::put($supportedLocales, 'fresns_web_languages', ['fresnsWeb', 'fresnsWebConfigs']);
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

        $cookiePrefix = fs_db_config('engine_cookie_prefix', 'fresns_');
        Cookie::queue("{$cookiePrefix}lang_tag", $langTag);
    }
}
