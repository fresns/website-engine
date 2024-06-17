<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jevan Tang
 * Released under the Apache-2.0 License.
 */

namespace Fresns\WebsiteEngine\Http\Controllers;

use App\Helpers\ConfigHelper;
use Browser;
use Fresns\WebsiteEngine\Helpers\QueryHelper;
use Fresns\WebsiteEngine\Interfaces\CommentInterface;
use Fresns\WebsiteEngine\Interfaces\GeotagInterface;
use Fresns\WebsiteEngine\Interfaces\GroupInterface;
use Fresns\WebsiteEngine\Interfaces\HashtagInterface;
use Fresns\WebsiteEngine\Interfaces\PostInterface;
use Fresns\WebsiteEngine\Interfaces\UserInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;

class HomeController extends Controller
{
    public function index(Request $request)
    {
        $defaultHomepage = ConfigHelper::fresnsConfigByItemKey('default_homepage');

        switch ($defaultHomepage) {
            case 'user':
                $query = QueryHelper::convertOptionToRequestParam(QueryHelper::TYPE_USER, $request->all());

                $result = UserInterface::list($query);

                // ajax
                if ($request->ajax()) {
                    $html = '';
                    foreach ($result['data']['list'] as $user) {
                        $html .= View::make('components.users.list', compact('user'))->render();
                    }

                    return response()->json([
                        'pagination' => $result['data']['pagination'],
                        'html' => $html,
                    ]);
                }

                // view
                $users = QueryHelper::convertApiDataToPaginate(
                    items: $result['data']['list'],
                    pagination: $result['data']['pagination'],
                );

                return view('users.index', compact('users'));
                break;

            case 'group':
                $indexType = fs_config('channel_group_type') ?? 'tree';

                $groupTree = [];
                $groups = [];

                if ($indexType == 'tree') {
                    $result = GroupInterface::tree();

                    $groupTree = $result['data'];

                    // view
                    return view('groups.index', compact('groupTree', 'groups'));
                }

                $query = QueryHelper::convertOptionToRequestParam(QueryHelper::TYPE_GROUP, $request->all());

                $result = GroupInterface::list($query);

                // ajax
                if ($request->ajax()) {
                    $html = '';
                    foreach ($result['data']['list'] as $group) {
                        $html .= View::make('components.groups.list', compact('group'))->render();
                    }

                    return response()->json([
                        'pagination' => $result['data']['pagination'],
                        'html' => $html,
                    ]);
                }

                // view
                $groups = QueryHelper::convertApiDataToPaginate(
                    items: $result['data']['list'],
                    pagination: $result['data']['pagination'],
                );

                return view('groups.index', compact('groupTree', 'groups'));
                break;

            case 'hashtag':
                $query = QueryHelper::convertOptionToRequestParam(QueryHelper::TYPE_HASHTAG, $request->all());

                $result = HashtagInterface::list($query);

                // ajax
                if ($request->ajax()) {
                    $html = '';
                    foreach ($result['data']['list'] as $hashtag) {
                        $html .= View::make('components.hashtags.list', compact('hashtag'))->render();
                    }

                    return response()->json([
                        'pagination' => $result['data']['pagination'],
                        'html' => $html,
                    ]);
                }

                // view
                $hashtags = QueryHelper::convertApiDataToPaginate(
                    items: $result['data']['list'],
                    pagination: $result['data']['pagination'],
                );

                return view('hashtags.index', compact('hashtags'));
                break;

            case 'geotag':
                $query = QueryHelper::convertOptionToRequestParam(QueryHelper::TYPE_GEOTAG, $request->all());

                $result = GeotagInterface::list($query);

                // ajax
                if ($request->ajax()) {
                    $html = '';
                    foreach ($result['data']['list'] as $geotag) {
                        $html .= View::make('components.geotags.list', compact('geotag'))->render();
                    }

                    return response()->json([
                        'pagination' => $result['data']['pagination'],
                        'html' => $html,
                    ]);
                }

                // view
                $geotags = QueryHelper::convertApiDataToPaginate(
                    items: $result['data']['list'],
                    pagination: $result['data']['pagination'],
                );

                return view('geotags.index', compact('geotags'));
                break;

            case 'post':
                $query = QueryHelper::convertOptionToRequestParam(QueryHelper::TYPE_POST, $request->all());

                $result = PostInterface::list($query);

                // ajax
                if ($request->ajax()) {
                    $html = '';
                    foreach ($result['data']['list'] as $post) {
                        $html .= View::make('components.posts.list', compact('post'))->render();
                    }

                    return response()->json([
                        'pagination' => $result['data']['pagination'],
                        'html' => $html,
                    ]);
                }

                // view
                $posts = QueryHelper::convertApiDataToPaginate(
                    items: $result['data']['list'],
                    pagination: $result['data']['pagination'],
                );

                return view('posts.index', compact('posts'));
                break;

            case 'comment':
                $query = QueryHelper::convertOptionToRequestParam(QueryHelper::TYPE_COMMENT, $request->all());

                $result = CommentInterface::list($query);

                // ajax
                if ($request->ajax()) {
                    $html = '';
                    foreach ($result['data']['list'] as $comment) {
                        $html .= View::make('components.comments.list', compact('comment'))->render();
                    }

                    return response()->json([
                        'pagination' => $result['data']['pagination'],
                        'html' => $html,
                    ]);
                }

                // view
                $comments = QueryHelper::convertApiDataToPaginate(
                    items: $result['data']['list'],
                    pagination: $result['data']['pagination'],
                );

                return view('comments.index', compact('comments'));
                break;

            default:
                $portalContent = Browser::isMobile() ? ConfigHelper::fresnsConfigByItemKey('portal_3') : ConfigHelper::fresnsConfigByItemKey('portal_2');

                $content = ConfigHelper::fresnsConfigByItemKey('portal_4') ?? $portalContent;

                return view('portal.index', compact('content'));
                break;
        }
    }
}
