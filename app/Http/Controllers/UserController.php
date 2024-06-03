<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jevan Tang
 * Released under the Apache-2.0 License.
 */

namespace Fresns\WebsiteEngine\Http\Controllers;

use Fresns\WebsiteEngine\Helpers\QueryHelper;
use Fresns\WebsiteEngine\Interfaces\UserInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\View;

class UserController extends Controller
{
    // index
    public function index(Request $request)
    {
        if (! fs_config('channel_user_status')) {
            return Response::view('404', [], 404);
        }

        $query = QueryHelper::convertOptionToRequestParam(QueryHelper::TYPE_USER, $request->all());

        $result = UserInterface::list($query);

        // ajax
        if ($request->ajax()) {
            $html = '';
            foreach ($result['data']['list'] as $user) {
                $html .= View::make('components.users.list', compact('user'))->render();
            }

            return response()->json([
                'pagination' => $result['data']['pagination'],
                'html' => $html,
            ]);
        }

        // view
        $users = QueryHelper::convertApiDataToPaginate(
            items: $result['data']['list'],
            pagination: $result['data']['pagination'],
        );

        return view('users.index', compact('users'));
    }

    // list
    public function list(Request $request)
    {
        if (! fs_config('channel_user_list_status')) {
            return Response::view('404', [], 404);
        }

        $query = QueryHelper::convertOptionToRequestParam(QueryHelper::TYPE_USER_LIST, $request->all());

        $result = UserInterface::list($query);

        // ajax
        if ($request->ajax()) {
            $html = '';
            foreach ($result['data']['list'] as $user) {
                $html .= View::make('components.users.list', compact('user'))->render();
            }

            return response()->json([
                'pagination' => $result['data']['pagination'],
                'html' => $html,
            ]);
        }

        // view
        $users = QueryHelper::convertApiDataToPaginate(
            items: $result['data']['list'],
            pagination: $result['data']['pagination'],
        );

        return view('users.list', compact('users'));
    }

    // likes
    public function likes(Request $request)
    {
        $uid = (int) fs_user('detail.uid');

        $result = UserInterface::markList($uid, 'like', 'users', $request->all());

        // ajax
        if ($request->ajax()) {
            $html = '';
            foreach ($result['data']['list'] as $user) {
                $html .= View::make('components.users.list', compact('user'))->render();
            }

            return response()->json([
                'pagination' => $result['data']['pagination'],
                'html' => $html,
            ]);
        }

        // view
        $users = QueryHelper::convertApiDataToPaginate(
            items: $result['data']['list'],
            pagination: $result['data']['pagination'],
        );

        return view('users.likes', compact('users'));
    }

    // dislikes
    public function dislikes(Request $request)
    {
        $uid = (int) fs_user('detail.uid');

        $result = UserInterface::markList($uid, 'dislike', 'users', $request->all());

        // ajax
        if ($request->ajax()) {
            $html = '';
            foreach ($result['data']['list'] as $user) {
                $html .= View::make('components.users.list', compact('user'))->render();
            }

            return response()->json([
                'pagination' => $result['data']['pagination'],
                'html' => $html,
            ]);
        }

        // view
        $users = QueryHelper::convertApiDataToPaginate(
            items: $result['data']['list'],
            pagination: $result['data']['pagination'],
        );

        return view('users.dislikes', compact('users'));
    }

    // following
    public function following(Request $request)
    {
        $uid = (int) fs_user('detail.uid');

        $result = UserInterface::markList($uid, 'follow', 'users', $request->all());

        // ajax
        if ($request->ajax()) {
            $html = '';
            foreach ($result['data']['list'] as $user) {
                $html .= View::make('components.users.list', compact('user'))->render();
            }

            return response()->json([
                'pagination' => $result['data']['pagination'],
                'html' => $html,
            ]);
        }

        // view
        $users = QueryHelper::convertApiDataToPaginate(
            items: $result['data']['list'],
            pagination: $result['data']['pagination'],
        );

        return view('users.following', compact('users'));
    }

    // blocking
    public function blocking(Request $request)
    {
        $uid = (int) fs_user('detail.uid');

        $result = UserInterface::markList($uid, 'block', 'users', $request->all());

        // ajax
        if ($request->ajax()) {
            $html = '';
            foreach ($result['data']['list'] as $user) {
                $html .= View::make('components.users.list', compact('user'))->render();
            }

            return response()->json([
                'pagination' => $result['data']['pagination'],
                'html' => $html,
            ]);
        }

        // view
        $users = QueryHelper::convertApiDataToPaginate(
            items: $result['data']['list'],
            pagination: $result['data']['pagination'],
        );

        return view('users.blocking', compact('users'));
    }
}
