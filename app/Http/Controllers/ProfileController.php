<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jarvis Tang
 * Released under the Apache-2.0 License.
 */

namespace Plugins\FresnsEngine\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;
use Plugins\FresnsEngine\Exceptions\ErrorException;
use Plugins\FresnsEngine\Helpers\ApiHelper;
use Plugins\FresnsEngine\Helpers\QueryHelper;

class ProfileController extends Controller
{
    // posts
    public function posts(Request $request, string $uidOrUsername)
    {
        $query = $request->all();
        $query['uidOrUsername'] = $uidOrUsername;

        if (! fs_db_config('website_status')) {
            $query['pageSize'] = fs_db_config('website_number');
            $query['page'] = 1;
        }

        $client = ApiHelper::make();

        $results = $client->unwrapRequests([
            'profile' => $client->getAsync("/api/v2/user/{$uidOrUsername}/detail"),
            'followersYouFollow' => $client->getAsync("/api/v2/user/{$uidOrUsername}/followers-you-follow", [
                'query' => [
                    'pageSize' => 3,
                    'page' => 1,
                ],
            ]),
            'posts' => $client->getAsync('/api/v2/post/list', [
                'query' => $query,
            ]),
        ]);

        if ($results['profile']['code'] != 0) {
            throw new ErrorException($results['profile']['message'], $results['profile']['code']);
        }

        $items = $results['profile']['data']['items'];
        $profile = $results['profile']['data']['detail'];
        $followersYouFollow = $results['followersYouFollow']['data']['list'];

        $posts = QueryHelper::convertApiDataToPaginate(
            items: $results['posts']['data']['list'],
            paginate: $results['posts']['data']['paginate'],
        );

        // ajax
        if ($request->ajax()) {
            $html = '';
            foreach ($results['posts']['data']['list'] as $post) {
                $html .= View::make('components.post.list', compact('post'))->render();
            }

            return response()->json([
                'paginate' => $results['posts']['data']['paginate'],
                'html' => $html,
            ]);
        }

        // view
        return view('profile.posts', compact('items', 'profile', 'followersYouFollow', 'posts'));
    }

    // comments
    public function comments(Request $request, string $uidOrUsername)
    {
        $query = $request->all();
        $query['uidOrUsername'] = $uidOrUsername;

        if (! fs_db_config('website_status')) {
            $query['pageSize'] = fs_db_config('website_number');
            $query['page'] = 1;
        }

        $client = ApiHelper::make();

        $results = $client->unwrapRequests([
            'profile' => $client->getAsync("/api/v2/user/{$uidOrUsername}/detail"),
            'followersYouFollow' => $client->getAsync("/api/v2/user/{$uidOrUsername}/followers-you-follow", [
                'query' => [
                    'pageSize' => 3,
                    'page' => 1,
                ],
            ]),
            'comments' => $client->getAsync('/api/v2/comment/list', [
                'query' => $query,
            ]),
        ]);

        if ($results['profile']['code'] != 0) {
            throw new ErrorException($results['profile']['message'], $results['profile']['code']);
        }

        $items = $results['profile']['data']['items'];
        $profile = $results['profile']['data']['detail'];
        $followersYouFollow = $results['followersYouFollow']['data']['list'];

        $comments = QueryHelper::convertApiDataToPaginate(
            items: $results['comments']['data']['list'],
            paginate: $results['comments']['data']['paginate'],
        );

        // ajax
        if ($request->ajax()) {
            $html = '';
            foreach ($results['comments']['data']['list'] as $comment) {
                $html .= View::make('components.comment.list', compact('comment'))->render();
            }

            return response()->json([
                'paginate' => $results['comments']['data']['paginate'],
                'html' => $html,
            ]);
        }

        // view
        return view('profile.comments', compact('items', 'profile', 'followersYouFollow', 'comments'));
    }

    // likers
    public function likers(Request $request, string $uidOrUsername)
    {
        $query = $request->all();

        if (! fs_db_config('website_status')) {
            $query['pageSize'] = fs_db_config('website_number');
            $query['page'] = 1;
        }

        $client = ApiHelper::make();

        $results = $client->unwrapRequests([
            'profile' => $client->getAsync("/api/v2/user/{$uidOrUsername}/detail"),
            'followersYouFollow' => $client->getAsync("/api/v2/user/{$uidOrUsername}/followers-you-follow", [
                'query' => [
                    'pageSize' => 3,
                    'page' => 1,
                ],
            ]),
            'users' => $client->getAsync("/api/v2/user/{$uidOrUsername}/interaction/like", [
                'query' => $query,
            ]),
        ]);

        if ($results['profile']['code'] != 0) {
            throw new ErrorException($results['profile']['message'], $results['profile']['code']);
        }

        $items = $results['profile']['data']['items'];
        $profile = $results['profile']['data']['detail'];
        $followersYouFollow = $results['followersYouFollow']['data']['list'];

        $users = QueryHelper::convertApiDataToPaginate(
            items: $results['users']['data']['list'],
            paginate: $results['users']['data']['paginate'],
        );

        // ajax
        if ($request->ajax()) {
            $html = '';
            foreach ($results['users']['data']['list'] as $user) {
                $html .= View::make('components.user.list', compact('user'))->render();
            }

            return response()->json([
                'paginate' => $results['users']['data']['paginate'],
                'html' => $html,
            ]);
        }

        // view
        return view('profile.likers', compact('items', 'profile', 'followersYouFollow', 'users'));
    }

