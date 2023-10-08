<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jevan Tang
 * Released under the Apache-2.0 License.
 */

namespace Fresns\WebEngine\Providers;

use App\Helpers\CacheHelper;
use App\Helpers\ConfigHelper;
use Fresns\WebEngine\Auth\AccountGuard;
use Fresns\WebEngine\Auth\UserGuard;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\ServiceProvider;

class WebEngineServiceProvider extends ServiceProvider
{
    /**
     * Register the service provider.
     */
    public function register(): void
    {
        $this->registerTranslations();
        $this->registerViews();
    }

    /**
     * Boot the application events.
     */
    public function boot(): void
    {
        // web engine admin
        $this->app->register(AdminRouteServiceProvider::class);

        // web engine client
        if (! fs_db_config('webengine_status', false)) {
            return;
        }

        $this->registerAuthenticator();
        $this->app->register(RouteServiceProvider::class);
        Paginator::useBootstrap();

        config()->set('laravellocalization.useAcceptLanguageHeader', false);

        config()->set('laravellocalization.hideDefaultLocaleInURL', true);

        // Keep the default configuration if you can't query data from the database
        try {
            $defaultLangTag = ConfigHelper::fresnsConfigDefaultLangTag();

            $cacheKey = 'fresns_web_languages';
            $cacheTags = ['fresnsWeb', 'fresnsWebConfigs'];

            $supportedLocales = CacheHelper::get($cacheKey, $cacheTags);

            if (empty($supportedLocales)) {
                $langMenus = ConfigHelper::fresnsConfigByItemKey('language_menus') ?? [
                    [
                        'isEnabled' => true,
                        'langTag' => $defaultLangTag,
                        'langName' => $defaultLangTag,
                    ],
                ];

                $localeMenus = [];
                foreach ($langMenus as $menu) {
                    if (! $menu['isEnabled']) {
                        continue;
                    }

                    $localeMenus[$menu['langTag']] = ['name' => $menu['langName']];
                }

                $supportedLocales = $localeMenus;
            }
        } catch (\Throwable $e) {
            $cookiePrefix = ConfigHelper::fresnsConfigByItemKey('website_cookie_prefix') ?? 'fresns_';
            $langCookie = "{$cookiePrefix}lang_tag";

            $defaultLangTag = \request()->header('X-Fresns-Client-Lang-Tag') ?? \request()->cookie($langCookie) ?? ConfigHelper::fresnsConfigDefaultLangTag();

            $supportedLocales = [
                $defaultLangTag => ['name' => $defaultLangTag],
            ];
        }

        config()->set('laravellocalization.supportedLocales', $supportedLocales);

        config()->set('app.locale', $defaultLangTag);
    }

    /**
     * Register Authenticator.
     */
    protected function registerAuthenticator(): void
    {
        app()->singleton('fresns.account', function ($app) {
            return new AccountGuard($app);
        });

        app()->singleton('fresns.user', function ($app) {
            return new UserGuard($app);
        });
    }

    /**
     * Register translations.
     */
    public function registerTranslations(): void
    {
        $this->loadTranslationsFrom(dirname(__DIR__, 2).'/resources/lang', 'WebEngine');
    }

    /**
     * Register views.
     */
    public function registerViews(): void
    {
        $this->loadViewsFrom(dirname(__DIR__, 2).'/resources/views', 'WebEngine');
    }
}
