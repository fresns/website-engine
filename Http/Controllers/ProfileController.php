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

class ProfileController extends Controller
{
    /**
     * @param  string  $username
     * @return View
     *
     * @throws GuzzleException
     */
    public function index(string $username): View
    {
        $user = Arr::get(Factory::user()->auth->detail(null, $username), 'data.detail');
        $posts = paginator(Arr::get(Factory::content()->post->lists(
            request()->get('pageSize'),
            request()->get('page'),
            request()->get('searchType'),
            request()->get('searchKey'),
            $user['uid'],
        ), 'data'));

        return view('profile.index', compact('user', 'posts'));
    }

    /**
     * @param  string  $username
     * @return Application|\Illuminate\Contracts\View\Factory|View|RedirectResponse
     *
     * @throws GuzzleException
     */
    public function posts(string $username)
    {
        $user = Arr::get(Factory::user()->auth->detail(null, $username), 'data.detail');
        try {
            $result = Factory::content()->post->lists(
                request()->get('pageSize'),
                request()->get('page'),
                request()->get('searchType'),
                request()->get('searchKey'),
                $user['uid'],
            );

            if (Arr::get($result, 'code') === 0) {
                $posts = paginator(Arr::get($result, 'data'));

                return view('profile.posts', compact('user', 'posts'));
            }

            if (Arr::get($result, 'message')) {
                return back()->with(['failure' => $result['message'], 'code' => $result['code']]);
            }
        } catch (\Exception $exception) {
            return back()->withErrors($exception->getMessage());
        }
    }

    /**
     * @param  string  $username
     * @return Application|\Illuminate\Contracts\View\Factory|View|RedirectResponse
     *
     * @throws GuzzleException
     */
    public function comments(string $username)
    {
        $user = Arr::get(Factory::user()->auth->detail(null, $username), 'data.detail');
        try {
            $result = Factory::content()->comment->lists(
                request('searchPid'),
                request('searchCid'),
                request('sortType'),
                request('sortDirection'),
                request('pageSize'),
                request('page'),
                null,
                null,
                $user['uid']
            );

            if (Arr::get($result, 'code') === 0) {
                $comments = paginator(Arr::get($result, 'data'));

                return view('profile.comments', compact('user', 'comments'));
            }

            if (Arr::get($result, 'message')) {
                return back()->with(['failure' => $result['message'], 'code' => $result['code']]);
            }
        } catch (\Exception $exception) {
            return back()->withErrors($exception->getMessage());
        }
    }

    /**
     * @param  string  $username
     * @return Application|\Illuminate\Contracts\View\Factory|View|RedirectResponse
     *
     * @throws GuzzleException
     */
    public function likers(string $username)
    {
        $user = Arr::get(Factory::user()->auth->detail(null, $username), 'data.detail');
        try {
            $result = Factory::user()->content->interactions(
                1,
                1,
                $user['uid'],
                request('sortDirection'),
                request('pageSize'),
                request('page'),
            );
            if (Arr::get($result, 'code') === 0) {
                $users = paginator(Arr::get($result, 'data'));

                return view('profile.likers', compact('user', 'users'));
            }

            if (Arr::get($result, 'message')) {
                return back()->with(['failure' => $result['message'], 'code' => $result['code']]);
            }
        } catch (\Exception $exception) {
            return back()->withErrors($exception->getMessage());
        }
    }

    /**
     * @param  string  $username
     * @return Application|\Illuminate\Contracts\View\Factory|View|RedirectResponse
     *
     * @throws GuzzleException
     */
    public function followers(string $username)
    {
        $user = Arr::get(Factory::user()->auth->detail(null, $username), 'data.detail');

        try {
            $result = Factory::user()->content->interactions(
                2,
                1,
                $user['uid'],
                request('sortDirection'),
                request('pageSize'),
                request('page'),
            );
            if (Arr::get($result, 'code') === 0) {
                $users = paginator(Arr::get($result, 'data'));

                return view('profile.followers', compact('user', 'users'));
            }

            if (Arr::get($result, 'message')) {
                return back()->with(['failure' => $result['message'], 'code' => $result['code']]);
            }
        } catch (\Exception $exception) {
            return back()->withErrors($exception->getMessage());
        }
    }