    // dislikers
    public function dislikers(Request $request, string $uidOrUsername)
    {
        $query = $request->all();

        if (! fs_db_config('website_status')) {
            $query['pageSize'] = fs_db_config('website_number');
            $query['page'] = 1;
        }

        $client = ApiHelper::make();

        $results = $client->unwrapRequests([
            'profile' => $client->getAsync("/api/v2/user/{$uidOrUsername}/detail"),
            'followersYouFollow' => $client->getAsync("/api/v2/user/{$uidOrUsername}/followers-you-follow", [
                'query' => [
                    'pageSize' => 3,
                    'page' => 1,
                ],
            ]),
            'users' => $client->getAsync("/api/v2/user/{$uidOrUsername}/interaction/dislike", [
                'query' => $query,
            ]),
        ]);

        if ($results['profile']['code'] != 0) {
            throw new ErrorException($results['profile']['message'], $results['profile']['code']);
        }

        $items = $results['profile']['data']['items'];
        $profile = $results['profile']['data']['detail'];
        $followersYouFollow = $results['followersYouFollow']['data']['list'];

        $users = QueryHelper::convertApiDataToPaginate(
            items: $results['users']['data']['list'],
            paginate: $results['users']['data']['paginate'],
        );

        // ajax
        if ($request->ajax()) {
            $html = '';
            foreach ($results['users']['data']['list'] as $user) {
                $html .= View::make('components.user.list', compact('user'))->render();
            }

            return response()->json([
                'paginate' => $results['users']['data']['paginate'],
                'html' => $html,
            ]);
        }

        // view
        return view('profile.dislikers', compact('items', 'profile', 'followersYouFollow', 'users'));
    }

    // followers
    public function followers(Request $request, string $uidOrUsername)
    {
        $query = $request->all();

        if (! fs_db_config('website_status')) {
            $query['pageSize'] = fs_db_config('website_number');
            $query['page'] = 1;
        }

        $client = ApiHelper::make();

        $results = $client->unwrapRequests([
            'profile' => $client->getAsync("/api/v2/user/{$uidOrUsername}/detail"),
            'followersYouFollow' => $client->getAsync("/api/v2/user/{$uidOrUsername}/followers-you-follow", [
                'query' => [
                    'pageSize' => 3,
                    'page' => 1,
                ],
            ]),
            'users' => $client->getAsync("/api/v2/user/{$uidOrUsername}/interaction/follow", [
                'query' => $query,
            ]),
        ]);

        if ($results['profile']['code'] != 0) {
            throw new ErrorException($results['profile']['message'], $results['profile']['code']);
        }

        $items = $results['profile']['data']['items'];
        $profile = $results['profile']['data']['detail'];
        $followersYouFollow = $results['followersYouFollow']['data']['list'];

        $users = QueryHelper::convertApiDataToPaginate(
            items: $results['users']['data']['list'],
            paginate: $results['users']['data']['paginate'],
        );

        // ajax
        if ($request->ajax()) {
            $html = '';
            foreach ($results['users']['data']['list'] as $user) {
                $html .= View::make('components.user.list', compact('user'))->render();
            }

            return response()->json([
                'paginate' => $results['users']['data']['paginate'],
                'html' => $html,
            ]);
        }

        // view
        return view('profile.followers', compact('items', 'profile', 'followersYouFollow', 'users'));
    }

    // followers you follow
    public function followersYouFollow(Request $request, string $uidOrUsername)
    {
        $query = $request->all();

        if (! fs_db_config('website_status')) {
            $query['pageSize'] = fs_db_config('website_number');
            $query['page'] = 1;
        }

        $client = ApiHelper::make();

        $results = $client->unwrapRequests([
            'profile' => $client->getAsync("/api/v2/user/{$uidOrUsername}/detail"),
            'followersYouFollow' => $client->getAsync("/api/v2/user/{$uidOrUsername}/followers-you-follow", [
                'query' => [
                    'pageSize' => 3,
                    'page' => 1,
                ],
            ]),
            'users' => $client->getAsync("/api/v2/user/{$uidOrUsername}/followers-you-follow", [
                'query' => $query,
            ]),
        ]);

        if ($results['profile']['code'] != 0) {
            throw new ErrorException($results['profile']['message'], $results['profile']['code']);
        }

        $items = $results['profile']['data']['items'];
        $profile = $results['profile']['data']['detail'];
        $followersYouFollow = $results['followersYouFollow']['data']['list'];

        $users = QueryHelper::convertApiDataToPaginate(
            items: $results['users']['data']['list'],
            paginate: $results['users']['data']['paginate'],
        );

        // ajax
        if ($request->ajax()) {
            $html = '';
            foreach ($results['users']['data']['list'] as $user) {
                $html .= View::make('components.user.list', compact('user'))->render();
            }

            return response()->json([
                'paginate' => $results['users']['data']['paginate'],
                'html' => $html,
            ]);
        }

        // view
        return view('profile.followers-you-follow', compact('items', 'profile', 'followersYouFollow', 'users'));
    }

    // blockers
    public function blockers(Request $request, string $uidOrUsername)
    {
        $query = $request->all();

        if (! fs_db_config('website_status')) {
            $query['pageSize'] = fs_db_config('website_number');
            $query['page'] = 1;
        }

        $client = ApiHelper::make();

        $results = $client->unwrapRequests([
            'profile' => $client->getAsync("/api/v2/user/{$uidOrUsername}/detail"),
            'followersYouFollow' => $client->getAsync("/api/v2/user/{$uidOrUsername}/followers-you-follow", [
                'query' => [
                    'pageSize' => 3,
                    'page' => 1,
                ],
            ]),
            'users' => $client->getAsync("/api/v2/user/{$uidOrUsername}/interaction/block", [
                'query' => $query,
            ]),
        ]);

        if ($results['profile']['code'] != 0) {
            throw new ErrorException($results['profile']['message'], $results['profile']['code']);
        }

        $items = $results['profile']['data']['items'];
        $profile = $results['profile']['data']['detail'];
        $followersYouFollow = $results['followersYouFollow']['data']['list'];

        $users = QueryHelper::convertApiDataToPaginate(
            items: $results['users']['data']['list'],
            paginate: $results['users']['data']['paginate'],
        );

        // ajax
        if ($request->ajax()) {
            $html = '';
            foreach ($results['users']['data']['list'] as $user) {
                $html .= View::make('components.user.list', compact('user'))->render();
            }

            return response()->json([
                'paginate' => $results['users']['data']['paginate'],
                'html' => $html,
            ]);
        }

        // view
        return view('profile.blockers', compact('items', 'profile', 'followersYouFollow', 'users'));
    }

