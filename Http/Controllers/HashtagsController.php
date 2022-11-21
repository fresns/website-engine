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
use Illuminate\Support\Arr;
use Plugins\FresnsEngine\Sdk\Factory;

class HashtagsController extends Controller
{
    /**
     * @return Application|\Illuminate\Contracts\View\Factory|View|RedirectResponse
     */
    public function index()
    {
        try {
            $defualtParams = fresnsengine_config('menu_hashtag_config');

            $params = [];

            foreach ($this->getListKeys() as $key) {
                $params[$key] = request($key) ?? (empty($defualtParams[$key]) ? null : $defualtParams[$key]);
            }

            $result = call_user_func_array([Factory::content()->hashtag, 'lists'], $params);

            if (Arr::get($result, 'code') === 0) {
                $hashtags = paginator(Arr::get($result, 'data'));

                return view('hashtags.index', compact('hashtags'));
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
            $defualtParams = fresnsengine_config('menu_hashtag_list_config');

            $params = [];

            foreach ($this->getListKeys() as $key) {
                $params[$key] = request($key) ?? (empty($defualtParams[$key]) ? null : $defualtParams[$key]);
            }

            $result = call_user_func_array([Factory::content()->hashtag, 'lists'], $params);

            if (Arr::get($result, 'code') === 0) {
                $hashtags = paginator(Arr::get($result, 'data'));

                return view('hashtags.list', compact('hashtags'));
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
            $result = Factory::user()->content->markLists(1, 3, user()->get('uid'));

            if (Arr::get($result, 'code') === 0) {
                $hashtags = paginator(Arr::get($result, 'data'));

                return view('hashtags.likes', compact('hashtags'));
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
            $result = Factory::user()->content->markLists(2, 3, user()->get('uid'));

            if (Arr::get($result, 'code') === 0) {
                $hashtags = paginator(Arr::get($result, 'data'));

                return view('hashtags.following', compact('hashtags'));
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
            $result = Factory::user()->content->markLists(3, 3, user()->get('uid'));

            if (Arr::get($result, 'code') === 0) {
                $hashtags = paginator(Arr::get($result, 'data'));

                return view('hashtags.blocking', compact('hashtags'));
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
                'hashtag'
            );

            if (Arr::get($result, 'code') === 0) {
                $posts = paginator(Arr::get($result, 'data'));

                return view('hashtags.follow', compact('posts'));
            }

            if (Arr::get($result, 'message')) {
                return back()->with(['failure' => $result['message'], 'code' => $result['code']]);
            }
        } catch (\Exception $exception) {
            return back()->withErrors($exception->getMessage());
        }
    }

    /**
     * @param  string  $hrui
     * @return Application|\Illuminate\Contracts\View\Factory|View|RedirectResponse
     *
     * @throws GuzzleException
     */
    public function detail(string $hrui)
    {
        try {
            $result = Factory::content()->hashtag->detail($hrui);

            if (Arr::get($result, 'code') !== 0) {
                return back()->with(['failure' => $result['message'], 'code' => $result['code']]);
            }

            $hashtag = Arr::get($result, 'data');

            $result = Factory::content()->post->lists(
                request()->get('pageSize'),
                request()->get('page'),
                request()->get('searchType'),
                request()->get('searchKey'),
                null,
                null,
                $hrui
            );

            if (Arr::get($result, 'code') !== 0) {
                return back()->with(['failure' => $result['message'], 'code' => $result['code']]);
            }

            $posts = paginator(Arr::get($result, 'data'));

            return view('hashtags.detail', compact('hashtag', 'posts'));
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
            'searchKey',
            'viewCountGt',
            'viewCountLt',
            'likeCountGt',
            'likeCountLt',
            'followCountGt',
            'followCountLt',
            'blockCountGt',
            'blockCountLt',
            'postCountGt',
            'postCountLt',
            'digestCountGt',
            'digestCountLt',
            'createdTimeGt',
            'createdTimeLt',
            'sortType',
            'sortDirection',
        ];
    }
}
