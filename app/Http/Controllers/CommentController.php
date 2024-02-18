<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jevan Tang
 * Released under the Apache-2.0 License.
 */

namespace Fresns\WebEngine\Http\Controllers;

use Fresns\WebEngine\Exceptions\ErrorException;
use Fresns\WebEngine\Helpers\QueryHelper;
use Fresns\WebEngine\Interfaces\CommentInterface;
use Fresns\WebEngine\Interfaces\UserInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Str;

class CommentController extends Controller
{
    // index
    public function index(Request $request)
    {
        $query = QueryHelper::convertOptionToRequestParam(QueryHelper::TYPE_COMMENT, $request->all());

        $result = CommentInterface::list($query);

        if (data_get($result, 'code') !== 0) {
            throw new ErrorException($result['message'], $result['code']);
        }

        $comments = QueryHelper::convertApiDataToPaginate(
            items: $result['data']['list'],
            pagination: $result['data']['pagination'],
        );

        // ajax
        if ($request->ajax()) {
            $html = '';
            foreach ($result['data']['list'] as $comment) {
                $html .= View::make('components.comment.list', compact('comment'))->render();
            }

            return response()->json([
                'pagination' => $result['data']['pagination'],
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

        $result = CommentInterface::list($query);

        $comments = QueryHelper::convertApiDataToPaginate(
            items: $result['data']['list'],
            pagination: $result['data']['pagination'],
        );

        // ajax
        if ($request->ajax()) {
            $html = '';
            foreach ($result['data']['list'] as $comment) {
                $html .= View::make('components.comment.list', compact('comment'))->render();
            }

            return response()->json([
                'pagination' => $result['data']['pagination'],
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
            $result = CommentInterface::nearby($query);
        }

        $comments = QueryHelper::convertApiDataToPaginate(
            items: $result['data']['list'],
            pagination: $result['data']['pagination'],
        );

        // ajax
        if ($request->ajax()) {
            $html = '';
            foreach ($result['data']['list'] as $comment) {
                $html .= View::make('components.comment.list', compact('comment'))->render();
            }

            return response()->json([
                'pagination' => $result['data']['pagination'],
                'html' => $html,
            ]);
        }

        // view
        return view('comments.nearby', compact('comments'));
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

        $result = CommentInterface::nearby($query);

        $comments = QueryHelper::convertApiDataToPaginate(
            items: $result['data']['list'],
            pagination: $result['data']['pagination'],
        );

        // ajax
        if ($request->ajax()) {
            $html = '';
            foreach ($result['data']['list'] as $comment) {
                $html .= View::make('components.comment.list', compact('comment'))->render();
            }

            return response()->json([
                'pagination' => $result['data']['pagination'],
                'html' => $html,
            ]);
        }

        // view
        return view('comments.location', compact('location', 'encode', 'comments'));
    }

    // likes
    public function likes(Request $request)
    {
        $uid = (int) fs_user('detail.uid');

        $result = UserInterface::markList($uid, 'like', 'comments', $request->all());

        $comments = QueryHelper::convertApiDataToPaginate(
            items: $result['data']['list'],
            pagination: $result['data']['pagination'],
        );

        // ajax
        if ($request->ajax()) {
            $html = '';
            foreach ($result['data']['list'] as $comment) {
                $html .= View::make('components.comment.list', compact('comment'))->render();
            }

            return response()->json([
                'pagination' => $result['data']['pagination'],
                'html' => $html,
            ]);
        }

        // view
        return view('comments.likes', compact('comments'));
    }

    // dislikes
    public function dislikes(Request $request)
    {
        $uid = (int) fs_user('detail.uid');

        $result = UserInterface::markList($uid, 'dislike', 'comments', $request->all());

        $comments = QueryHelper::convertApiDataToPaginate(
            items: $result['data']['list'],
            pagination: $result['data']['pagination'],
        );

        // ajax
        if ($request->ajax()) {
            $html = '';
            foreach ($result['data']['list'] as $comment) {
                $html .= View::make('components.comment.list', compact('comment'))->render();
            }

            return response()->json([
                'pagination' => $result['data']['pagination'],
                'html' => $html,
            ]);
        }

        // view
        return view('comments.dislikes', compact('comments'));
    }

    // following
    public function following(Request $request)
    {
        $uid = (int) fs_user('detail.uid');

        $result = UserInterface::markList($uid, 'follow', 'comments', $request->all());

        $comments = QueryHelper::convertApiDataToPaginate(
            items: $result['data']['list'],
            pagination: $result['data']['pagination'],
        );

        // ajax
        if ($request->ajax()) {
            $html = '';
            foreach ($result['data']['list'] as $comment) {
                $html .= View::make('components.comment.list', compact('comment'))->render();
            }

            return response()->json([
                'pagination' => $result['data']['pagination'],
                'html' => $html,
            ]);
        }

        // view
        return view('comments.following', compact('comments'));
    }

    // blocking
    public function blocking(Request $request)
    {
        $uid = (int) fs_user('detail.uid');

        $result = UserInterface::markList($uid, 'block', 'comments', $request->all());

        $comments = QueryHelper::convertApiDataToPaginate(
            items: $result['data']['list'],
            pagination: $result['data']['pagination'],
        );

        // ajax
        if ($request->ajax()) {
            $html = '';
            foreach ($result['data']['list'] as $comment) {
                $html .= View::make('components.comment.list', compact('comment'))->render();
            }

            return response()->json([
                'pagination' => $result['data']['pagination'],
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

        $results = CommentInterface::detail($cid, $query);

        if ($results['comment']['code'] != 0) {
            throw new ErrorException($results['comment']['message'], $results['comment']['code']);
        }

        $items = $results['comment']['data']['items'];
        $comment = $results['comment']['data']['detail'];

        $comments = QueryHelper::convertApiDataToPaginate(
            items: $results['comments']['data']['list'],
            pagination: $results['comments']['data']['pagination'],
        );

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
        return view('comments.detail', compact('items', 'comment', 'comments'));
    }
}