    /**
     * like.
     */

    // likeUsers
    public function likeUsers(Request $request, string $uidOrUsername)
    {
        $query = $request->all();

        if (! fs_db_config('website_status')) {
            $query['pageSize'] = fs_db_config('website_number');
            $query['page'] = 1;
        }

        $client = ApiHelper::make();

        $results = $client->unwrapRequests([
            'profile' => $client->getAsync("/api/v2/user/{$uidOrUsername}/detail"),
            'followersYouFollow' => $client->getAsync("/api/v2/user/{$uidOrUsername}/followers-you-follow", [
                'query' => [
                    'pageSize' => 3,
                    'page' => 1,
                ],
            ]),
            'users' => $client->getAsync("/api/v2/user/{$uidOrUsername}/mark/like/users", [
                'query' => $query,
            ]),
        ]);

        if ($results['profile']['code'] != 0) {
            throw new ErrorException($results['profile']['message'], $results['profile']['code']);
        }

        $items = $results['profile']['data']['items'];
        $profile = $results['profile']['data']['detail'];
        $followersYouFollow = $results['followersYouFollow']['data']['list'];

        $users = QueryHelper::convertApiDataToPaginate(
            items: $results['users']['data']['list'],
            paginate: $results['users']['data']['paginate'],
        );

        // ajax
        if ($request->ajax()) {
            $html = '';
            foreach ($results['users']['data']['list'] as $user) {
                $html .= View::make('components.user.list', compact('user'))->render();
            }

            return response()->json([
                'paginate' => $results['users']['data']['paginate'],
                'html' => $html,
            ]);
        }

        // view
        return view('profile.likes.users', compact('items', 'profile', 'followersYouFollow', 'users'));
    }

    // likeGroups
    public function likeGroups(Request $request, string $uidOrUsername)
    {
        $query = $request->all();

        if (! fs_db_config('website_status')) {
            $query['pageSize'] = fs_db_config('website_number');
            $query['page'] = 1;
        }

        $client = ApiHelper::make();

        $results = $client->unwrapRequests([
            'profile' => $client->getAsync("/api/v2/user/{$uidOrUsername}/detail"),
            'followersYouFollow' => $client->getAsync("/api/v2/user/{$uidOrUsername}/followers-you-follow", [
                'query' => [
                    'pageSize' => 3,
                    'page' => 1,
                ],
            ]),
            'groups' => $client->getAsync("/api/v2/user/{$uidOrUsername}/mark/like/groups", [
                'query' => $query,
            ]),
        ]);

        if ($results['profile']['code'] != 0) {
            throw new ErrorException($results['profile']['message'], $results['profile']['code']);
        }

        $items = $results['profile']['data']['items'];
        $profile = $results['profile']['data']['detail'];
        $followersYouFollow = $results['followersYouFollow']['data']['list'];

        $groups = QueryHelper::convertApiDataToPaginate(
            items: $results['groups']['data']['list'],
            paginate: $results['groups']['data']['paginate'],
        );

        // ajax
        if ($request->ajax()) {
            $html = '';
            foreach ($results['groups']['data']['list'] as $group) {
                $html .= View::make('components.group.list', compact('group'))->render();
            }

            return response()->json([
                'paginate' => $results['groups']['data']['paginate'],
                'html' => $html,
            ]);
        }

        // view
        return view('profile.likes.groups', compact('items', 'profile', 'followersYouFollow', 'groups'));
    }

    // likeHashtags
    public function likeHashtags(Request $request, string $uidOrUsername)
    {
        $query = $request->all();

        if (! fs_db_config('website_status')) {
            $query['pageSize'] = fs_db_config('website_number');
            $query['page'] = 1;
        }

        $client = ApiHelper::make();

        $results = $client->unwrapRequests([
            'profile' => $client->getAsync("/api/v2/user/{$uidOrUsername}/detail"),
            'followersYouFollow' => $client->getAsync("/api/v2/user/{$uidOrUsername}/followers-you-follow", [
                'query' => [
                    'pageSize' => 3,
                    'page' => 1,
                ],
            ]),
            'hashtags' => $client->getAsync("/api/v2/user/{$uidOrUsername}/mark/like/hashtags", [
                'query' => $query,
            ]),
        ]);

        if ($results['profile']['code'] != 0) {
            throw new ErrorException($results['profile']['message'], $results['profile']['code']);
        }

        $items = $results['profile']['data']['items'];
        $profile = $results['profile']['data']['detail'];
        $followersYouFollow = $results['followersYouFollow']['data']['list'];

        $hashtags = QueryHelper::convertApiDataToPaginate(
            items: $results['hashtags']['data']['list'],
            paginate: $results['hashtags']['data']['paginate'],
        );

        // ajax
        if ($request->ajax()) {
            $html = '';
            foreach ($results['hashtags']['data']['list'] as $hashtag) {
                $html .= View::make('components.hashtag.list', compact('hashtag'))->render();
            }

            return response()->json([
                'paginate' => $results['hashtags']['data']['paginate'],
                'html' => $html,
            ]);
        }

        // view
        return view('profile.likes.hashtags', compact('items', 'profile', 'followersYouFollow', 'hashtags'));
    }

