<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jevan Tang
 * Released under the Apache-2.0 License.
 */

use Fresns\WebEngine\Http\Controllers\ThemeFunctionController;
use Illuminate\Support\Facades\Route;

Route::middleware(['panel', 'panelAuth'])->group(function () {
    Route::name('theme-admin.')->prefix('fresns-theme')->group(function () {
        Route::get('{fskey}/functions', [ThemeFunctionController::class, 'index'])->name('index');
    });

    Route::name('api.')->prefix('api/theme')->group(function () {
        Route::put('{fskey}/functions', [ThemeFunctionController::class, 'functions'])->name('functions');
    });
});
