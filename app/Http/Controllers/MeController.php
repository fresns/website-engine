<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jevan Tang
 * Released under the Apache-2.0 License.
 */

namespace Fresns\WebsiteEngine\Http\Controllers;

use Fresns\WebsiteEngine\Helpers\HttpHelper;
use Fresns\WebsiteEngine\Helpers\QueryHelper;
use Fresns\WebsiteEngine\Interfaces\MeInterface;
use Illuminate\Http\Request;

class MeController extends Controller
{
    // index
    public function index()
    {
        return view('me.index');
    }

    // userExtcredits
    public function userExtcredits(Request $request)
    {
        $result = MeInterface::extcreditsRecords($request->all());

        $records = QueryHelper::convertApiDataToPaginate(
            items: $result['data']['list'],
            pagination: $result['data']['pagination'],
        );

        $extcreditsId = (int) $request->extcreditsId;

        return view('me.extcredits', compact('extcreditsId', 'records'));
    }

    // drafts
    public function drafts(Request $request)
    {
        $draftType = $request->type;

        $type = match ($draftType) {
            'posts' => 'post',
            'comments' => 'comment',
            'post' => 'post',
            'comment' => 'comment',
            default => 'post',
        };

        $query = $request->all();

        $result = MeInterface::drafts($type, $query);

        $drafts = QueryHelper::convertApiDataToPaginate(
            items: $result['data']['list'],
            pagination: $result['data']['pagination'],
        );

        return view('me.drafts', compact('drafts', 'type'));
    }

    // users
    public function users()
    {
        return view('me.users');
    }

    // wallet
    public function wallet(Request $request)
    {
        $result = MeInterface::walletRecords($request->all());

        $records = QueryHelper::convertApiDataToPaginate(
            items: $result['data']['list'],
            pagination: $result['data']['pagination'],
        );

        return view('me.wallet', compact('records'));
    }

    // settings
    public function settings()
    {
        $result = MeInterface::archives('user');

        $archives = $result['data'];

        return view('me.settings', compact('archives'));
    }

    // logout
    public function logout(Request $request)
    {
        $redirectURL = $request->get('redirectURL') ?? route('fresns.home');

        $result = HttpHelper::delete('/api/fresns/v1/account/auth-token');

        if ($result['code'] == 0) {
            fs_account()->logout();
        }

        return redirect()->intended($redirectURL);
    }
}
