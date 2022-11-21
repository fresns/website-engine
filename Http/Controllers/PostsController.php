<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jarvis Tang
 * Released under the Apache-2.0 License.
 */

namespace Plugins\FresnsEngine\Http\Controllers;

use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Validator;
use Plugins\FresnsEngine\Facades\User;
use Plugins\FresnsEngine\Http\Services\ErrorCodeService;
use Plugins\FresnsEngine\Sdk\Factory;

class PostsController extends Controller
{
    /**
     * @return Application|\Illuminate\Contracts\View\Factory|View|RedirectResponse
     */
    public function index()
    {
        try {
            $defualtParams = fresnsengine_config('menu_post_config');
            $params = [];

            foreach ($this->getListKeys() as $key) {
                $params[$key] = request($key) ?? (empty($defualtParams[$key]) ? null : $defualtParams[$key]);
            }

            $result = call_user_func_array([Factory::content()->post, 'lists'], $params);

            if (Arr::get($result, 'code') === 0) {
                $posts = paginator(Arr::get($result, 'data'));

                return view('posts.index', compact('posts'));
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
     */
    public function list()
    {
        try {
            $defualtParams = fresnsengine_config('menu_post_list_config');
            $params = [];

            foreach ($this->getListKeys() as $key) {
                $params[$key] = request($key) ?? (empty($defualtParams[$key]) ? null : $defualtParams[$key]);
            }

            $result = call_user_func_array([Factory::content()->post, 'lists'], $params);

            if (Arr::get($result, 'code') === 0) {
                $posts = paginator(Arr::get($result, 'data'));

                return view('posts.list', compact('posts'));
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
                4,
                User::get('uid'),
                null,
                request('pageSize'),
                request()->get('page')
            );
            if (Arr::get($result, 'code') === 0) {
                $posts = paginator(Arr::get($result, 'data'));

                return view('posts.likes', compact('posts'));
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
                4,
                User::get('uid'),
                null,
                request('pageSize'),
                request()->get('page')
            );
            if (Arr::get($result, 'code') === 0) {
                $posts = paginator(Arr::get($result, 'data'));

                return view('posts.following', compact('posts'));
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
                4,
                User::get('uid'),
                null,
                request('pageSize'),
                request()->get('page')
            );
            if (Arr::get($result, 'code') === 0) {
                $posts = paginator(Arr::get($result, 'data'));

                return view('posts.blocking', compact('posts'));
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
    public function follows()
    {
        try {
            $result = Factory::content()->post->follows(
                request('pageSize'),
                request('page')
            );
            if (Arr::get($result, 'code') === 0) {
                $posts = paginator(Arr::get($result, 'data'));

                return view('posts.follows', compact('posts'));
            }

            if (Arr::get($result, 'message')) {
                return back()->with(['failure' => $result['message'], 'code' => $result['code']]);
            }
        } catch (\Exception $exception) {
            return back()->withErrors($exception->getMessage());
        }
    }

    /**
     * @return View
     */
    public function nearby(): View
    {
        return view('posts.nearby');
    }

    public function nearbyPosts(Request $request)
    {
        $validator = Validator::make($request->input(), [
            'longitude' => ['required', "regex:/^[-]?((((1[0-7][0-9])|([0-9]?[0-9]))\.(\d+))|180(\.0+)?)$/"],
            'latitude' => ['required', "regex:/^[-]?(([0-8]?[0-9])\.(\d+))|(90(\.0+)?)$/"],
        ]);

        if ($validator->fails()) {
            return Response::json(['message' => fs_lang('location').': '.fs_lang('errorUnknown')], 400);
        }
        try {
            $result = Factory::content()->post->nearbys(
                $request->input('longitude'),
                $request->input('latitude'),
                1,
                $request->input('searchType'),
                $request->input('searchKey'),
                $request->input('length'),
                $request->input('lengthUnits'),
                $request->input('pageSize'),
                $request->input('page'),
                $request->input('rankNumber'),
            );

            if (Arr::get($result, 'code') === 0) {
                $posts = paginator(Arr::get($result, 'data'));

                return view('posts.nearby-posts', compact('posts'));
            }

            if (Arr::get($result, 'message')) {
                return Response::json(['message' => Arr::get($result, 'message'), 'code' => $result['code']], 500);
            }
        } catch (\Exception $exception) {
            return Response::json(['message' => $exception->getMessage(), 'code' => $exception->getCode()], 500);
        }
    }

    /**
     * @param  string  $pid
     * @return Application|\Illuminate\Contracts\View\Factory|View|RedirectResponse
     *
     * @throws GuzzleException
     */
    public function location(string $pid)
    {
        try {
            $result = Factory::content()->post->detail($pid);

            if (Arr::get($result, 'code') !== 0) {
                return back()->with(['failure' => $result['message'], 'code' => $result['code']]);
            }
            $post = Arr::get($result, 'data.detail');

            $result = Factory::content()->post->nearbys(
                $post['location']['longitude'] ?? '',
                $post['location']['latitude'] ?? '',
                $post['location']['mapId'] ?? ''
            );

            if (Arr::get($result, 'code') !== 0) {
                return back()->with(['failure' => $result['message'], 'code' => $result['code']]);
            }

            $posts = paginator(Arr::get($result, 'data'));

            return view('posts.location', compact('posts', 'post'));
        } catch (\Exception $exception) {
            return back()->withErrors($exception->getMessage());
        }
    }

    /**
     * @param  string  $pid
     * @return Application|\Illuminate\Contracts\View\Factory|View|RedirectResponse
     *
     * @throws GuzzleException
     */
    public function detail(string $pid)
    {
        try {
            $result = Factory::content()->post->detail($pid);

            if (Arr::get($result, 'code') !== 0) {
                return back()->with(['failure' => $result['message'], 'code' => $result['code']]);
            }

            $post = Arr::get($result, 'data');

            $result = Factory::content()->comment->lists($pid, null, 'like', 2);

            if (Arr::get($result, 'code') !== 0) {
                return back()->with(['failure' => $result['message'], 'code' => $result['code']]);
            }

            $comments = paginator(Arr::get($result, 'data'));

            return view('posts.detail', compact('post', 'comments'));
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
            'pageSize',
            'page',
            'searchType',
            'searchKey',
            'searchUid',
            'searchGid',
            'searchHuri',
            'searchDigest',
            'searchSticky',
            'viewCountGt',
            'viewCountLt',
            'likeCountGt',
            'likeCountLt',
            'followCountGt',
            'followCountLt',
            'blockCountGt',
            'blockCountLt',
            'commentCountGt',
            'commentCountLt',
            'createdTimeGt',
            'createdTimeLt',
            'mapId',
            'longitude',
            'latitude',
            'sortType',
            'sortDirection',
            'rankNumber',
        ];
    }
}