    // likePosts
    public function likePosts(Request $request, string $uidOrUsername)
    {
        $query = $request->all();

        if (! fs_db_config('website_status')) {
            $query['pageSize'] = fs_db_config('website_number');
            $query['page'] = 1;
        }

        $client = ApiHelper::make();

        $results = $client->unwrapRequests([
            'profile' => $client->getAsync("/api/v2/user/{$uidOrUsername}/detail"),
            'followersYouFollow' => $client->getAsync("/api/v2/user/{$uidOrUsername}/followers-you-follow", [
                'query' => [
                    'pageSize' => 3,
                    'page' => 1,
                ],
            ]),
            'posts' => $client->getAsync("/api/v2/user/{$uidOrUsername}/mark/like/posts", [
                'query' => $query,
            ]),
        ]);

        if ($results['profile']['code'] != 0) {
            throw new ErrorException($results['profile']['message'], $results['profile']['code']);
        }

        $items = $results['profile']['data']['items'];
        $profile = $results['profile']['data']['detail'];
        $followersYouFollow = $results['followersYouFollow']['data']['list'];

        $posts = QueryHelper::convertApiDataToPaginate(
            items: $results['posts']['data']['list'],
            paginate: $results['posts']['data']['paginate'],
        );

        // ajax
        if ($request->ajax()) {
            $html = '';
            foreach ($results['posts']['data']['list'] as $post) {
                $html .= View::make('components.post.list', compact('post'))->render();
            }

            return response()->json([
                'paginate' => $results['posts']['data']['paginate'],
                'html' => $html,
            ]);
        }

        // view
        return view('profile.likes.posts', compact('items', 'profile', 'followersYouFollow', 'posts'));
    }

    // likeComments
    public function likeComments(Request $request, string $uidOrUsername)
    {
        $query = $request->all();

        if (! fs_db_config('website_status')) {
            $query['pageSize'] = fs_db_config('website_number');
            $query['page'] = 1;
        }

        $client = ApiHelper::make();

        $results = $client->unwrapRequests([
            'profile' => $client->getAsync("/api/v2/user/{$uidOrUsername}/detail"),
            'followersYouFollow' => $client->getAsync("/api/v2/user/{$uidOrUsername}/followers-you-follow", [
                'query' => [
                    'pageSize' => 3,
                    'page' => 1,
                ],
            ]),
            'comments' => $client->getAsync("/api/v2/user/{$uidOrUsername}/mark/like/comments", [
                'query' => $query,
            ]),
        ]);

        if ($results['profile']['code'] != 0) {
            throw new ErrorException($results['profile']['message'], $results['profile']['code']);
        }

        $items = $results['profile']['data']['items'];
        $profile = $results['profile']['data']['detail'];
        $followersYouFollow = $results['followersYouFollow']['data']['list'];

        $comments = QueryHelper::convertApiDataToPaginate(
            items: $results['comments']['data']['list'],
            paginate: $results['comments']['data']['paginate'],
        );

        // ajax
        if ($request->ajax()) {
            $html = '';
            foreach ($results['comments']['data']['list'] as $comment) {
                $html .= View::make('components.comment.list', compact('comment'))->render();
            }

            return response()->json([
                'paginate' => $results['comments']['data']['paginate'],
                'html' => $html,
            ]);
        }

        // view
        return view('profile.likes.comments', compact('items', 'profile', 'followersYouFollow', 'comments'));
    }

    /**
     * dislike.
     */

    // dislikeUsers
    public function dislikeUsers(Request $request, string $uidOrUsername)
    {
        $query = $request->all();

        if (! fs_db_config('website_status')) {
            $query['pageSize'] = fs_db_config('website_number');
            $query['page'] = 1;
        }

        $client = ApiHelper::make();

        $results = $client->unwrapRequests([
            'profile' => $client->getAsync("/api/v2/user/{$uidOrUsername}/detail"),
            'followersYouFollow' => $client->getAsync("/api/v2/user/{$uidOrUsername}/followers-you-follow", [
                'query' => [
                    'pageSize' => 3,
                    'page' => 1,
                ],
            ]),
            'users' => $client->getAsync("/api/v2/user/{$uidOrUsername}/mark/dislike/users", [
                'query' => $query,
            ]),
        ]);

        if ($results['profile']['code'] != 0) {
            throw new ErrorException($results['profile']['message'], $results['profile']['code']);
        }

        $items = $results['profile']['data']['items'];
        $profile = $results['profile']['data']['detail'];
        $followersYouFollow = $results['followersYouFollow']['data']['list'];

        $users = QueryHelper::convertApiDataToPaginate(
            items: $results['users']['data']['list'],
            paginate: $results['users']['data']['paginate'],
        );

        // ajax
        if ($request->ajax()) {
            $html = '';
            foreach ($results['users']['data']['list'] as $user) {
                $html .= View::make('components.user.list', compact('user'))->render();
            }

            return response()->json([
                'paginate' => $results['users']['data']['paginate'],
                'html' => $html,
            ]);
        }

        // view
        return view('profile.dislikes.users', compact('items', 'profile', 'followersYouFollow', 'users'));
    }

