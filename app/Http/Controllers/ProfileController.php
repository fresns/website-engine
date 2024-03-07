<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jevan Tang
 * Released under the Apache-2.0 License.
 */

namespace Fresns\WebsiteEngine\Http\Controllers;

use Fresns\WebsiteEngine\Helpers\QueryHelper;
use Fresns\WebsiteEngine\Interfaces\UserInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\View;

class ProfileController extends Controller
{
    // posts
    public function posts(Request $request, int|string $uidOrUsername)
    {
        if (! fs_config('profile_posts_enabled')) {
            return Response::view('404', [], 404);
        }

        $query = $request->all();
        $query['uidOrUsername'] = $uidOrUsername;

        $results = UserInterface::detail($uidOrUsername, 'posts', 'posts', $query);

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
        $items = $results['profile']['data']['items'];
        $profile = $results['profile']['data']['detail'];
        $followersYouFollow = $results['followersYouFollow']['data']['list'];

        $posts = QueryHelper::convertApiDataToPaginate(
            items: $results['posts']['data']['list'],
            pagination: $results['posts']['data']['pagination'],
        );

        return view('profile.posts', compact('items', 'profile', 'followersYouFollow', 'posts'));
    }

    // comments
    public function comments(Request $request, int|string $uidOrUsername)
    {
        if (! fs_config('profile_comments_enabled')) {
            return Response::view('404', [], 404);
        }

        $query = $request->all();
        $query['uidOrUsername'] = $uidOrUsername;

        $results = UserInterface::detail($uidOrUsername, 'comments', 'comments', $query);

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
        $items = $results['profile']['data']['items'];
        $profile = $results['profile']['data']['detail'];
        $followersYouFollow = $results['followersYouFollow']['data']['list'];

        $comments = QueryHelper::convertApiDataToPaginate(
            items: $results['comments']['data']['list'],
            pagination: $results['comments']['data']['pagination'],
        );

        return view('profile.comments', compact('items', 'profile', 'followersYouFollow', 'comments'));
    }

    // likers
    public function likers(Request $request, int|string $uidOrUsername)
    {
        if (fs_config('user_like_public_record') == 1) {
            return Response::view('404', [], 404);
        }

        if (fs_config('user_like_public_record') == 2) {
            if (fs_user()->guest()) {
                return Response::view('404', [], 404);
            }

            $uid = fs_user('detail.uid');
            $username = fs_user('detail.username');

            if ($uidOrUsername != $uid && $uidOrUsername != $username) {
                return Response::view('404', [], 404);
            }
        }

        $query = $request->all();

        $results = UserInterface::detail($uidOrUsername, 'interaction', 'likers', $query);

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
        $items = $results['profile']['data']['items'];
        $profile = $results['profile']['data']['detail'];
        $followersYouFollow = $results['followersYouFollow']['data']['list'];

        $users = QueryHelper::convertApiDataToPaginate(
            items: $results['users']['data']['list'],
            pagination: $results['users']['data']['pagination'],
        );

        return view('profile.interactions.likers', compact('items', 'profile', 'followersYouFollow', 'users'));
    }

    // dislikers
    public function dislikers(Request $request, int|string $uidOrUsername)
    {
        if (fs_config('user_dislike_public_record') == 1) {
            return Response::view('404', [], 404);
        }

        if (fs_config('user_dislike_public_record') == 2) {
            if (fs_user()->guest()) {
                return Response::view('404', [], 404);
            }

            $uid = fs_user('detail.uid');
            $username = fs_user('detail.username');

            if ($uidOrUsername != $uid && $uidOrUsername != $username) {
                return Response::view('404', [], 404);
            }
        }

        $query = $request->all();

        $results = UserInterface::detail($uidOrUsername, 'interaction', 'dislikers', $query);

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
        $items = $results['profile']['data']['items'];
        $profile = $results['profile']['data']['detail'];
        $followersYouFollow = $results['followersYouFollow']['data']['list'];

        $users = QueryHelper::convertApiDataToPaginate(
            items: $results['users']['data']['list'],
            pagination: $results['users']['data']['pagination'],
        );

        return view('profile.interactions.dislikers', compact('items', 'profile', 'followersYouFollow', 'users'));
    }

