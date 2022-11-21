<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jarvis Tang
 * Released under the Apache-2.0 License.
 */

namespace Plugins\FresnsEngine\Http\Controllers;

use Carbon\Carbon;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Validator;
use Plugins\FresnsEngine\Http\Services\ErrorCodeService;
use Plugins\FresnsEngine\Sdk\Factory;

class UsersController extends Controller
{
    /**
     * @return Application|\Illuminate\Contracts\View\Factory|View|RedirectResponse
     */
    public function index()
    {
        try {
            $defualtParams = fresnsengine_config('menu_user_config');
            $params = [];

            foreach ($this->getListKeys() as $key) {
                $params[$key] = request($key) ?? (empty($defualtParams[$key]) ? null : $defualtParams[$key]);
            }

            $result = call_user_func_array([Factory::user()->auth, 'lists'], $params);

            if (Arr::get($result, 'code') === 0) {
                $users = paginator(Arr::get($result, 'data'));

                return view('users.index', compact('users'));
            }

            if (Arr::get($result, 'message')) {
                return back()->with(['failure' => $result['message'], 'code' => $result['code']]);
            }
        } catch (\Exception $exception) {
            return back()->withErrors($exception->getMessage());
        }
    }

    /**
     * @return Application|\Illuminate\Contracts\View\Factory|View|RedirectResponse
     *
     * @throws GuzzleException
     */
    public function list()
    {
        try {
            $defualtParams = fresnsengine_config('menu_user_list_config');

            $params = [];

            foreach ($this->getListKeys() as $key) {
                $params[$key] = request($key) ?? (empty($defualtParams[$key]) ? null : $defualtParams[$key]);
            }

            $result = call_user_func_array([Factory::user()->auth, 'lists'], $params);

            if (Arr::get($result, 'code') === 0) {
                $users = paginator(Arr::get($result, 'data'));

                return view('users.list', compact('users'));
            }

            if (Arr::get($result, 'message')) {
                return back()->with(['failure' => $result['message'], 'code' => $result['code']]);
            }
        } catch (\Exception $exception) {
            return back()->withErrors($exception->getMessage());
        }
    }

    /**
     * @return Application|\Illuminate\Contracts\View\Factory|View|RedirectResponse
     *
     * @throws GuzzleException
     */
    public function likes()
    {
        try {
            $result = Factory::user()->content->markLists(
                1,
                1,
                user()->get('uid'),
                request('viewUsername'),
                request('pageSize'),
                request('page')
            );

            if (Arr::get($result, 'code') === 0) {
                $users = paginator(Arr::get($result, 'data'));

                return view('users.likes', compact('users'));
            }

            if (Arr::get($result, 'message')) {
                return back()->with(['failure' => $result['message'], 'code' => $result['code']]);
            }
        } catch (\Exception $exception) {
            return back()->withErrors($exception->getMessage());
        }
    }

    /**
     * @return Application|\Illuminate\Contracts\View\Factory|View|RedirectResponse
     *
     * @throws GuzzleException
     */
    public function following()
    {
        try {
            $result = Factory::user()->content->markLists(
                2,
                1,
                user()->get('uid'),
                request('viewUsername'),
                request('pageSize'),
                request('page')
            );

            if (Arr::get($result, 'code') === 0) {
                $users = paginator(Arr::get($result, 'data'));

                return view('users.following', compact('users'));
            }

            if (Arr::get($result, 'message')) {
                return back()->with(['failure' => $result['message'], 'code' => $result['code']]);
            }
        } catch (\Exception $exception) {
            return back()->withErrors($exception->getMessage());
        }
    }

    /**
     * @return Application|\Illuminate\Contracts\View\Factory|View|RedirectResponse
     *
     * @throws GuzzleException
     */
    public function blocking()
    {
        try {
            $result = Factory::user()->content->markLists(
                3,
                1,
                user()->get('uid'),
                request('viewUsername'),
                request('pageSize'),
                request('page')
            );

            if (Arr::get($result, 'code') === 0) {
                $users = paginator(Arr::get($result, 'data'));

                return view('users.blocking', compact('users'));
            }

            if (Arr::get($result, 'message')) {
                return back()->with(['failure' => $result['message'], 'code' => $result['code']]);
            }
        } catch (\Exception $exception) {
            return back()->withErrors($exception->getMessage());
        }
    }

    /**
     * @return Application|\Illuminate\Contracts\View\Factory|View|RedirectResponse
     *
     * @throws GuzzleException
     */
    public function follow()
    {
        try {
            $result = Factory::content()->post->follows(
                request('pageSize'),
                request('page'),
                request('searchType'),
                request('searchKey'),
                'user'
            );

            if (Arr::get($result, 'code') === 0) {
                $posts = paginator(Arr::get($result, 'data'));

                return view('users.follow', compact('posts'));
            }

            if (Arr::get($result, 'message')) {
                return back()->with(['failure' => $result['message'], 'code' => $result['code']]);
            }
        } catch (\Exception $exception) {
            return back()->withErrors($exception->getMessage());
        }
    }

