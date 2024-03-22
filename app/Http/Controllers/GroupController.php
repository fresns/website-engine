<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jevan Tang
 * Released under the Apache-2.0 License.
 */

namespace Fresns\WebsiteEngine\Http\Controllers;

use Fresns\WebsiteEngine\Helpers\QueryHelper;
use Fresns\WebsiteEngine\Interfaces\GroupInterface;
use Fresns\WebsiteEngine\Interfaces\UserInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\View;

class GroupController extends Controller
{
    // index
    public function index(Request $request)
    {
        if (! fs_config('channel_group_status')) {
            return Response::view('404', [], 404);
        }

        $indexType = fs_config('channel_group_type') ?? 'tree';

        $groupTree = [];
        $groups = [];

        if ($indexType == 'tree') {
            $result = GroupInterface::tree();

            $groupTree = $result['data'];

            // view
            return view('groups.index', compact('groupTree', 'groups'));
        }

        $query = QueryHelper::convertOptionToRequestParam(QueryHelper::TYPE_GROUP, $request->all());

        $result = GroupInterface::list($query);

        // ajax
        if ($request->ajax()) {
            $html = '';
            foreach ($result['data']['list'] as $group) {
                $html .= View::make('components.group.list', compact('group'))->render();
            }

            return response()->json([
                'pagination' => $result['data']['pagination'],
                'html' => $html,
            ]);
        }

        // view
        $groups = QueryHelper::convertApiDataToPaginate(
            items: $result['data']['list'],
            pagination: $result['data']['pagination'],
        );

        return view('groups.index', compact('groupTree', 'groups'));
    }

    // list
    public function list(Request $request)
    {
        if (! fs_config('channel_group_list_status')) {
            return Response::view('404', [], 404);
        }

        $query = QueryHelper::convertOptionToRequestParam(QueryHelper::TYPE_GROUP_LIST, $request->all());

        $result = GroupInterface::list($query);

        // ajax
        if ($request->ajax()) {
            $html = '';
            foreach ($result['data']['list'] as $group) {
                $html .= View::make('components.group.list', compact('group'))->render();
            }

            return response()->json([
                'pagination' => $result['data']['pagination'],
                'html' => $html,
            ]);
        }

        // view
        $groups = QueryHelper::convertApiDataToPaginate(
            items: $result['data']['list'],
            pagination: $result['data']['pagination'],
        );

        return view('groups.list', compact('groups'));
    }

    // likes
    public function likes(Request $request)
    {
        $uid = (int) fs_user('detail.uid');

        $result = UserInterface::markList($uid, 'like', 'groups', $request->all());

        // ajax
        if ($request->ajax()) {
            $html = '';
            foreach ($result['data']['list'] as $group) {
                $html .= View::make('components.group.list', compact('group'))->render();
            }

            return response()->json([
                'pagination' => $result['data']['pagination'],
                'html' => $html,
            ]);
        }

        // view
        $groups = QueryHelper::convertApiDataToPaginate(
            items: $result['data']['list'],
            pagination: $result['data']['pagination'],
        );

        return view('groups.likes', compact('groups'));
    }

    // dislikes
    public function dislikes(Request $request)
    {
        $uid = (int) fs_user('detail.uid');

        $result = UserInterface::markList($uid, 'dislike', 'groups', $request->all());

        // ajax
        if ($request->ajax()) {
            $html = '';
            foreach ($result['data']['list'] as $group) {
                $html .= View::make('components.group.list', compact('group'))->render();
            }

            return response()->json([
                'pagination' => $result['data']['pagination'],
                'html' => $html,
            ]);
        }

        // view
        $groups = QueryHelper::convertApiDataToPaginate(
            items: $result['data']['list'],
            pagination: $result['data']['pagination'],
        );

        return view('groups.dislikes', compact('groups'));
    }

    // following
    public function following(Request $request)
    {
        $uid = (int) fs_user('detail.uid');

        $result = UserInterface::markList($uid, 'follow', 'groups', $request->all());

        // ajax
        if ($request->ajax()) {
            $html = '';
            foreach ($result['data']['list'] as $group) {
                $html .= View::make('components.group.list', compact('group'))->render();
            }

            return response()->json([
                'pagination' => $result['data']['pagination'],
                'html' => $html,
            ]);
        }

        // view
        $groups = QueryHelper::convertApiDataToPaginate(
            items: $result['data']['list'],
            pagination: $result['data']['pagination'],
        );

        return view('groups.following', compact('groups'));
    }

