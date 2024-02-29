<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jevan Tang
 * Released under the Apache-2.0 License.
 */

use Fresns\WebEngine\Http\Controllers\ApiController;
use Illuminate\Support\Facades\Route;

Route::prefix('theme')->middleware(['web'])->group(function () {
    Route::post('access-token', [ApiController::class, 'makeAccessToken'])->name('make-access-token');

    Route::get('actions/{path}', [ApiController::class, 'get'])->where('path', '.*')->name('get');
    Route::post('actions/{path}', [ApiController::class, 'post'])->where('path', '.*')->name('post');
    Route::put('actions/{path}', [ApiController::class, 'put'])->where('path', '.*')->name('put');
    Route::patch('actions/{path}', [ApiController::class, 'patch'])->where('path', '.*')->name('patch');
    Route::delete('actions/{path}', [ApiController::class, 'delete'])->where('path', '.*')->name('delete');
});
