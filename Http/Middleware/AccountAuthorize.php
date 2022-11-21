<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jarvis Tang
 * Released under the Apache-2.0 License.
 */

namespace Plugins\FresnsEngine\Http\Middleware;

use App\Utilities\ConfigUtility;
use Carbon\Carbon;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Response;
use Plugins\FresnsEngine\Facades\Account;
use Plugins\FresnsEngine\Facades\User;
use Plugins\FresnsEngine\Sdk\Factory;

class AccountAuthorize
{
    public function handle(Request $request, Closure $next)
    {
        try {
            if (Account::check()) {
                $users = Account::get('users');

                if (User::guest() && count($users) === 1 && Arr::get($users, '0.password') === false) {
                    $uid = Arr::get($users, '0.uid');

                    $result = Factory::user()->auth->login($uid);

                    if (Arr::get($result, 'code') === 0) {
                        if (Arr::get($result, 'data.tokenExpiredTime')) {
                            $minutes = Carbon::parse(Arr::get($result, 'data.tokenExpiredTime'))->diffInMinutes(now());
                            $cookies = [
                                Cookie::make('uid', $uid, $minutes),
                                Cookie::make('token', $result['data']['token'], $minutes),
                            ];
                        } else {
                            $cookies = [
                                Cookie::forever('uid', $uid),
                                Cookie::forever('token', $result['data']['token']),
                            ];
                        }

                        return redirect()->route('fresns.account')->withCookies($cookies);
                    }

                    if (Arr::get($result, 'message')) {
                        return redirect()->route('fresns.account.login')->with([
                            'failure' => $result['message'], 'code' => $result['code'],
                        ])->withInput();
                    }
                }

                return $next($request);
            } else {
                $langTag = current_lang() ?? '';
                $accountLoginTip = ConfigUtility::getCodeMessage(31501, 'Fresns', $langTag);

                return $this->shouldLoginRender($accountLoginTip);
            }
        } catch (\Exception $exception) {
            return $this->shouldLoginRender($exception->getMessage());
        }
    }

    public function shouldLoginRender(string $message, int $code = 401)
    {
        if (request()->ajax()) {
            return Response::json(compact('message', $code), 401);
        } else {
            return redirect()->route('fresns.account.login')->withErrors($message);
        }
    }
}