    // blocking
    public function blocking(Request $request)
    {
        $uid = (int) fs_user('detail.uid');

        $result = UserInterface::markList($uid, 'block', 'groups', $request->all());

        // ajax
        if ($request->ajax()) {
            $html = '';
            foreach ($result['data']['list'] as $group) {
                $html .= View::make('components.group.list', compact('group'))->render();
            }

            return response()->json([
                'pagination' => $result['data']['pagination'],
                'html' => $html,
            ]);
        }

        // view
        $groups = QueryHelper::convertApiDataToPaginate(
            items: $result['data']['list'],
            pagination: $result['data']['pagination'],
        );

        return view('groups.blocking', compact('groups'));
    }

    // detail
    public function detail(Request $request, string $gid, ?string $type = null)
    {
        $query = $request->all();
        $query['groups'] = $gid;

        $type = match ($type) {
            'posts' => 'posts',
            'comments' => 'comments',
            default => 'posts',
        };

        $results = GroupInterface::detail($gid, $type, $query);

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
                'pagination' => $results[$type]['data']['pagination'],
                'html' => $html,
            ]);
        }

        // view
        $items = $results['group']['data']['items'];
        $group = $results['group']['data']['detail'];

        $posts = [];
        $comments = [];

        switch ($type) {
            case 'posts':
                $posts = QueryHelper::convertApiDataToPaginate(
                    items: $results['posts']['data']['list'],
                    pagination: $results['posts']['data']['pagination'],
                );
                break;

            case 'comments':
                $comments = QueryHelper::convertApiDataToPaginate(
                    items: $results['comments']['data']['list'],
                    pagination: $results['comments']['data']['pagination'],
                );
                break;
        }

        return view('groups.detail', compact('items', 'group', 'type', 'posts', 'comments'));
    }

    // detail likers
    public function detailLikers(Request $request, string $gid)
    {
        $results = GroupInterface::interaction($gid, 'likers', $request->all());

        if (! $results['group']['data']['detail']['interaction']['likePublicRecord']) {
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
        $items = $results['group']['data']['items'];
        $group = $results['group']['data']['detail'];

        $users = QueryHelper::convertApiDataToPaginate(
            items: $results['users']['data']['list'],
            pagination: $results['users']['data']['pagination'],
        );

        return view('groups.detail-likers', compact('items', 'group', 'users'));
    }

    // detail dislikers
    public function detailDislikers(Request $request, string $gid)
    {
        $results = GroupInterface::interaction($gid, 'dislikers', $request->all());

        if (! $results['group']['data']['detail']['interaction']['likePublicRecord']) {
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
        $items = $results['group']['data']['items'];
        $group = $results['group']['data']['detail'];

        $users = QueryHelper::convertApiDataToPaginate(
            items: $results['users']['data']['list'],
            pagination: $results['users']['data']['pagination'],
        );

        return view('groups.detail-dislikers', compact('items', 'group', 'users'));
    }

    // detail followers
    public function detailFollowers(Request $request, string $gid)
    {
        $results = GroupInterface::interaction($gid, 'followers', $request->all());

        if (! $results['group']['data']['detail']['interaction']['likePublicRecord']) {
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
        $items = $results['group']['data']['items'];
        $group = $results['group']['data']['detail'];

        $users = QueryHelper::convertApiDataToPaginate(
            items: $results['users']['data']['list'],
            pagination: $results['users']['data']['pagination'],
        );

        return view('groups.detail-followers', compact('items', 'group', 'users'));
    }

    // detail blockers
    public function detailBlockers(Request $request, string $gid)
    {
        $results = GroupInterface::interaction($gid, 'blockers', $request->all());

        if (! $results['group']['data']['detail']['interaction']['likePublicRecord']) {
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
        $items = $results['group']['data']['items'];
        $group = $results['group']['data']['detail'];

        $users = QueryHelper::convertApiDataToPaginate(
            items: $results['users']['data']['list'],
            pagination: $results['users']['data']['pagination'],
        );

        return view('groups.detail-blockers', compact('items', 'group', 'users'));
    }
}
