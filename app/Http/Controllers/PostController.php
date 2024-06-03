<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jevan Tang
 * Released under the Apache-2.0 License.
 */

namespace Fresns\WebsiteEngine\Http\Controllers;

use Fresns\WebsiteEngine\Helpers\QueryHelper;
use Fresns\WebsiteEngine\Interfaces\PostInterface;
use Fresns\WebsiteEngine\Interfaces\UserInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\View;

class PostController extends Controller
{
    // index
    public function index(Request $request)
    {
        if (! fs_config('channel_post_status')) {
            return Response::view('404', [], 404);
        }

        $query = QueryHelper::convertOptionToRequestParam(QueryHelper::TYPE_POST, $request->all());

        $result = PostInterface::list($query);

        // ajax
        if ($request->ajax()) {
            $html = '';
            foreach ($result['data']['list'] as $post) {
                $html .= View::make('components.posts.list', compact('post'))->render();
            }

            return response()->json([
                'pagination' => $result['data']['pagination'],
                'html' => $html,
            ]);
        }

        // view
        $posts = QueryHelper::convertApiDataToPaginate(
            items: $result['data']['list'],
            pagination: $result['data']['pagination'],
        );

        return view('posts.index', compact('posts'));
    }

    // list
    public function list(Request $request)
    {
        if (! fs_config('channel_post_list_status')) {
            return Response::view('404', [], 404);
        }

        $query = QueryHelper::convertOptionToRequestParam(QueryHelper::TYPE_POST_LIST, $request->all());

        $result = PostInterface::list($query);

        // ajax
        if ($request->ajax()) {
            $html = '';
            foreach ($result['data']['list'] as $post) {
                $html .= View::make('components.posts.list', compact('post'))->render();
            }

            return response()->json([
                'pagination' => $result['data']['pagination'],
                'html' => $html,
            ]);
        }

        // view
        $posts = QueryHelper::convertApiDataToPaginate(
            items: $result['data']['list'],
            pagination: $result['data']['pagination'],
        );

        return view('posts.list', compact('posts'));
    }

    // likes
    public function likes(Request $request)
    {
        $uid = (int) fs_user('detail.uid');

        $result = UserInterface::markList($uid, 'like', 'posts', $request->all());

        // ajax
        if ($request->ajax()) {
            $html = '';
            foreach ($result['data']['list'] as $post) {
                $html .= View::make('components.posts.list', compact('post'))->render();
            }

            return response()->json([
                'pagination' => $result['data']['pagination'],
                'html' => $html,
            ]);
        }

        // view
        $posts = QueryHelper::convertApiDataToPaginate(
            items: $result['data']['list'],
            pagination: $result['data']['pagination'],
        );

        return view('posts.likes', compact('posts'));
    }

    // dislikes
    public function dislikes(Request $request)
    {
        $uid = (int) fs_user('detail.uid');

        $result = UserInterface::markList($uid, 'dislike', 'posts', $request->all());

        // ajax
        if ($request->ajax()) {
            $html = '';
            foreach ($result['data']['list'] as $post) {
                $html .= View::make('components.posts.list', compact('post'))->render();
            }

            return response()->json([
                'pagination' => $result['data']['pagination'],
                'html' => $html,
            ]);
        }

        // view
        $posts = QueryHelper::convertApiDataToPaginate(
            items: $result['data']['list'],
            pagination: $result['data']['pagination'],
        );

        return view('posts.dislikes', compact('posts'));
    }

    // following
    public function following(Request $request)
    {
        $uid = (int) fs_user('detail.uid');

        $result = UserInterface::markList($uid, 'follow', 'posts', $request->all());

        // ajax
        if ($request->ajax()) {
            $html = '';
            foreach ($result['data']['list'] as $post) {
                $html .= View::make('components.posts.list', compact('post'))->render();
            }

            return response()->json([
                'pagination' => $result['data']['pagination'],
                'html' => $html,
            ]);
        }

        // view
        $posts = QueryHelper::convertApiDataToPaginate(
            items: $result['data']['list'],
            pagination: $result['data']['pagination'],
        );

        return view('posts.following', compact('posts'));
    }

    // blocking
    public function blocking(Request $request)
    {
        $uid = (int) fs_user('detail.uid');

        $result = UserInterface::markList($uid, 'block', 'posts', $request->all());

        // ajax
        if ($request->ajax()) {
            $html = '';
            foreach ($result['data']['list'] as $post) {
                $html .= View::make('components.posts.list', compact('post'))->render();
            }

            return response()->json([
                'pagination' => $result['data']['pagination'],
                'html' => $html,
            ]);
        }

        // view
        $posts = QueryHelper::convertApiDataToPaginate(
            items: $result['data']['list'],
            pagination: $result['data']['pagination'],
        );

        return view('posts.blocking', compact('posts'));
    }

