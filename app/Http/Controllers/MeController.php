<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jevan Tang
 * Released under the Apache-2.0 License.
 */

namespace Fresns\WebEngine\Http\Controllers;

use Fresns\WebEngine\Exceptions\ErrorException;
use Fresns\WebEngine\Helpers\ApiHelper;
use Fresns\WebEngine\Helpers\QueryHelper;
use Fresns\WebEngine\Interfaces\AccountInterface;
use Illuminate\Http\Request;

class MeController extends Controller
{
    // index
    public function index()
    {
        return view('me.index');
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
            pagination: $result['data']['pagination'],
        );

        return view('me.wallet', compact('logs'));
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
            pagination: $result['data']['pagination'],
        );

        $extcreditsId = (int) $request->extcreditsId;

        return view('me.user-extcredits', compact('extcreditsId', 'logs'));
    }

    // users
    public function users()
    {
        return view('me.users');
    }

    // settings
    public function settings()
    {
        return view('me.settings');
    }

    // logout
    public function logout(Request $request)
    {
        $redirectURL = $request->get('redirectURL') ?? fs_route(route('fresns.home'));

        fs_account()->logout();

        ApiHelper::make()->delete('/api/fresns/v1/account/logout');

        return redirect()->intended($redirectURL);
    }
}
