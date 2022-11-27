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
    public function boot()
    {
        config()->set('laravellocalization.useAcceptLanguageHeader', false);

        config()->set('laravellocalization.hideDefaultLocaleInURL', true);

        // Keep the default configuration if you can't query data from the database
        try {
            $defaultLanguage = fs_db_config('default_language');

            $supportedLocales = Cache::get('supportedLocales');
            if (! $supportedLocales) {
                $supportedLocales = [
                    $defaultLanguage => ['name' => $defaultLanguage],
                ];
            }

            // The database cannot query the language configuration information
            if (empty($defaultLanguage)) {
                $defaultLanguage = config('app.locale');
            }
        } catch (\Throwable $e) {
            $cookiePrefix = fs_db_config('engine_cookie_prefix', 'fresns_');
            $langTag = "{$cookiePrefix}lang_tag";

            $defaultLanguage = \request()->header('langTag') ?? \request()->cookie($langTag) ?? config('app.locale');

            $supportedLocales = [
                $defaultLanguage => ['name' => $defaultLanguage],
            ];
        }

        config()->set('laravellocalization.supportedLocales', $supportedLocales);

        config()->set('app.locale', $defaultLanguage);

        $this->app->register(RouteServiceProvider::class);

        Paginator::useBootstrap();
    }

    public function register()
    {
        $this->registerAuthenticator();
        $this->registerTranslations();

        app(\Illuminate\Contracts\Debug\ExceptionHandler::class)->ignore(Plugins\FresnsEngine\Exceptions\ErrorException::class);
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