    // followers
    public function followers(Request $request, int|string $uidOrUsername)
    {
        if (fs_config('user_follow_public_record') == 1) {
            return Response::view('404', [], 404);
        }

        if (fs_config('user_follow_public_record') == 2) {
            if (fs_user()->guest()) {
                return Response::view('404', [], 404);
            }

            $uid = fs_user('detail.uid');
            $username = fs_user('detail.username');

            if ($uidOrUsername != $uid && $uidOrUsername != $username) {
                return Response::view('404', [], 404);
            }
        }

        $query = $request->all();

        $results = UserInterface::detail($uidOrUsername, 'interaction', 'followers', $query);

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
        $items = $results['profile']['data']['items'];
        $profile = $results['profile']['data']['detail'];
        $followersYouFollow = $results['followersYouFollow']['data']['list'];

        $users = QueryHelper::convertApiDataToPaginate(
            items: $results['users']['data']['list'],
            pagination: $results['users']['data']['pagination'],
        );

        return view('profile.interactions.followers', compact('items', 'profile', 'followersYouFollow', 'users'));
    }

    // blockers
    public function blockers(Request $request, int|string $uidOrUsername)
    {
        if (fs_config('user_block_public_record') == 1) {
            return Response::view('404', [], 404);
        }

        if (fs_config('user_block_public_record') == 2) {
            if (fs_user()->guest()) {
                return Response::view('404', [], 404);
            }

            $uid = fs_user('detail.uid');
            $username = fs_user('detail.username');

            if ($uidOrUsername != $uid && $uidOrUsername != $username) {
                return Response::view('404', [], 404);
            }
        }

        $query = $request->all();

        $results = UserInterface::detail($uidOrUsername, 'interaction', 'blockers', $query);

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
        $items = $results['profile']['data']['items'];
        $profile = $results['profile']['data']['detail'];
        $followersYouFollow = $results['followersYouFollow']['data']['list'];

        $users = QueryHelper::convertApiDataToPaginate(
            items: $results['users']['data']['list'],
            pagination: $results['users']['data']['pagination'],
        );

        return view('profile.interactions.blockers', compact('items', 'profile', 'followersYouFollow', 'users'));
    }

    // followers you follow
    public function followersYouFollow(Request $request, int|string $uidOrUsername)
    {
        if (! fs_config('profile_followers_you_follow_enabled')) {
            return Response::view('404', [], 404);
        }

        $query = $request->all();

        $results = UserInterface::detail($uidOrUsername, 'followersYouFollow', 'users', $query);

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
        $items = $results['profile']['data']['items'];
        $profile = $results['profile']['data']['detail'];
        $followersYouFollow = $results['followersYouFollow']['data']['list'];

        $users = QueryHelper::convertApiDataToPaginate(
            items: $results['users']['data']['list'],
            pagination: $results['users']['data']['pagination'],
        );

        return view('profile.interactions.followers-you-follow', compact('items', 'profile', 'followersYouFollow', 'users'));
    }

    /**
     * like.
     */

    // likeUsers
    public function likeUsers(Request $request, int|string $uidOrUsername)
    {
        if (! fs_config('profile_likes_users_enabled')) {
            return Response::view('404', [], 404);
        }

        $query = $request->all();

        $results = UserInterface::detail($uidOrUsername, 'like', 'users', $query);

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
        $items = $results['profile']['data']['items'];
        $profile = $results['profile']['data']['detail'];
        $followersYouFollow = $results['followersYouFollow']['data']['list'];

        $users = QueryHelper::convertApiDataToPaginate(
            items: $results['users']['data']['list'],
            pagination: $results['users']['data']['pagination'],
        );

        return view('profile.likes.users', compact('items', 'profile', 'followersYouFollow', 'users'));
    }

