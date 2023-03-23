<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jevan Tang
 * Released under the Apache-2.0 License.
 */

namespace Plugins\FresnsEngine\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;
use Plugins\FresnsEngine\Exceptions\ErrorException;
use Plugins\FresnsEngine\Helpers\ApiHelper;
use Plugins\FresnsEngine\Helpers\QueryHelper;

class FollowController extends Controller
{
    // all posts
    public function allPosts(Request $request)
    {
        $query = $request->all();

        $result = ApiHelper::make()->get('/api/v2/post/follow/all', [
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
        return view('follows.all-posts', compact('posts'));
    }

    // user posts
    public function userPosts(Request $request)
    {
        $query = $request->all();

        $result = ApiHelper::make()->get('/api/v2/post/follow/user', [
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
        return view('follows.user-posts', compact('posts'));
    }

    // group posts
    public function groupPosts(Request $request)
    {
        $query = $request->all();

        $result = ApiHelper::make()->get('/api/v2/post/follow/group', [
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
        return view('follows.group-posts', compact('posts'));
    }

    // hashtag posts
    public function hashtagPosts(Request $request)
    {
        $query = $request->all();

        $result = ApiHelper::make()->get('/api/v2/post/follow/hashtag', [
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
        return view('follows.hashtag-posts', compact('posts'));
    }

    // all comments
    public function allComments(Request $request)
    {
        $query = $request->all();

        $result = ApiHelper::make()->get('/api/v2/comment/follow/all', [
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
        return view('follows.all-comments', compact('comments'));
    }

    // user comments
    public function userComments(Request $request)
    {
        $query = $request->all();

        $result = ApiHelper::make()->get('/api/v2/comment/follow/user', [
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
        return view('follows.user-comments', compact('comments'));
    }

    // group comments
    public function groupComments(Request $request)
    {
        $query = $request->all();

        $result = ApiHelper::make()->get('/api/v2/comment/follow/group', [
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
        return view('follows.group-comments', compact('comments'));
    }

    // hashtag comments
    public function hashtagComments(Request $request)
    {
        $query = $request->all();

        $result = ApiHelper::make()->get('/api/v2/comment/follow/hashtag', [
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
        return view('follows.hashtag-comments', compact('comments'));
    }
}