    /**
     * @param  string  $username
     * @return Application|\Illuminate\Contracts\View\Factory|View|RedirectResponse
     *
     * @throws GuzzleException
     */
    public function blockers(string $username)
    {
        $user = Arr::get(Factory::user()->auth->detail(null, $username), 'data.detail');
        try {
            $result = Factory::user()->content->interactions(
                3,
                1,
                $user['uid'],
                request('sortDirection'),
                request('pageSize'),
                request('page'),
            );
            if (Arr::get($result, 'code') === 0) {
                $users = paginator(Arr::get($result, 'data'));

                return view('profile.blockers', compact('user', 'users'));
            }

            if (Arr::get($result, 'message')) {
                return back()->with(['failure' => $result['message'], 'code' => $result['code']]);
            }
        } catch (\Exception $exception) {
            return back()->withErrors($exception->getMessage());
        }
    }

    /**
     * @param  string  $username
     * @return Application|\Illuminate\Contracts\View\Factory|View|RedirectResponse
     *
     * @throws GuzzleException
     */
    public function likeUsers(string $username)
    {
        $user = Arr::get(Factory::user()->auth->detail(null, $username), 'data.detail');
        try {
            $result = Factory::user()->content->markLists(
                1,
                1,
                $user['uid'],
                null,
                request('pageSize'),
                request('page'),
            );

            if (Arr::get($result, 'code') === 0) {
                $users = paginator(Arr::get($result, 'data'));

                return view('profile.likes_users', compact('user', 'users'));
            }

            if (Arr::get($result, 'message')) {
                return back()->with(['failure' => $result['message'], 'code' => $result['code']]);
            }
        } catch (\Exception $exception) {
            return back()->withErrors($exception->getMessage());
        }
    }

    /**
     * @param  string  $username
     * @return Application|\Illuminate\Contracts\View\Factory|View|RedirectResponse
     *
     * @throws GuzzleException
     */
    public function likeGroups(string $username)
    {
        $user = Arr::get(Factory::user()->auth->detail(null, $username), 'data.detail');
        try {
            $result = Factory::user()->content->markLists(
                1,
                2,
                $user['uid'],
                null,
                request('pageSize'),
                request('page'),
            );

            if (Arr::get($result, 'code') === 0) {
                $groups = paginator(Arr::get($result, 'data'));

                return view('profile.likes_groups', compact('user', 'groups'));
            }

            if (Arr::get($result, 'message')) {
                return back()->with(['failure' => $result['message'], 'code' => $result['code']]);
            }
        } catch (\Exception $exception) {
            return back()->withErrors($exception->getMessage());
        }
    }

    /**
     * @param  string  $username
     * @return Application|\Illuminate\Contracts\View\Factory|View|RedirectResponse
     *
     * @throws GuzzleException
     */
    public function likeHashtags(string $username)
    {
        $user = Arr::get(Factory::user()->auth->detail(null, $username), 'data.detail');

        try {
            $result = Factory::user()->content->markLists(
                1,
                3,
                $user['uid'],
                null,
                request('pageSize'),
                request('page'),
            );

            if (Arr::get($result, 'code') === 0) {
                $hashtags = paginator(Arr::get($result, 'data'));

                return view('profile.likes_hashtags', compact('user', 'hashtags'));
            }

            if (Arr::get($result, 'message')) {
                return back()->with(['failure' => $result['message'], 'code' => $result['code']]);
            }
        } catch (\Exception $exception) {
            return back()->withErrors($exception->getMessage());
        }
    }

    /**
     * @param  string  $username
     * @return Application|\Illuminate\Contracts\View\Factory|View|RedirectResponse
     *
     * @throws GuzzleException
     */
    public function likePosts(string $username)
    {
        $user = Arr::get(Factory::user()->auth->detail(null, $username), 'data.detail');

        try {
            $result = Factory::user()->content->markLists(
                1,
                4,
                $user['uid'],
                null,
                request('pageSize'),
                request('page'),
            );

            if (Arr::get($result, 'code') === 0) {
                $posts = paginator(Arr::get($result, 'data'));

                return view('profile.likes_posts', compact('user', 'posts'));
            }

            if (Arr::get($result, 'message')) {
                return back()->with(['failure' => $result['message'], 'code' => $result['code']]);
            }
        } catch (\Exception $exception) {
            return back()->withErrors($exception->getMessage());
        }
    }

    /**
     * @param  string  $username
     * @return Application|\Illuminate\Contracts\View\Factory|View|RedirectResponse
     *
     * @throws GuzzleException
     */
    public function likeComments(string $username)
    {
        $user = Arr::get(Factory::user()->auth->detail(null, $username), 'data.detail');

        try {
            $result = Factory::user()->content->markLists(
                1,
                5,
                $user['uid'],
                null,
                request('pageSize'),
                request('page'),
            );

            if (Arr::get($result, 'code') === 0) {
                $comments = paginator(Arr::get($result, 'data'));

                return view('profile.likes_comments', compact('user', 'comments'));
            }

            if (Arr::get($result, 'message')) {
                return back()->with(['failure' => $result['message'], 'code' => $result['code']]);
            }
        } catch (\Exception $exception) {
            return back()->withErrors($exception->getMessage());
        }
    }