    // likeGroups
    public function likeGroups(Request $request, int|string $uidOrUsername)
    {
        if (! fs_config('profile_likes_groups_enabled')) {
            return Response::view('404', [], 404);
        }

        $query = $request->all();

        $results = UserInterface::detail($uidOrUsername, 'like', 'groups', $query);

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
        $items = $results['profile']['data']['items'];
        $profile = $results['profile']['data']['detail'];
        $followersYouFollow = $results['followersYouFollow']['data']['list'];

        $groups = QueryHelper::convertApiDataToPaginate(
            items: $results['groups']['data']['list'],
            pagination: $results['groups']['data']['pagination'],
        );

        return view('profile.likes.groups', compact('items', 'profile', 'followersYouFollow', 'groups'));
    }

    // likeHashtags
    public function likeHashtags(Request $request, int|string $uidOrUsername)
    {
        if (! fs_config('profile_likes_hashtags_enabled')) {
            return Response::view('404', [], 404);
        }

        $query = $request->all();

        $results = UserInterface::detail($uidOrUsername, 'like', 'hashtags', $query);

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
        $items = $results['profile']['data']['items'];
        $profile = $results['profile']['data']['detail'];
        $followersYouFollow = $results['followersYouFollow']['data']['list'];

        $hashtags = QueryHelper::convertApiDataToPaginate(
            items: $results['hashtags']['data']['list'],
            pagination: $results['hashtags']['data']['pagination'],
        );

        return view('profile.likes.hashtags', compact('items', 'profile', 'followersYouFollow', 'hashtags'));
    }

    // likeGeotags
    public function likeGeotags(Request $request, int|string $uidOrUsername)
    {
        if (! fs_config('profile_likes_geotags_enabled')) {
            return Response::view('404', [], 404);
        }

        $query = $request->all();

        $results = UserInterface::detail($uidOrUsername, 'like', 'geotags', $query);

        // ajax
        if ($request->ajax()) {
            $html = '';
            foreach ($results['geotags']['data']['list'] as $geotag) {
                $html .= View::make('components.geotag.list', compact('geotag'))->render();
            }

            return response()->json([
                'pagination' => $results['geotags']['data']['pagination'],
                'html' => $html,
            ]);
        }

        // view
        $items = $results['profile']['data']['items'];
        $profile = $results['profile']['data']['detail'];
        $followersYouFollow = $results['followersYouFollow']['data']['list'];

        $geotags = QueryHelper::convertApiDataToPaginate(
            items: $results['geotags']['data']['list'],
            pagination: $results['geotags']['data']['pagination'],
        );

        return view('profile.likes.geotags', compact('items', 'profile', 'followersYouFollow', 'geotags'));
    }

    // likePosts
    public function likePosts(Request $request, int|string $uidOrUsername)
    {
        if (! fs_config('profile_likes_posts_enabled')) {
            return Response::view('404', [], 404);
        }

        $query = $request->all();

        $results = UserInterface::detail($uidOrUsername, 'like', 'posts', $query);

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
        $items = $results['profile']['data']['items'];
        $profile = $results['profile']['data']['detail'];
        $followersYouFollow = $results['followersYouFollow']['data']['list'];

        $posts = QueryHelper::convertApiDataToPaginate(
            items: $results['posts']['data']['list'],
            pagination: $results['posts']['data']['pagination'],
        );

        return view('profile.likes.posts', compact('items', 'profile', 'followersYouFollow', 'posts'));
    }

