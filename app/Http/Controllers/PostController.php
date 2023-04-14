<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jevan Tang
 * Released under the Apache-2.0 License.
 */

namespace Plugins\FresnsEngine\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Str;
use Plugins\FresnsEngine\Exceptions\ErrorException;
use Plugins\FresnsEngine\Helpers\ApiHelper;
use Plugins\FresnsEngine\Helpers\QueryHelper;

class PostController extends Controller
{
    // index
    public function index(Request $request)
    {
        $query = QueryHelper::convertOptionToRequestParam(QueryHelper::TYPE_POST, $request->all());

        if (! fs_db_config('website_status')) {
            $query['pageSize'] = fs_db_config('website_number');
            $query['page'] = 1;
        }

        $result = ApiHelper::make()->get('/api/v2/post/list', [
            'query' => $query,
        ]);

        if (data_get($result, 'code') !== 0) {
            throw new ErrorException($result['message'], $result['code']);
        }

        $posts = QueryHelper::convertApiDataToPaginate(
            items: $result['data']['list'],
            paginate: $result['data']['paginate'],
        );

        // ajax
        if ($request->ajax()) {
            $html = '';
            foreach ($result['data']['list'] as $post) {
                $html .= View::make('components.post.list', compact('post'))->render();
            }

            return response()->json([
                'paginate' => $result['data']['paginate'],
                'html' => $html,
            ]);
        }

        // view
        return view('posts.index', compact('posts'));
    }

    // list
    public function list(Request $request)
    {
        $query = QueryHelper::convertOptionToRequestParam(QueryHelper::TYPE_POST_LIST, $request->all());

        if (! fs_db_config('website_status')) {
            $query['pageSize'] = fs_db_config('website_number');
            $query['page'] = 1;
        }

        $result = ApiHelper::make()->get('/api/v2/post/list', [
            'query' => $query,
        ]);

        if (data_get($result, 'code') !== 0) {
            throw new ErrorException($result['message'], $result['code']);
        }

        $posts = QueryHelper::convertApiDataToPaginate(
            items: $result['data']['list'],
            paginate: $result['data']['paginate'],
        );

        // ajax
        if ($request->ajax()) {
            $html = '';
            foreach ($result['data']['list'] as $post) {
                $html .= View::make('components.post.list', compact('post'))->render();
            }

            return response()->json([
                'paginate' => $result['data']['paginate'],
                'html' => $html,
            ]);
        }

        // view
        return view('posts.list', compact('posts'));
    }

    // nearby
    public function nearby(Request $request)
    {
        $query = $request->all();
        $query['mapId'] = $request->mapId ?? 1;
        $query['mapLng'] = $request->mapLng ?? null;
        $query['mapLat'] = $request->mapLat ?? null;
        $query['unit'] = $request->unit ?? null;
        $query['length'] = $request->length ?? null;

        if (! fs_db_config('website_status')) {
            $query['pageSize'] = fs_db_config('website_number');
            $query['page'] = 1;
        }

        if (empty($request->mapLng) || empty($request->mapLat)) {
            $result = [
                'data' => [
                    'paginate' => [
                        'total' => 0,
                        'pageSize' => 15,
                        'currentPage' => 1,
                        'lastPage' => 1,
                    ],
                    'list' => [],
                ],
            ];
        } else {
            $result = ApiHelper::make()->get('/api/v2/post/nearby', [
                'query' => $query,
            ]);
        }

        $posts = QueryHelper::convertApiDataToPaginate(
            items: $result['data']['list'],
            paginate: $result['data']['paginate'],
        );

        // ajax
        if ($request->ajax()) {
            $html = '';
            foreach ($result['data']['list'] as $post) {
                $html .= View::make('components.post.list', compact('post'))->render();
            }

            return response()->json([
                'paginate' => $result['data']['paginate'],
                'html' => $html,
            ]);
        }

        // view
        return view('posts.nearby', compact('posts'));
    }

    // location
    public function location(Request $request, string $encode)
    {
        $locationData = urldecode(base64_decode($encode));
        $location = json_decode($locationData, true) ?? [];

        $langTag = current_lang_tag();

        $query = $request->all();
        $query['mapId'] = $location['mapId'] ?? null;
        $query['mapLng'] = $location['longitude'] ?? null;
        $query['mapLat'] = $location['latitude'] ?? null;
        $query['unit'] = 'km';
        $query['length'] = 1;

        if (! fs_db_config('website_status')) {
            $query['pageSize'] = fs_db_config('website_number');
            $query['page'] = 1;
        }

        $result = ApiHelper::make()->get('/api/v2/post/nearby', [
            'query' => $query,
        ]);

        $posts = QueryHelper::convertApiDataToPaginate(
            items: $result['data']['list'],
            paginate: $result['data']['paginate'],
        );

        // ajax
        if ($request->ajax()) {
            $html = '';
            foreach ($result['data']['list'] as $post) {
                $html .= View::make('components.post.list', compact('post'))->render();
            }

            return response()->json([
                'paginate' => $result['data']['paginate'],
                'html' => $html,
            ]);
        }

        // view
        return view('posts.location', compact('location', 'encode', 'posts'));
    }

