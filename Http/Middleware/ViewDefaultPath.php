<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jarvis Tang
 * Released under the Apache-2.0 License.
 */

namespace Plugins\FresnsEngine\Http\Middleware;

use Browser;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\View;
use Plugins\FresnsEngine\Facades\User;
use Plugins\FresnsEngine\Sdk\Factory;

class ViewDefaultPath
{
    public function handle(Request $request, Closure $next)
    {
        $path = Browser::isMobile() ? fresnsengine_config('FresnsEngine_Mobile') : fresnsengine_config('FresnsEngine_Pc');

        if (! fresnsengine_config('fresnsengine_apihost') || ! fresnsengine_config('fresnsengine_appid') || ! fresnsengine_config('fresnsengine_appsecret')) {
            return Response::view('error', [
                'errorMessage' => '<p>'.__('FresnsEngine::tips.errorApi').'</p><p>'.__('FresnsEngine::tips.settingKeyTip').'</p>',
                'errorCode' => 500,
            ], 500);
        }

        if (! $path) {
            return Response::view('error', [
                'errorMessage' => Browser::isMobile() ? '<p>'.__('FresnsEngine::tips.errorMobileTheme').'</p><p>'.__('FresnsEngine::tips.settingThemeTip').'</p>' : '<p>'.__('FresnsEngine::tips.errorPcTheme').'</p><p>'.__('FresnsEngine::tips.settingThemeTip').'</p>',
                'errorCode' => 500,
            ], 500);
        }

        $this->loadLanguages();
        $finder = app('view')->getFinder();
        $finder->prependLocation(resource_path("themes/{$path}"));
        $this->setViewInfo();

        return $next($request);
    }

    private function setViewInfo(): void
    {
        $langTag = current_lang();
        $hashtagShow = fresnsengine_config('hashtag_show');
        $groupCategories = Arr::get(Factory::content()->group->lists(1), 'data.list', []);  // Data not available due to permission issues

        if (User::check()) {
            $overview = Arr::get(Factory::information()->overview->get(), 'data');
            View::share('dialogs', Arr::get(Factory::message()->dialog->lists(10, 1), 'data.list', [])); // Data not available due to permission issues
            View::share('notifyUnread', Arr::get($overview, 'notifyUnread'));
            View::share('dialogUnread', Arr::get($overview, 'dialogUnread'));
        }
        View::share('hashtagShow', $hashtagShow);
        View::share('groupCategories', $groupCategories);
        View::share('lang', $langTag);
    }

    public function loadLanguages()
    {
        $menus = fs_config('language_menus', default_lang());

        $supportedLocales = [];
        foreach ($menus as $menu) {
            $supportedLocales[$menu['langTag']] = ['name' => $menu['langName']];
        }

        app()->get('laravellocalization')->setSupportedLocales($supportedLocales);

        fs_config('language_status') ? Cache::put('supportedLocales', $supportedLocales) : Cache::forget('supportedLocales');
    }
}
