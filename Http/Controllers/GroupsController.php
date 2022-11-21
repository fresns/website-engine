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
use Illuminate\Support\Facades\Cache;
use Plugins\FresnsEngine\Sdk\Factory;

class GroupsController extends Controller
{
    /**
     * @return Application|\Illuminate\Contracts\View\Factory|View|RedirectResponse
     *
     * @throws GuzzleException
     */
    public function index()
    {
        try {
            $defualtParams = fresnsengine_config('menu_group_config');

            $result = Factory::content()->group->trees(
                request('groupSize') ?? (empty($defualtParams['groupSize']) ? null : $defualtParams['groupSize']),
                request('pageSize') ?? (empty($defualtParams['pageSize']) ? null : $defualtParams['pageSize']),
                request('page') ?? (empty($defualtParams['page']) ? null : $defualtParams['page']),
            );

            if (Arr::get($result, 'code') === 0) {
                $groupTrees = paginator(Arr::get($result, 'data'));

                return view('groups.index', compact('groupTrees'));
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
            $defualtParams = fresnsengine_config('menu_group_list_config');

            $params = [];

            foreach ($this->getListKeys() as $key) {
                $params[$key] = request($key) ?? (empty($defualtParams[$key]) ? null : $defualtParams[$key]);
            }

            $params['type'] = 2;

            $result = call_user_func_array([Factory::content()->group, 'lists'], $params);

            if (Arr::get($result, 'code') === 0) {
                $groups = paginator(Arr::get($result, 'data'));

                return view('groups.list', compact('groups'));
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
            $result = Factory::user()->content->markLists(1, 2, user()->get('uid'));

            if (Arr::get($result, 'code') === 0) {
                $groups = paginator(Arr::get($result, 'data'));

                return view('groups.likes', compact('groups'));
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
            $result = Factory::user()->content->markLists(2, 2, user()->get('uid'));

            if (Arr::get($result, 'code') === 0) {
                $groups = paginator(Arr::get($result, 'data'));

                return view('groups.following', compact('groups'));
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
            $result = Factory::user()->content->markLists(3, 2, user()->get('uid'));

            if (Arr::get($result, 'code') === 0) {
                $groups = paginator(Arr::get($result, 'data'));

                return view('groups.blocking', compact('groups'));
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
                'group'
            );

            if (Arr::get($result, 'code') === 0) {
                $posts = paginator(Arr::get($result, 'data'));

                return view('groups.follow', compact('posts'));
            }

            if (Arr::get($result, 'message')) {
                return back()->with(['failure' => $result['message'], 'code' => $result['code']]);
            }
        } catch (\Exception $exception) {
            return back()->withErrors($exception->getMessage());
        }
    }

    /**
     * @param  string  $gid
     * @return Application|\Illuminate\Contracts\View\Factory|View|RedirectResponse
     *
     * @throws GuzzleException
     */
    public function detail(string $gid)
    {
        try {
            $result = Factory::content()->group->detail($gid);

            if (Arr::get($result, 'code') !== 0) {
                return back()->with(['failure' => $result['message'], 'code' => $result['code']]);
            }

            $group = Arr::get($result, 'data');

            $result = Factory::content()->post->lists(
                request()->get('pageSize'),
                request()->get('page'),
                request()->get('searchType'),
                request()->get('searchKey'),
                null,
                $gid
            );

            if (Arr::get($result, 'code') !== 0) {
                return back()->with(['failure' => $result['message'], 'code' => $result['code']]);
            }

            $posts = paginator(Arr::get($result, 'data'));

            return view('groups.detail', compact('group', 'posts'));
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
            'type',
            'pageSize',
            'page',
            'searchKey',
            'parentGid',
            'recommend',
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
