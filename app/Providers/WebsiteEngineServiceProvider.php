<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jevan Tang
 * Released under the Apache-2.0 License.
 */

namespace Fresns\WebsiteEngine\Providers;

use App\Helpers\ConfigHelper;
use Fresns\WebsiteEngine\Auth\AccountGuard;
use Fresns\WebsiteEngine\Auth\UserGuard;
use Illuminate\Support\ServiceProvider;

class WebsiteEngineServiceProvider extends ServiceProvider
{
    /**
     * Boot the application events.
     */
    public function boot(): void
    {
        // website engine status
        $websiteEngineStatus = ConfigHelper::fresnsConfigByItemKey('website_engine_status') ?? false;
        if (! $websiteEngineStatus) {
            return;
        }

        $this->registerAuthenticator();
        $this->app->register(RouteServiceProvider::class);
    }

    /**
     * Register the service provider.
     */
    public function register(): void
    {
        $this->registerViews();
        $this->registerTranslations();
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
        $this->loadViewsFrom(dirname(__DIR__, 2).'/resources/views', 'ThemeFunctions');
    }

    /**
     * Register translations.
     */
    public function registerTranslations(): void
    {
        $this->loadTranslationsFrom(dirname(__DIR__, 2).'/resources/lang', 'WebsiteEngine');
    }
}
