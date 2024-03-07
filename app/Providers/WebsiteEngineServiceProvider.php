<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jevan Tang
 * Released under the Apache-2.0 License.
 */

namespace Fresns\WebsiteEngine\Providers;

use App\Helpers\CacheHelper;
use App\Helpers\ConfigHelper;
use Fresns\WebsiteEngine\Auth\AccountGuard;
use Fresns\WebsiteEngine\Auth\UserGuard;
use Illuminate\Support\ServiceProvider;

class WebsiteEngineServiceProvider extends ServiceProvider
{
    /**
     * Register the service provider.
     */
    public function register(): void
    {
        $this->registerViews();
        $this->registerTranslations();
    }

    /**
     * Boot the application events.
     */
    public function boot(): void
    {
        // web engine client
        $websiteEngineStatus = ConfigHelper::fresnsConfigByItemKey('website_engine_status') ?? false;
        if (! $websiteEngineStatus) {
            return;
        }

        $this->registerAuthenticator();
        $this->app->register(RouteServiceProvider::class);

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
     * Register views.
     */
    protected function registerViews(): void
    {
        $this->loadViewsFrom(dirname(__DIR__, 2).'/resources/views', 'FsTheme');
    }

    /**
     * Register translations.
     */
    public function registerTranslations(): void
    {
        $this->loadTranslationsFrom(dirname(__DIR__, 2).'/resources/lang', 'WebsiteEngine');
    }
}
