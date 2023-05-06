<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jevan Tang
 * Released under the Apache-2.0 License.
 */

namespace Plugins\FresnsEngine\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\View;
use Plugins\FresnsEngine\Exceptions\ErrorException;
use Plugins\FresnsEngine\Helpers\ApiHelper;
use Plugins\FresnsEngine\Helpers\QueryHelper;

class HashtagController extends Controller
{
    // index
    public function index(Request $request)
    {
        if (! fs_db_config('menu_hashtag_status')) {
            return Response::view('404', [], 404);
        }

        $query = QueryHelper::convertOptionToRequestParam(QueryHelper::TYPE_HASHTAG, $request->all());

        $result = ApiHelper::make()->get('/api/v2/hashtag/list', [
            'query' => $query,
        ]);

        if (data_get($result, 'code') !== 0) {
            throw new ErrorException($result['message'], $result['code']);
        }

        $hashtags = QueryHelper::convertApiDataToPaginate(
            items: $result['data']['list'],
            paginate: $result['data']['paginate'],
        );

        // ajax
        if ($request->ajax()) {
            $html = '';
            foreach ($result['data']['list'] as $hashtag) {
                $html .= View::make('components.hashtag.list', compact('hashtag'))->render();
            }

            return response()->json([
                'paginate' => $result['data']['paginate'],
                'html' => $html,
            ]);
        }

        // view
        return view('hashtags.index', compact('hashtags'));
    }

    // list
    public function list(Request $request)
    {
        if (! fs_db_config('menu_hashtag_list_status')) {
            return Response::view('404', [], 404);
        }

        $query = QueryHelper::convertOptionToRequestParam(QueryHelper::TYPE_HASHTAG_LIST, $request->all());

        $result = ApiHelper::make()->get('/api/v2/hashtag/list', [
            'query' => $query,
        ]);

        if (data_get($result, 'code') !== 0) {
            throw new ErrorException($result['message'], $result['code']);
        }

        $hashtags = QueryHelper::convertApiDataToPaginate(
            items: $result['data']['list'],
            paginate: $result['data']['paginate'],
        );

        // ajax
        if ($request->ajax()) {
            $html = '';
            foreach ($result['data']['list'] as $hashtag) {
                $html .= View::make('components.hashtag.list', compact('hashtag'))->render();
            }

            return response()->json([
                'paginate' => $result['data']['paginate'],
                'html' => $html,
            ]);
        }

        // view
        return view('hashtags.list', compact('hashtags'));
    }

    // likes
    public function likes(Request $request)
    {
        $uid = fs_user('detail.uid');

        $result = ApiHelper::make()->get("/api/v2/user/{$uid}/mark/like/hashtags", [
            'query' => $request->all(),
        ]);

        if (data_get($result, 'code') !== 0) {
            throw new ErrorException($result['message'], $result['code']);
        }

        $hashtags = QueryHelper::convertApiDataToPaginate(
            items: $result['data']['list'],
            paginate: $result['data']['paginate'],
        );

        // ajax
        if ($request->ajax()) {
            $html = '';
            foreach ($result['data']['list'] as $hashtag) {
                $html .= View::make('components.hashtag.list', compact('hashtag'))->render();
            }

            return response()->json([
                'paginate' => $result['data']['paginate'],
                'html' => $html,
            ]);
        }

        // view
        return view('hashtags.likes', compact('hashtags'));
    }

    // dislikes
    public function dislikes(Request $request)
    {
        $uid = fs_user('detail.uid');

        $result = ApiHelper::make()->get("/api/v2/user/{$uid}/mark/dislike/hashtags", [
            'query' => $request->all(),
        ]);

        if (data_get($result, 'code') !== 0) {
            throw new ErrorException($result['message'], $result['code']);
        }

        $hashtags = QueryHelper::convertApiDataToPaginate(
            items: $result['data']['list'],
            paginate: $result['data']['paginate'],
        );

        // ajax
        if ($request->ajax()) {
            $html = '';
            foreach ($result['data']['list'] as $hashtag) {
                $html .= View::make('components.hashtag.list', compact('hashtag'))->render();
            }

            return response()->json([
                'paginate' => $result['data']['paginate'],
                'html' => $html,
            ]);
        }

        // view
        return view('hashtags.dislikes', compact('hashtags'));
    }

