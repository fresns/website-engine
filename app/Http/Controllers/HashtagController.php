<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jevan Tang
 * Released under the Apache-2.0 License.
 */

namespace Fresns\WebsiteEngine\Http\Controllers;

use Fresns\WebsiteEngine\Helpers\QueryHelper;
use Fresns\WebsiteEngine\Interfaces\HashtagInterface;
use Fresns\WebsiteEngine\Interfaces\UserInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\View;

class HashtagController extends Controller
{
    // index
    public function index(Request $request)
    {
        if (! fs_config('channel_hashtag_status')) {
            return Response::view('404', [], 404);
        }

        $query = QueryHelper::convertOptionToRequestParam(QueryHelper::TYPE_HASHTAG, $request->all());

        $result = HashtagInterface::list($query);

        // ajax
        if ($request->ajax()) {
            $html = '';
            foreach ($result['data']['list'] as $hashtag) {
                $html .= View::make('components.hashtags.list', compact('hashtag'))->render();
            }

            return response()->json([
                'pagination' => $result['data']['pagination'],
                'html' => $html,
            ]);
        }

        // view
        $hashtags = QueryHelper::convertApiDataToPaginate(
            items: $result['data']['list'],
            pagination: $result['data']['pagination'],
        );

        return view('hashtags.index', compact('hashtags'));
    }

    // list
    public function list(Request $request)
    {
        if (! fs_config('channel_hashtag_list_status')) {
            return Response::view('404', [], 404);
        }

        $query = QueryHelper::convertOptionToRequestParam(QueryHelper::TYPE_HASHTAG_LIST, $request->all());

        $result = HashtagInterface::list($query);

        // ajax
        if ($request->ajax()) {
            $html = '';
            foreach ($result['data']['list'] as $hashtag) {
                $html .= View::make('components.hashtags.list', compact('hashtag'))->render();
            }

            return response()->json([
                'pagination' => $result['data']['pagination'],
                'html' => $html,
            ]);
        }

        // view
        $hashtags = QueryHelper::convertApiDataToPaginate(
            items: $result['data']['list'],
            pagination: $result['data']['pagination'],
        );

        return view('hashtags.list', compact('hashtags'));
    }

    // likes
    public function likes(Request $request)
    {
        $uid = (int) fs_user('detail.uid');

        $result = UserInterface::markList($uid, 'like', 'hashtags', $request->all());

        // ajax
        if ($request->ajax()) {
            $html = '';
            foreach ($result['data']['list'] as $hashtag) {
                $html .= View::make('components.hashtags.list', compact('hashtag'))->render();
            }

            return response()->json([
                'pagination' => $result['data']['pagination'],
                'html' => $html,
            ]);
        }

        // view
        $hashtags = QueryHelper::convertApiDataToPaginate(
            items: $result['data']['list'],
            pagination: $result['data']['pagination'],
        );

        return view('hashtags.likes', compact('hashtags'));
    }

    // dislikes
    public function dislikes(Request $request)
    {
        $uid = (int) fs_user('detail.uid');

        $result = UserInterface::markList($uid, 'dislike', 'hashtags', $request->all());

        // ajax
        if ($request->ajax()) {
            $html = '';
            foreach ($result['data']['list'] as $hashtag) {
                $html .= View::make('components.hashtags.list', compact('hashtag'))->render();
            }

            return response()->json([
                'pagination' => $result['data']['pagination'],
                'html' => $html,
            ]);
        }

        // view
        $hashtags = QueryHelper::convertApiDataToPaginate(
            items: $result['data']['list'],
            pagination: $result['data']['pagination'],
        );

        return view('hashtags.dislikes', compact('hashtags'));
    }

    // following
    public function following(Request $request)
    {
        $uid = (int) fs_user('detail.uid');

        $result = UserInterface::markList($uid, 'follow', 'hashtags', $request->all());

        // ajax
        if ($request->ajax()) {
            $html = '';
            foreach ($result['data']['list'] as $hashtag) {
                $html .= View::make('components.hashtags.list', compact('hashtag'))->render();
            }

            return response()->json([
                'pagination' => $result['data']['pagination'],
                'html' => $html,
            ]);
        }

        // view
        $hashtags = QueryHelper::convertApiDataToPaginate(
            items: $result['data']['list'],
            pagination: $result['data']['pagination'],
        );

        return view('hashtags.following', compact('hashtags'));
    }

