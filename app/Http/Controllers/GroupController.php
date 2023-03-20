<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jarvis Tang
 * Released under the Apache-2.0 License.
 */

namespace Plugins\FresnsEngine\Http\Controllers;

use App\Helpers\ConfigHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;
use Plugins\FresnsEngine\Exceptions\ErrorException;
use Plugins\FresnsEngine\Helpers\ApiHelper;
use Plugins\FresnsEngine\Helpers\QueryHelper;

class GroupController extends Controller
{
    // index
    public function index(Request $request)
    {
        $indexType = ConfigHelper::fresnsConfigByItemKey('menu_group_type');

        $groupTree = [];
        $groups = [];

        if ($indexType == 'tree') {
            $result = ApiHelper::make()->get('/api/v2/group/tree');

            if (data_get($result, 'code') !== 0) {
                throw new ErrorException($result['message'], $result['code']);
            }

            $groupTree = $result['data'];
        } else {
            $query = QueryHelper::convertOptionToRequestParam(QueryHelper::TYPE_GROUP, $request->all());

            $result = ApiHelper::make()->get('/api/v2/group/list', [
                'query' => $query,
            ]);

            if (data_get($result, 'code') !== 0) {
                throw new ErrorException($result['message'], $result['code']);
            }

            $groups = QueryHelper::convertApiDataToPaginate(
                items: $result['data']['list'],
                paginate: $result['data']['paginate'],
            );

            // ajax
            if ($request->ajax()) {
                $html = '';
                foreach ($result['data']['list'] as $group) {
                    $html .= View::make('components.group.list', compact('group'))->render();
                }

                return response()->json([
                    'paginate' => $result['data']['paginate'],
                    'html' => $html,
                ]);
            }
        }

        // view
        return view('groups.index', compact('groupTree', 'groups'));
    }

    // list
    public function list(Request $request)
    {
        $query = QueryHelper::convertOptionToRequestParam(QueryHelper::TYPE_GROUP_LIST, $request->all());

        $result = ApiHelper::make()->get('/api/v2/group/list', [
            'query' => $query,
        ]);

        if (data_get($result, 'code') !== 0) {
            throw new ErrorException($result['message'], $result['code']);
        }

        $groups = QueryHelper::convertApiDataToPaginate(
            items: $result['data']['list'],
            paginate: $result['data']['paginate'],
        );

        // ajax
        if ($request->ajax()) {
            $html = '';
            foreach ($result['data']['list'] as $group) {
                $html .= View::make('components.group.list', compact('group'))->render();
            }

            return response()->json([
                'paginate' => $result['data']['paginate'],
                'html' => $html,
            ]);
        }

        // view
        return view('groups.list', compact('groups'));
    }

    // likes
    public function likes(Request $request)
    {
        $uid = fs_user('detail.uid');

        $result = ApiHelper::make()->get("/api/v2/user/{$uid}/mark/like/groups", [
            'query' => $request->all(),
        ]);

        if (data_get($result, 'code') !== 0) {
            throw new ErrorException($result['message'], $result['code']);
        }

        $groups = QueryHelper::convertApiDataToPaginate(
            items: $result['data']['list'],
            paginate: $result['data']['paginate'],
        );

        // ajax
        if ($request->ajax()) {
            $html = '';
            foreach ($result['data']['list'] as $group) {
                $html .= View::make('components.group.list', compact('group'))->render();
            }

            return response()->json([
                'paginate' => $result['data']['paginate'],
                'html' => $html,
            ]);
        }

        // view
        return view('groups.likes', compact('groups'));
    }

    // dislikes
    public function dislikes(Request $request)
    {
        $uid = fs_user('detail.uid');

        $result = ApiHelper::make()->get("/api/v2/user/{$uid}/mark/dislike/groups", [
            'query' => $request->all(),
        ]);

        if (data_get($result, 'code') !== 0) {
            throw new ErrorException($result['message'], $result['code']);
        }

        $groups = QueryHelper::convertApiDataToPaginate(
            items: $result['data']['list'],
            paginate: $result['data']['paginate'],
        );

        // ajax
        if ($request->ajax()) {
            $html = '';
            foreach ($result['data']['list'] as $group) {
                $html .= View::make('components.group.list', compact('group'))->render();
            }

            return response()->json([
                'paginate' => $result['data']['paginate'],
                'html' => $html,
            ]);
        }

        // view
        return view('groups.dislikes', compact('groups'));
    }

    // following
    public function following(Request $request)
    {
        $uid = fs_user('detail.uid');

        $result = ApiHelper::make()->get("/api/v2/user/{$uid}/mark/follow/groups", [
            'query' => $request->all(),
        ]);

        if (data_get($result, 'code') !== 0) {
            throw new ErrorException($result['message'], $result['code']);
        }

        $groups = QueryHelper::convertApiDataToPaginate(
            items: $result['data']['list'],
            paginate: $result['data']['paginate'],
        );

        // ajax
        if ($request->ajax()) {
            $html = '';
            foreach ($result['data']['list'] as $group) {
                $html .= View::make('components.group.list', compact('group'))->render();
            }

            return response()->json([
                'paginate' => $result['data']['paginate'],
                'html' => $html,
            ]);
        }

        // view
        return view('groups.following', compact('groups'));
    }

    // blocking
    public function blocking(Request $request)
    {
        $uid = fs_user('detail.uid');

        $result = ApiHelper::make()->get("/api/v2/user/{$uid}/mark/block/groups", [
            'query' => $request->all(),
        ]);

        if (data_get($result, 'code') !== 0) {
            throw new ErrorException($result['message'], $result['code']);
        }

        $groups = QueryHelper::convertApiDataToPaginate(
            items: $result['data']['list'],
            paginate: $result['data']['paginate'],
        );

        // ajax
        if ($request->ajax()) {
            $html = '';
            foreach ($result['data']['list'] as $group) {
                $html .= View::make('components.group.list', compact('group'))->render();
            }

            return response()->json([
                'paginate' => $result['data']['paginate'],
                'html' => $html,
            ]);
        }

        // view
        return view('groups.blocking', compact('groups'));
    }

    // detail
    public function detail(Request $request, string $gid, ?string $type = null)
    {
        $query = $request->all();
        $query['gid'] = $gid;

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
                    'group' => $client->getAsync("/api/v2/group/{$gid}/detail"),
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
                    'group' => $client->getAsync("/api/v2/group/{$gid}/detail"),
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

        if ($results['group']['code'] != 0) {
            throw new ErrorException($results['group']['message'], $results['group']['code']);
        }

        $items = $results['group']['data']['items'];
        $group = $results['group']['data']['detail'];

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
        return view('groups.detail', compact('items', 'group', 'type', 'posts', 'comments'));
    }
}
