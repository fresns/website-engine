<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jevan Tang
 * Released under the Apache-2.0 License.
 */

namespace Fresns\WebsiteEngine\Http\Controllers;

use Fresns\WebsiteEngine\Helpers\QueryHelper;
use Fresns\WebsiteEngine\Interfaces\TimelineInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\View;

class TimelineController extends Controller
{
    // index
    public function index()
    {
        $channelType = fs_config('channel_timeline_type');

        $redirectURL = match ($channelType) {
            'posts' => fs_route(route('fresns.timeline.posts')),
            'comments' => fs_route(route('fresns.timeline.comments')),
        };

        return redirect()->intended($redirectURL);
    }

    // posts
    public function posts(Request $request)
    {
        if (! fs_config('channel_timeline_posts_status')) {
            return Response::view('404', [], 404);
        }

        $query = $request->all();

        $result = TimelineInterface::posts($query);

        // ajax
        if ($request->ajax()) {
            $html = '';
            foreach ($result['data']['list'] as $post) {
                $html .= View::make('components.post.list', compact('post'))->render();
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

        return view('timelines.posts', compact('posts'));
    }

    // user posts
    public function userPosts(Request $request)
    {
        if (! fs_config('channel_timeline_posts_status')) {
            return Response::view('404', [], 404);
        }

        $query = $request->all();
        $query['type'] = 'user';

        $result = TimelineInterface::posts($query);

        // ajax
        if ($request->ajax()) {
            $html = '';
            foreach ($result['data']['list'] as $post) {
                $html .= View::make('components.post.list', compact('post'))->render();
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

        return view('timelines.user-posts', compact('posts'));
    }

    // group posts
    public function groupPosts(Request $request)
    {
        if (! fs_config('channel_timeline_posts_status')) {
            return Response::view('404', [], 404);
        }

        $query = $request->all();
        $query['type'] = 'group';

        $result = TimelineInterface::posts($query);

        // ajax
        if ($request->ajax()) {
            $html = '';
            foreach ($result['data']['list'] as $post) {
                $html .= View::make('components.post.list', compact('post'))->render();
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

        return view('timelines.group-posts', compact('posts'));
    }

    // hashtag posts
    public function hashtagPosts(Request $request)
    {
        if (! fs_config('channel_timeline_posts_status')) {
            return Response::view('404', [], 404);
        }

        $query = $request->all();
        $query['type'] = 'hashtag';

        $result = TimelineInterface::posts($query);

        // ajax
        if ($request->ajax()) {
            $html = '';
            foreach ($result['data']['list'] as $post) {
                $html .= View::make('components.post.list', compact('post'))->render();
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

        return view('timelines.hashtag-posts', compact('posts'));
    }

    // geotag posts
    public function geotagPosts(Request $request)
    {
        if (! fs_config('channel_timeline_posts_status')) {
            return Response::view('404', [], 404);
        }

        $query = $request->all();
        $query['type'] = 'geotag';

        $result = TimelineInterface::posts($query);

        // ajax
        if ($request->ajax()) {
            $html = '';
            foreach ($result['data']['list'] as $post) {
                $html .= View::make('components.post.list', compact('post'))->render();
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

        return view('timelines.geotag-posts', compact('posts'));
    }

    // comments
    public function comments(Request $request)
    {
        if (! fs_config('channel_timeline_comments_status')) {
            return Response::view('404', [], 404);
        }

        $query = $request->all();

        $result = TimelineInterface::comments($query);

        // ajax
        if ($request->ajax()) {
            $html = '';
            foreach ($result['data']['list'] as $comment) {
                $html .= View::make('components.comment.list', compact('comment'))->render();
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

        return view('timelines.comments', compact('comments'));
    }

    // user comments
    public function userComments(Request $request)
    {
        if (! fs_config('channel_timeline_comments_status')) {
            return Response::view('404', [], 404);
        }

        $query = $request->all();
        $query['type'] = 'user';

        $result = TimelineInterface::comments($query);

        // ajax
        if ($request->ajax()) {
            $html = '';
            foreach ($result['data']['list'] as $comment) {
                $html .= View::make('components.comment.list', compact('comment'))->render();
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

        return view('timelines.user-comments', compact('comments'));
    }

    // group comments
    public function groupComments(Request $request)
    {
        if (! fs_config('channel_timeline_comments_status')) {
            return Response::view('404', [], 404);
        }

        $query = $request->all();
        $query['type'] = 'group';

        $result = TimelineInterface::comments($query);

        // ajax
        if ($request->ajax()) {
            $html = '';
            foreach ($result['data']['list'] as $comment) {
                $html .= View::make('components.comment.list', compact('comment'))->render();
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

        return view('timelines.group-comments', compact('comments'));
    }

    // hashtag comments
    public function hashtagComments(Request $request)
    {
        if (! fs_config('channel_timeline_comments_status')) {
            return Response::view('404', [], 404);
        }

        $query = $request->all();
        $query['type'] = 'hashtag';

        $result = TimelineInterface::comments($query);

        // ajax
        if ($request->ajax()) {
            $html = '';
            foreach ($result['data']['list'] as $comment) {
                $html .= View::make('components.comment.list', compact('comment'))->render();
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

        return view('timelines.hashtag-comments', compact('comments'));
    }

    // geotag comments
    public function geotagComments(Request $request)
    {
        if (! fs_config('channel_timeline_comments_status')) {
            return Response::view('404', [], 404);
        }

        $query = $request->all();
        $query['type'] = 'geotag';

        $result = TimelineInterface::comments($query);

        // ajax
        if ($request->ajax()) {
            $html = '';
            foreach ($result['data']['list'] as $comment) {
                $html .= View::make('components.comment.list', compact('comment'))->render();
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

        return view('timelines.geotag-comments', compact('comments'));
    }
}
