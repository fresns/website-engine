<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jevan Tang
 * Released under the Apache-2.0 License.
 */

namespace Fresns\WebEngine\Http\Controllers;

use Fresns\WebEngine\Exceptions\ErrorException;
use Fresns\WebEngine\Helpers\QueryHelper;
use Fresns\WebEngine\Interfaces\UserInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\View;

class UserController extends Controller
{
    // index
    public function index(Request $request)
    {
        if (! fs_db_config('menu_user_status')) {
            return Response::view('404', [], 404);
        }

        $query = QueryHelper::convertOptionToRequestParam(QueryHelper::TYPE_USER, $request->all());

        $result = UserInterface::list($query);

        if (data_get($result, 'code') !== 0) {
            throw new ErrorException($result['message'], $result['code']);
        }

        $users = QueryHelper::convertApiDataToPaginate(
            items: $result['data']['list'],
            paginate: $result['data']['paginate'],
        );

        // ajax
        if ($request->ajax()) {
            $html = '';
            foreach ($result['data']['list'] as $user) {
                $html .= View::make('components.user.list', compact('user'))->render();
            }

            return response()->json([
                'paginate' => $result['data']['paginate'],
                'html' => $html,
            ]);
        }

        // view
        return view("{$this->viewNamespace}::users.index", compact('users'));
    }

    // list
    public function list(Request $request)
    {
        if (! fs_db_config('menu_user_list_status')) {
            return Response::view('404', [], 404);
        }

        $query = QueryHelper::convertOptionToRequestParam(QueryHelper::TYPE_USER_LIST, $request->all());

        $result = UserInterface::list($query);

        if (data_get($result, 'code') !== 0) {
            throw new ErrorException($result['message'], $result['code']);
        }

        $users = QueryHelper::convertApiDataToPaginate(
            items: $result['data']['list'],
            paginate: $result['data']['paginate'],
        );

        // ajax
        if ($request->ajax()) {
            $html = '';
            foreach ($result['data']['list'] as $user) {
                $html .= View::make('components.user.list', compact('user'))->render();
            }

            return response()->json([
                'paginate' => $result['data']['paginate'],
                'html' => $html,
            ]);
        }

        // view
        return view("{$this->viewNamespace}::users.list", compact('users'));
    }

    // likes
    public function likes(Request $request)
    {
        $uid = (int) fs_user('detail.uid');

        $result = UserInterface::markList($uid, 'like', 'users', $request->all());

        if (data_get($result, 'code') !== 0) {
            throw new ErrorException($result['message'], $result['code']);
        }

        $users = QueryHelper::convertApiDataToPaginate(
            items: $result['data']['list'],
            paginate: $result['data']['paginate'],
        );

        // ajax
        if ($request->ajax()) {
            $html = '';
            foreach ($result['data']['list'] as $user) {
                $html .= View::make('components.user.list', compact('user'))->render();
            }

            return response()->json([
                'paginate' => $result['data']['paginate'],
                'html' => $html,
            ]);
        }

        // view
        return view("{$this->viewNamespace}::users.likes", compact('users'));
    }

    // dislikes
    public function dislikes(Request $request)
    {
        $uid = (int) fs_user('detail.uid');

        $result = UserInterface::markList($uid, 'dislike', 'users', $request->all());

        if (data_get($result, 'code') !== 0) {
            throw new ErrorException($result['message'], $result['code']);
        }

        $users = QueryHelper::convertApiDataToPaginate(
            items: $result['data']['list'],
            paginate: $result['data']['paginate'],
        );

        // ajax
        if ($request->ajax()) {
            $html = '';
            foreach ($result['data']['list'] as $user) {
                $html .= View::make('components.user.list', compact('user'))->render();
            }

            return response()->json([
                'paginate' => $result['data']['paginate'],
                'html' => $html,
            ]);
        }

        // view
        return view("{$this->viewNamespace}::users.dislikes", compact('users'));
    }

    // following
    public function following(Request $request)
    {
        $uid = (int) fs_user('detail.uid');

        $result = UserInterface::markList($uid, 'follow', 'users', $request->all());

        if (data_get($result, 'code') !== 0) {
            throw new ErrorException($result['message'], $result['code']);
        }

        $users = QueryHelper::convertApiDataToPaginate(
            items: $result['data']['list'],
            paginate: $result['data']['paginate'],
        );

        // ajax
        if ($request->ajax()) {
            $html = '';
            foreach ($result['data']['list'] as $user) {
                $html .= View::make('components.user.list', compact('user'))->render();
            }

            return response()->json([
                'paginate' => $result['data']['paginate'],
                'html' => $html,
            ]);
        }

        // view
        return view("{$this->viewNamespace}::users.following", compact('users'));
    }

    // blocking
    public function blocking(Request $request)
    {
        $uid = (int) fs_user('detail.uid');

        $result = UserInterface::markList($uid, 'block', 'users', $request->all());

        $users = QueryHelper::convertApiDataToPaginate(
            items: $result['data']['list'],
            paginate: $result['data']['paginate'],
        );

        // ajax
        if ($request->ajax()) {
            $html = '';
            foreach ($result['data']['list'] as $user) {
                $html .= View::make('components.user.list', compact('user'))->render();
            }

            return response()->json([
                'paginate' => $result['data']['paginate'],
                'html' => $html,
            ]);
        }

        // view
        return view("{$this->viewNamespace}::users.blocking", compact('users'));
    }
}
