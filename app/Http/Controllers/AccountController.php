<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jevan Tang
 * Released under the Apache-2.0 License.
 */

namespace Plugins\FresnsEngine\Http\Controllers;

use Illuminate\Http\Request;
use Plugins\FresnsEngine\Exceptions\ErrorException;
use Plugins\FresnsEngine\Helpers\ApiHelper;
use Plugins\FresnsEngine\Helpers\QueryHelper;

class AccountController extends Controller
{
    // register
    public function register(Request $request)
    {
        $redirectURL = $request->get('redirectURL') ?? fs_route(route('fresns.home'));

        if (fs_account()->check() || fs_user()->check()) {
            return redirect()->intended($redirectURL);
        }

        return view('account.register');
    }

    // login
    public function login(Request $request)
    {
        $redirectURL = $request->get('redirectURL') ?? fs_route(route('fresns.home'));

        if (fs_account()->check() && fs_user()->check()) {
            return redirect()->intended($redirectURL);
        }

        return view('account.login');
    }

    // logout
    public function logout(Request $request)
    {
        $redirectURL = $request->get('redirectURL') ?? fs_route(route('fresns.home'));

        fs_account()->logout();

        ApiHelper::make()->delete('/api/v2/account/logout');

        return redirect()->intended($redirectURL);
    }

    // reset password
    public function resetPassword(Request $request)
    {
        $redirectURL = $request->get('redirectURL') ?? fs_route(route('fresns.home'));

        if (fs_account()->check() || fs_user()->check()) {
            return redirect()->intended($redirectURL);
        }

        return view('account.reset-password');
    }

    // index
    public function index()
    {
        return view('account.index');
    }

    // wallet
    public function wallet(Request $request)
    {
        $result = ApiHelper::make()->get('/api/v2/account/wallet-logs', [
            'query' => $request->all(),
        ]);

        if (data_get($result, 'code') !== 0) {
            throw new ErrorException($result['message'], $result['code']);
        }

        $logs = QueryHelper::convertApiDataToPaginate(
            items: $result['data']['list'],
            paginate: $result['data']['paginate'],
        );

        return view('account.wallet', compact('logs'));
    }

    // users
    public function users()
    {
        return view('account.users');
    }

    // settings
    public function settings()
    {
        return view('account.settings');
    }
}