    // dislikeGroups
    public function dislikeGroups(Request $request, string $uidOrUsername)
    {
        $query = $request->all();

        if (! fs_db_config('website_status')) {
            $query['pageSize'] = fs_db_config('website_number');
            $query['page'] = 1;
        }

        $client = ApiHelper::make();

        $results = $client->unwrapRequests([
            'profile' => $client->getAsync("/api/v2/user/{$uidOrUsername}/detail"),
            'followersYouFollow' => $client->getAsync("/api/v2/user/{$uidOrUsername}/followers-you-follow", [
                'query' => [
                    'pageSize' => 3,
                    'page' => 1,
                ],
            ]),
            'groups' => $client->getAsync("/api/v2/user/{$uidOrUsername}/mark/dislike/groups", [
                'query' => $query,
            ]),
        ]);

        if ($results['profile']['code'] != 0) {
            throw new ErrorException($results['profile']['message'], $results['profile']['code']);
        }

        $items = $results['profile']['data']['items'];
        $profile = $results['profile']['data']['detail'];
        $followersYouFollow = $results['followersYouFollow']['data']['list'];

        $groups = QueryHelper::convertApiDataToPaginate(
            items: $results['groups']['data']['list'],
            paginate: $results['groups']['data']['paginate'],
        );

        // ajax
        if ($request->ajax()) {
            $html = '';
            foreach ($results['groups']['data']['list'] as $group) {
                $html .= View::make('components.group.list', compact('group'))->render();
            }

            return response()->json([
                'paginate' => $results['groups']['data']['paginate'],
                'html' => $html,
            ]);
        }

        // view
        return view('profile.dislikes.groups', compact('items', 'profile', 'followersYouFollow', 'groups'));
    }

    // dislikeHashtags
    public function dislikeHashtags(Request $request, string $uidOrUsername)
    {
        $query = $request->all();

        if (! fs_db_config('website_status')) {
            $query['pageSize'] = fs_db_config('website_number');
            $query['page'] = 1;
        }

        $client = ApiHelper::make();

        $results = $client->unwrapRequests([
            'profile' => $client->getAsync("/api/v2/user/{$uidOrUsername}/detail"),
            'followersYouFollow' => $client->getAsync("/api/v2/user/{$uidOrUsername}/followers-you-follow", [
                'query' => [
                    'pageSize' => 3,
                    'page' => 1,
                ],
            ]),
            'hashtags' => $client->getAsync("/api/v2/user/{$uidOrUsername}/mark/dislike/hashtags", [
                'query' => $query,
            ]),
        ]);

        if ($results['profile']['code'] != 0) {
            throw new ErrorException($results['profile']['message'], $results['profile']['code']);
        }

        $items = $results['profile']['data']['items'];
        $profile = $results['profile']['data']['detail'];
        $followersYouFollow = $results['followersYouFollow']['data']['list'];

        $hashtags = QueryHelper::convertApiDataToPaginate(
            items: $results['hashtags']['data']['list'],
            paginate: $results['hashtags']['data']['paginate'],
        );

        // ajax
        if ($request->ajax()) {
            $html = '';
            foreach ($results['hashtags']['data']['list'] as $hashtag) {
                $html .= View::make('components.hashtag.list', compact('hashtag'))->render();
            }

            return response()->json([
                'paginate' => $results['hashtags']['data']['paginate'],
                'html' => $html,
            ]);
        }

        // view
        return view('profile.dislikes.hashtags', compact('items', 'profile', 'followersYouFollow', 'hashtags'));
    }

    // dislikePosts
    public function dislikePosts(Request $request, string $uidOrUsername)
    {
        $query = $request->all();

        if (! fs_db_config('website_status')) {
            $query['pageSize'] = fs_db_config('website_number');
            $query['page'] = 1;
        }

        $client = ApiHelper::make();

        $results = $client->unwrapRequests([
            'profile' => $client->getAsync("/api/v2/user/{$uidOrUsername}/detail"),
            'followersYouFollow' => $client->getAsync("/api/v2/user/{$uidOrUsername}/followers-you-follow", [
                'query' => [
                    'pageSize' => 3,
                    'page' => 1,
                ],
            ]),
            'posts' => $client->getAsync("/api/v2/user/{$uidOrUsername}/mark/dislike/posts", [
                'query' => $query,
            ]),
        ]);

        if ($results['profile']['code'] != 0) {
            throw new ErrorException($results['profile']['message'], $results['profile']['code']);
        }

        $items = $results['profile']['data']['items'];
        $profile = $results['profile']['data']['detail'];
        $followersYouFollow = $results['followersYouFollow']['data']['list'];

        $posts = QueryHelper::convertApiDataToPaginate(
            items: $results['posts']['data']['list'],
            paginate: $results['posts']['data']['paginate'],
        );

        // ajax
        if ($request->ajax()) {
            $html = '';
            foreach ($results['posts']['data']['list'] as $post) {
                $html .= View::make('components.post.list', compact('post'))->render();
            }

            return response()->json([
                'paginate' => $results['posts']['data']['paginate'],
                'html' => $html,
            ]);
        }

        // view
        return view('profile.dislikes.posts', compact('items', 'profile', 'followersYouFollow', 'posts'));
    }

    // dislikeComments
    public function dislikeComments(Request $request, string $uidOrUsername)
    {
        $query = $request->all();

        if (! fs_db_config('website_status')) {
            $query['pageSize'] = fs_db_config('website_number');
            $query['page'] = 1;
        }

        $client = ApiHelper::make();

        $results = $client->unwrapRequests([
            'profile' => $client->getAsync("/api/v2/user/{$uidOrUsername}/detail"),
            'followersYouFollow' => $client->getAsync("/api/v2/user/{$uidOrUsername}/followers-you-follow", [
                'query' => [
                    'pageSize' => 3,
                    'page' => 1,
                ],
            ]),
            'comments' => $client->getAsync("/api/v2/user/{$uidOrUsername}/mark/dislike/comments", [
                'query' => $query,
            ]),
        ]);

        if ($results['profile']['code'] != 0) {
            throw new ErrorException($results['profile']['message'], $results['profile']['code']);
        }

        $items = $results['profile']['data']['items'];
        $profile = $results['profile']['data']['detail'];
        $followersYouFollow = $results['followersYouFollow']['data']['list'];

        $comments = QueryHelper::convertApiDataToPaginate(
            items: $results['comments']['data']['list'],
            paginate: $results['comments']['data']['paginate'],
        );

        // ajax
        if ($request->ajax()) {
            $html = '';
            foreach ($results['comments']['data']['list'] as $comment) {
                $html .= View::make('components.comment.list', compact('comment'))->render();
            }

            return response()->json([
                'paginate' => $results['comments']['data']['paginate'],
                'html' => $html,
            ]);
        }

        // view
        return view('profile.dislikes.comments', compact('items', 'profile', 'followersYouFollow', 'comments'));
    }