    // following
    public function following(Request $request)
    {
        $uid = fs_user('detail.uid');

        $result = ApiHelper::make()->get("/api/v2/user/{$uid}/mark/follow/hashtags", [
            'query' => $request->all(),
        ]);

        if (data_get($result, 'code') !== 0) {
            throw new ErrorException($result['message'], $result['code']);
        }

        $hashtags = QueryHelper::convertApiDataToPaginate(
            items: $result['data']['list'],
            paginate: $result['data']['paginate'],
        );

        // ajax
        if ($request->ajax()) {
            $html = '';
            foreach ($result['data']['list'] as $hashtag) {
                $html .= View::make('components.hashtag.list', compact('hashtag'))->render();
            }

            return response()->json([
                'paginate' => $result['data']['paginate'],
                'html' => $html,
            ]);
        }

        // view
        return view('hashtags.following', compact('hashtags'));
    }

    // blocking
    public function blocking(Request $request)
    {
        $uid = fs_user('detail.uid');

        $result = ApiHelper::make()->get("/api/v2/user/{$uid}/mark/block/hashtags", [
            'query' => $request->all(),
        ]);

        if (data_get($result, 'code') !== 0) {
            throw new ErrorException($result['message'], $result['code']);
        }

        $hashtags = QueryHelper::convertApiDataToPaginate(
            items: $result['data']['list'],
            paginate: $result['data']['paginate'],
        );

        // ajax
        if ($request->ajax()) {
            $html = '';
            foreach ($result['data']['list'] as $hashtag) {
                $html .= View::make('components.hashtag.list', compact('hashtag'))->render();
            }

            return response()->json([
                'paginate' => $result['data']['paginate'],
                'html' => $html,
            ]);
        }

        // view
        return view('hashtags.blocking', compact('hashtags'));
    }

    // detail
    public function detail(Request $request, string $hid, ?string $type = null)
    {
        $query = $request->all();
        $query['hid'] = $hid;

        $client = ApiHelper::make();

        $type = match ($type) {
            'posts' => 'posts',
            'comments' => 'comments',
            default => 'posts',
        };

        switch ($type) {
            // posts
            case 'posts':
                $results = $client->unwrapRequests([
                    'hashtag' => $client->getAsync("/api/v2/hashtag/{$hid}/detail"),
                    'posts' => $client->getAsync('/api/v2/post/list', [
                        'query' => $query,
                    ]),
                ]);

                $posts = QueryHelper::convertApiDataToPaginate(
                    items: $results['posts']['data']['list'],
                    paginate: $results['posts']['data']['paginate'],
                );
                $paginate = $results['posts']['data']['paginate'];

                $comments = [];
                break;

                // comments
            case 'comments':
                $results = $client->unwrapRequests([
                    'hashtag' => $client->getAsync("/api/v2/hashtag/{$hid}/detail"),
                    'comments' => $client->getAsync('/api/v2/comment/list', [
                        'query' => $query,
                    ]),
                ]);

                $comments = QueryHelper::convertApiDataToPaginate(
                    items: $results['comments']['data']['list'],
                    paginate: $results['comments']['data']['paginate'],
                );
                $paginate = $results['comments']['data']['paginate'];

                $posts = [];
                break;
        }

        if ($results['hashtag']['code'] != 0) {
            throw new ErrorException($results['hashtag']['message'], $results['hashtag']['code']);
        }

        $items = $results['hashtag']['data']['items'];
        $hashtag = $results['hashtag']['data']['detail'];

        // ajax
        if ($request->ajax()) {
            $html = '';

            switch ($type) {
                // posts
                case 'posts':
                    foreach ($results['posts']['data']['list'] as $post) {
                        $html .= View::make('components.post.list', compact('post'))->render();
                    }
                    break;

                    // comments
                case 'comments':
                    foreach ($results['comments']['data']['list'] as $comment) {
                        $html .= View::make('components.comment.list', compact('comment'))->render();
                    }
                    break;
            }

            return response()->json([
                'paginate' => $paginate,
                'html' => $html,
            ]);
        }

        // view
        return view('hashtags.detail', compact('items', 'hashtag', 'type', 'posts', 'comments'));
    }
}
