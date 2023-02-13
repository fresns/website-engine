<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jarvis Tang
 * Released under the Apache-2.0 License.
 */

namespace Plugins\FresnsEngine\Http\Controllers;

use App\Helpers\CacheHelper;
use App\Models\File;
use Illuminate\Http\Request;
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

        return view('posts.nearby', compact('posts'));
    }

    // location
    public function location(Request $request, string $pid, ?string $type = null)
    {
        $langTag = current_lang_tag();

        $cacheKey = "fresns_web_post_{$pid}";
        $cacheTags = ['fresnsWeb', 'fresnsWebPostData'];

        $post = CacheHelper::get($cacheKey, $cacheTags);

        if (empty($post)) {
            $post = ApiHelper::make()->get("/api/v2/post/{$pid}/detail");

            $cacheTime = CacheHelper::fresnsCacheTimeByFileType(File::TYPE_ALL);
            CacheHelper::put($post, $cacheKey, $cacheTags, null, $cacheTime);
        }

        if ($post['code'] != 0) {
            CacheHelper::forgetFresnsKey($cacheKey);

            throw new ErrorException($post['message'], $post['code']);
        }

        $archive = $post['data']['detail'];

        $isLbs = $archive['location']['isLbs'] ?? false;
        $mapId = $archive['location']['mapId'] ?? 1;
        $latitude = $archive['location']['latitude'] ?? null;
        $longitude = $archive['location']['longitude'] ?? null;

        if (! $isLbs || empty($latitude) || empty($longitude)) {
            return back()->with([
                'failure' => fs_lang('location').': '.fs_lang('errorEmpty'),
            ]);
        }

        $type = match ($type) {
            'posts' => 'posts',
            'comments' => 'comments',
            default => 'posts',
        };

        $query = $request->all();
        $query['mapId'] = $mapId;
        $query['mapLng'] = $longitude;
        $query['mapLat'] = $latitude;
        $query['unit'] = $post['detail']['location']['unit'] ?? null;

        if (! fs_db_config('website_status')) {
            $query['pageSize'] = fs_db_config('website_number');
            $query['page'] = 1;
        }

        if ($type == 'posts') {
            $result = ApiHelper::make()->get('/api/v2/post/nearby', [
                'query' => $query,
            ]);

            $posts = QueryHelper::convertApiDataToPaginate(
                items: $result['data']['list'],
                paginate: $result['data']['paginate'],
            );

            $comments = [];
        } else {
            $result = ApiHelper::make()->get('/api/v2/comment/nearby', [
                'query' => $query,
            ]);

            $comments = QueryHelper::convertApiDataToPaginate(
                items: $result['data']['list'],
                paginate: $result['data']['paginate'],
            );

            $posts = [];
        }

        return view('posts.location', compact('archive', 'type', 'posts', 'comments'));
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

        return view('posts.detail', compact('items', 'post', 'comments', 'stickies'));
    }
}
