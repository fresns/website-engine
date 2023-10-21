<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jevan Tang
 * Released under the Apache-2.0 License.
 */

namespace Fresns\WebEngine\Http\Controllers;

use Fresns\WebEngine\Exceptions\ErrorException;
use Fresns\WebEngine\Helpers\QueryHelper;
use Fresns\WebEngine\Interfaces\UserInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;

class ProfileController extends Controller
{
    // posts
    public function posts(Request $request, string $uidOrUsername)
    {
        $query = $request->all();
        $query['uidOrUsername'] = $uidOrUsername;

        if (! fs_db_config('webengine_interaction_status')) {
            $query['pageSize'] = fs_db_config('webengine_interaction_number');
            $query['page'] = 1;
        }

        $results = UserInterface::detail($uidOrUsername, 'posts', 'posts', $query);

        if ($results['profile']['code'] != 0) {
            throw new ErrorException($results['profile']['message'], $results['profile']['code']);
        }

        $items = $results['profile']['data']['items'];
        $profile = $results['profile']['data']['detail'];
        $followersYouFollow = $results['followersYouFollow']['data']['list'];

        $posts = QueryHelper::convertApiDataToPaginate(
            items: $results['posts']['data']['list'],
            pagination: $results['posts']['data']['pagination'],
        );

        // ajax
        if ($request->ajax()) {
            $html = '';
            foreach ($results['posts']['data']['list'] as $post) {
                $html .= View::make('components.post.list', compact('post'))->render();
            }

            return response()->json([
                'pagination' => $results['posts']['data']['pagination'],
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

        if (! fs_db_config('webengine_interaction_status')) {
            $query['pageSize'] = fs_db_config('webengine_interaction_number');
            $query['page'] = 1;
        }

        $results = UserInterface::detail($uidOrUsername, 'comments', 'comments', $query);

        if ($results['profile']['code'] != 0) {
            throw new ErrorException($results['profile']['message'], $results['profile']['code']);
        }

        $items = $results['profile']['data']['items'];
        $profile = $results['profile']['data']['detail'];
        $followersYouFollow = $results['followersYouFollow']['data']['list'];

        $comments = QueryHelper::convertApiDataToPaginate(
            items: $results['comments']['data']['list'],
            pagination: $results['comments']['data']['pagination'],
        );

        // ajax
        if ($request->ajax()) {
            $html = '';
            foreach ($results['comments']['data']['list'] as $comment) {
                $html .= View::make('components.comment.list', compact('comment'))->render();
            }

            return response()->json([
                'pagination' => $results['comments']['data']['pagination'],
                'html' => $html,
            ]);
        }

        // view
        return view('profile.comments', compact('items', 'profile', 'followersYouFollow', 'comments'));
    }

    // followers you follow
    public function followersYouFollow(Request $request, string $uidOrUsername)
    {
        $query = $request->all();

        if (! fs_db_config('webengine_interaction_status')) {
            $query['pageSize'] = fs_db_config('webengine_interaction_number');
            $query['page'] = 1;
        }

        $results = UserInterface::detail($uidOrUsername, 'followersYouFollow', 'users', $query);

        if ($results['profile']['code'] != 0) {
            throw new ErrorException($results['profile']['message'], $results['profile']['code']);
        }

        $items = $results['profile']['data']['items'];
        $profile = $results['profile']['data']['detail'];
        $followersYouFollow = $results['followersYouFollow']['data']['list'];

        $users = QueryHelper::convertApiDataToPaginate(
            items: $results['users']['data']['list'],
            pagination: $results['users']['data']['pagination'],
        );

        // ajax
        if ($request->ajax()) {
            $html = '';
            foreach ($results['users']['data']['list'] as $user) {
                $html .= View::make('components.user.list', compact('user'))->render();
            }

            return response()->json([
                'pagination' => $results['users']['data']['pagination'],
                'html' => $html,
            ]);
        }

        // view
        return view('profile.interactions.followers-you-follow', compact('items', 'profile', 'followersYouFollow', 'users'));
    }

    // likers
    public function likers(Request $request, string $uidOrUsername)
    {
        $query = $request->all();

        if (! fs_db_config('webengine_interaction_status')) {
            $query['pageSize'] = fs_db_config('webengine_interaction_number');
            $query['page'] = 1;
        }

        $results = UserInterface::detail($uidOrUsername, 'interaction', 'likers', $query);

        if ($results['profile']['code'] != 0) {
            throw new ErrorException($results['profile']['message'], $results['profile']['code']);
        }

        $items = $results['profile']['data']['items'];
        $profile = $results['profile']['data']['detail'];
        $followersYouFollow = $results['followersYouFollow']['data']['list'];

        $users = QueryHelper::convertApiDataToPaginate(
            items: $results['users']['data']['list'],
            pagination: $results['users']['data']['pagination'],
        );

        // ajax
        if ($request->ajax()) {
            $html = '';
            foreach ($results['users']['data']['list'] as $user) {
                $html .= View::make('components.user.list', compact('user'))->render();
            }

            return response()->json([
                'pagination' => $results['users']['data']['pagination'],
                'html' => $html,
            ]);
        }

        // view
        return view('profile.interactions.likers', compact('items', 'profile', 'followersYouFollow', 'users'));
    }

    // dislikers
    public function dislikers(Request $request, string $uidOrUsername)
    {
        $query = $request->all();

        if (! fs_db_config('webengine_interaction_status')) {
            $query['pageSize'] = fs_db_config('webengine_interaction_number');
            $query['page'] = 1;
        }

        $results = UserInterface::detail($uidOrUsername, 'interaction', 'dislikers', $query);

        if ($results['profile']['code'] != 0) {
            throw new ErrorException($results['profile']['message'], $results['profile']['code']);
        }

        $items = $results['profile']['data']['items'];
        $profile = $results['profile']['data']['detail'];
        $followersYouFollow = $results['followersYouFollow']['data']['list'];

        $users = QueryHelper::convertApiDataToPaginate(
            items: $results['users']['data']['list'],
            pagination: $results['users']['data']['pagination'],
        );

        // ajax
        if ($request->ajax()) {
            $html = '';
            foreach ($results['users']['data']['list'] as $user) {
                $html .= View::make('components.user.list', compact('user'))->render();
            }

            return response()->json([
                'pagination' => $results['users']['data']['pagination'],
                'html' => $html,
            ]);
        }

        // view
        return view('profile.interactions.dislikers', compact('items', 'profile', 'followersYouFollow', 'users'));
    }

    // followers
    public function followers(Request $request, string $uidOrUsername)
    {
        $query = $request->all();

        if (! fs_db_config('webengine_interaction_status')) {
            $query['pageSize'] = fs_db_config('webengine_interaction_number');
            $query['page'] = 1;
        }

        $results = UserInterface::detail($uidOrUsername, 'interaction', 'followers', $query);

        if ($results['profile']['code'] != 0) {
            throw new ErrorException($results['profile']['message'], $results['profile']['code']);
        }

        $items = $results['profile']['data']['items'];
        $profile = $results['profile']['data']['detail'];
        $followersYouFollow = $results['followersYouFollow']['data']['list'];

        $users = QueryHelper::convertApiDataToPaginate(
            items: $results['users']['data']['list'],
            pagination: $results['users']['data']['pagination'],
        );

        // ajax
        if ($request->ajax()) {
            $html = '';
            foreach ($results['users']['data']['list'] as $user) {
                $html .= View::make('components.user.list', compact('user'))->render();
            }

            return response()->json([
                'pagination' => $results['users']['data']['pagination'],
                'html' => $html,
            ]);
        }

        // view
        return view('profile.interactions.followers', compact('items', 'profile', 'followersYouFollow', 'users'));
    }

    // blockers
    public function blockers(Request $request, string $uidOrUsername)
    {
        $query = $request->all();

        if (! fs_db_config('webengine_interaction_status')) {
            $query['pageSize'] = fs_db_config('webengine_interaction_number');
            $query['page'] = 1;
        }

        $results = UserInterface::detail($uidOrUsername, 'interaction', 'blockers', $query);

        if ($results['profile']['code'] != 0) {
            throw new ErrorException($results['profile']['message'], $results['profile']['code']);
        }

        $items = $results['profile']['data']['items'];
        $profile = $results['profile']['data']['detail'];
        $followersYouFollow = $results['followersYouFollow']['data']['list'];

        $users = QueryHelper::convertApiDataToPaginate(
            items: $results['users']['data']['list'],
            pagination: $results['users']['data']['pagination'],
        );

        // ajax
        if ($request->ajax()) {
            $html = '';
            foreach ($results['users']['data']['list'] as $user) {
                $html .= View::make('components.user.list', compact('user'))->render();
            }

            return response()->json([
                'pagination' => $results['users']['data']['pagination'],
                'html' => $html,
            ]);
        }

        // view
        return view('profile.interactions.blockers', compact('items', 'profile', 'followersYouFollow', 'users'));
    }

    /**
     * like.
     */

    // likeUsers
    public function likeUsers(Request $request, string $uidOrUsername)
    {
        $query = $request->all();

        if (! fs_db_config('webengine_interaction_status')) {
            $query['pageSize'] = fs_db_config('webengine_interaction_number');
            $query['page'] = 1;
        }

        $results = UserInterface::detail($uidOrUsername, 'like', 'users', $query);

        if ($results['profile']['code'] != 0) {
            throw new ErrorException($results['profile']['message'], $results['profile']['code']);
        }

        $items = $results['profile']['data']['items'];
        $profile = $results['profile']['data']['detail'];
        $followersYouFollow = $results['followersYouFollow']['data']['list'];

        $users = QueryHelper::convertApiDataToPaginate(
            items: $results['users']['data']['list'],
            pagination: $results['users']['data']['pagination'],
        );

        // ajax
        if ($request->ajax()) {
            $html = '';
            foreach ($results['users']['data']['list'] as $user) {
                $html .= View::make('components.user.list', compact('user'))->render();
            }

            return response()->json([
                'pagination' => $results['users']['data']['pagination'],
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

        if (! fs_db_config('webengine_interaction_status')) {
            $query['pageSize'] = fs_db_config('webengine_interaction_number');
            $query['page'] = 1;
        }

        $results = UserInterface::detail($uidOrUsername, 'like', 'groups', $query);

        if ($results['profile']['code'] != 0) {
            throw new ErrorException($results['profile']['message'], $results['profile']['code']);
        }

        $items = $results['profile']['data']['items'];
        $profile = $results['profile']['data']['detail'];
        $followersYouFollow = $results['followersYouFollow']['data']['list'];

        $groups = QueryHelper::convertApiDataToPaginate(
            items: $results['groups']['data']['list'],
            pagination: $results['groups']['data']['pagination'],
        );

        // ajax
        if ($request->ajax()) {
            $html = '';
            foreach ($results['groups']['data']['list'] as $group) {
                $html .= View::make('components.group.list', compact('group'))->render();
            }

            return response()->json([
                'pagination' => $results['groups']['data']['pagination'],
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

        if (! fs_db_config('webengine_interaction_status')) {
            $query['pageSize'] = fs_db_config('webengine_interaction_number');
            $query['page'] = 1;
        }

        $results = UserInterface::detail($uidOrUsername, 'like', 'hashtags', $query);

        if ($results['profile']['code'] != 0) {
            throw new ErrorException($results['profile']['message'], $results['profile']['code']);
        }

        $items = $results['profile']['data']['items'];
        $profile = $results['profile']['data']['detail'];
        $followersYouFollow = $results['followersYouFollow']['data']['list'];

        $hashtags = QueryHelper::convertApiDataToPaginate(
            items: $results['hashtags']['data']['list'],
            pagination: $results['hashtags']['data']['pagination'],
        );

        // ajax
        if ($request->ajax()) {
            $html = '';
            foreach ($results['hashtags']['data']['list'] as $hashtag) {
                $html .= View::make('components.hashtag.list', compact('hashtag'))->render();
            }

            return response()->json([
                'pagination' => $results['hashtags']['data']['pagination'],
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

        if (! fs_db_config('webengine_interaction_status')) {
            $query['pageSize'] = fs_db_config('webengine_interaction_number');
            $query['page'] = 1;
        }

        $results = UserInterface::detail($uidOrUsername, 'like', 'posts', $query);

        if ($results['profile']['code'] != 0) {
            throw new ErrorException($results['profile']['message'], $results['profile']['code']);
        }

        $items = $results['profile']['data']['items'];
        $profile = $results['profile']['data']['detail'];
        $followersYouFollow = $results['followersYouFollow']['data']['list'];

        $posts = QueryHelper::convertApiDataToPaginate(
            items: $results['posts']['data']['list'],
            pagination: $results['posts']['data']['pagination'],
        );

        // ajax
        if ($request->ajax()) {
            $html = '';
            foreach ($results['posts']['data']['list'] as $post) {
                $html .= View::make('components.post.list', compact('post'))->render();
            }

            return response()->json([
                'pagination' => $results['posts']['data']['pagination'],
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

        if (! fs_db_config('webengine_interaction_status')) {
            $query['pageSize'] = fs_db_config('webengine_interaction_number');
            $query['page'] = 1;
        }

        $results = UserInterface::detail($uidOrUsername, 'like', 'comments', $query);

        if ($results['profile']['code'] != 0) {
            throw new ErrorException($results['profile']['message'], $results['profile']['code']);
        }

        $items = $results['profile']['data']['items'];
        $profile = $results['profile']['data']['detail'];
        $followersYouFollow = $results['followersYouFollow']['data']['list'];

        $comments = QueryHelper::convertApiDataToPaginate(
            items: $results['comments']['data']['list'],
            pagination: $results['comments']['data']['pagination'],
        );

        // ajax
        if ($request->ajax()) {
            $html = '';
            foreach ($results['comments']['data']['list'] as $comment) {
                $html .= View::make('components.comment.list', compact('comment'))->render();
            }

            return response()->json([
                'pagination' => $results['comments']['data']['pagination'],
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

        if (! fs_db_config('webengine_interaction_status')) {
            $query['pageSize'] = fs_db_config('webengine_interaction_number');
            $query['page'] = 1;
        }

        $results = UserInterface::detail($uidOrUsername, 'dislike', 'users', $query);

        if ($results['profile']['code'] != 0) {
            throw new ErrorException($results['profile']['message'], $results['profile']['code']);
        }

        $items = $results['profile']['data']['items'];
        $profile = $results['profile']['data']['detail'];
        $followersYouFollow = $results['followersYouFollow']['data']['list'];

        $users = QueryHelper::convertApiDataToPaginate(
            items: $results['users']['data']['list'],
            pagination: $results['users']['data']['pagination'],
        );

        // ajax
        if ($request->ajax()) {
            $html = '';
            foreach ($results['users']['data']['list'] as $user) {
                $html .= View::make('components.user.list', compact('user'))->render();
            }

            return response()->json([
                'pagination' => $results['users']['data']['pagination'],
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

        if (! fs_db_config('webengine_interaction_status')) {
            $query['pageSize'] = fs_db_config('webengine_interaction_number');
            $query['page'] = 1;
        }

        $results = UserInterface::detail($uidOrUsername, 'dislike', 'groups', $query);

        if ($results['profile']['code'] != 0) {
            throw new ErrorException($results['profile']['message'], $results['profile']['code']);
        }

        $items = $results['profile']['data']['items'];
        $profile = $results['profile']['data']['detail'];
        $followersYouFollow = $results['followersYouFollow']['data']['list'];

        $groups = QueryHelper::convertApiDataToPaginate(
            items: $results['groups']['data']['list'],
            pagination: $results['groups']['data']['pagination'],
        );

        // ajax
        if ($request->ajax()) {
            $html = '';
            foreach ($results['groups']['data']['list'] as $group) {
                $html .= View::make('components.group.list', compact('group'))->render();
            }

            return response()->json([
                'pagination' => $results['groups']['data']['pagination'],
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

        if (! fs_db_config('webengine_interaction_status')) {
            $query['pageSize'] = fs_db_config('webengine_interaction_number');
            $query['page'] = 1;
        }

        $results = UserInterface::detail($uidOrUsername, 'dislike', 'hashtags', $query);

        if ($results['profile']['code'] != 0) {
            throw new ErrorException($results['profile']['message'], $results['profile']['code']);
        }

        $items = $results['profile']['data']['items'];
        $profile = $results['profile']['data']['detail'];
        $followersYouFollow = $results['followersYouFollow']['data']['list'];

        $hashtags = QueryHelper::convertApiDataToPaginate(
            items: $results['hashtags']['data']['list'],
            pagination: $results['hashtags']['data']['pagination'],
        );

        // ajax
        if ($request->ajax()) {
            $html = '';
            foreach ($results['hashtags']['data']['list'] as $hashtag) {
                $html .= View::make('components.hashtag.list', compact('hashtag'))->render();
            }

            return response()->json([
                'pagination' => $results['hashtags']['data']['pagination'],
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

        if (! fs_db_config('webengine_interaction_status')) {
            $query['pageSize'] = fs_db_config('webengine_interaction_number');
            $query['page'] = 1;
        }

        $results = UserInterface::detail($uidOrUsername, 'dislike', 'posts', $query);

        if ($results['profile']['code'] != 0) {
            throw new ErrorException($results['profile']['message'], $results['profile']['code']);
        }

        $items = $results['profile']['data']['items'];
        $profile = $results['profile']['data']['detail'];
        $followersYouFollow = $results['followersYouFollow']['data']['list'];

        $posts = QueryHelper::convertApiDataToPaginate(
            items: $results['posts']['data']['list'],
            pagination: $results['posts']['data']['pagination'],
        );

        // ajax
        if ($request->ajax()) {
            $html = '';
            foreach ($results['posts']['data']['list'] as $post) {
                $html .= View::make('components.post.list', compact('post'))->render();
            }

            return response()->json([
                'pagination' => $results['posts']['data']['pagination'],
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

        if (! fs_db_config('webengine_interaction_status')) {
            $query['pageSize'] = fs_db_config('webengine_interaction_number');
            $query['page'] = 1;
        }

        $results = UserInterface::detail($uidOrUsername, 'dislike', 'comments', $query);

        if ($results['profile']['code'] != 0) {
            throw new ErrorException($results['profile']['message'], $results['profile']['code']);
        }

        $items = $results['profile']['data']['items'];
        $profile = $results['profile']['data']['detail'];
        $followersYouFollow = $results['followersYouFollow']['data']['list'];

        $comments = QueryHelper::convertApiDataToPaginate(
            items: $results['comments']['data']['list'],
            pagination: $results['comments']['data']['pagination'],
        );

        // ajax
        if ($request->ajax()) {
            $html = '';
            foreach ($results['comments']['data']['list'] as $comment) {
                $html .= View::make('components.comment.list', compact('comment'))->render();
            }

            return response()->json([
                'pagination' => $results['comments']['data']['pagination'],
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

        if (! fs_db_config('webengine_interaction_status')) {
            $query['pageSize'] = fs_db_config('webengine_interaction_number');
            $query['page'] = 1;
        }

        $results = UserInterface::detail($uidOrUsername, 'follow', 'users', $query);

        if ($results['profile']['code'] != 0) {
            throw new ErrorException($results['profile']['message'], $results['profile']['code']);
        }

        $items = $results['profile']['data']['items'];
        $profile = $results['profile']['data']['detail'];
        $followersYouFollow = $results['followersYouFollow']['data']['list'];

        $users = QueryHelper::convertApiDataToPaginate(
            items: $results['users']['data']['list'],
            pagination: $results['users']['data']['pagination'],
        );

        // ajax
        if ($request->ajax()) {
            $html = '';
            foreach ($results['users']['data']['list'] as $user) {
                $html .= View::make('components.user.list', compact('user'))->render();
            }

            return response()->json([
                'pagination' => $results['users']['data']['pagination'],
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

        if (! fs_db_config('webengine_interaction_status')) {
            $query['pageSize'] = fs_db_config('webengine_interaction_number');
            $query['page'] = 1;
        }

        $results = UserInterface::detail($uidOrUsername, 'follow', 'groups', $query);

        if ($results['profile']['code'] != 0) {
            throw new ErrorException($results['profile']['message'], $results['profile']['code']);
        }

        $items = $results['profile']['data']['items'];
        $profile = $results['profile']['data']['detail'];
        $followersYouFollow = $results['followersYouFollow']['data']['list'];

        $groups = QueryHelper::convertApiDataToPaginate(
            items: $results['groups']['data']['list'],
            pagination: $results['groups']['data']['pagination'],
        );

        // ajax
        if ($request->ajax()) {
            $html = '';
            foreach ($results['groups']['data']['list'] as $group) {
                $html .= View::make('components.group.list', compact('group'))->render();
            }

            return response()->json([
                'pagination' => $results['groups']['data']['pagination'],
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

        if (! fs_db_config('webengine_interaction_status')) {
            $query['pageSize'] = fs_db_config('webengine_interaction_number');
            $query['page'] = 1;
        }

        $results = UserInterface::detail($uidOrUsername, 'follow', 'hashtags', $query);

        if ($results['profile']['code'] != 0) {
            throw new ErrorException($results['profile']['message'], $results['profile']['code']);
        }

        $items = $results['profile']['data']['items'];
        $profile = $results['profile']['data']['detail'];
        $followersYouFollow = $results['followersYouFollow']['data']['list'];

        $hashtags = QueryHelper::convertApiDataToPaginate(
            items: $results['hashtags']['data']['list'],
            pagination: $results['hashtags']['data']['pagination'],
        );

        // ajax
        if ($request->ajax()) {
            $html = '';
            foreach ($results['hashtags']['data']['list'] as $hashtag) {
                $html .= View::make('components.hashtag.list', compact('hashtag'))->render();
            }

            return response()->json([
                'pagination' => $results['hashtags']['data']['pagination'],
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

        if (! fs_db_config('webengine_interaction_status')) {
            $query['pageSize'] = fs_db_config('webengine_interaction_number');
            $query['page'] = 1;
        }

        $results = UserInterface::detail($uidOrUsername, 'follow', 'posts', $query);

        if ($results['profile']['code'] != 0) {
            throw new ErrorException($results['profile']['message'], $results['profile']['code']);
        }

        $items = $results['profile']['data']['items'];
        $profile = $results['profile']['data']['detail'];
        $followersYouFollow = $results['followersYouFollow']['data']['list'];

        $posts = QueryHelper::convertApiDataToPaginate(
            items: $results['posts']['data']['list'],
            pagination: $results['posts']['data']['pagination'],
        );

        // ajax
        if ($request->ajax()) {
            $html = '';
            foreach ($results['posts']['data']['list'] as $post) {
                $html .= View::make('components.post.list', compact('post'))->render();
            }

            return response()->json([
                'pagination' => $results['posts']['data']['pagination'],
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

        if (! fs_db_config('webengine_interaction_status')) {
            $query['pageSize'] = fs_db_config('webengine_interaction_number');
            $query['page'] = 1;
        }

        $results = UserInterface::detail($uidOrUsername, 'follow', 'comments', $query);

        if ($results['profile']['code'] != 0) {
            throw new ErrorException($results['profile']['message'], $results['profile']['code']);
        }

        $items = $results['profile']['data']['items'];
        $profile = $results['profile']['data']['detail'];
        $followersYouFollow = $results['followersYouFollow']['data']['list'];

        $comments = QueryHelper::convertApiDataToPaginate(
            items: $results['comments']['data']['list'],
            pagination: $results['comments']['data']['pagination'],
        );

        // ajax
        if ($request->ajax()) {
            $html = '';
            foreach ($results['comments']['data']['list'] as $comment) {
                $html .= View::make('components.comment.list', compact('comment'))->render();
            }

            return response()->json([
                'pagination' => $results['comments']['data']['pagination'],
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

        if (! fs_db_config('webengine_interaction_status')) {
            $query['pageSize'] = fs_db_config('webengine_interaction_number');
            $query['page'] = 1;
        }

        $results = UserInterface::detail($uidOrUsername, 'block', 'users', $query);

        if ($results['profile']['code'] != 0) {
            throw new ErrorException($results['profile']['message'], $results['profile']['code']);
        }

        $items = $results['profile']['data']['items'];
        $profile = $results['profile']['data']['detail'];
        $followersYouFollow = $results['followersYouFollow']['data']['list'];

        $users = QueryHelper::convertApiDataToPaginate(
            items: $results['users']['data']['list'],
            pagination: $results['users']['data']['pagination'],
        );

        // ajax
        if ($request->ajax()) {
            $html = '';
            foreach ($results['users']['data']['list'] as $user) {
                $html .= View::make('components.user.list', compact('user'))->render();
            }

            return response()->json([
                'pagination' => $results['users']['data']['pagination'],
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

        if (! fs_db_config('webengine_interaction_status')) {
            $query['pageSize'] = fs_db_config('webengine_interaction_number');
            $query['page'] = 1;
        }

        $results = UserInterface::detail($uidOrUsername, 'block', 'groups', $query);

        if ($results['profile']['code'] != 0) {
            throw new ErrorException($results['profile']['message'], $results['profile']['code']);
        }

        $items = $results['profile']['data']['items'];
        $profile = $results['profile']['data']['detail'];
        $followersYouFollow = $results['followersYouFollow']['data']['list'];

        $groups = QueryHelper::convertApiDataToPaginate(
            items: $results['groups']['data']['list'],
            pagination: $results['groups']['data']['pagination'],
        );

        // ajax
        if ($request->ajax()) {
            $html = '';
            foreach ($results['groups']['data']['list'] as $group) {
                $html .= View::make('components.group.list', compact('group'))->render();
            }

            return response()->json([
                'pagination' => $results['groups']['data']['pagination'],
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

        if (! fs_db_config('webengine_interaction_status')) {
            $query['pageSize'] = fs_db_config('webengine_interaction_number');
            $query['page'] = 1;
        }

        $results = UserInterface::detail($uidOrUsername, 'block', 'hashtags', $query);

        if ($results['profile']['code'] != 0) {
            throw new ErrorException($results['profile']['message'], $results['profile']['code']);
        }

        $items = $results['profile']['data']['items'];
        $profile = $results['profile']['data']['detail'];
        $followersYouFollow = $results['followersYouFollow']['data']['list'];

        $hashtags = QueryHelper::convertApiDataToPaginate(
            items: $results['hashtags']['data']['list'],
            pagination: $results['hashtags']['data']['pagination'],
        );

        // ajax
        if ($request->ajax()) {
            $html = '';
            foreach ($results['hashtags']['data']['list'] as $hashtag) {
                $html .= View::make('components.hashtag.list', compact('hashtag'))->render();
            }

            return response()->json([
                'pagination' => $results['hashtags']['data']['pagination'],
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

        if (! fs_db_config('webengine_interaction_status')) {
            $query['pageSize'] = fs_db_config('webengine_interaction_number');
            $query['page'] = 1;
        }

        $results = UserInterface::detail($uidOrUsername, 'block', 'posts', $query);

        if ($results['profile']['code'] != 0) {
            throw new ErrorException($results['profile']['message'], $results['profile']['code']);
        }

        $items = $results['profile']['data']['items'];
        $profile = $results['profile']['data']['detail'];
        $followersYouFollow = $results['followersYouFollow']['data']['list'];

        $posts = QueryHelper::convertApiDataToPaginate(
            items: $results['posts']['data']['list'],
            pagination: $results['posts']['data']['pagination'],
        );

        // ajax
        if ($request->ajax()) {
            $html = '';
            foreach ($results['posts']['data']['list'] as $post) {
                $html .= View::make('components.post.list', compact('post'))->render();
            }

            return response()->json([
                'pagination' => $results['posts']['data']['pagination'],
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

        if (! fs_db_config('webengine_interaction_status')) {
            $query['pageSize'] = fs_db_config('webengine_interaction_number');
            $query['page'] = 1;
        }

        $results = UserInterface::detail($uidOrUsername, 'block', 'comments', $query);

        if ($results['profile']['code'] != 0) {
            throw new ErrorException($results['profile']['message'], $results['profile']['code']);
        }

        $items = $results['profile']['data']['items'];
        $profile = $results['profile']['data']['detail'];
        $followersYouFollow = $results['followersYouFollow']['data']['list'];

        $comments = QueryHelper::convertApiDataToPaginate(
            items: $results['comments']['data']['list'],
            pagination: $results['comments']['data']['pagination'],
        );

        // ajax
        if ($request->ajax()) {
            $html = '';
            foreach ($results['comments']['data']['list'] as $comment) {
                $html .= View::make('components.comment.list', compact('comment'))->render();
            }

            return response()->json([
                'pagination' => $results['comments']['data']['pagination'],
                'html' => $html,
            ]);
        }

        // view
        return view('profile.blocking.comments', compact('items', 'profile', 'followersYouFollow', 'comments'));
    }
}