    // likeComments
    public function likeComments(Request $request, int|string $uidOrUsername)
    {
        if (! fs_config('profile_likes_comments_enabled')) {
            return Response::view('404', [], 404);
        }

        $query = $request->all();

        $results = UserInterface::detail($uidOrUsername, 'like', 'comments', $query);

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
        $items = $results['profile']['data']['items'];
        $profile = $results['profile']['data']['detail'];
        $followersYouFollow = $results['followersYouFollow']['data']['list'];

        $comments = QueryHelper::convertApiDataToPaginate(
            items: $results['comments']['data']['list'],
            pagination: $results['comments']['data']['pagination'],
        );

        return view('profile.likes.comments', compact('items', 'profile', 'followersYouFollow', 'comments'));
    }

    /**
     * dislike.
     */

    // dislikeUsers
    public function dislikeUsers(Request $request, int|string $uidOrUsername)
    {
        if (! fs_config('profile_dislikes_users_enabled')) {
            return Response::view('404', [], 404);
        }

        $query = $request->all();

        $results = UserInterface::detail($uidOrUsername, 'dislike', 'users', $query);

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
        $items = $results['profile']['data']['items'];
        $profile = $results['profile']['data']['detail'];
        $followersYouFollow = $results['followersYouFollow']['data']['list'];

        $users = QueryHelper::convertApiDataToPaginate(
            items: $results['users']['data']['list'],
            pagination: $results['users']['data']['pagination'],
        );

        return view('profile.dislikes.users', compact('items', 'profile', 'followersYouFollow', 'users'));
    }

    // dislikeGroups
    public function dislikeGroups(Request $request, int|string $uidOrUsername)
    {
        if (! fs_config('profile_dislikes_groups_enabled')) {
            return Response::view('404', [], 404);
        }

        $query = $request->all();

        $results = UserInterface::detail($uidOrUsername, 'dislike', 'groups', $query);

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
        $items = $results['profile']['data']['items'];
        $profile = $results['profile']['data']['detail'];
        $followersYouFollow = $results['followersYouFollow']['data']['list'];

        $groups = QueryHelper::convertApiDataToPaginate(
            items: $results['groups']['data']['list'],
            pagination: $results['groups']['data']['pagination'],
        );

        return view('profile.dislikes.groups', compact('items', 'profile', 'followersYouFollow', 'groups'));
    }

    // dislikeHashtags
    public function dislikeHashtags(Request $request, int|string $uidOrUsername)
    {
        if (! fs_config('profile_dislikes_hashtags_enabled')) {
            return Response::view('404', [], 404);
        }

        $query = $request->all();

        $results = UserInterface::detail($uidOrUsername, 'dislike', 'hashtags', $query);

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
        $items = $results['profile']['data']['items'];
        $profile = $results['profile']['data']['detail'];
        $followersYouFollow = $results['followersYouFollow']['data']['list'];

        $hashtags = QueryHelper::convertApiDataToPaginate(
            items: $results['hashtags']['data']['list'],
            pagination: $results['hashtags']['data']['pagination'],
        );

        return view('profile.dislikes.hashtags', compact('items', 'profile', 'followersYouFollow', 'hashtags'));
    }

    // dislikeGeotags
    public function dislikeGeotags(Request $request, int|string $uidOrUsername)
    {
        if (! fs_config('profile_dislikes_geotags_enabled')) {
            return Response::view('404', [], 404);
        }

        $query = $request->all();

        $results = UserInterface::detail($uidOrUsername, 'dislike', 'geotags', $query);

        // ajax
        if ($request->ajax()) {
            $html = '';
            foreach ($results['geotags']['data']['list'] as $geotag) {
                $html .= View::make('components.geotag.list', compact('geotag'))->render();
            }

            return response()->json([
                'pagination' => $results['geotags']['data']['pagination'],
                'html' => $html,
            ]);
        }

        // view
        $items = $results['profile']['data']['items'];
        $profile = $results['profile']['data']['detail'];
        $followersYouFollow = $results['followersYouFollow']['data']['list'];

        $geotags = QueryHelper::convertApiDataToPaginate(
            items: $results['geotags']['data']['list'],
            pagination: $results['geotags']['data']['pagination'],
        );

        return view('profile.dislikes.geotags', compact('items', 'profile', 'followersYouFollow', 'geotags'));
    }

