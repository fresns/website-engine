<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jevan Tang
 * Released under the Apache-2.0 License.
 */

namespace Fresns\WebEngine\Http\Controllers;

use Illuminate\Http\Request;
use Fresns\WebEngine\Exceptions\ErrorException;
use Fresns\WebEngine\Helpers\ApiHelper;
use Fresns\WebEngine\Helpers\QueryHelper;
use Fresns\WebEngine\Interfaces\AccountInterface;

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
        $result = AccountInterface::walletLogs($request->all());

        if (data_get($result, 'code') !== 0) {
            throw new ErrorException($result['message'], $result['code']);
        }

        $logs = QueryHelper::convertApiDataToPaginate(
            items: $result['data']['list'],
            paginate: $result['data']['paginate'],
        );

        return view('account.wallet', compact('logs'));
    }

    // userExtcredits
    public function userExtcredits(Request $request)
    {
        $result = AccountInterface::extcreditsLogs($request->all());

        if (data_get($result, 'code') !== 0) {
            throw new ErrorException($result['message'], $result['code']);
        }

        $logs = QueryHelper::convertApiDataToPaginate(
            items: $result['data']['list'],
            paginate: $result['data']['paginate'],
        );

        $extcreditsId = (int) $request->extcreditsId;

        return view('account.user-extcredits', compact('extcreditsId', 'logs'));
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