    /**
     * following.
     */

    // followingUsers
    public function followingUsers(Request $request, string $uidOrUsername)
    {
        $query = $request->all();

        if (! fs_db_config('website_status')) {
            $query['pageSize'] = fs_db_config('website_number');
            $query['page'] = 1;
        }

        $client = ApiHelper::make();

        $results = $client->unwrapRequests([
            'profile' => $client->getAsync("/api/v2/user/{$uidOrUsername}/detail"),
            'followersYouFollow' => $client->getAsync("/api/v2/user/{$uidOrUsername}/followers-you-follow", [
                'query' => [
                    'pageSize' => 3,
                    'page' => 1,
                ],
            ]),
            'users' => $client->getAsync("/api/v2/user/{$uidOrUsername}/mark/follow/users", [
                'query' => $query,
            ]),
        ]);

        if ($results['profile']['code'] != 0) {
            throw new ErrorException($results['profile']['message'], $results['profile']['code']);
        }

        $items = $results['profile']['data']['items'];
        $profile = $results['profile']['data']['detail'];
        $followersYouFollow = $results['followersYouFollow']['data']['list'];

        $users = QueryHelper::convertApiDataToPaginate(
            items: $results['users']['data']['list'],
            paginate: $results['users']['data']['paginate'],
        );

        // ajax
        if ($request->ajax()) {
            $html = '';
            foreach ($results['users']['data']['list'] as $user) {
                $html .= View::make('components.user.list', compact('user'))->render();
            }

            return response()->json([
                'paginate' => $results['users']['data']['paginate'],
                'html' => $html,
            ]);
        }

        // view
        return view('profile.following.users', compact('items', 'profile', 'followersYouFollow', 'users'));
    }

    // followingGroups
    public function followingGroups(Request $request, string $uidOrUsername)
    {
        $query = $request->all();

        if (! fs_db_config('website_status')) {
            $query['pageSize'] = fs_db_config('website_number');
            $query['page'] = 1;
        }

        $client = ApiHelper::make();

        $results = $client->unwrapRequests([
            'profile' => $client->getAsync("/api/v2/user/{$uidOrUsername}/detail"),
            'followersYouFollow' => $client->getAsync("/api/v2/user/{$uidOrUsername}/followers-you-follow", [
                'query' => [
                    'pageSize' => 3,
                    'page' => 1,
                ],
            ]),
            'groups' => $client->getAsync("/api/v2/user/{$uidOrUsername}/mark/follow/groups", [
                'query' => $query,
            ]),
        ]);

        if ($results['profile']['code'] != 0) {
            throw new ErrorException($results['profile']['message'], $results['profile']['code']);
        }

        $items = $results['profile']['data']['items'];
        $profile = $results['profile']['data']['detail'];
        $followersYouFollow = $results['followersYouFollow']['data']['list'];

        $groups = QueryHelper::convertApiDataToPaginate(
            items: $results['groups']['data']['list'],
            paginate: $results['groups']['data']['paginate'],
        );

        // ajax
        if ($request->ajax()) {
            $html = '';
            foreach ($results['groups']['data']['list'] as $group) {
                $html .= View::make('components.group.list', compact('group'))->render();
            }

            return response()->json([
                'paginate' => $results['groups']['data']['paginate'],
                'html' => $html,
            ]);
        }

        // view
        return view('profile.following.groups', compact('items', 'profile', 'followersYouFollow', 'groups'));
    }

    // followingHashtags
    public function followingHashtags(Request $request, string $uidOrUsername)
    {
        $query = $request->all();

        if (! fs_db_config('website_status')) {
            $query['pageSize'] = fs_db_config('website_number');
            $query['page'] = 1;
        }

        $client = ApiHelper::make();

        $results = $client->unwrapRequests([
            'profile' => $client->getAsync("/api/v2/user/{$uidOrUsername}/detail"),
            'followersYouFollow' => $client->getAsync("/api/v2/user/{$uidOrUsername}/followers-you-follow", [
                'query' => [
                    'pageSize' => 3,
                    'page' => 1,
                ],
            ]),
            'hashtags' => $client->getAsync("/api/v2/user/{$uidOrUsername}/mark/follow/hashtags", [
                'query' => $query,
            ]),
        ]);

        if ($results['profile']['code'] != 0) {
            throw new ErrorException($results['profile']['message'], $results['profile']['code']);
        }

        $items = $results['profile']['data']['items'];
        $profile = $results['profile']['data']['detail'];
        $followersYouFollow = $results['followersYouFollow']['data']['list'];

        $hashtags = QueryHelper::convertApiDataToPaginate(
            items: $results['hashtags']['data']['list'],
            paginate: $results['hashtags']['data']['paginate'],
        );

        // ajax
        if ($request->ajax()) {
            $html = '';
            foreach ($results['hashtags']['data']['list'] as $hashtag) {
                $html .= View::make('components.hashtag.list', compact('hashtag'))->render();
            }

            return response()->json([
                'paginate' => $results['hashtags']['data']['paginate'],
                'html' => $html,
            ]);
        }

        // view
        return view('profile.following.hashtags', compact('items', 'profile', 'followersYouFollow', 'hashtags'));
    }