    // dislikePosts
    public function dislikePosts(Request $request, int|string $uidOrUsername)
    {
        if (! fs_config('profile_dislikes_posts_enabled')) {
            return Response::view('404', [], 404);
        }

        $query = $request->all();

        $results = UserInterface::detail($uidOrUsername, 'dislike', 'posts', $query);

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
        $items = $results['profile']['data']['items'];
        $profile = $results['profile']['data']['detail'];
        $followersYouFollow = $results['followersYouFollow']['data']['list'];

        $posts = QueryHelper::convertApiDataToPaginate(
            items: $results['posts']['data']['list'],
            pagination: $results['posts']['data']['pagination'],
        );

        return view('profile.dislikes.posts', compact('items', 'profile', 'followersYouFollow', 'posts'));
    }

    // dislikeComments
    public function dislikeComments(Request $request, int|string $uidOrUsername)
    {
        if (! fs_config('profile_dislikes_comments_enabled')) {
            return Response::view('404', [], 404);
        }

        $query = $request->all();

        $results = UserInterface::detail($uidOrUsername, 'dislike', 'comments', $query);

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
        $items = $results['profile']['data']['items'];
        $profile = $results['profile']['data']['detail'];
        $followersYouFollow = $results['followersYouFollow']['data']['list'];

        $comments = QueryHelper::convertApiDataToPaginate(
            items: $results['comments']['data']['list'],
            pagination: $results['comments']['data']['pagination'],
        );

        return view('profile.dislikes.comments', compact('items', 'profile', 'followersYouFollow', 'comments'));
    }

    /**
     * following.
     */

    // followingUsers
    public function followingUsers(Request $request, int|string $uidOrUsername)
    {
        if (! fs_config('profile_following_users_enabled')) {
            return Response::view('404', [], 404);
        }

        $query = $request->all();

        $results = UserInterface::detail($uidOrUsername, 'follow', 'users', $query);

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
        $items = $results['profile']['data']['items'];
        $profile = $results['profile']['data']['detail'];
        $followersYouFollow = $results['followersYouFollow']['data']['list'];

        $users = QueryHelper::convertApiDataToPaginate(
            items: $results['users']['data']['list'],
            pagination: $results['users']['data']['pagination'],
        );

        return view('profile.following.users', compact('items', 'profile', 'followersYouFollow', 'users'));
    }

    // followingGroups
    public function followingGroups(Request $request, int|string $uidOrUsername)
    {
        if (! fs_config('profile_following_groups_enabled')) {
            return Response::view('404', [], 404);
        }

        $query = $request->all();

        $results = UserInterface::detail($uidOrUsername, 'follow', 'groups', $query);

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
        $items = $results['profile']['data']['items'];
        $profile = $results['profile']['data']['detail'];
        $followersYouFollow = $results['followersYouFollow']['data']['list'];

        $groups = QueryHelper::convertApiDataToPaginate(
            items: $results['groups']['data']['list'],
            pagination: $results['groups']['data']['pagination'],
        );

        return view('profile.following.groups', compact('items', 'profile', 'followersYouFollow', 'groups'));
    }

    // followingHashtags
    public function followingHashtags(Request $request, int|string $uidOrUsername)
    {
        if (! fs_config('profile_following_hashtags_enabled')) {
            return Response::view('404', [], 404);
        }

        $query = $request->all();

        $results = UserInterface::detail($uidOrUsername, 'follow', 'hashtags', $query);

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
        $items = $results['profile']['data']['items'];
        $profile = $results['profile']['data']['detail'];
        $followersYouFollow = $results['followersYouFollow']['data']['list'];

        $hashtags = QueryHelper::convertApiDataToPaginate(
            items: $results['hashtags']['data']['list'],
            pagination: $results['hashtags']['data']['pagination'],
        );

        return view('profile.following.hashtags', compact('items', 'profile', 'followersYouFollow', 'hashtags'));
    }

