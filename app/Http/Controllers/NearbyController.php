<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jevan Tang
 * Released under the Apache-2.0 License.
 */

namespace Fresns\WebsiteEngine\Http\Controllers;

use Fresns\WebsiteEngine\Helpers\QueryHelper;
use Fresns\WebsiteEngine\Interfaces\CommentInterface;
use Fresns\WebsiteEngine\Interfaces\PostInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\View;

class NearbyController extends Controller
{
    // index
    public function index()
    {
        $channelType = fs_config('channel_nearby_type');

        $redirectURL = match ($channelType) {
            'posts' => fs_route(route('fresns.nearby.posts')),
            'comments' => fs_route(route('fresns.nearby.comments')),
        };

        return redirect()->intended($redirectURL);
    }

    // posts
    public function posts(Request $request)
    {
        if (! fs_config('channel_nearby_posts_status')) {
            return Response::view('404', [], 404);
        }

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
        $posts = QueryHelper::convertApiDataToPaginate(
            items: $result['data']['list'],
            pagination: $result['data']['pagination'],
        );

        return view('nearby.posts', compact('posts'));
    }

    // comments
    public function comments(Request $request)
    {
        if (! fs_config('channel_nearby_comments_status')) {
            return Response::view('404', [], 404);
        }

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
        $comments = QueryHelper::convertApiDataToPaginate(
            items: $result['data']['list'],
            pagination: $result['data']['pagination'],
        );

        return view('nearby.comments', compact('comments'));
    }
}