    // followingPosts
    public function followingPosts(Request $request, string $uidOrUsername)
    {
        $query = $request->all();

        if (! fs_db_config('website_status')) {
            $query['pageSize'] = fs_db_config('website_number');
            $query['page'] = 1;
        }

        $client = ApiHelper::make();

        $results = $client->unwrapRequests([
            'profile' => $client->getAsync("/api/v2/user/{$uidOrUsername}/detail"),
            'followersYouFollow' => $client->getAsync("/api/v2/user/{$uidOrUsername}/followers-you-follow", [
                'query' => [
                    'pageSize' => 3,
                    'page' => 1,
                ],
            ]),
            'posts' => $client->getAsync("/api/v2/user/{$uidOrUsername}/mark/follow/posts", [
                'query' => $query,
            ]),
        ]);

        if ($results['profile']['code'] != 0) {
            throw new ErrorException($results['profile']['message'], $results['profile']['code']);
        }

        $items = $results['profile']['data']['items'];
        $profile = $results['profile']['data']['detail'];
        $followersYouFollow = $results['followersYouFollow']['data']['list'];

        $posts = QueryHelper::convertApiDataToPaginate(
            items: $results['posts']['data']['list'],
            paginate: $results['posts']['data']['paginate'],
        );

        // ajax
        if ($request->ajax()) {
            $html = '';
            foreach ($results['posts']['data']['list'] as $post) {
                $html .= View::make('components.post.list', compact('post'))->render();
            }

            return response()->json([
                'paginate' => $results['posts']['data']['paginate'],
                'html' => $html,
            ]);
        }

        // view
        return view('profile.following.posts', compact('items', 'profile', 'followersYouFollow', 'posts'));
    }

    // followingComments
    public function followingComments(Request $request, string $uidOrUsername)
    {
        $query = $request->all();

        if (! fs_db_config('website_status')) {
            $query['pageSize'] = fs_db_config('website_number');
            $query['page'] = 1;
        }

        $client = ApiHelper::make();

        $results = $client->unwrapRequests([
            'profile' => $client->getAsync("/api/v2/user/{$uidOrUsername}/detail"),
            'followersYouFollow' => $client->getAsync("/api/v2/user/{$uidOrUsername}/followers-you-follow", [
                'query' => [
                    'pageSize' => 3,
                    'page' => 1,
                ],
            ]),
            'comments' => $client->getAsync("/api/v2/user/{$uidOrUsername}/mark/follow/comments", [
                'query' => $query,
            ]),
        ]);

        if ($results['profile']['code'] != 0) {
            throw new ErrorException($results['profile']['message'], $results['profile']['code']);
        }

        $items = $results['profile']['data']['items'];
        $profile = $results['profile']['data']['detail'];
        $followersYouFollow = $results['followersYouFollow']['data']['list'];

        $comments = QueryHelper::convertApiDataToPaginate(
            items: $results['comments']['data']['list'],
            paginate: $results['comments']['data']['paginate'],
        );

        // ajax
        if ($request->ajax()) {
            $html = '';
            foreach ($results['comments']['data']['list'] as $comment) {
                $html .= View::make('components.comment.list', compact('comment'))->render();
            }

            return response()->json([
                'paginate' => $results['comments']['data']['paginate'],
                'html' => $html,
            ]);
        }

        // view
        return view('profile.following.comments', compact('items', 'profile', 'followersYouFollow', 'comments'));
    }

    /**
     * blocking.
     */

    // blockingUsers
    public function blockingUsers(Request $request, string $uidOrUsername)
    {
        $query = $request->all();

        if (! fs_db_config('website_status')) {
            $query['pageSize'] = fs_db_config('website_number');
            $query['page'] = 1;
        }

        $client = ApiHelper::make();

        $results = $client->unwrapRequests([
            'profile' => $client->getAsync("/api/v2/user/{$uidOrUsername}/detail"),
            'followersYouFollow' => $client->getAsync("/api/v2/user/{$uidOrUsername}/followers-you-follow", [
                'query' => [
                    'pageSize' => 3,
                    'page' => 1,
                ],
            ]),
            'users' => $client->getAsync("/api/v2/user/{$uidOrUsername}/mark/block/users", [
                'query' => $query,
            ]),
        ]);

        if ($results['profile']['code'] != 0) {
            throw new ErrorException($results['profile']['message'], $results['profile']['code']);
        }

        $items = $results['profile']['data']['items'];
        $profile = $results['profile']['data']['detail'];
        $followersYouFollow = $results['followersYouFollow']['data']['list'];

        $users = QueryHelper::convertApiDataToPaginate(
            items: $results['users']['data']['list'],
            paginate: $results['users']['data']['paginate'],
        );

        // ajax
        if ($request->ajax()) {
            $html = '';
            foreach ($results['users']['data']['list'] as $user) {
                $html .= View::make('components.user.list', compact('user'))->render();
            }

            return response()->json([
                'paginate' => $results['users']['data']['paginate'],
                'html' => $html,
            ]);
        }

        // view
        return view('profile.blocking.users', compact('items', 'profile', 'followersYouFollow', 'users'));
    }

    // blockingGroups
    public function blockingGroups(Request $request, string $uidOrUsername)
    {
        $query = $request->all();

        if (! fs_db_config('website_status')) {
            $query['pageSize'] = fs_db_config('website_number');
            $query['page'] = 1;
        }

        $client = ApiHelper::make();

        $results = $client->unwrapRequests([
            'profile' => $client->getAsync("/api/v2/user/{$uidOrUsername}/detail"),
            'followersYouFollow' => $client->getAsync("/api/v2/user/{$uidOrUsername}/followers-you-follow", [
                'query' => [
                    'pageSize' => 3,
                    'page' => 1,
                ],
            ]),
            'groups' => $client->getAsync("/api/v2/user/{$uidOrUsername}/mark/block/groups", [
                'query' => $query,
            ]),
        ]);

        if ($results['profile']['code'] != 0) {
            throw new ErrorException($results['profile']['message'], $results['profile']['code']);
        }

        $items = $results['profile']['data']['items'];
        $profile = $results['profile']['data']['detail'];
        $followersYouFollow = $results['followersYouFollow']['data']['list'];

        $groups = QueryHelper::convertApiDataToPaginate(
            items: $results['groups']['data']['list'],
            paginate: $results['groups']['data']['paginate'],
        );

        // ajax
        if ($request->ajax()) {
            $html = '';
            foreach ($results['groups']['data']['list'] as $group) {
                $html .= View::make('components.group.list', compact('group'))->render();
            }

            return response()->json([
                'paginate' => $results['groups']['data']['paginate'],
                'html' => $html,
            ]);
        }

        // view
        return view('profile.blocking.groups', compact('items', 'profile', 'followersYouFollow', 'groups'));
    }

