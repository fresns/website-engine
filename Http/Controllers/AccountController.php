<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jarvis Tang
 * Released under the Apache-2.0 License.
 */

namespace Plugins\FresnsEngine\Http\Controllers;

use Carbon\Carbon;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Validator;
use Plugins\FresnsEngine\Http\Services\ErrorCodeService;
use Plugins\FresnsEngine\Sdk\Factory;

class AccountController extends Controller
{
    /**
     * @return View
     */
    public function index(): View
    {
        return view('account.index');
    }

    /**
     * @return View
     */
    public function register(): View
    {
        return view('account.register', [
            'smsCodes' => fs_config('send_sms_supported_codes'),
            'defaultSmsCode' => fs_config('send_sms_default_code'),
        ]);
    }

    /**
     * @param  Request  $request
     * @return RedirectResponse
     *
     * @throws GuzzleException
     */
    public function postRegister(Request $request): RedirectResponse
    {
        $validator = Validator::make($request->post(),
            [
                'type' => 'required|in:1,2',
                'email' => 'required_if:type,1',
                'phone' => 'required_if:type,2',
                'verifyCode' => 'required',
                'password' => 'required|confirmed',
                'nickname' => 'required',
            ],
            [
                'email.required_if' => fs_lang('email').': '.fs_lang('errorEmpty'),
                'phone.required_id' => fs_lang('phone').': '.fs_lang('errorEmpty'),
                'verifyCode.required' => fs_lang('verifyCode').': '.fs_lang('errorEmpty'),
                'password.required' => fs_lang('pleaseEnter').': '.fs_lang('accountPassword'),
                'password.confirmed' => fs_lang('accountPassword').': '.fs_lang('errorNotMatch'),
                'nickname.required' => fs_config('user_nickname_name').': '.fs_lang('errorEmpty'),
            ]
        );

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        try {
            $result = Factory::account()->auth->register(
                $request->post('type'),
                (int) $request->post('type') === 1 ? $request->post('email') : $request->post('phone'),
                $request->post('countryCode'),
                $request->post('verifyCode'),
                $request->post('password'),
                $request->post('nickname')
            );

            if (Arr::get($result, 'code') === 0) {
                if (Arr::get($result, 'data.tokenExpiredTime')) {
                    $minutes = Carbon::parse(Arr::get($result, 'data.tokenExpiredTime'))->diffInMinutes(now());
                    $cookies = [
                        Cookie::make('aid', $result['data']['aid'], $minutes),
                        Cookie::make('token', $result['data']['token'], $minutes),
                    ];
                } else {
                    $cookies = [
                        Cookie::forever('aid', $result['data']['aid']),
                        Cookie::forever('token', $result['data']['token']),
                    ];
                }

                return redirect(fs_route(route('fresns.account')))->with('success', fs_lang('accountLogin').': '.fs_lang('success'))->withCookies($cookies);
            }

            if (isset($result['message'])) {
                return back()->with(['failure' => $result['message'], 'code' => $result['code']])->withInput();
            }
        } catch (\Exception $exception) {
            return back()->withErrors($exception->getMessage())->withInput();
        }
    }

    /**
     * @return View
     */
    public function login(): View
    {
        return view('account.login', [
            'smsCodes' => fs_config('send_sms_supported_codes'),
            'defaultSmsCode' => fs_config('send_sms_default_code'),
        ]);
    }

    /**
     * @param  Request  $request
     * @return RedirectResponse
     *
     * @throws GuzzleException
     */
    public function postLogin(Request $request): RedirectResponse
    {
        $validator = Validator::make($request->post(),
            [
                'type' => 'required|in:1,2',
                'email' => 'required_if:type,1',
                'phone' => 'required_if:type,2',
            ],
            [
                'email.required_if' => fs_lang('email').': '.fs_lang('errorEmpty'),
                'phone.required_id' => fs_lang('phone').': '.fs_lang('errorEmpty'),
            ]
        );

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }
        try {
            $result = Factory::account()->auth->login(
                $request->post('type'),
                (int) $request->post('type') === 1 ? $request->post('email') : $request->post('phone'),
                $request->post('countryCode'),
                $request->post('verifyCode'),
                $request->post('password'),
            );

            if (Arr::get($result, 'code') === 0) {
                if (Arr::get($result, 'data.tokenExpiredTime')) {
                    $minutes = Carbon::parse(Arr::get($result, 'data.tokenExpiredTime'))->diffInMinutes(now());
                    \Cookie::queue('aid', $result['data']['aid'], $minutes);
                    \Cookie::queue('token', $result['data']['token'], $minutes);
                } else {
                    \Cookie::queue('aid', $result['data']['aid']);
                    \Cookie::queue('token', $result['data']['token']);
                }

                return redirect(fs_route(route('fresns.account')))->with('success', fs_lang('accountLogin').': '.fs_lang('success'));
            }
            if (isset($result['message'])) {
                return back()->with(['failure' => $result['message'], 'code' => $result['code']])->withInput();
            }
        } catch (\Exception $exception) {
            return back()->withErrors($exception->getMessage())->withInput();
        }
    }

    /**
     * @return View
     */
    public function reset(): View
    {
        return view('account.reset', [
            'smsCodes' => fs_config('send_sms_supported_codes'),
            'defaultSmsCode' => fs_config('send_sms_default_code'),
        ]);
    }

    public function postReset(Request $request)
    {
        $validator = Validator::make($request->post(),
            [
                'type' => 'required|in:1,2',
                'email' => 'required_if:type,1',
                'phone' => 'required_if:type,2',
                'verifyCode' => 'required',
                'password' => 'required|confirmed',
            ],
            [
                'email.required_if' => fs_lang('email').': '.fs_lang('errorEmpty'),
                'phone.required_id' => fs_lang('phone').': '.fs_lang('errorEmpty'),
                'verifyCode.required' => fs_lang('verifyCode').': '.fs_lang('errorEmpty'),
                'password.required' => fs_lang('pleaseEnter').': '.fs_lang('passwordNew'),
                'password.confirmed' => fs_lang('accountPassword').': '.fs_lang('errorNotMatch'),
            ]
        );

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        try {
            $result = Factory::account()->auth->reset(
                $request->post('type'),
                (int) $request->post('type') === 1 ? $request->post('email') : $request->post('phone'),
                $request->post('countryCode'),
                $request->post('verifyCode'),
                $request->post('password'),
            );

            if (Arr::get($result, 'code') === 0) {
                return redirect(fs_route(route('fresns.account.login')))->with('success', fs_lang('accountReset').': '.fs_lang('success'));
            }
            if (isset($result['message'])) {
                return back()->with(['failure' => $result['message'], 'code' => $result['code']])->withInput();
            }
        } catch (\Exception $exception) {
            return back()->withErrors($exception->getMessage())->withInput();
        }
    }

    /**
     * @return View
     */
    public function wallet(): View
    {
        return view('account.wallet');
    }

    /**
     * @return View
     */
    public function settings(): View
    {
        return view('account.settings');
    }

    /**
     * @return View
     */
    public function users(): View
    {
        return view('account.users');
    }

    /**
     * @return RedirectResponse
     */
    public function logout(): RedirectResponse
    {
        try {
            account()->logout();
            user()->logout();

            return redirect()->back()->with('success', fs_lang('accountLogout').': '.fs_lang('success'));
        } catch (\Exception $exception) {
            return back()->withErrors($exception->getMessage());
        }
    }

    /**
     * @return View
     */
    public function privateModel(): View
    {
        return view('private');
    }
}
