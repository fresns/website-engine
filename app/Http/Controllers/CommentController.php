<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jevan Tang
 * Released under the Apache-2.0 License.
 */

namespace Fresns\WebsiteEngine\Http\Controllers;

use Fresns\WebsiteEngine\Helpers\QueryHelper;
use Fresns\WebsiteEngine\Interfaces\CommentInterface;
use Fresns\WebsiteEngine\Interfaces\UserInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\View;

class CommentController extends Controller
{
    // index
    public function index(Request $request)
    {
        if (! fs_config('channel_comment_status')) {
            return Response::view('404', [], 404);
        }

        $query = QueryHelper::convertOptionToRequestParam(QueryHelper::TYPE_COMMENT, $request->all());

        $result = CommentInterface::list($query);

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

        return view('comments.index', compact('comments'));
    }

    // list
    public function list(Request $request)
    {
        if (! fs_config('channel_comment_list_status')) {
            return Response::view('404', [], 404);
        }

        $query = QueryHelper::convertOptionToRequestParam(QueryHelper::TYPE_COMMENT_LIST, $request->all());

        $result = CommentInterface::list($query);

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

        return view('comments.list', compact('comments'));
    }

    // likes
    public function likes(Request $request)
    {
        $uid = (int) fs_user('detail.uid');

        $result = UserInterface::markList($uid, 'like', 'comments', $request->all());

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

        return view('comments.likes', compact('comments'));
    }

    // dislikes
    public function dislikes(Request $request)
    {
        $uid = (int) fs_user('detail.uid');

        $result = UserInterface::markList($uid, 'dislike', 'comments', $request->all());

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

        return view('comments.dislikes', compact('comments'));
    }

    // following
    public function following(Request $request)
    {
        $uid = (int) fs_user('detail.uid');

        $result = UserInterface::markList($uid, 'follow', 'comments', $request->all());

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

        return view('comments.following', compact('comments'));
    }

    // blocking
    public function blocking(Request $request)
    {
        $uid = (int) fs_user('detail.uid');

        $result = UserInterface::markList($uid, 'block', 'comments', $request->all());

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

        return view('comments.blocking', compact('comments'));
    }

    // detail
    public function detail(Request $request, string $cid)
    {
        $query = $request->all();
        $query['cid'] = $cid;
        $query['orderDirection'] = $query['orderDirection'] ?? 'asc';

        $results = CommentInterface::detail($cid, $query);

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
        $items = $results['comment']['data']['items'];
        $comment = $results['comment']['data']['detail'];

        $comments = QueryHelper::convertApiDataToPaginate(
            items: $results['comments']['data']['list'],
            pagination: $results['comments']['data']['pagination'],
        );

        return view('comments.detail', compact('items', 'comment', 'comments'));
    }

    // detail likers
    public function detailLikers(Request $request, string $cid)
    {
        $results = CommentInterface::interaction($cid, 'likers', $request->all());

        if (! $results['comment']['data']['detail']['interaction']['likePublicRecord']) {
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
        $items = $results['comment']['data']['items'];
        $comment = $results['comment']['data']['detail'];

        $users = QueryHelper::convertApiDataToPaginate(
            items: $results['users']['data']['list'],
            pagination: $results['users']['data']['pagination'],
        );

        return view('comments.detail-likers', compact('items', 'comment', 'users'));
    }

    // detail dislikers
    public function detailDislikers(Request $request, string $cid)
    {
        $results = CommentInterface::interaction($cid, 'dislikers', $request->all());

        if (! $results['comment']['data']['detail']['interaction']['likePublicRecord']) {
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
        $items = $results['comment']['data']['items'];
        $comment = $results['comment']['data']['detail'];

        $users = QueryHelper::convertApiDataToPaginate(
            items: $results['users']['data']['list'],
            pagination: $results['users']['data']['pagination'],
        );

        return view('comments.detail-dislikers', compact('items', 'comment', 'users'));
    }

    // detail followers
    public function detailFollowers(Request $request, string $cid)
    {
        $results = CommentInterface::interaction($cid, 'followers', $request->all());

        if (! $results['comment']['data']['detail']['interaction']['likePublicRecord']) {
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
        $items = $results['comment']['data']['items'];
        $comment = $results['comment']['data']['detail'];

        $users = QueryHelper::convertApiDataToPaginate(
            items: $results['users']['data']['list'],
            pagination: $results['users']['data']['pagination'],
        );

        return view('comments.detail-followers', compact('items', 'comment', 'users'));
    }

    // detail blockers
    public function detailBlockers(Request $request, string $cid)
    {
        $results = CommentInterface::interaction($cid, 'blockers', $request->all());

        if (! $results['comment']['data']['detail']['interaction']['likePublicRecord']) {
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
        $items = $results['comment']['data']['items'];
        $comment = $results['comment']['data']['detail'];

        $users = QueryHelper::convertApiDataToPaginate(
            items: $results['users']['data']['list'],
            pagination: $results['users']['data']['pagination'],
        );

        return view('comments.detail-blockers', compact('items', 'comment', 'users'));
    }
}
