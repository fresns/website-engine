<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jarvis Tang
 * Released under the Apache-2.0 License.
 */

namespace Plugins\FresnsEngine\Providers;

use App\Helpers\ConfigHelper;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\ServiceProvider;
use Plugins\FresnsEngine\Auth\AccountGuard;
use Plugins\FresnsEngine\Auth\UserGuard;

class FresnsEngineServiceProvider extends ServiceProvider
{
    public function boot()
    {
        config()->set('laravellocalization.useAcceptLanguageHeader', false);

        config()->set('laravellocalization.hideDefaultLocaleInURL', true);

        $supportedLocales = Cache::get('fresns_web_languages');

        if (empty($supportedLocales)) {
            $langMenus = ConfigHelper::fresnsConfigByItemKey('language_menus') ?? [];

            $localeMenus = [];
            foreach ($langMenus as $menu) {
                if (! $menu['isEnable']) {
                    continue;
                }

                $localeMenus[$menu['langTag']] = ['name' => $menu['langName']];
            }

            if (empty($localeMenus)) {
                $cookiePrefix = ConfigHelper::fresnsConfigByItemKey('engine_cookie_prefix') ?? 'fresns_';
                $langCookie = "{$cookiePrefix}lang_tag";

                $defaultLanguage = \request()->header('langTag') ?? \request()->cookie($langCookie) ?? ConfigHelper::fresnsConfigDefaultLangTag();

                $localeMenus = [
                    $defaultLanguage => ['name' => $defaultLanguage],
                ];
            }

            $supportedLocales = $localeMenus;
        }

        config()->set('laravellocalization.supportedLocales', $supportedLocales);

        $defaultLangTag = ConfigHelper::fresnsConfigDefaultLangTag();
        config()->set('app.locale', $defaultLangTag);

        $this->app->register(RouteServiceProvider::class);

        Paginator::useBootstrap();
    }

    public function register()
    {
        $this->registerAuthenticator();
        $this->registerTranslations();
    }

    protected function registerAuthenticator(): void
    {
        app()->singleton('fresns.account', function ($app) {
            return new AccountGuard($app);
        });

        app()->singleton('fresns.user', function ($app) {
            return new UserGuard($app);
        });
    }

    protected function registerTranslations()
    {
        $this->loadTranslationsFrom(__DIR__.'/../../resources/lang', 'FsWeb');
    }
}
