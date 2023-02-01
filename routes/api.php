<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jarvis Tang
 * Released under the Apache-2.0 License.
 */

use Illuminate\Support\Facades\Route;
use Plugins\FresnsEngine\Http\Controllers\ApiController;
use Plugins\FresnsEngine\Http\Middleware\AccountAuthorize;
use Plugins\FresnsEngine\Http\Middleware\CheckSiteModel;
use Plugins\FresnsEngine\Http\Middleware\UserAuthorize;

Route::prefix('engine')
    ->middleware([
        'web',
        AccountAuthorize::class,
        UserAuthorize::class,
        CheckSiteModel::class,
    ])
    ->group(function () {
        Route::get('url-authorization', [ApiController::class, 'urlAuthorization'])->name('url.authorization')->withoutMiddleware([AccountAuthorize::class, UserAuthorize::class, CheckSiteModel::class]);

        // fresns.api.index.list /api/engine/index-list/posts
        // fresns.api.list /api/engine/list/posts
        Route::get('index-list/{type}', [ApiController::class, 'indexList'])->name('index.list');
        Route::get('list/{type}', [ApiController::class, 'list'])->name('list');

        // fresns.api.sub.groups /api/engine/sub-groups/gid
        Route::get('sub-groups/{gid}', [ApiController::class, 'subGroups'])->name('sub.groups');

        Route::get('input-tips', [ApiController::class, 'getInputTips'])->name('input.tips')->withoutMiddleware([AccountAuthorize::class, UserAuthorize::class]);
        Route::get('archives', [ApiController::class, 'getArchives'])->name('archives')->withoutMiddleware([UserAuthorize::class]);
        Route::post('send-verify-code', [ApiController::class, 'sendVerifyCode'])->name('send.verify.code')->withoutMiddleware([AccountAuthorize::class, UserAuthorize::class]);
        Route::post('upload-file', [ApiController::class, 'uploadFile'])->name('upload.file');

        Route::prefix('account')->name('account.')->withoutMiddleware([UserAuthorize::class, CheckSiteModel::class])->group(function () {
            Route::post('register', [ApiController::class, 'accountRegister'])->name('register')->withoutMiddleware([AccountAuthorize::class]);
            Route::post('login', [ApiController::class, 'accountLogin'])->name('login')->withoutMiddleware([AccountAuthorize::class]);
            Route::post('connect-login', [ApiController::class, 'accountConnectLogin'])->name('connect.login')->withoutMiddleware([AccountAuthorize::class]);
            Route::post('reset-password', [ApiController::class, 'accountResetPassword'])->name('reset.password')->withoutMiddleware([AccountAuthorize::class]);
            Route::post('verify-identity', [ApiController::class, 'accountVerifyIdentity'])->name('verify.identity');
            Route::post('edit', [ApiController::class, 'accountEdit'])->name('edit');
            Route::post('apply-delete', [ApiController::class, 'accountApplyDelete'])->name('apply.delete')->withoutMiddleware([UserAuthorize::class]);
            Route::post('recall-delete', [ApiController::class, 'accountRecallDelete'])->name('recall.delete')->withoutMiddleware([UserAuthorize::class]);
        });

        Route::prefix('user')->name('user.')->group(function () {
            Route::post('auth', [ApiController::class, 'userAuth'])->name('auth')->withoutMiddleware([UserAuthorize::class, CheckSiteModel::class]);
            Route::post('edit', [ApiController::class, 'userEdit'])->name('edit');
            Route::post('mark', [ApiController::class, 'userMark'])->name('mark');
            Route::put('mark-note', [ApiController::class, 'userMarkNote'])->name('mark.note');
        });

        Route::prefix('message')->name('message.')->group(function () {
            Route::put('{type}', [ApiController::class, 'messageMarkAsRead'])->name('mark.as.read');
            Route::delete('{type}', [ApiController::class, 'messageDelete'])->name('delete');
            Route::post('send', [ApiController::class, 'messageSend'])->name('send');
        });

        Route::prefix('content')->name('content.')->group(function () {
            Route::get('file/{fid}/link', [ApiController::class, 'contentFileLink'])->name('file.link');
            Route::get('file/{fid}/users', [ApiController::class, 'contentFileUsers'])->name('file.users')->withoutMiddleware([AccountAuthorize::class, UserAuthorize::class]);
            Route::delete('{type}/{fsid}', [ApiController::class, 'contentDelete'])->name('delete');
        });

        Route::prefix('editor')->name('editor.')->group(function () {
            Route::post('{type}/quick-publish', [ApiController::class, 'editorQuickPublish'])->name('quick.publish');
            Route::post('{type}/update/{draftId}', [ApiController::class, 'editorUpdate'])->name('update');
            Route::post('{type}/publish/{draftId}', [ApiController::class, 'editorPublish'])->name('publish');
            Route::patch('{type}/recall/{draftId}', [ApiController::class, 'editorRecall'])->name('recall');
            Route::delete('{type}/delete/{draftId}', [ApiController::class, 'editorDelete'])->name('delete');
            Route::post('upload-file', [ApiController::class, 'editorUploadFile'])->name('upload.file');
        });

        // FsLang
        Route::get('js/{locale?}/translations', function ($locale) {
            $languagePack = fs_api_config('language_pack_contents');

            // get request, return translation content
            return \response()->json([
                'data' => $languagePack,
            ]);
        })->name('translations')->withoutMiddleware([AccountAuthorize::class, UserAuthorize::class, CheckSiteModel::class]);
    });
