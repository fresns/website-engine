<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jarvis Tang
 * Released under the Apache-2.0 License.
 */

use App\Http\Middleware\EncryptCookies;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Support\Facades\Route;
use Plugins\FresnsEngine\Http\Controllers;
use Plugins\FresnsEngine\Http\Middleware\Register;

Route::name('fresns.api.')->prefix('fresnsengine')->middleware([
    'api',
    AddQueuedCookiesToResponse::class,
    EncryptCookies::class,
    Register::class,
])->group(function () {
    Route::get('/input-tips', [Controllers\ApiController::class, 'getInputTips']);
    Route::get('/drafts', [Controllers\ApiController::class, 'drafts']);
    Route::post('/draft', [Controllers\ApiController::class, 'postDraft']);
    Route::post('/send/verify-code', [Controllers\ApiController::class, 'sendVerifyCode'])->name('send.verifyCode');
    Route::get('/group/list/{category}', [Controllers\ApiController::class, 'groupListByCategory'])->name('group.list');
    Route::post('/account/edit', [Controllers\ApiController::class, 'postAccountSite'])->name('account.edit');
    Route::post('/account/verification', [Controllers\ApiController::class, 'accountVerification'])->name('account.verification');
    Route::post('/user/edit', [Controllers\ApiController::class, 'postUserSite'])->name('user.edit');
    Route::get('/sign', [Controllers\ApiController::class, 'getSign'])->name('get.sign');
    Route::post('/editor/publish', [Controllers\ApiController::class, 'publish'])->name('editor.publish');
});
