<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jarvis Tang
 * Released under the Apache-2.0 License.
 */

use App\Models\Config;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;
use Mcamara\LaravelLocalization\Middleware\LaravelLocalizationRedirectFilter;
use Plugins\FresnsEngine\Http\Controllers;
use Plugins\FresnsEngine\Http\Middleware\AccountAuthorize;
use Plugins\FresnsEngine\Http\Middleware\ChangeLanguage;
use Plugins\FresnsEngine\Http\Middleware\CheckModel;
use Plugins\FresnsEngine\Http\Middleware\Register;
use Plugins\FresnsEngine\Http\Middleware\UserAuthorize;
use Plugins\FresnsEngine\Http\Middleware\ViewDefaultPath;

// Backend Route
Route::name('fresnsengine.')->prefix('fresnsengine')->middleware([
    'web',
    'panelAuth',
    ChangeLanguage::class,
])->group(function () {
    // settings
    Route::get('/settings/general', [Controllers\SettingsController::class, 'general'])->name('setting.general');
    Route::post('/settings/general', [Controllers\SettingsController::class, 'postGeneral']);
    Route::get('/settings/key', [Controllers\SettingsController::class, 'key'])->name('setting.key');
    Route::post('/settings/key', [Controllers\SettingsController::class, 'postKey']);
});

