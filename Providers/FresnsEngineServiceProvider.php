<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jarvis Tang
 * Released under the Apache-2.0 License.
 */

namespace Plugins\FresnsEngine\Providers;

use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\ServiceProvider;
use Plugins\FresnsEngine\Auth\AccountGuard;
use Plugins\FresnsEngine\Auth\UserGuard;

class FresnsEngineServiceProvider extends ServiceProvider
{
    /**
     * Booting the package.
     */
    public function boot()
    {
        config()->set('laravellocalization.useAcceptLanguageHeader', false);

        config()->set('laravellocalization.hideDefaultLocaleInURL', true);

        Paginator::useBootstrap();
    }

    /**
     * Register the service provider.
     */
    public function register()
    {
        require_once __DIR__.'/../function.php';

        // init default value
        config()->set('app.locale', default_lang());

        config()->set('laravellocalization.supportedLocales', Cache::get('supportedLocales') ?: [
            default_lang() => ['name' => default_lang()],
        ]);

        $this->registerAuthenticator();
        $this->registerViews();
        $this->registerTranslations();
    }

    /**
     * Register the authenticator services.
     *
     * @return void
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
     *
     * @return void
     */
    protected function registerViews()
    {
        $this->loadViewsFrom(__DIR__.'/../Resources/views', 'FresnsEngine');
    }

    /**
     * Register translations.
     *
     * @return void
     */
    protected function registerTranslations()
    {
        $this->loadTranslationsFrom(__DIR__.'/../Resources/lang', 'FresnsEngine');
    }
}
