<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jevan Tang
 * Released under the Apache-2.0 License.
 */

namespace Fresns\WebEngine\Http\Controllers;

use Fresns\WebEngine\Exceptions\ErrorException;
use Fresns\WebEngine\Helpers\QueryHelper;
use Fresns\WebEngine\Interfaces\GeotagInterface;
use Fresns\WebEngine\Interfaces\UserInterface;
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

        if (data_get($result, 'code') !== 0) {
            throw new ErrorException($result['message'], $result['code']);
        }

        $geotags = QueryHelper::convertApiDataToPaginate(
            items: $result['data']['list'],
            pagination: $result['data']['pagination'],
        );

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

        if (data_get($result, 'code') !== 0) {
            throw new ErrorException($result['message'], $result['code']);
        }

        $geotags = QueryHelper::convertApiDataToPaginate(
            items: $result['data']['list'],
            pagination: $result['data']['pagination'],
        );

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
        return view('geotags.list', compact('geotags'));
    }

    // likes
    public function likes(Request $request)
    {
        $uid = (int) fs_user('detail.uid');

        $result = UserInterface::markList($uid, 'like', 'geotags', $request->all());

        if (data_get($result, 'code') !== 0) {
            throw new ErrorException($result['message'], $result['code']);
        }

        $geotags = QueryHelper::convertApiDataToPaginate(
            items: $result['data']['list'],
            pagination: $result['data']['pagination'],
        );

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
        return view('geotags.likes', compact('geotags'));
    }

    // dislikes
    public function dislikes(Request $request)
    {
        $uid = (int) fs_user('detail.uid');

        $result = UserInterface::markList($uid, 'dislike', 'geotags', $request->all());

        if (data_get($result, 'code') !== 0) {
            throw new ErrorException($result['message'], $result['code']);
        }

        $geotags = QueryHelper::convertApiDataToPaginate(
            items: $result['data']['list'],
            pagination: $result['data']['pagination'],
        );

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
        return view('geotags.dislikes', compact('geotags'));
    }

    // following
    public function following(Request $request)
    {
        $uid = (int) fs_user('detail.uid');

        $result = UserInterface::markList($uid, 'follow', 'geotags', $request->all());

        if (data_get($result, 'code') !== 0) {
            throw new ErrorException($result['message'], $result['code']);
        }

        $geotags = QueryHelper::convertApiDataToPaginate(
            items: $result['data']['list'],
            pagination: $result['data']['pagination'],
        );

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
        return view('geotags.following', compact('geotags'));
    }

    // blocking
    public function blocking(Request $request)
    {
        $uid = (int) fs_user('detail.uid');

        $result = UserInterface::markList($uid, 'block', 'geotags', $request->all());

        if (data_get($result, 'code') !== 0) {
            throw new ErrorException($result['message'], $result['code']);
        }

        $geotags = QueryHelper::convertApiDataToPaginate(
            items: $result['data']['list'],
            pagination: $result['data']['pagination'],
        );

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
        return view('geotags.blocking', compact('geotags'));
    }

    // detail
    public function detail(Request $request, string $gtid, ?string $type = null)
    {
        $query = $request->all();
        $query['gtid'] = $gtid;

        $type = match ($type) {
            'posts' => 'posts',
            'comments' => 'comments',
            default => 'posts',
        };

        $posts = [];
        $comments = [];

        switch ($type) {
            case 'posts':
                $results = GeotagInterface::detail($gtid, 'posts', $query);

                $posts = QueryHelper::convertApiDataToPaginate(
                    items: $results['posts']['data']['list'],
                    pagination: $results['posts']['data']['pagination'],
                );
                $pagination = $results['posts']['data']['pagination'];
                break;

            case 'comments':
                $results = GeotagInterface::detail($gtid, 'comments', $query);

                $comments = QueryHelper::convertApiDataToPaginate(
                    items: $results['comments']['data']['list'],
                    pagination: $results['comments']['data']['pagination'],
                );
                $pagination = $results['comments']['data']['pagination'];
                break;
        }

        if ($results['geotag']['code'] != 0) {
            throw new ErrorException($results['geotag']['message'], $results['geotag']['code']);
        }

        $items = $results['geotag']['data']['items'];
        $geotag = $results['geotag']['data']['detail'];

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
                'pagination' => $pagination,
                'html' => $html,
            ]);
        }

        // view
        return view('geotags.detail', compact('items', 'geotag', 'type', 'posts', 'comments'));
    }
}