    /**
     * @param  string  $username
     * @return Application|\Illuminate\Contracts\View\Factory|View|RedirectResponse
     *
     * @throws GuzzleException
     */
    public function followUsers(string $username)
    {
        $user = Arr::get(Factory::user()->auth->detail(null, $username), 'data.detail');

        try {
            $result = Factory::user()->content->markLists(
                2,
                1,
                $user['uid'],
                null,
                request('pageSize'),
                request('page'),
            );

            if (Arr::get($result, 'code') === 0) {
                $users = paginator(Arr::get($result, 'data'));

                return view('profile.following_users', compact('user', 'users'));
            }

            if (Arr::get($result, 'message')) {
                return back()->with(['failure' => $result['message'], 'code' => $result['code']]);
            }
        } catch (\Exception $exception) {
            return back()->withErrors($exception->getMessage());
        }
    }

    /**
     * @param  string  $username
     * @return Application|\Illuminate\Contracts\View\Factory|View|RedirectResponse
     *
     * @throws GuzzleException
     */
    public function followGroups(string $username)
    {
        $user = Arr::get(Factory::user()->auth->detail(null, $username), 'data.detail');

        try {
            $result = Factory::user()->content->markLists(
                2,
                2,
                $user['uid'],
                null,
                request('pageSize'),
                request('page'),
            );

            if (Arr::get($result, 'code') === 0) {
                $groups = paginator(Arr::get($result, 'data'));

                return view('profile.following_groups', compact('user', 'groups'));
            }

            if (Arr::get($result, 'message')) {
                return back()->with(['failure' => $result['message'], 'code' => $result['code']]);
            }
        } catch (\Exception $exception) {
            return back()->withErrors($exception->getMessage());
        }
    }

    /**
     * @param  string  $username
     * @return Application|\Illuminate\Contracts\View\Factory|View|RedirectResponse
     *
     * @throws GuzzleException
     */
    public function followHashtags(string $username)
    {
        $user = Arr::get(Factory::user()->auth->detail(null, $username), 'data.detail');

        try {
            $result = Factory::user()->content->markLists(
                2,
                3,
                $user['uid'],
                null,
                request('pageSize'),
                request('page'),
            );

            if (Arr::get($result, 'code') === 0) {
                $hashtags = paginator(Arr::get($result, 'data'));

                return view('profile.following_hashtags', compact('user', 'hashtags'));
            }

            if (Arr::get($result, 'message')) {
                return back()->with(['failure' => $result['message'], 'code' => $result['code']]);
            }
        } catch (\Exception $exception) {
            return back()->withErrors($exception->getMessage());
        }
    }

    /**
     * @param  string  $username
     * @return Application|\Illuminate\Contracts\View\Factory|View|RedirectResponse
     *
     * @throws GuzzleException
     */
    public function followPosts(string $username)
    {
        $user = Arr::get(Factory::user()->auth->detail(null, $username), 'data.detail');

        try {
            $result = Factory::user()->content->markLists(
                2,
                4,
                $user['uid'],
                null,
                request('pageSize'),
                request('page'),
            );

            if (Arr::get($result, 'code') === 0) {
                $posts = paginator(Arr::get($result, 'data'));

                return view('profile.following_posts', compact('user', 'posts'));
            }

            if (Arr::get($result, 'message')) {
                return back()->with(['failure' => $result['message'], 'code' => $result['code']]);
            }
        } catch (\Exception $exception) {
            return back()->withErrors($exception->getMessage());
        }
    }

    /**
     * @param  string  $username
     * @return Application|\Illuminate\Contracts\View\Factory|View|RedirectResponse
     *
     * @throws GuzzleException
     */
    public function followComments(string $username)
    {
        $user = Arr::get(Factory::user()->auth->detail(null, $username), 'data.detail');

        try {
            $result = Factory::user()->content->markLists(
                2,
                5,
                $user['uid'],
                null,
                request('pageSize'),
                request('page'),
            );

            if (Arr::get($result, 'code') === 0) {
                $comments = paginator(Arr::get($result, 'data'));

                return view('profile.following_comments', compact('user', 'comments'));
            }

            if (Arr::get($result, 'message')) {
                return back()->with(['failure' => $result['message'], 'code' => $result['code']]);
            }
        } catch (\Exception $exception) {
            return back()->withErrors($exception->getMessage());
        }
    }