    public function switch(Request $request): RedirectResponse
    {
        $users = account()->get('users');

        $user = collect($users)->first(function (array $user) use ($request) {
            return (int) $request->input('uid') === $user['uid'];
        });

        if ($user) {
            if ($user['password'] === false) {
                try {
                    $result = Factory::user()->auth->login($user['uid']);
                    if (Arr::get($result, 'code') === 0) {
                        if (Arr::get($result, 'data.tokenExpiredTime')) {
                            $minutes = Carbon::parse(Arr::get($result, 'data.tokenExpiredTime'))->diffInMinutes(now());
                            $cookies = [
                                Cookie::make('uid', $user['uid'], $minutes),
                                Cookie::make('token', $result['data']['token'], $minutes),
                                Cookie::make('timezone', $result['data']['timezone'], $minutes),
                            ];
                        } else {
                            $cookies = [
                                Cookie::forever('uid', $user['uid']),
                                Cookie::forever('token', $result['data']['token']),
                                Cookie::forever('timezone', $result['data']['timezone']),
                            ];
                        }

                        return back()->with('success', fs_lang('optionUser').': '.fs_lang('success'))->withCookies($cookies);
                    }

                    if (isset($result['message'])) {
                        return back()->with(['failure' => $result['message'], 'code' => $result['code']])->withInput();
                    }
                } catch (\Exception $exception) {
                    return back()->withErrors($exception->getMessage());
                }
            }
        } else {
            return back()->with('failure', fs_config('user_name').': '.fs_lang('errorNotExist'));
        }
    }

    /**
     * @param  Request  $request
     * @return JsonResponse
     *
     * @throws GuzzleException
     */
    public function login(Request $request): JsonResponse
    {
        $validator = Validator::make($request->post(),
            [
                'password' => 'required',
                'uid' => 'required',
            ],
            [
                'password.required' => fs_lang('pleaseEnter').': '.fs_lang('userAuthPassword'),
            ]
        );

        if ($validator->fails()) {
            return Response::json(['message' => $validator->errors()->first()], 400);
        }
        try {
            $result = Factory::user()->auth->login(request()->post('uid'), request()->post('password'));

            if (Arr::get($result, 'code') === 0) {
                if (Arr::get($result, 'data.tokenExpiredTime')) {
                    $minutes = Carbon::parse(Arr::get($result, 'data.tokenExpiredTime'))->diffInMinutes(now());
                    $cookies = [
                        Cookie::make('uid', request()->post('uid'), $minutes),
                        Cookie::make('token', $result['data']['token'], $minutes),
                        Cookie::make('timezone', $result['data']['timezone'], $minutes),
                    ];
                } else {
                    $cookies = [
                        Cookie::forever('uid', request()->post('uid')),
                        Cookie::forever('token', $result['data']['token']),
                        Cookie::forever('timezone', $result['data']['timezone']),
                    ];
                }

                return Response::json(['message' => fs_lang('userAuth').': '.fs_lang('success')])->withCookie($cookies);
            }

            if (Arr::get($result, 'message')) {
                return Response::json(['message' => Arr::get($result, 'message')], 400);
            }
        } catch (\Exception $exception) {
            return Response::json(['message' => $exception->getMessage()], 400);
        }
    }

    /**
     * @param  Request  $request
     * @return JsonResponse
     *
     * @throws GuzzleException
     */
    public function mark(Request $request): JsonResponse
    {
        $validator = Validator::make($request->post(),
            [
                'type' => 'required|integer',
                'markType' => 'required|integer',
                'markTarget' => 'required|integer',
                'markId' => 'required',
            ]
        );
        if ($validator->fails()) {
            return Response::json(['message' => fs_lang('errorEmpty'), 'code' => 400], 400);
        }

        try {
            $result = Factory::user()->content->mark(
                (int) $request->post('type'),
                (int) $request->post('markType'),
                (int) $request->post('markTarget'),
                $request->post('markId')
            );

            if (Arr::get($result, 'code') === 0) {
                return Response::json(['message' => fs_lang('success')]);
            }

            if (Arr::get($result, 'message')) {
                return Response::json(['message' => Arr::get($result, 'message'), 'code' => $result['code']], 500);
            }
        } catch (\Exception $exception) {
            return Response::json(['message' => $exception->getMessage(), 'code' => $exception->getCode()], 500);
        }
    }

    /**
     * @param  int  $type
     * @param  string  $fsid
     * @return RedirectResponse
     *
     * @throws GuzzleException
     */
    public function delete(int $type, string $fsid): RedirectResponse
    {
        try {
            $result = Factory::user()->content->delete($type, $fsid);

            if (Arr::get($result, 'code') === 0) {
                return redirect(fs_route(route($type === 1 ? 'fresnsengine.posts' : 'fresnsengine.comments')))->with('success', fs_lang('success'));
            }

            if (Arr::get($result, 'message')) {
                return back()->with(['failure' => $result['message'], 'code' => $result['code']]);
            }
        } catch (\Exception $exception) {
            return back()->withErrors($exception->getMessage());
        }
    }

    /**
     * @return string[]
     */
    private function getListKeys(): array
    {
        return [
            'searchKey',
            'gender',
            'createdTimeGt',
            'createdTimeLt',
            'sortType',
            'sortDirection',
            'pageSize',
            'page',
        ];
    }
}
