<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jevan Tang
 * Released under the Apache-2.0 License.
 */

namespace Fresns\WebEngine\Http\Controllers;

use App\Helpers\ConfigHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\View;
use Fresns\WebEngine\Exceptions\ErrorException;
use Fresns\WebEngine\Helpers\QueryHelper;
use Fresns\WebEngine\Interfaces\GroupInterface;
use Fresns\WebEngine\Interfaces\UserInterface;

class GroupController extends Controller
{
    // index
    public function index(Request $request)
    {
        if (! fs_db_config('menu_group_status')) {
            return Response::view('404', [], 404);
        }

        $indexType = ConfigHelper::fresnsConfigByItemKey('menu_group_type');

        $groupTree = [];
        $groups = [];

        if ($indexType == 'tree') {
            $result = GroupInterface::tree();

            if (data_get($result, 'code') !== 0) {
                throw new ErrorException($result['message'], $result['code']);
            }

            $groupTree = $result['data'];
        } else {
            $query = QueryHelper::convertOptionToRequestParam(QueryHelper::TYPE_GROUP, $request->all());

            $result = GroupInterface::list($query);

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
        if (! fs_db_config('menu_group_list_status')) {
            return Response::view('404', [], 404);
        }

        $query = QueryHelper::convertOptionToRequestParam(QueryHelper::TYPE_GROUP_LIST, $request->all());

        $result = GroupInterface::list($query);

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
        $uid = (int) fs_user('detail.uid');

        $result = UserInterface::markList($uid, 'like', 'groups', $request->all());

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
        $uid = (int) fs_user('detail.uid');

        $result = UserInterface::markList($uid, 'dislike', 'groups', $request->all());

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
        $uid = (int) fs_user('detail.uid');

        $result = UserInterface::markList($uid, 'follow', 'groups', $request->all());

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
        $uid = (int) fs_user('detail.uid');

        $result = UserInterface::markList($uid, 'block', 'groups', $request->all());

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

        $type = match ($type) {
            'posts' => 'posts',
            'comments' => 'comments',
            default => 'posts',
        };

        switch ($type) {
            case 'posts':
                $results = GroupInterface::detail($gid, 'posts', $query);

                $posts = QueryHelper::convertApiDataToPaginate(
                    items: $results['posts']['data']['list'],
                    paginate: $results['posts']['data']['paginate'],
                );
                $paginate = $results['posts']['data']['paginate'];

                $comments = [];
                break;

            case 'comments':
                $results = GroupInterface::detail($gid, 'comments', $query);

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
                case 'posts':
                    foreach ($results['posts']['data']['list'] as $post) {
                        $html .= View::make('components.post.list', compact('post'))->render();
                    }
                    break;

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