    // likes
    public function likes(Request $request)
    {
        $uid = fs_user('detail.uid');

        $result = ApiHelper::make()->get("/api/v2/user/{$uid}/mark/like/posts", [
            'query' => $request->all(),
        ]);

        $posts = QueryHelper::convertApiDataToPaginate(
            items: $result['data']['list'],
            paginate: $result['data']['paginate'],
        );

        // ajax
        if ($request->ajax()) {
            $html = '';
            foreach ($result['data']['list'] as $post) {
                $html .= View::make('components.post.list', compact('post'))->render();
            }

            return response()->json([
                'paginate' => $result['data']['paginate'],
                'html' => $html,
            ]);
        }

        // view
        return view('posts.likes', compact('posts'));
    }

    // dislikes
    public function dislikes(Request $request)
    {
        $uid = fs_user('detail.uid');

        $result = ApiHelper::make()->get("/api/v2/user/{$uid}/mark/dislike/posts", [
            'query' => $request->all(),
        ]);

        $posts = QueryHelper::convertApiDataToPaginate(
            items: $result['data']['list'],
            paginate: $result['data']['paginate'],
        );

        // ajax
        if ($request->ajax()) {
            $html = '';
            foreach ($result['data']['list'] as $post) {
                $html .= View::make('components.post.list', compact('post'))->render();
            }

            return response()->json([
                'paginate' => $result['data']['paginate'],
                'html' => $html,
            ]);
        }

        // view
        return view('posts.dislikes', compact('posts'));
    }

    // following
    public function following(Request $request)
    {
        $uid = fs_user('detail.uid');

        $result = ApiHelper::make()->get("/api/v2/user/{$uid}/mark/follow/posts", [
            'query' => $request->all(),
        ]);

        $posts = QueryHelper::convertApiDataToPaginate(
            items: $result['data']['list'],
            paginate: $result['data']['paginate'],
        );

        // ajax
        if ($request->ajax()) {
            $html = '';
            foreach ($result['data']['list'] as $post) {
                $html .= View::make('components.post.list', compact('post'))->render();
            }

            return response()->json([
                'paginate' => $result['data']['paginate'],
                'html' => $html,
            ]);
        }

        // view
        return view('posts.following', compact('posts'));
    }

    // blocking
    public function blocking(Request $request)
    {
        $uid = fs_user('detail.uid');

        $result = ApiHelper::make()->get("/api/v2/user/{$uid}/mark/block/posts", [
            'query' => $request->all(),
        ]);

        $posts = QueryHelper::convertApiDataToPaginate(
            items: $result['data']['list'],
            paginate: $result['data']['paginate'],
        );

        // ajax
        if ($request->ajax()) {
            $html = '';
            foreach ($result['data']['list'] as $post) {
                $html .= View::make('components.post.list', compact('post'))->render();
            }

            return response()->json([
                'paginate' => $result['data']['paginate'],
                'html' => $html,
            ]);
        }

        // view
        return view('posts.blocking', compact('posts'));
    }

    // detail
    public function detail(Request $request, string $pid)
    {
        $query = $request->all();
        $query['pid'] = $pid;
        $query['orderDirection'] = $query['orderDirection'] ?? 'asc';

        if (! fs_db_config('website_status')) {
            $query['pageSize'] = fs_db_config('website_number');
            $query['page'] = 1;
        }

        $client = ApiHelper::make();

        $results = $client->unwrapRequests([
            'post' => $client->getAsync("/api/v2/post/{$pid}/detail"),
            'comments' => $client->getAsync('/api/v2/comment/list', [
                'query' => $query,
            ]),
            'stickies' => $client->getAsync('/api/v2/comment/list', [
                'query' => [
                    'pid' => $pid,
                    'sticky' => true,
                ],
            ]),
        ]);

        if ($results['post']['code'] != 0) {
            throw new ErrorException($results['post']['message'], $results['post']['code']);
        }

        $items = $results['post']['data']['items'];
        $post = $results['post']['data']['detail'];

        if (! fs_db_config('website_status')) {
            $websiteProportion = intval(fs_db_config('website_proportion')) / 100;
            $websiteContentLength = intval($post['contentLength'] * $websiteProportion);
            $post['content'] = Str::limit($post['content'], $websiteContentLength);
        }

        $comments = QueryHelper::convertApiDataToPaginate(
            items: $results['comments']['data']['list'],
            paginate: $results['comments']['data']['paginate'],
        );

        $stickies = data_get($results, 'stickies.data.list', []) ?? [];

        // ajax
        if ($request->ajax()) {
            $html = '';
            foreach ($results['comments']['data']['list'] as $post) {
                $html .= View::make('components.comment.list', compact('comment'))->render();
            }

            return response()->json([
                'paginate' => $results['comments']['data']['paginate'],
                'html' => $html,
            ]);
        }

        // view
        return view('posts.detail', compact('items', 'post', 'comments', 'stickies'));
    }
}