    // followingGeotags
    public function followingGeotags(Request $request, int|string $uidOrUsername)
    {
        if (! fs_config('profile_following_geotags_enabled')) {
            return Response::view('404', [], 404);
        }

        $query = $request->all();

        $results = UserInterface::detail($uidOrUsername, 'follow', 'geotags', $query);

        // ajax
        if ($request->ajax()) {
            $html = '';
            foreach ($results['geotags']['data']['list'] as $geotag) {
                $html .= View::make('components.geotag.list', compact('geotag'))->render();
            }

            return response()->json([
                'pagination' => $results['geotags']['data']['pagination'],
                'html' => $html,
            ]);
        }

        // view
        $items = $results['profile']['data']['items'];
        $profile = $results['profile']['data']['detail'];
        $followersYouFollow = $results['followersYouFollow']['data']['list'];

        $geotags = QueryHelper::convertApiDataToPaginate(
            items: $results['geotags']['data']['list'],
            pagination: $results['geotags']['data']['pagination'],
        );

        return view('profile.following.geotags', compact('items', 'profile', 'followersYouFollow', 'geotags'));
    }

    // followingPosts
    public function followingPosts(Request $request, int|string $uidOrUsername)
    {
        if (! fs_config('profile_following_posts_enabled')) {
            return Response::view('404', [], 404);
        }

        $query = $request->all();

        $results = UserInterface::detail($uidOrUsername, 'follow', 'posts', $query);

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
        $items = $results['profile']['data']['items'];
        $profile = $results['profile']['data']['detail'];
        $followersYouFollow = $results['followersYouFollow']['data']['list'];

        $posts = QueryHelper::convertApiDataToPaginate(
            items: $results['posts']['data']['list'],
            pagination: $results['posts']['data']['pagination'],
        );

        return view('profile.following.posts', compact('items', 'profile', 'followersYouFollow', 'posts'));
    }

    // followingComments
    public function followingComments(Request $request, int|string $uidOrUsername)
    {
        if (! fs_config('profile_following_comments_enabled')) {
            return Response::view('404', [], 404);
        }

        $query = $request->all();

        $results = UserInterface::detail($uidOrUsername, 'follow', 'comments', $query);

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
        $items = $results['profile']['data']['items'];
        $profile = $results['profile']['data']['detail'];
        $followersYouFollow = $results['followersYouFollow']['data']['list'];

        $comments = QueryHelper::convertApiDataToPaginate(
            items: $results['comments']['data']['list'],
            pagination: $results['comments']['data']['pagination'],
        );

        return view('profile.following.comments', compact('items', 'profile', 'followersYouFollow', 'comments'));
    }

    /**
     * blocking.
     */

    // blockingUsers
    public function blockingUsers(Request $request, int|string $uidOrUsername)
    {
        if (! fs_config('profile_blocking_users_enabled')) {
            return Response::view('404', [], 404);
        }

        $query = $request->all();

        $results = UserInterface::detail($uidOrUsername, 'block', 'users', $query);

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
        $items = $results['profile']['data']['items'];
        $profile = $results['profile']['data']['detail'];
        $followersYouFollow = $results['followersYouFollow']['data']['list'];

        $users = QueryHelper::convertApiDataToPaginate(
            items: $results['users']['data']['list'],
            pagination: $results['users']['data']['pagination'],
        );

        return view('profile.blocking.users', compact('items', 'profile', 'followersYouFollow', 'users'));
    }

