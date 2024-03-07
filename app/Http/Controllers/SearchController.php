<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jevan Tang
 * Released under the Apache-2.0 License.
 */

namespace Fresns\WebEngine\Http\Controllers;

use Fresns\WebEngine\Helpers\QueryHelper;
use Fresns\WebEngine\Interfaces\SearchInterface;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    // index
    public function index(Request $request)
    {
        $searchType = $request->get('searchType');
        $searchKey = $request->get('searchKey');

        if (empty($searchType)) {
            return view('search.index');
        }

        switch ($searchType) {
            // user
            case 'user':
                return redirect()->to(fs_route(route('fresns.search.users', ['searchKey' => $searchKey])));
                break;

                // group
            case 'group':
                return redirect()->to(fs_route(route('fresns.search.groups', ['searchKey' => $searchKey])));
                break;

                // hashtag
            case 'hashtag':
                return redirect()->to(fs_route(route('fresns.search.hashtags', ['searchKey' => $searchKey])));
                break;

                // geotag
            case 'geotag':
                return redirect()->to(fs_route(route('fresns.search.geotags', ['searchKey' => $searchKey])));
                break;

                // post
            case 'post':
                return redirect()->to(fs_route(route('fresns.search.posts', ['searchKey' => $searchKey])));
                break;

                // comment
            case 'comment':
                return redirect()->to(fs_route(route('fresns.search.comments', ['searchKey' => $searchKey])));
                break;

                // default
            default:
                return view('search.index');
                break;
        }
    }

    // users
    public function users(Request $request)
    {
        $query = $request->all();

        $result = SearchInterface::search('users', $query);

        $users = QueryHelper::convertApiDataToPaginate(
            items: $result['data']['list'],
            pagination: $result['data']['pagination'],
        );

        return view('search.users', compact('users'));
    }

    // groups
    public function groups(Request $request)
    {
        $query = $request->all();

        $result = SearchInterface::search('groups', $query);

        $groups = QueryHelper::convertApiDataToPaginate(
            items: $result['data']['list'],
            pagination: $result['data']['pagination'],
        );

        return view('search.groups', compact('groups'));
    }

    // hashtags
    public function hashtags(Request $request)
    {
        $query = $request->all();

        $result = SearchInterface::search('hashtags', $query);

        $hashtags = QueryHelper::convertApiDataToPaginate(
            items: $result['data']['list'],
            pagination: $result['data']['pagination'],
        );

        return view('search.hashtags', compact('hashtags'));
    }

    // geotags
    public function geotags(Request $request)
    {
        $query = $request->all();

        $result = SearchInterface::search('geotags', $query);

        $geotags = QueryHelper::convertApiDataToPaginate(
            items: $result['data']['list'],
            pagination: $result['data']['pagination'],
        );

        return view('search.geotags', compact('geotags'));
    }

    // posts
    public function posts(Request $request)
    {
        $query = $request->all();

        $result = SearchInterface::search('posts', $query);

        $posts = QueryHelper::convertApiDataToPaginate(
            items: $result['data']['list'],
            pagination: $result['data']['pagination'],
        );

        return view('search.posts', compact('posts'));
    }

    // comments
    public function comments(Request $request)
    {
        $query = $request->all();

        $result = SearchInterface::search('comments', $query);

        $comments = QueryHelper::convertApiDataToPaginate(
            items: $result['data']['list'],
            pagination: $result['data']['pagination'],
        );

        return view('search.comments', compact('comments'));
    }
}