// Client Route
Route::name('fresns.')->middleware([
    'web',
    Register::class,
    ViewDefaultPath::class,
    LaravelLocalizationRedirectFilter::class,
])->prefix(LaravelLocalization::setLocale())->group(function () {
    // site model
    Route::get('/private', [Controllers\AccountController::class, 'privateModel'])->name('private');

    // account register and login
    Route::get('/account/register', [Controllers\AccountController::class, 'register'])->name('account.register');
    Route::post('/account/register', [Controllers\AccountController::class, 'postRegister']);
    Route::get('/account/login', [Controllers\AccountController::class, 'login'])->name('account.login');
    Route::post('/account/login', [Controllers\AccountController::class, 'postLogin']);
    Route::get('/account/reset', [Controllers\AccountController::class, 'reset'])->name('account.reset');
    Route::post('/account/reset', [Controllers\AccountController::class, 'postReset']);

    Route::middleware(CheckModel::class)->group(function () {
        // homepage
        try {
            $defaultHomepage = Config::query()->where('item_key', 'default_homepage')->value('item_value');
            Route::get('/', [sprintf('%s\\%sController', Controllers::class, Str::ucfirst($defaultHomepage)), 'index'])->name('home');
        } catch (\Throwable $e) {
        }
        // portal
        Route::get('/portal', [Controllers\PortalController::class, 'index'])->name('portal');
        // users
        Route::get('/users', [Controllers\UsersController::class, 'index'])->name('users.index');
        Route::get('/users/list', [Controllers\UsersController::class, 'list'])->name('users.list');
        // groups
        Route::get('/groups', [Controllers\GroupsController::class, 'index'])->name('groups.index');
        Route::get('/groups/list', [Controllers\GroupsController::class, 'list'])->name('groups.list');
        Route::get('/group/{gid}', [Controllers\GroupsController::class, 'detail'])->name('groups.detail');
        // hashtags
        Route::get('/hashtags', [Controllers\HashtagsController::class, 'index'])->name('hashtags.index');
        Route::get('/hashtags/list', [Controllers\HashtagsController::class, 'list'])->name('hashtags.list');
        Route::get('/hashtag/{hrui}', [Controllers\HashtagsController::class, 'detail'])->name('hashtags.detail');
        // posts
        Route::get('/posts', [Controllers\PostsController::class, 'index'])->name('posts.index');
        Route::get('/posts/list', [Controllers\PostsController::class, 'list'])->name('posts.list');
        Route::get('/posts/nearby', [Controllers\PostsController::class, 'nearby'])->name('posts.nearby');
        Route::get('/posts/nearby/list', [Controllers\PostsController::class, 'nearbyPosts'])->name('posts.nearby.list');
        Route::get('/posts/location/{pid}', [Controllers\PostsController::class, 'location'])->name('posts.location');
        Route::get('/post/{pid}', [Controllers\PostsController::class, 'detail'])->name('posts.detail');
        // comments
        Route::get('/comments', [Controllers\CommentsController::class, 'index'])->name('comments.index');
        Route::get('/comments/list', [Controllers\CommentsController::class, 'list'])->name('comments.list');
        Route::get('/comment/{cid}', [Controllers\CommentsController::class, 'detail'])->name('comments.detail');
        // search
        Route::get('/search', [Controllers\SearchController::class, 'index'])->name('search');

        // account authorize
        Route::group(['middleware' => AccountAuthorize::class], function () {
            Route::get('/account', [Controllers\AccountController::class, 'index'])->name('account');
            Route::any('/users/switch', [Controllers\UsersController::class, 'switch'])->name('users.switch');
            Route::post('/users/login', [Controllers\UsersController::class, 'login'])->name('users.login');
        });

        // user authorize
        Route::group(['middleware' =>[UserAuthorize::class, AddQueuedCookiesToResponse::class]], function () {
            // account
            Route::get('/account/logout', [Controllers\AccountController::class, 'logout'])->name('account.logout');
            Route::get('/account/wallet', [Controllers\AccountController::class, 'wallet'])->name('account.wallet');
            Route::get('/account/users', [Controllers\AccountController::class, 'users'])->name('account.users');
            Route::get('/account/settings', [Controllers\AccountController::class, 'settings'])->name('account.settings');
            // users
            Route::get('/users/likes', [Controllers\UsersController::class, 'likes'])->name('users.likes');
            Route::get('/users/following', [Controllers\UsersController::class, 'following'])->name('users.following');
            Route::get('/users/blocking', [Controllers\UsersController::class, 'blocking'])->name('users.blocking');
            Route::get('/users/follow', [Controllers\UsersController::class, 'follow'])->name('users.follow');
            // groups
            Route::get('/groups/likes', [Controllers\GroupsController::class, 'likes'])->name('groups.likes');
            Route::get('/groups/following', [Controllers\GroupsController::class, 'following'])->name('groups.following');
            Route::get('/groups/blocking', [Controllers\GroupsController::class, 'blocking'])->name('groups.blocking');
            Route::get('/groups/follow', [Controllers\GroupsController::class, 'follow'])->name('groups.follow');
            // hashtags
            Route::get('/hashtags/likes', [Controllers\HashtagsController::class, 'likes'])->name('hashtags.likes');
            Route::get('/hashtags/following', [Controllers\HashtagsController::class, 'following'])->name('hashtags.following');
            Route::get('/hashtags/blocking', [Controllers\HashtagsController::class, 'blocking'])->name('hashtags.blocking');
            Route::get('/hashtags/follow', [Controllers\HashtagsController::class, 'follow'])->name('hashtags.follow');
            // posts
            Route::get('/posts/likes', [Controllers\PostsController::class, 'likes'])->name('posts.likes');
            Route::get('/posts/following', [Controllers\PostsController::class, 'following'])->name('posts.following');
            Route::get('/posts/blocking', [Controllers\PostsController::class, 'blocking'])->name('posts.blocking');
            Route::get('/posts/follows', [Controllers\PostsController::class, 'follows'])->name('posts.follows');
            // comments
            Route::get('/comments/likes', [Controllers\CommentsController::class, 'likes'])->name('comments.likes');
            Route::get('/comments/following', [Controllers\CommentsController::class, 'following'])->name('comments.following');
            Route::get('/comments/blocking', [Controllers\CommentsController::class, 'blocking'])->name('comments.blocking');
            // messages
            Route::get('/messages', [Controllers\MessagesController::class, 'index'])->name('messages.index');
            Route::get('/messages/dialog/{dialogId}', [Controllers\MessagesController::class, 'dialog'])->name('messages.dialog');
            Route::get('/messages/notify/{type}', [Controllers\MessagesController::class, 'notify'])->name('messages.notify');
            Route::post('/messages/notify/read/{type}', [Controllers\MessagesController::class, 'read'])->name('messages.notify.read');
            // editor
            Route::get('/editor/drafts', [Controllers\EditorController::class, 'drafts'])->name('editor.drafts');
            Route::post('/editor/publish', [Controllers\EditorController::class, 'publish'])->name('editor.publish');
            Route::get('/editor/upload', [Controllers\EditorController::class, 'upload'])->name('editor.upload');
            Route::post('/editor/upload', [Controllers\EditorController::class, 'postUpload']);
            Route::resource('/editor', Controllers\EditorController::class);
            // interactive
            Route::get('/file/download/{type}/{fsid}/{fid}', [Controllers\FilesController::class, 'download'])->name('file.download');
            Route::post('/users/mark', [Controllers\UsersController::class, 'mark'])->name('users.mark');
            Route::delete('/users/delete/{type}/{fsid}', [Controllers\UsersController::class, 'delete'])->name('users.delete');
        });

        // profile
        try {
            $itHomeList = Config::query()->where('item_key', 'it_home_list')->value('item_value');
            Route::get('/u/{username}', [Controllers\ProfileController::class, Str::camel(str_replace('it_', '', $itHomeList))])->name('profile');
        } catch (\Throwable $e) {
        }
        Route::get('/u/{username}/posts', [Controllers\ProfileController::class, 'posts'])->name('profile.posts');
        Route::get('/u/{username}/comments', [Controllers\ProfileController::class, 'comments'])->name('profile.comments');
        Route::get('/u/{username}/likers', [Controllers\ProfileController::class, 'likers'])->name('profile.likers');
        Route::get('/u/{username}/followers', [Controllers\ProfileController::class, 'followers'])->name('profile.followers');
        Route::get('/u/{username}/blockers', [Controllers\ProfileController::class, 'blockers'])->name('profile.blockers');
        Route::get('/u/{username}/likes/users', [Controllers\ProfileController::class, 'likeUsers'])->name('profile.likes.users');
        Route::get('/u/{username}/likes/groups', [Controllers\ProfileController::class, 'likeGroups'])->name('profile.likes.groups');
        Route::get('/u/{username}/likes/hashtags', [Controllers\ProfileController::class, 'likeHashtags'])->name('profile.likes.hashtags');
        Route::get('/u/{username}/likes/posts', [Controllers\ProfileController::class, 'likePosts'])->name('profile.likes.posts');
        Route::get('/u/{username}/likes/comments', [Controllers\ProfileController::class, 'likeComments'])->name('profile.likes.comments');
        Route::get('/u/{username}/following/users', [Controllers\ProfileController::class, 'followUsers'])->name('profile.following.users');
        Route::get('/u/{username}/following/groups', [Controllers\ProfileController::class, 'followGroups'])->name('profile.following.groups');
        Route::get('/u/{username}/following/hashtags', [Controllers\ProfileController::class, 'followHashtags'])->name('profile.following.hashtags');
        Route::get('/u/{username}/following/posts', [Controllers\ProfileController::class, 'followPosts'])->name('profile.following.posts');
        Route::get('/u/{username}/following/comments', [Controllers\ProfileController::class, 'followComments'])->name('profile.following.comments');
        Route::get('/u/{username}/blocking/users', [Controllers\ProfileController::class, 'blockUsers'])->name('profile.blocking.users');
        Route::get('/u/{username}/blocking/groups', [Controllers\ProfileController::class, 'blockGroups'])->name('profile.blocking.groups');
        Route::get('/u/{username}/blocking/hashtags', [Controllers\ProfileController::class, 'blockHashtags'])->name('profile.blocking.hashtags');
        Route::get('/u/{username}/blocking/posts', [Controllers\ProfileController::class, 'blockPosts'])->name('profile.blocking.posts');
        Route::get('/u/{username}/blocking/comments', [Controllers\ProfileController::class, 'blockComments'])->name('profile.blocking.comments');
    });
});
