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

class CommentsController extends Controller
{
    /**
     * @return Application|\Illuminate\Contracts\View\Factory|View|RedirectResponse
     */
    public function index()
    {
        try {
            $defualtParams = fresnsengine_config('menu_comment_config');

            $params = [];

            foreach ($this->getListKeys() as $key) {
                $params[$key] = request($key) ?? (empty($defualtParams[$key]) ? null : $defualtParams[$key]);
            }

            $result = call_user_func_array([Factory::content()->comment, 'lists'], $params);

            if (Arr::get($result, 'code') === 0) {
                $comments = paginator(Arr::get($result, 'data'));

                return view('comments.index', compact('comments'));
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
            $defualtParams = fresnsengine_config('menu_comment_list_config');

            $params = [];

            foreach ($this->getListKeys() as $key) {
                $params[$key] = request($key) ?? (empty($defualtParams[$key]) ? null : $defualtParams[$key]);
            }

            $result = call_user_func_array([Factory::content()->comment, 'lists'], $params);

            if (Arr::get($result, 'code') === 0) {
                $comments = paginator(Arr::get($result, 'data'));

                return view('comments.list', compact('comments'));
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
            $result = Factory::user()->content->markLists(1, 5, user()->get('uid'));

            if (Arr::get($result, 'code') === 0) {
                $comments = paginator(Arr::get($result, 'data'));

                return view('comments.likes', compact('comments'));
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
            $result = Factory::user()->content->markLists(2, 5, user()->get('uid'));

            if (Arr::get($result, 'code') === 0) {
                $comments = paginator(Arr::get($result, 'data'));

                return view('comments.following', compact('comments'));
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
            $result = Factory::user()->content->markLists(3, 5, user()->get('uid'));

            if (Arr::get($result, 'code') === 0) {
                $comments = paginator(Arr::get($result, 'data'));

                return view('comments.blocking', compact('comments'));
            }

            if (Arr::get($result, 'message')) {
                return back()->with(['failure' => $result['message'], 'code' => $result['code']]);
            }
        } catch (\Exception $exception) {
            return back()->withErrors($exception->getMessage());
        }
    }

    /**
     * @param  string  $cid
     * @return Application|\Illuminate\Contracts\View\Factory|View|RedirectResponse
     *
     * @throws GuzzleException
     */
    public function detail(string $cid)
    {
        try {
            $result = Factory::content()->comment->detail($cid);

            if (Arr::get($result, 'code') !== 0) {
                return back()->with(['failure' => $result['message'], 'code' => $result['code']]);
            }

            $comment = Arr::get($result, 'data');

            $result = Factory::content()->comment->lists($comment['detail']['pid'], $cid, 'like', 2);

            if (Arr::get($result, 'code') !== 0) {
                return back()->with(['failure' => $result['message'], 'code' => $result['code']]);
            }

            $comments = paginator(Arr::get($result, 'data'));

            return view('comments.detail', compact('comment', 'comments'));
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
            'searchPid',
            'searchCid',
            'sortType',
            'sortDirection',
            'pageSize',
            'page',
            'searchType',
            'searchKey',
            'searchUid',
            'searchSticky',
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
        ];
    }
}
