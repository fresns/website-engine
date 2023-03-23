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

class CommentController extends Controller
{
    // index
    public function index(Request $request)
    {
        $query = QueryHelper::convertOptionToRequestParam(QueryHelper::TYPE_COMMENT, $request->all());

        if (! fs_db_config('website_status')) {
            $query['pageSize'] = fs_db_config('website_number');
            $query['page'] = 1;
        }

        $result = ApiHelper::make()->get('/api/v2/comment/list', [
            'query' => $query,
        ]);

        if (data_get($result, 'code') !== 0) {
            throw new ErrorException($result['message'], $result['code']);
        }

        $comments = QueryHelper::convertApiDataToPaginate(
            items: $result['data']['list'],
            paginate: $result['data']['paginate'],
        );

        // ajax
        if ($request->ajax()) {
            $html = '';
            foreach ($result['data']['list'] as $comment) {
                $html .= View::make('components.comment.list', compact('comment'))->render();
            }

            return response()->json([
                'paginate' => $result['data']['paginate'],
                'html' => $html,
            ]);
        }

        // view
        return view('comments.index', compact('comments'));
    }

    // list
    public function list(Request $request)
    {
        $query = QueryHelper::convertOptionToRequestParam(QueryHelper::TYPE_COMMENT_LIST, $request->all());

        if (! fs_db_config('website_status')) {
            $query['pageSize'] = fs_db_config('website_number');
            $query['page'] = 1;
        }

        $result = ApiHelper::make()->get('/api/v2/comment/list', [
            'query' => $query,
        ]);

        $comments = QueryHelper::convertApiDataToPaginate(
            items: $result['data']['list'],
            paginate: $result['data']['paginate'],
        );

        // ajax
        if ($request->ajax()) {
            $html = '';
            foreach ($result['data']['list'] as $comment) {
                $html .= View::make('components.comment.list', compact('comment'))->render();
            }

            return response()->json([
                'paginate' => $result['data']['paginate'],
                'html' => $html,
            ]);
        }

        // view
        return view('comments.list', compact('comments'));
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
            $result = ApiHelper::make()->get('/api/v2/comment/nearby', [
                'query' => $query,
            ]);
        }

        $comments = QueryHelper::convertApiDataToPaginate(
            items: $result['data']['list'],
            paginate: $result['data']['paginate'],
        );

        // ajax
        if ($request->ajax()) {
            $html = '';
            foreach ($result['data']['list'] as $comment) {
                $html .= View::make('components.comment.list', compact('comment'))->render();
            }

            return response()->json([
                'paginate' => $result['data']['paginate'],
                'html' => $html,
            ]);
        }

        // view
        return view('comments.nearby', compact('comments'));
    }

    // location
    public function location(Request $request, string $encoded)
    {
        $locationData = urldecode(base64_decode($encoded));
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

        $result = ApiHelper::make()->get('/api/v2/comment/nearby', [
            'query' => $query,
        ]);

        $comments = QueryHelper::convertApiDataToPaginate(
            items: $result['data']['list'],
            paginate: $result['data']['paginate'],
        );

        // ajax
        if ($request->ajax()) {
            $html = '';
            foreach ($result['data']['list'] as $comment) {
                $html .= View::make('components.comment.list', compact('comment'))->render();
            }

            return response()->json([
                'paginate' => $result['data']['paginate'],
                'html' => $html,
            ]);
        }

        // view
        return view('comments.location', compact('location', 'encoded', 'comments'));
    }

    // likes
    public function likes(Request $request)
    {
        $uid = fs_user('detail.uid');

        $result = ApiHelper::make()->get("/api/v2/user/{$uid}/mark/like/comments", [
            'query' => $request->all(),
        ]);

        $comments = QueryHelper::convertApiDataToPaginate(
            items: $result['data']['list'],
            paginate: $result['data']['paginate'],
        );

        // ajax
        if ($request->ajax()) {
            $html = '';
            foreach ($result['data']['list'] as $comment) {
                $html .= View::make('components.comment.list', compact('comment'))->render();
            }

            return response()->json([
                'paginate' => $result['data']['paginate'],
                'html' => $html,
            ]);
        }

        // view
        return view('comments.likes', compact('comments'));
    }

    // dislikes
    public function dislikes(Request $request)
    {
        $uid = fs_user('detail.uid');

        $result = ApiHelper::make()->get("/api/v2/user/{$uid}/mark/dislike/comments", [
            'query' => $request->all(),
        ]);

        $comments = QueryHelper::convertApiDataToPaginate(
            items: $result['data']['list'],
            paginate: $result['data']['paginate'],
        );

        // ajax
        if ($request->ajax()) {
            $html = '';
            foreach ($result['data']['list'] as $comment) {
                $html .= View::make('components.comment.list', compact('comment'))->render();
            }

            return response()->json([
                'paginate' => $result['data']['paginate'],
                'html' => $html,
            ]);
        }

        // view
        return view('comments.dislikes', compact('comments'));
    }

    // following
    public function following(Request $request)
    {
        $uid = fs_user('detail.uid');

        $result = ApiHelper::make()->get("/api/v2/user/{$uid}/mark/follow/comments", [
            'query' => $request->all(),
        ]);

        $comments = QueryHelper::convertApiDataToPaginate(
            items: $result['data']['list'],
            paginate: $result['data']['paginate'],
        );

        // ajax
        if ($request->ajax()) {
            $html = '';
            foreach ($result['data']['list'] as $comment) {
                $html .= View::make('components.comment.list', compact('comment'))->render();
            }

            return response()->json([
                'paginate' => $result['data']['paginate'],
                'html' => $html,
            ]);
        }

        // view
        return view('comments.following', compact('comments'));
    }

    // blocking
    public function blocking(Request $request)
    {
        $uid = fs_user('detail.uid');

        $result = ApiHelper::make()->get("/api/v2/user/{$uid}/mark/block/comments", [
            'query' => $request->all(),
        ]);

        $comments = QueryHelper::convertApiDataToPaginate(
            items: $result['data']['list'],
            paginate: $result['data']['paginate'],
        );

        // ajax
        if ($request->ajax()) {
            $html = '';
            foreach ($result['data']['list'] as $comment) {
                $html .= View::make('components.comment.list', compact('comment'))->render();
            }

            return response()->json([
                'paginate' => $result['data']['paginate'],
                'html' => $html,
            ]);
        }

        // view
        return view('comments.blocking', compact('comments'));
    }

    // detail
    public function detail(Request $request, string $cid)
    {
        $query = $request->all();
        $query['cid'] = $cid;
        $query['orderDirection'] = $query['orderDirection'] ?? 'asc';

        if (! fs_db_config('website_status')) {
            $query['pageSize'] = fs_db_config('website_number');
            $query['page'] = 1;
        }

        $client = ApiHelper::make();

        $results = $client->unwrapRequests([
            'comment' => $client->getAsync("/api/v2/comment/{$cid}/detail"),
            'comments' => $client->getAsync('/api/v2/comment/list', [
                'query' => $query,
            ]),
        ]);

        if ($results['comment']['code'] != 0) {
            throw new ErrorException($results['comment']['message'], $results['comment']['code']);
        }

        $items = $results['comment']['data']['items'];
        $comment = $results['comment']['data']['detail'];

        if (! fs_db_config('website_status')) {
            $websiteProportion = intval(fs_db_config('website_proportion')) / 100;
            $websiteContentLength = intval($comment['contentLength'] * $websiteProportion);
            $comment['content'] = Str::limit($comment['content'], $websiteContentLength);
        }

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
        return view('comments.detail', compact('items', 'comment', 'comments'));
    }
}