    // blockingHashtags
    public function blockingHashtags(Request $request, string $uidOrUsername)
    {
        $query = $request->all();

        if (! fs_db_config('website_status')) {
            $query['pageSize'] = fs_db_config('website_number');
            $query['page'] = 1;
        }

        $client = ApiHelper::make();

        $results = $client->unwrapRequests([
            'profile' => $client->getAsync("/api/v2/user/{$uidOrUsername}/detail"),
            'followersYouFollow' => $client->getAsync("/api/v2/user/{$uidOrUsername}/followers-you-follow", [
                'query' => [
                    'pageSize' => 3,
                    'page' => 1,
                ],
            ]),
            'hashtags' => $client->getAsync("/api/v2/user/{$uidOrUsername}/mark/block/hashtags", [
                'query' => $query,
            ]),
        ]);

        if ($results['profile']['code'] != 0) {
            throw new ErrorException($results['profile']['message'], $results['profile']['code']);
        }

        $items = $results['profile']['data']['items'];
        $profile = $results['profile']['data']['detail'];
        $followersYouFollow = $results['followersYouFollow']['data']['list'];

        $hashtags = QueryHelper::convertApiDataToPaginate(
            items: $results['hashtags']['data']['list'],
            paginate: $results['hashtags']['data']['paginate'],
        );

        // ajax
        if ($request->ajax()) {
            $html = '';
            foreach ($results['hashtags']['data']['list'] as $hashtag) {
                $html .= View::make('components.hashtag.list', compact('hashtag'))->render();
            }

            return response()->json([
                'paginate' => $results['hashtags']['data']['paginate'],
                'html' => $html,
            ]);
        }

        // view
        return view('profile.blocking.hashtags', compact('items', 'profile', 'followersYouFollow', 'hashtags'));
    }

    // blockingPosts
    public function blockingPosts(Request $request, string $uidOrUsername)
    {
        $query = $request->all();

        if (! fs_db_config('website_status')) {
            $query['pageSize'] = fs_db_config('website_number');
            $query['page'] = 1;
        }

        $client = ApiHelper::make();

        $results = $client->unwrapRequests([
            'profile' => $client->getAsync("/api/v2/user/{$uidOrUsername}/detail"),
            'followersYouFollow' => $client->getAsync("/api/v2/user/{$uidOrUsername}/followers-you-follow", [
                'query' => [
                    'pageSize' => 3,
                    'page' => 1,
                ],
            ]),
            'posts' => $client->getAsync("/api/v2/user/{$uidOrUsername}/mark/block/posts", [
                'query' => $query,
            ]),
        ]);

        if ($results['profile']['code'] != 0) {
            throw new ErrorException($results['profile']['message'], $results['profile']['code']);
        }

        $items = $results['profile']['data']['items'];
        $profile = $results['profile']['data']['detail'];
        $followersYouFollow = $results['followersYouFollow']['data']['list'];

        $posts = QueryHelper::convertApiDataToPaginate(
            items: $results['posts']['data']['list'],
            paginate: $results['posts']['data']['paginate'],
        );

        // ajax
        if ($request->ajax()) {
            $html = '';
            foreach ($results['posts']['data']['list'] as $post) {
                $html .= View::make('components.post.list', compact('post'))->render();
            }

            return response()->json([
                'paginate' => $results['posts']['data']['paginate'],
                'html' => $html,
            ]);
        }

        // view
        return view('profile.blocking.posts', compact('items', 'profile', 'followersYouFollow', 'posts'));
    }

    // blockingComments
    public function blockingComments(Request $request, string $uidOrUsername)
    {
        $query = $request->all();

        if (! fs_db_config('website_status')) {
            $query['pageSize'] = fs_db_config('website_number');
            $query['page'] = 1;
        }

        $client = ApiHelper::make();

        $results = $client->unwrapRequests([
            'profile' => $client->getAsync("/api/v2/user/{$uidOrUsername}/detail"),
            'followersYouFollow' => $client->getAsync("/api/v2/user/{$uidOrUsername}/followers-you-follow", [
                'query' => [
                    'pageSize' => 3,
                    'page' => 1,
                ],
            ]),
            'comments' => $client->getAsync("/api/v2/user/{$uidOrUsername}/mark/block/comments", [
                'query' => $query,
            ]),
        ]);

        if ($results['profile']['code'] != 0) {
            throw new ErrorException($results['profile']['message'], $results['profile']['code']);
        }

        $items = $results['profile']['data']['items'];
        $profile = $results['profile']['data']['detail'];
        $followersYouFollow = $results['followersYouFollow']['data']['list'];

        $comments = QueryHelper::convertApiDataToPaginate(
            items: $results['comments']['data']['list'],
            paginate: $results['comments']['data']['paginate'],
        );

        // ajax
        if ($request->ajax()) {
            $html = '';
            foreach ($results['comments']['data']['list'] as $comment) {
                $html .= View::make('components.comment.list', compact('comment'))->render();
            }

            return response()->json([
                'paginate' => $results['comments']['data']['paginate'],
                'html' => $html,
            ]);
        }

        // view
        return view('profile.blocking.comments', compact('items', 'profile', 'followersYouFollow', 'comments'));
    }
}
