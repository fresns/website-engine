<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jevan Tang
 * Released under the Apache-2.0 License.
 */

use App\Helpers\ConfigHelper;
use Fresns\WebsiteEngine\Http\Controllers\CommentController;
use Fresns\WebsiteEngine\Http\Controllers\EditorController;
use Fresns\WebsiteEngine\Http\Controllers\GeotagController;
use Fresns\WebsiteEngine\Http\Controllers\GroupController;
use Fresns\WebsiteEngine\Http\Controllers\HashtagController;
use Fresns\WebsiteEngine\Http\Controllers\MeController;
use Fresns\WebsiteEngine\Http\Controllers\MessageController;
use Fresns\WebsiteEngine\Http\Controllers\NearbyController;
use Fresns\WebsiteEngine\Http\Controllers\PortalController;
use Fresns\WebsiteEngine\Http\Controllers\PostController;
use Fresns\WebsiteEngine\Http\Controllers\ProfileController;
use Fresns\WebsiteEngine\Http\Controllers\SearchController;
use Fresns\WebsiteEngine\Http\Controllers\TimelineController;
use Fresns\WebsiteEngine\Http\Controllers\UserController;
use Fresns\WebsiteEngine\Http\Middleware\AccountAuthorize;
use Fresns\WebsiteEngine\Http\Middleware\CheckSiteModel;
use Fresns\WebsiteEngine\Http\Middleware\UserAuthorize;
use Fresns\WebsiteEngine\Http\Middleware\WebConfiguration;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;

