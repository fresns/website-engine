<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jevan Tang
 * Released under the Apache-2.0 License.
 */

namespace Fresns\WebsiteEngine\Http\Controllers;

use Fresns\WebsiteEngine\Helpers\QueryHelper;
use Fresns\WebsiteEngine\Interfaces\GeotagInterface;
use Fresns\WebsiteEngine\Interfaces\UserInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\View;

class GeotagController extends Controller
{
    // index
    public function index(Request $request)
    {
        if (! fs_config('channel_geotag_status')) {
            return Response::view('404', [], 404);
        }

        $query = QueryHelper::convertOptionToRequestParam(QueryHelper::TYPE_GEOTAG, $request->all());

        $result = GeotagInterface::list($query);

        // ajax
        if ($request->ajax()) {
            $html = '';
            foreach ($result['data']['list'] as $geotag) {
                $html .= View::make('components.geotag.list', compact('geotag'))->render();
            }

            return response()->json([
                'pagination' => $result['data']['pagination'],
                'html' => $html,
            ]);
        }

        // view
        $geotags = QueryHelper::convertApiDataToPaginate(
            items: $result['data']['list'],
            pagination: $result['data']['pagination'],
        );

        return view('geotags.index', compact('geotags'));
    }

    // list
    public function list(Request $request)
    {
        if (! fs_config('channel_geotag_list_status')) {
            return Response::view('404', [], 404);
        }

        $query = QueryHelper::convertOptionToRequestParam(QueryHelper::TYPE_GEOTAG_LIST, $request->all());

        $result = GeotagInterface::list($query);

        // ajax
        if ($request->ajax()) {
            $html = '';
            foreach ($result['data']['list'] as $geotag) {
                $html .= View::make('components.geotag.list', compact('geotag'))->render();
            }

            return response()->json([
                'pagination' => $result['data']['pagination'],
                'html' => $html,
            ]);
        }

        // view
        $geotags = QueryHelper::convertApiDataToPaginate(
            items: $result['data']['list'],
            pagination: $result['data']['pagination'],
        );

        return view('geotags.list', compact('geotags'));
    }

    // likes
    public function likes(Request $request)
    {
        $uid = (int) fs_user('detail.uid');

        $result = UserInterface::markList($uid, 'like', 'geotags', $request->all());

        // ajax
        if ($request->ajax()) {
            $html = '';
            foreach ($result['data']['list'] as $geotag) {
                $html .= View::make('components.geotag.list', compact('geotag'))->render();
            }

            return response()->json([
                'pagination' => $result['data']['pagination'],
                'html' => $html,
            ]);
        }

        // view
        $geotags = QueryHelper::convertApiDataToPaginate(
            items: $result['data']['list'],
            pagination: $result['data']['pagination'],
        );

        return view('geotags.likes', compact('geotags'));
    }

    // dislikes
    public function dislikes(Request $request)
    {
        $uid = (int) fs_user('detail.uid');

        $result = UserInterface::markList($uid, 'dislike', 'geotags', $request->all());

        // ajax
        if ($request->ajax()) {
            $html = '';
            foreach ($result['data']['list'] as $geotag) {
                $html .= View::make('components.geotag.list', compact('geotag'))->render();
            }

            return response()->json([
                'pagination' => $result['data']['pagination'],
                'html' => $html,
            ]);
        }

        // view
        $geotags = QueryHelper::convertApiDataToPaginate(
            items: $result['data']['list'],
            pagination: $result['data']['pagination'],
        );

        return view('geotags.dislikes', compact('geotags'));
    }

    // following
    public function following(Request $request)
    {
        $uid = (int) fs_user('detail.uid');

        $result = UserInterface::markList($uid, 'follow', 'geotags', $request->all());

        // ajax
        if ($request->ajax()) {
            $html = '';
            foreach ($result['data']['list'] as $geotag) {
                $html .= View::make('components.geotag.list', compact('geotag'))->render();
            }

            return response()->json([
                'pagination' => $result['data']['pagination'],
                'html' => $html,
            ]);
        }

        // view
        $geotags = QueryHelper::convertApiDataToPaginate(
            items: $result['data']['list'],
            pagination: $result['data']['pagination'],
        );

        return view('geotags.following', compact('geotags'));
    }

