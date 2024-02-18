<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jevan Tang
 * Released under the Apache-2.0 License.
 */

namespace Fresns\WebEngine\Http\Controllers;

use Fresns\WebEngine\Exceptions\ErrorException;
use Fresns\WebEngine\Helpers\QueryHelper;
use Fresns\WebEngine\Interfaces\PostInterface;
use Fresns\WebEngine\Interfaces\UserInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Str;

class PostController extends Controller
{
    // index
    public function index(Request $request)
    {
        $query = QueryHelper::convertOptionToRequestParam(QueryHelper::TYPE_POST, $request->all());

        $result = PostInterface::list($query);

        if (data_get($result, 'code') !== 0) {
            throw new ErrorException($result['message'], $result['code']);
        }

        $posts = QueryHelper::convertApiDataToPaginate(
            items: $result['data']['list'],
            pagination: $result['data']['pagination'],
        );

        // ajax
        if ($request->ajax()) {
            $html = '';
            foreach ($result['data']['list'] as $post) {
                $html .= View::make('components.post.list', compact('post'))->render();
            }

            return response()->json([
                'pagination' => $result['data']['pagination'],
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

        $result = PostInterface::list($query);

        if (data_get($result, 'code') !== 0) {
            throw new ErrorException($result['message'], $result['code']);
        }

        $posts = QueryHelper::convertApiDataToPaginate(
            items: $result['data']['list'],
            pagination: $result['data']['pagination'],
        );

        // ajax
        if ($request->ajax()) {
            $html = '';
            foreach ($result['data']['list'] as $post) {
                $html .= View::make('components.post.list', compact('post'))->render();
            }

            return response()->json([
                'pagination' => $result['data']['pagination'],
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

        if (empty($request->mapLng) || empty($request->mapLat)) {
            $result = [
                'data' => [
                    'pagination' => [
                        'total' => 0,
                        'pageSize' => 15,
                        'currentPage' => 1,
                        'lastPage' => 1,
                    ],
                    'list' => [],
                ],
            ];
        } else {
            $result = PostInterface::nearby($query);
        }

        $posts = QueryHelper::convertApiDataToPaginate(
            items: $result['data']['list'],
            pagination: $result['data']['pagination'],
        );

        // ajax
        if ($request->ajax()) {
            $html = '';
            foreach ($result['data']['list'] as $post) {
                $html .= View::make('components.post.list', compact('post'))->render();
            }

            return response()->json([
                'pagination' => $result['data']['pagination'],
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

        $result = PostInterface::nearby($query);

        $posts = QueryHelper::convertApiDataToPaginate(
            items: $result['data']['list'],
            pagination: $result['data']['pagination'],
        );

        // ajax
        if ($request->ajax()) {
            $html = '';
            foreach ($result['data']['list'] as $post) {
                $html .= View::make('components.post.list', compact('post'))->render();
            }

            return response()->json([
                'pagination' => $result['data']['pagination'],
                'html' => $html,
            ]);
        }

        // view
        return view('posts.location', compact('location', 'encode', 'posts'));
    }

    // likes
    public function likes(Request $request)
    {
        $uid = (int) fs_user('detail.uid');

        $result = UserInterface::markList($uid, 'like', 'posts', $request->all());

        $posts = QueryHelper::convertApiDataToPaginate(
            items: $result['data']['list'],
            pagination: $result['data']['pagination'],
        );

        // ajax
        if ($request->ajax()) {
            $html = '';
            foreach ($result['data']['list'] as $post) {
                $html .= View::make('components.post.list', compact('post'))->render();
            }

            return response()->json([
                'pagination' => $result['data']['pagination'],
                'html' => $html,
            ]);
        }

        // view
        return view('posts.likes', compact('posts'));
    }

    // dislikes
    public function dislikes(Request $request)
    {
        $uid = (int) fs_user('detail.uid');

        $result = UserInterface::markList($uid, 'dislike', 'posts', $request->all());

        $posts = QueryHelper::convertApiDataToPaginate(
            items: $result['data']['list'],
            pagination: $result['data']['pagination'],
        );

        // ajax
        if ($request->ajax()) {
            $html = '';
            foreach ($result['data']['list'] as $post) {
                $html .= View::make('components.post.list', compact('post'))->render();
            }

            return response()->json([
                'pagination' => $result['data']['pagination'],
                'html' => $html,
            ]);
        }

        // view
        return view('posts.dislikes', compact('posts'));
    }

    // following
    public function following(Request $request)
    {
        $uid = (int) fs_user('detail.uid');

        $result = UserInterface::markList($uid, 'follow', 'posts', $request->all());

        $posts = QueryHelper::convertApiDataToPaginate(
            items: $result['data']['list'],
            pagination: $result['data']['pagination'],
        );

        // ajax
        if ($request->ajax()) {
            $html = '';
            foreach ($result['data']['list'] as $post) {
                $html .= View::make('components.post.list', compact('post'))->render();
            }

            return response()->json([
                'pagination' => $result['data']['pagination'],
                'html' => $html,
            ]);
        }

        // view
        return view('posts.following', compact('posts'));
    }

    // blocking
    public function blocking(Request $request)
    {
        $uid = (int) fs_user('detail.uid');

        $result = UserInterface::markList($uid, 'block', 'posts', $request->all());

        $posts = QueryHelper::convertApiDataToPaginate(
            items: $result['data']['list'],
            pagination: $result['data']['pagination'],
        );

        // ajax
        if ($request->ajax()) {
            $html = '';
            foreach ($result['data']['list'] as $post) {
                $html .= View::make('components.post.list', compact('post'))->render();
            }

            return response()->json([
                'pagination' => $result['data']['pagination'],
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

        $results = PostInterface::detail($pid, $query);

        if ($results['post']['code'] != 0) {
            throw new ErrorException($results['post']['message'], $results['post']['code']);
        }

        $items = $results['post']['data']['items'];
        $post = $results['post']['data']['detail'];

        $comments = QueryHelper::convertApiDataToPaginate(
            items: $results['comments']['data']['list'],
            pagination: $results['comments']['data']['pagination'],
        );

        $stickies = data_get($results, 'stickies.data.list', []) ?? [];

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
        return view('posts.detail', compact('items', 'post', 'comments', 'stickies'));
    }
}