    // blocking
    public function blocking(Request $request)
    {
        $uid = (int) fs_user('detail.uid');

        $result = UserInterface::markList($uid, 'block', 'hashtags', $request->all());

        // ajax
        if ($request->ajax()) {
            $html = '';
            foreach ($result['data']['list'] as $hashtag) {
                $html .= View::make('components.hashtags.list', compact('hashtag'))->render();
            }

            return response()->json([
                'pagination' => $result['data']['pagination'],
                'html' => $html,
            ]);
        }

        // view
        $hashtags = QueryHelper::convertApiDataToPaginate(
            items: $result['data']['list'],
            pagination: $result['data']['pagination'],
        );

        return view('hashtags.blocking', compact('hashtags'));
    }

    // detail
    public function detail(Request $request, string $htid, ?string $type = null)
    {
        $query = $request->all();
        $query['hashtags'] = $htid;

        $type = match ($type) {
            'posts' => 'posts',
            'comments' => 'comments',
            default => 'posts',
        };

        $results = HashtagInterface::detail($htid, $type, $query);

        // ajax
        if ($request->ajax()) {
            $html = '';

            switch ($type) {
                case 'posts':
                    foreach ($results['posts']['data']['list'] as $post) {
                        $html .= View::make('components.posts.list', compact('post'))->render();
                    }
                    break;

                case 'comments':
                    foreach ($results['comments']['data']['list'] as $comment) {
                        $html .= View::make('components.comments.list', compact('comment'))->render();
                    }
                    break;
            }

            return response()->json([
                'pagination' => $results[$type]['data']['pagination'],
                'html' => $html,
            ]);
        }

        // view
        $items = $results['hashtag']['data']['items'];
        $hashtag = $results['hashtag']['data']['detail'];

        $posts = [];
        $comments = [];

        switch ($type) {
            case 'posts':
                $posts = QueryHelper::convertApiDataToPaginate(
                    items: $results['posts']['data']['list'],
                    pagination: $results['posts']['data']['pagination'],
                );
                break;

            case 'comments':
                $comments = QueryHelper::convertApiDataToPaginate(
                    items: $results['comments']['data']['list'],
                    pagination: $results['comments']['data']['pagination'],
                );
                break;
        }

        return view('hashtags.detail', compact('items', 'hashtag', 'type', 'posts', 'comments'));
    }

    // detail likers
    public function detailLikers(Request $request, string $htid)
    {
        $results = HashtagInterface::interaction($htid, 'likers', $request->all());

        if (! $results['hashtag']['data']['detail']['interaction']['likePublicRecord']) {
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
        $items = $results['hashtag']['data']['items'];
        $hashtag = $results['hashtag']['data']['detail'];

        $users = QueryHelper::convertApiDataToPaginate(
            items: $results['users']['data']['list'],
            pagination: $results['users']['data']['pagination'],
        );

        return view('hashtags.detail-likers', compact('items', 'hashtag', 'users'));
    }

    // detail dislikers
    public function detailDislikers(Request $request, string $htid)
    {
        $results = HashtagInterface::interaction($htid, 'dislikers', $request->all());

        if (! $results['hashtag']['data']['detail']['interaction']['likePublicRecord']) {
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
        $items = $results['hashtag']['data']['items'];
        $hashtag = $results['hashtag']['data']['detail'];

        $users = QueryHelper::convertApiDataToPaginate(
            items: $results['users']['data']['list'],
            pagination: $results['users']['data']['pagination'],
        );

        return view('hashtags.detail-dislikers', compact('items', 'hashtag', 'users'));
    }

    // detail followers
    public function detailFollowers(Request $request, string $htid)
    {
        $results = HashtagInterface::interaction($htid, 'followers', $request->all());

        if (! $results['hashtag']['data']['detail']['interaction']['likePublicRecord']) {
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
        $items = $results['hashtag']['data']['items'];
        $hashtag = $results['hashtag']['data']['detail'];

        $users = QueryHelper::convertApiDataToPaginate(
            items: $results['users']['data']['list'],
            pagination: $results['users']['data']['pagination'],
        );

        return view('hashtags.detail-followers', compact('items', 'hashtag', 'users'));
    }

    // detail blockers
    public function detailBlockers(Request $request, string $htid)
    {
        $results = HashtagInterface::interaction($htid, 'blockers', $request->all());

        if (! $results['hashtag']['data']['detail']['interaction']['likePublicRecord']) {
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
        $items = $results['hashtag']['data']['items'];
        $hashtag = $results['hashtag']['data']['detail'];

        $users = QueryHelper::convertApiDataToPaginate(
            items: $results['users']['data']['list'],
            pagination: $results['users']['data']['pagination'],
        );

        return view('hashtags.detail-blockers', compact('items', 'hashtag', 'users'));
    }
}