    /**
     * @param  string  $username
     * @return Application|\Illuminate\Contracts\View\Factory|View|RedirectResponse
     *
     * @throws GuzzleException
     */
    public function blockUsers(string $username)
    {
        $user = Arr::get(Factory::user()->auth->detail(null, $username), 'data.detail');

        try {
            $result = Factory::user()->content->markLists(
                3,
                1,
                $user['uid'],
                null,
                request('pageSize'),
                request('page'),
            );

            if (Arr::get($result, 'code') === 0) {
                $users = paginator(Arr::get($result, 'data'));

                return view('profile.blocking_users', compact('user', 'users'));
            }

            if (Arr::get($result, 'message')) {
                return back()->with(['failure' => $result['message'], 'code' => $result['code']]);
            }
        } catch (\Exception $exception) {
            return back()->withErrors($exception->getMessage());
        }
    }

    /**
     * @param  string  $username
     * @return Application|\Illuminate\Contracts\View\Factory|View|RedirectResponse
     *
     * @throws GuzzleException
     */
    public function blockGroups(string $username)
    {
        $user = Arr::get(Factory::user()->auth->detail(null, $username), 'data.detail');

        try {
            $result = Factory::user()->content->markLists(
                3,
                2,
                $user['uid'],
                null,
                request('pageSize'),
                request('page'),
            );

            if (Arr::get($result, 'code') === 0) {
                $groups = paginator(Arr::get($result, 'data'));

                return view('profile.blocking_groups', compact('user', 'groups'));
            }

            if (Arr::get($result, 'message')) {
                return back()->with(['failure' => $result['message'], 'code' => $result['code']]);
            }
        } catch (\Exception $exception) {
            return back()->withErrors($exception->getMessage());
        }
    }

    /**
     * @param  string  $username
     * @return Application|\Illuminate\Contracts\View\Factory|View|RedirectResponse
     *
     * @throws GuzzleException
     */
    public function blockHashtags(string $username)
    {
        $user = Arr::get(Factory::user()->auth->detail(null, $username), 'data.detail');

        try {
            $result = Factory::user()->content->markLists(
                3,
                3,
                $user['uid'],
                null,
                request('pageSize'),
                request('page'),
            );

            if (Arr::get($result, 'code') === 0) {
                $hashtags = paginator(Arr::get($result, 'data'));

                return view('profile.blocking_hashtags', compact('user', 'hashtags'));
            }

            if (Arr::get($result, 'message')) {
                return back()->with(['failure' => $result['message'], 'code' => $result['code']]);
            }
        } catch (\Exception $exception) {
            return back()->withErrors($exception->getMessage());
        }
    }

    /**
     * @param  string  $username
     * @return Application|\Illuminate\Contracts\View\Factory|View|RedirectResponse
     *
     * @throws GuzzleException
     */
    public function blockPosts(string $username)
    {
        $user = Arr::get(Factory::user()->auth->detail(null, $username), 'data.detail');

        try {
            $result = Factory::user()->content->markLists(
                3,
                4,
                $user['uid'],
                null,
                request('pageSize'),
                request('page'),
            );

            if (Arr::get($result, 'code') === 0) {
                $posts = paginator(Arr::get($result, 'data'));

                return view('profile.blocking_posts', compact('user', 'posts'));
            }

            if (Arr::get($result, 'message')) {
                return back()->with(['failure' => $result['message'], 'code' => $result['code']]);
            }
        } catch (\Exception $exception) {
            return back()->withErrors($exception->getMessage());
        }
    }

    /**
     * @param  string  $username
     * @return Application|\Illuminate\Contracts\View\Factory|View|RedirectResponse
     *
     * @throws GuzzleException
     */
    public function blockComments(string $username)
    {
        $user = Arr::get(Factory::user()->auth->detail(null, $username), 'data.detail');

        try {
            $result = Factory::user()->content->markLists(
                3,
                5,
                $user['uid'],
                null,
                request('pageSize'),
                request('page'),
            );

            if (Arr::get($result, 'code') === 0) {
                $comments = paginator(Arr::get($result, 'data'));

                return view('profile.blocking_comments', compact('user', 'comments'));
            }

            if (Arr::get($result, 'message')) {
                return back()->with(['failure' => $result['message'], 'code' => $result['code']]);
            }
        } catch (\Exception $exception) {
            return back()->withErrors($exception->getMessage());
        }
    }
}