    // blockingGroups
    public function blockingGroups(Request $request, int|string $uidOrUsername)
    {
        if (! fs_config('profile_blocking_groups_enabled')) {
            return Response::view('404', [], 404);
        }

        $query = $request->all();

        $results = UserInterface::detail($uidOrUsername, 'block', 'groups', $query);

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
        $items = $results['profile']['data']['items'];
        $profile = $results['profile']['data']['detail'];
        $followersYouFollow = $results['followersYouFollow']['data']['list'];

        $groups = QueryHelper::convertApiDataToPaginate(
            items: $results['groups']['data']['list'],
            pagination: $results['groups']['data']['pagination'],
        );

        return view('profile.blocking.groups', compact('items', 'profile', 'followersYouFollow', 'groups'));
    }

    // blockingHashtags
    public function blockingHashtags(Request $request, int|string $uidOrUsername)
    {
        if (! fs_config('profile_blocking_hashtags_enabled')) {
            return Response::view('404', [], 404);
        }

        $query = $request->all();

        $results = UserInterface::detail($uidOrUsername, 'block', 'hashtags', $query);

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
        $items = $results['profile']['data']['items'];
        $profile = $results['profile']['data']['detail'];
        $followersYouFollow = $results['followersYouFollow']['data']['list'];

        $hashtags = QueryHelper::convertApiDataToPaginate(
            items: $results['hashtags']['data']['list'],
            pagination: $results['hashtags']['data']['pagination'],
        );

        return view('profile.blocking.hashtags', compact('items', 'profile', 'followersYouFollow', 'hashtags'));
    }

    // blockingGeotags
    public function blockingGeotags(Request $request, int|string $uidOrUsername)
    {
        if (! fs_config('profile_blocking_geotags_enabled')) {
            return Response::view('404', [], 404);
        }

        $query = $request->all();

        $results = UserInterface::detail($uidOrUsername, 'block', 'geotags', $query);

        // ajax
        if ($request->ajax()) {
            $html = '';
            foreach ($results['geotags']['data']['list'] as $geotag) {
                $html .= View::make('components.geotag.list', compact('geotag'))->render();
            }

            return response()->json([
                'pagination' => $results['geotags']['data']['pagination'],
                'html' => $html,
            ]);
        }

        // view
        $items = $results['profile']['data']['items'];
        $profile = $results['profile']['data']['detail'];
        $followersYouFollow = $results['followersYouFollow']['data']['list'];

        $geotags = QueryHelper::convertApiDataToPaginate(
            items: $results['geotags']['data']['list'],
            pagination: $results['geotags']['data']['pagination'],
        );

        return view('profile.blocking.geotags', compact('items', 'profile', 'followersYouFollow', 'geotags'));
    }

    // blockingPosts
    public function blockingPosts(Request $request, int|string $uidOrUsername)
    {
        if (! fs_config('profile_blocking_posts_enabled')) {
            return Response::view('404', [], 404);
        }

        $query = $request->all();

        $results = UserInterface::detail($uidOrUsername, 'block', 'posts', $query);

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
        $items = $results['profile']['data']['items'];
        $profile = $results['profile']['data']['detail'];
        $followersYouFollow = $results['followersYouFollow']['data']['list'];

        $posts = QueryHelper::convertApiDataToPaginate(
            items: $results['posts']['data']['list'],
            pagination: $results['posts']['data']['pagination'],
        );

        return view('profile.blocking.posts', compact('items', 'profile', 'followersYouFollow', 'posts'));
    }

    // blockingComments
    public function blockingComments(Request $request, int|string $uidOrUsername)
    {
        if (! fs_config('profile_blocking_comments_enabled')) {
            return Response::view('404', [], 404);
        }

        $query = $request->all();

        $results = UserInterface::detail($uidOrUsername, 'block', 'comments', $query);

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
        $items = $results['profile']['data']['items'];
        $profile = $results['profile']['data']['detail'];
        $followersYouFollow = $results['followersYouFollow']['data']['list'];

        $comments = QueryHelper::convertApiDataToPaginate(
            items: $results['comments']['data']['list'],
            pagination: $results['comments']['data']['pagination'],
        );

        return view('profile.blocking.comments', compact('items', 'profile', 'followersYouFollow', 'comments'));
    }
}