    // blocking
    public function blocking(Request $request)
    {
        $uid = (int) fs_user('detail.uid');

        $result = UserInterface::markList($uid, 'block', 'geotags', $request->all());

        // ajax
        if ($request->ajax()) {
            $html = '';
            foreach ($result['data']['list'] as $geotag) {
                $html .= View::make('components.geotag.list', compact('geotag'))->render();
            }

            return response()->json([
                'pagination' => $result['data']['pagination'],
                'html' => $html,
            ]);
        }

        // view
        $geotags = QueryHelper::convertApiDataToPaginate(
            items: $result['data']['list'],
            pagination: $result['data']['pagination'],
        );

        return view('geotags.blocking', compact('geotags'));
    }

    // detail
    public function detail(Request $request, string $gtid, ?string $type = null)
    {
        $query = $request->all();
        $query['geotags'] = $gtid;

        $type = match ($type) {
            'posts' => 'posts',
            'comments' => 'comments',
            default => 'posts',
        };

        $posts = [];
        $comments = [];

        $results = GeotagInterface::detail($gtid, $type, $query);

        // ajax
        if ($request->ajax()) {
            $html = '';

            switch ($type) {
                case 'posts':
                    foreach ($results['posts']['data']['list'] as $post) {
                        $html .= View::make('components.post.list', compact('post'))->render();
                    }
                    break;

                case 'comments':
                    foreach ($results['comments']['data']['list'] as $comment) {
                        $html .= View::make('components.comment.list', compact('comment'))->render();
                    }
                    break;
            }

            return response()->json([
                'pagination' => $results[$type]['data']['pagination'],
                'html' => $html,
            ]);
        }

        // view
        $items = $results['geotag']['data']['items'];
        $geotag = $results['geotag']['data']['detail'];

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

        return view('geotags.detail', compact('items', 'geotag', 'type', 'posts', 'comments'));
    }

    // detail likers
    public function detailLikers(Request $request, string $gtid)
    {
        $results = GeotagInterface::interaction($gtid, 'likers', $request->all());

        if (! $results['geotag']['data']['detail']['interaction']['likePublicRecord']) {
            return Response::view('404', [], 404);
        }

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
        $items = $results['geotag']['data']['items'];
        $geotag = $results['geotag']['data']['detail'];

        $users = QueryHelper::convertApiDataToPaginate(
            items: $results['users']['data']['list'],
            pagination: $results['users']['data']['pagination'],
        );

        return view('geotags.detail-likers', compact('items', 'geotag', 'users'));
    }

    // detail dislikers
    public function detailDislikers(Request $request, string $gtid)
    {
        $results = GeotagInterface::interaction($gtid, 'dislikers', $request->all());

        if (! $results['geotag']['data']['detail']['interaction']['likePublicRecord']) {
            return Response::view('404', [], 404);
        }

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
        $items = $results['geotag']['data']['items'];
        $geotag = $results['geotag']['data']['detail'];

        $users = QueryHelper::convertApiDataToPaginate(
            items: $results['users']['data']['list'],
            pagination: $results['users']['data']['pagination'],
        );

        return view('geotags.detail-dislikers', compact('items', 'geotag', 'users'));
    }

    // detail followers
    public function detailFollowers(Request $request, string $gtid)
    {
        $results = GeotagInterface::interaction($gtid, 'followers', $request->all());

        if (! $results['geotag']['data']['detail']['interaction']['likePublicRecord']) {
            return Response::view('404', [], 404);
        }

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
        $items = $results['geotag']['data']['items'];
        $geotag = $results['geotag']['data']['detail'];

        $users = QueryHelper::convertApiDataToPaginate(
            items: $results['users']['data']['list'],
            pagination: $results['users']['data']['pagination'],
        );

        return view('geotags.detail-followers', compact('items', 'geotag', 'users'));
    }

    // detail blockers
    public function detailBlockers(Request $request, string $gtid)
    {
        $results = GeotagInterface::interaction($gtid, 'blockers', $request->all());

        if (! $results['geotag']['data']['detail']['interaction']['likePublicRecord']) {
            return Response::view('404', [], 404);
        }

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
        $items = $results['geotag']['data']['items'];
        $geotag = $results['geotag']['data']['detail'];

        $users = QueryHelper::convertApiDataToPaginate(
            items: $results['users']['data']['list'],
            pagination: $results['users']['data']['pagination'],
        );

        return view('geotags.detail-blockers', compact('items', 'geotag', 'users'));
    }
}