Route::middleware([
        'web',
        WebConfiguration::class,
        AccountAuthorize::class,
        UserAuthorize::class,
        CheckSiteModel::class,
    ])
    ->group(function () {
        $configs = ConfigHelper::fresnsConfigByItemKeys([
            'default_homepage',
            'website_portal_path',
            'website_user_path',
            'website_group_path',
            'website_hashtag_path',
            'website_geotag_path',
            'website_post_path',
            'website_comment_path',
            'website_user_detail_path',
            'website_group_detail_path',
            'website_hashtag_detail_path',
            'website_geotag_detail_path',
            'website_post_detail_path',
            'website_comment_detail_path',
        ]);

        // homepage
        try {
            $defaultHomepage = [sprintf('Fresns\WebsiteEngine\Http\Controllers\%sController', Str::ucfirst($configs['default_homepage'])), 'index'];
            Route::get('/', $defaultHomepage)->name('home')->withoutMiddleware([AccountAuthorize::class, UserAuthorize::class]);
        } catch (\Throwable $e) {
        }

        // portal
        Route::prefix($configs['website_portal_path'])->withoutMiddleware([AccountAuthorize::class, UserAuthorize::class])->group(function () {
            Route::get('/', [PortalController::class, 'index'])->name('portal');
            Route::get('about', [PortalController::class, 'about'])->name('about');
            Route::get('policies', [PortalController::class, 'policies'])->name('policies');
            Route::get('login', [PortalController::class, 'login'])->name('login');
            Route::get('private', [PortalController::class, 'private'])->name('private');
            Route::get('{name}', [PortalController::class, 'customPage'])->name('custom.page');
        });

        // users
        Route::name('user.')->prefix($configs['website_user_path'])->group(function () {
            Route::get('/', [UserController::class, 'index'])->name('index')->withoutMiddleware([AccountAuthorize::class, UserAuthorize::class]);
            Route::get('list', [UserController::class, 'list'])->name('list')->withoutMiddleware([AccountAuthorize::class, UserAuthorize::class]);
            Route::get('likes', [UserController::class, 'likes'])->name('likes');
            Route::get('dislikes', [UserController::class, 'dislikes'])->name('dislikes');
            Route::get('following', [UserController::class, 'following'])->name('following');
            Route::get('blocking', [UserController::class, 'blocking'])->name('blocking');
        });

        // groups
        Route::name('group.')->prefix($configs['website_group_path'])->group(function () {
            Route::get('/', [GroupController::class, 'index'])->name('index')->withoutMiddleware([AccountAuthorize::class, UserAuthorize::class]);
            Route::get('list', [GroupController::class, 'list'])->name('list')->withoutMiddleware([AccountAuthorize::class, UserAuthorize::class]);
            Route::get('likes', [GroupController::class, 'likes'])->name('likes');
            Route::get('dislikes', [GroupController::class, 'dislikes'])->name('dislikes');
            Route::get('following', [GroupController::class, 'following'])->name('following');
            Route::get('blocking', [GroupController::class, 'blocking'])->name('blocking');
        });
        Route::name('group.')->prefix($configs['website_group_detail_path'].'/{gid}')->withoutMiddleware([AccountAuthorize::class, UserAuthorize::class])->group(function () {
            Route::get('/', [GroupController::class, 'detail'])->name('detail');
            Route::get('likers', [GroupController::class, 'detailLikers'])->name('detail.likers');
            Route::get('dislikers', [GroupController::class, 'detailDislikers'])->name('detail.dislikers');
            Route::get('followers', [GroupController::class, 'detailFollowers'])->name('detail.followers');
            Route::get('blockers', [GroupController::class, 'detailBlockers'])->name('detail.blockers');
        });

        // hashtags
        Route::name('hashtag.')->prefix($configs['website_hashtag_path'])->group(function () {
            Route::get('/', [HashtagController::class, 'index'])->name('index')->withoutMiddleware([AccountAuthorize::class, UserAuthorize::class]);
            Route::get('list', [HashtagController::class, 'list'])->name('list')->withoutMiddleware([AccountAuthorize::class, UserAuthorize::class]);
            Route::get('likes', [HashtagController::class, 'likes'])->name('likes');
            Route::get('dislikes', [HashtagController::class, 'dislikes'])->name('dislikes');
            Route::get('following', [HashtagController::class, 'following'])->name('following');
            Route::get('blocking', [HashtagController::class, 'blocking'])->name('blocking');
        });
        Route::name('hashtag.')->prefix($configs['website_hashtag_detail_path'].'/{htid}')->withoutMiddleware([AccountAuthorize::class, UserAuthorize::class])->group(function () {
            Route::get('/', [HashtagController::class, 'detail'])->name('detail');
            Route::get('likers', [HashtagController::class, 'detailLikers'])->name('detail.likers');
            Route::get('dislikers', [HashtagController::class, 'detailDislikers'])->name('detail.dislikers');
            Route::get('followers', [HashtagController::class, 'detailFollowers'])->name('detail.followers');
            Route::get('blockers', [HashtagController::class, 'detailBlockers'])->name('detail.blockers');
        });

        // geotags
        Route::name('geotag.')->prefix($configs['website_geotag_path'])->group(function () {
            Route::get('/', [GeotagController::class, 'index'])->name('index')->withoutMiddleware([AccountAuthorize::class, UserAuthorize::class]);
            Route::get('list', [GeotagController::class, 'list'])->name('list')->withoutMiddleware([AccountAuthorize::class, UserAuthorize::class]);
            Route::get('likes', [GeotagController::class, 'likes'])->name('likes');
            Route::get('dislikes', [GeotagController::class, 'dislikes'])->name('dislikes');
            Route::get('following', [GeotagController::class, 'following'])->name('following');
            Route::get('blocking', [GeotagController::class, 'blocking'])->name('blocking');
        });
        Route::name('geotag.')->prefix($configs['website_geotag_detail_path'].'/{gtid}')->withoutMiddleware([AccountAuthorize::class, UserAuthorize::class])->group(function () {
            Route::get('/', [GeotagController::class, 'detail'])->name('detail');
            Route::get('likers', [GeotagController::class, 'detailLikers'])->name('detail.likers');
            Route::get('dislikers', [GeotagController::class, 'detailDislikers'])->name('detail.dislikers');
            Route::get('followers', [GeotagController::class, 'detailFollowers'])->name('detail.followers');
            Route::get('blockers', [GeotagController::class, 'detailBlockers'])->name('detail.blockers');
        });

        // posts
        Route::name('post.')->prefix($configs['website_post_path'])->group(function () {
            Route::get('/', [PostController::class, 'index'])->name('index')->withoutMiddleware([AccountAuthorize::class, UserAuthorize::class]);
            Route::get('list', [PostController::class, 'list'])->name('list')->withoutMiddleware([AccountAuthorize::class, UserAuthorize::class]);
            Route::get('likes', [PostController::class, 'likes'])->name('likes');
            Route::get('dislikes', [PostController::class, 'dislikes'])->name('dislikes');
            Route::get('following', [PostController::class, 'following'])->name('following');
            Route::get('blocking', [PostController::class, 'blocking'])->name('blocking');
        });
        Route::name('post.')->prefix($configs['website_post_detail_path'].'/{pid}')->withoutMiddleware([AccountAuthorize::class, UserAuthorize::class])->group(function () {
            Route::get('/', [PostController::class, 'detail'])->name('detail');
            Route::get('likers', [PostController::class, 'detailLikers'])->name('detail.likers');
            Route::get('dislikers', [PostController::class, 'detailDislikers'])->name('detail.dislikers');
            Route::get('followers', [PostController::class, 'detailFollowers'])->name('detail.followers');
            Route::get('blockers', [PostController::class, 'detailBlockers'])->name('detail.blockers');
        });

        // comments
        Route::name('comment.')->prefix($configs['website_comment_path'])->group(function () {
            Route::get('/', [CommentController::class, 'index'])->name('index')->withoutMiddleware([AccountAuthorize::class, UserAuthorize::class]);
            Route::get('list', [CommentController::class, 'list'])->name('list')->withoutMiddleware([AccountAuthorize::class, UserAuthorize::class]);
            Route::get('likes', [CommentController::class, 'likes'])->name('likes');
            Route::get('dislikes', [CommentController::class, 'dislikes'])->name('dislikes');
            Route::get('following', [CommentController::class, 'following'])->name('following');
            Route::get('blocking', [CommentController::class, 'blocking'])->name('blocking');
        });
        Route::name('comment.')->prefix($configs['website_comment_detail_path'].'/{cid}')->withoutMiddleware([AccountAuthorize::class, UserAuthorize::class])->group(function () {
            Route::get('/', [CommentController::class, 'detail'])->name('detail');
            Route::get('likers', [CommentController::class, 'detailLikers'])->name('detail.likers');
            Route::get('dislikers', [CommentController::class, 'detailDislikers'])->name('detail.dislikers');
            Route::get('followers', [CommentController::class, 'detailFollowers'])->name('detail.followers');
            Route::get('blockers', [CommentController::class, 'detailBlockers'])->name('detail.blockers');
        });

        // timeline
        Route::name('timeline.')->prefix('timelines')->group(function () {
            Route::get('/', [TimelineController::class, 'index'])->name('index');

            Route::get('posts', [TimelineController::class, 'posts'])->name('posts');
            Route::get('user-posts', [TimelineController::class, 'userPosts'])->name('user.posts');
            Route::get('group-posts', [TimelineController::class, 'groupPosts'])->name('group.posts');
            Route::get('hashtag-posts', [TimelineController::class, 'hashtagPosts'])->name('hashtag.posts');
            Route::get('geotag-posts', [TimelineController::class, 'geotagPosts'])->name('geotag.posts');

            Route::get('comments', [TimelineController::class, 'comments'])->name('comments');
            Route::get('user-comments', [TimelineController::class, 'userComments'])->name('user.comments');
            Route::get('group-comments', [TimelineController::class, 'groupComments'])->name('group.comments');
            Route::get('hashtag-comments', [TimelineController::class, 'hashtagComments'])->name('hashtag.comments');
            Route::get('geotag-comments', [TimelineController::class, 'geotagComments'])->name('geotag.comments');
        });

        // nearby
        Route::name('nearby.')->prefix('nearby')->group(function () {
            Route::get('/', [NearbyController::class, 'index'])->name('index');
            Route::get('posts', [NearbyController::class, 'posts'])->name('posts');
            Route::get('comments', [NearbyController::class, 'comments'])->name('comments');
        });

        // me
        Route::name('me.')->prefix('me')->withoutMiddleware([CheckSiteModel::class])->group(function () {
            Route::get('/', [MeController::class, 'index'])->name('index')->withoutMiddleware([UserAuthorize::class]);
            Route::get('extcredits', [MeController::class, 'userExtcredits'])->name('extcredits');
            Route::get('drafts', [MeController::class, 'drafts'])->name('drafts');
            Route::get('users', [MeController::class, 'users'])->name('users')->withoutMiddleware([UserAuthorize::class]);
            Route::get('wallet', [MeController::class, 'wallet'])->name('wallet')->withoutMiddleware([UserAuthorize::class]);
            Route::get('settings', [MeController::class, 'settings'])->name('settings')->withoutMiddleware([UserAuthorize::class]);
            Route::get('logout', [MeController::class, 'logout'])->name('logout')->withoutMiddleware([UserAuthorize::class]);
        });

        // messages
        Route::name('conversation.')->prefix('conversations')->group(function () {
            Route::get('/', [MessageController::class, 'index'])->name('index');
            Route::get('{uidOrUsername}', [MessageController::class, 'conversation'])->name('detail');
        });
        Route::name('notification.')->prefix('notifications')->group(function () {
            Route::get('/', [MessageController::class, 'notifications'])->name('index');
        });

        // profile
        Route::name('profile.')->prefix($configs['website_user_detail_path'].'/{uidOrUsername}')->withoutMiddleware([AccountAuthorize::class, UserAuthorize::class])->group(function () {
            try {
                $profilePath = ConfigHelper::fresnsConfigByItemKey('profile_default_homepage') ?? 'posts';

                Route::get('/', [ProfileController::class, Str::camel($profilePath)])->name('index');
            } catch (\Throwable $e) {
            }

            Route::get('posts', [ProfileController::class, 'posts'])->name('posts');
            Route::get('comments', [ProfileController::class, 'comments'])->name('comments');
            // mark records
            Route::get('likers', [ProfileController::class, 'likers'])->name('likers');
            Route::get('dislikers', [ProfileController::class, 'dislikers'])->name('dislikers');
            Route::get('followers', [ProfileController::class, 'followers'])->name('followers');
            Route::get('blockers', [ProfileController::class, 'blockers'])->name('blockers');
            Route::get('followers-you-follow', [ProfileController::class, 'followersYouFollow'])->name('followers.you.follow');
            // likers
            Route::get('likes/users', [ProfileController::class, 'likeUsers'])->name('likes.users');
            Route::get('likes/groups', [ProfileController::class, 'likeGroups'])->name('likes.groups');
            Route::get('likes/hashtags', [ProfileController::class, 'likeHashtags'])->name('likes.hashtags');
            Route::get('likes/geotags', [ProfileController::class, 'likeGeotags'])->name('likes.geotags');
            Route::get('likes/posts', [ProfileController::class, 'likePosts'])->name('likes.posts');
            Route::get('likes/comments', [ProfileController::class, 'likeComments'])->name('likes.comments');
            // dislikes
            Route::get('dislikes/users', [ProfileController::class, 'dislikeUsers'])->name('dislikes.users');
            Route::get('dislikes/groups', [ProfileController::class, 'dislikeGroups'])->name('dislikes.groups');
            Route::get('dislikes/hashtags', [ProfileController::class, 'dislikeHashtags'])->name('dislikes.hashtags');
            Route::get('dislikes/geotags', [ProfileController::class, 'dislikeGeotags'])->name('dislikes.geotags');
            Route::get('dislikes/posts', [ProfileController::class, 'dislikePosts'])->name('dislikes.posts');
            Route::get('dislikes/comments', [ProfileController::class, 'dislikeComments'])->name('dislikes.comments');
            // following
            Route::get('following/users', [ProfileController::class, 'followingUsers'])->name('following.users');
            Route::get('following/groups', [ProfileController::class, 'followingGroups'])->name('following.groups');
            Route::get('following/hashtags', [ProfileController::class, 'followingHashtags'])->name('following.hashtags');
            Route::get('following/geotags', [ProfileController::class, 'followingGeotags'])->name('following.geotags');
            Route::get('following/posts', [ProfileController::class, 'followingPosts'])->name('following.posts');
            Route::get('following/comments', [ProfileController::class, 'followingComments'])->name('following.comments');
            // blocking
            Route::get('blocking/users', [ProfileController::class, 'blockingUsers'])->name('blocking.users');
            Route::get('blocking/groups', [ProfileController::class, 'blockingGroups'])->name('blocking.groups');
            Route::get('blocking/hashtags', [ProfileController::class, 'blockingHashtags'])->name('blocking.hashtags');
            Route::get('blocking/geotags', [ProfileController::class, 'blockingGeotags'])->name('blocking.geotags');
            Route::get('blocking/posts', [ProfileController::class, 'blockingPosts'])->name('blocking.posts');
            Route::get('blocking/comments', [ProfileController::class, 'blockingComments'])->name('blocking.comments');
        });

        // search
        Route::name('search.')->prefix('search')->withoutMiddleware([AccountAuthorize::class, UserAuthorize::class])->group(function () {
            Route::get('/', [SearchController::class, 'index'])->name('index');
            Route::get('users', [SearchController::class, 'users'])->name('users');
            Route::get('groups', [SearchController::class, 'groups'])->name('groups');
            Route::get('hashtags', [SearchController::class, 'hashtags'])->name('hashtags');
            Route::get('geotags', [SearchController::class, 'geotags'])->name('geotags');
            Route::get('posts', [SearchController::class, 'posts'])->name('posts');
            Route::get('comments', [SearchController::class, 'comments'])->name('comments');
        });

        // editor
        Route::name('editor.')->prefix('editor')->group(function () {
            // post
            // new post: route('fresns.editor.post')
            // edit draft: route('fresns.editor.post', ['did' => ''])
            // edit published post: route('fresns.editor.post', ['pid' => ''])
            Route::get('post', [EditorController::class, 'post'])->name('post');

            // comment
            // new comment: route('fresns.editor.comment', ['pid' => ''])
            // edit draft: route('fresns.editor.comment', ['did' => ''])
            // edit published comment: route('fresns.editor.comment', ['cid' => ''])
            Route::get('comment', [EditorController::class, 'comment'])->name('comment');

            // edit
            // route('fresns.editor.edit', ['type' => '', 'did' => ''])
            Route::get('{type}/{did}', [EditorController::class, 'edit'])->name('edit');
        });
    });
