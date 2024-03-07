<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jevan Tang
 * Released under the Apache-2.0 License.
 */

namespace Fresns\WebsiteEngine\Providers;

use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends ServiceProvider
{
    public function boot()
    {
        parent::boot();
    }

    public function map()
    {
        $this->mapApiRoutes();
        $this->mapWebRoutes();
        $this->mapAdminRoutes();
    }

    protected function mapApiRoutes()
    {
        Route::prefix('api')->name('fresns.api.')->group(dirname(__DIR__, 2).'/routes/api.php');
    }

    protected function mapWebRoutes()
    {
        Route::name('fresns.')->group(dirname(__DIR__, 2).'/routes/web.php');
    }

    protected function mapAdminRoutes()
    {
        Route::name('fresns.')->group(dirname(__DIR__, 2).'/routes/admin.php');
    }
}