    // detail
    public function detail(Request $request, string $pid)
    {
        $query = $request->all();
        $query['pid'] = $pid;
        $query['orderDirection'] = $query['orderDirection'] ?? 'asc';

        $results = PostInterface::detail($pid, $query);

        // ajax
        if ($request->ajax()) {
            $html = '';
            foreach ($results['comments']['data']['list'] as $comment) {
                $html .= View::make('components.comments.list', compact('comment'))->render();
            }

            return response()->json([
                'pagination' => $results['comments']['data']['pagination'],
                'html' => $html,
            ]);
        }

        // view
        $items = $results['post']['data']['items'];
        $post = $results['post']['data']['detail'];

        $comments = QueryHelper::convertApiDataToPaginate(
            items: $results['comments']['data']['list'],
            pagination: $results['comments']['data']['pagination'],
        );

        $stickies = data_get($results, 'stickies.data.list', []) ?? [];

        return view('posts.detail', compact('items', 'post', 'comments', 'stickies'));
    }

    // detail likers
    public function detailLikers(Request $request, string $pid)
    {
        $results = PostInterface::interaction($pid, 'likers', $request->all());

        if (! $results['post']['data']['detail']['interaction']['likePublicRecord']) {
            return Response::view('404', [], 404);
        }

        // ajax
        if ($request->ajax()) {
            $html = '';
            foreach ($results['users']['data']['list'] as $user) {
                $html .= View::make('components.users.list', compact('user'))->render();
            }

            return response()->json([
                'pagination' => $results['users']['data']['pagination'],
                'html' => $html,
            ]);
        }

        // view
        $items = $results['post']['data']['items'];
        $post = $results['post']['data']['detail'];

        $users = QueryHelper::convertApiDataToPaginate(
            items: $results['users']['data']['list'],
            pagination: $results['users']['data']['pagination'],
        );

        return view('posts.detail-likers', compact('items', 'post', 'users'));
    }

    // detail dislikers
    public function detailDislikers(Request $request, string $pid)
    {
        $results = PostInterface::interaction($pid, 'dislikers', $request->all());

        if (! $results['post']['data']['detail']['interaction']['likePublicRecord']) {
            return Response::view('404', [], 404);
        }

        // ajax
        if ($request->ajax()) {
            $html = '';
            foreach ($results['users']['data']['list'] as $user) {
                $html .= View::make('components.users.list', compact('user'))->render();
            }

            return response()->json([
                'pagination' => $results['users']['data']['pagination'],
                'html' => $html,
            ]);
        }

        // view
        $items = $results['post']['data']['items'];
        $post = $results['post']['data']['detail'];

        $users = QueryHelper::convertApiDataToPaginate(
            items: $results['users']['data']['list'],
            pagination: $results['users']['data']['pagination'],
        );

        return view('posts.detail-dislikers', compact('items', 'post', 'users'));
    }

    // detail followers
    public function detailFollowers(Request $request, string $pid)
    {
        $results = PostInterface::interaction($pid, 'followers', $request->all());

        if (! $results['post']['data']['detail']['interaction']['likePublicRecord']) {
            return Response::view('404', [], 404);
        }

        // ajax
        if ($request->ajax()) {
            $html = '';
            foreach ($results['users']['data']['list'] as $user) {
                $html .= View::make('components.users.list', compact('user'))->render();
            }

            return response()->json([
                'pagination' => $results['users']['data']['pagination'],
                'html' => $html,
            ]);
        }

        // view
        $items = $results['post']['data']['items'];
        $post = $results['post']['data']['detail'];

        $users = QueryHelper::convertApiDataToPaginate(
            items: $results['users']['data']['list'],
            pagination: $results['users']['data']['pagination'],
        );

        return view('posts.detail-followers', compact('items', 'post', 'users'));
    }

    // detail blockers
    public function detailBlockers(Request $request, string $pid)
    {
        $results = PostInterface::interaction($pid, 'blockers', $request->all());

        if (! $results['post']['data']['detail']['interaction']['likePublicRecord']) {
            return Response::view('404', [], 404);
        }

        // ajax
        if ($request->ajax()) {
            $html = '';
            foreach ($results['users']['data']['list'] as $user) {
                $html .= View::make('components.users.list', compact('user'))->render();
            }

            return response()->json([
                'pagination' => $results['users']['data']['pagination'],
                'html' => $html,
            ]);
        }

        // view
        $items = $results['post']['data']['items'];
        $post = $results['post']['data']['detail'];

        $users = QueryHelper::convertApiDataToPaginate(
            items: $results['users']['data']['list'],
            pagination: $results['users']['data']['pagination'],
        );

        return view('posts.detail-blockers', compact('items', 'post', 'users'));
    }
}
