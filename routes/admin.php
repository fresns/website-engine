<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jevan Tang
 * Released under the Apache-2.0 License.
 */

use Fresns\WebEngine\Http\Controllers\AdminController;
use Illuminate\Support\Facades\Route;

Route::prefix('admin')->name('admin.')->middleware(['panel', 'panelAuth'])->group(function () {
    Route::get('/', [AdminController::class, 'index'])->name('index');
    Route::put('update', [AdminController::class, 'update'])->name('update');
});
