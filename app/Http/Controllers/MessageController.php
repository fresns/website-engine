<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jevan Tang
 * Released under the Apache-2.0 License.
 */

namespace Fresns\WebsiteEngine\Http\Controllers;

use App\Helpers\CacheHelper;
use Fresns\WebsiteEngine\Helpers\QueryHelper;
use Fresns\WebsiteEngine\Interfaces\MessageInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;

class MessageController extends Controller
{
    // index
    public function index(Request $request)
    {
        $results = MessageInterface::list();

        // ajax
        if ($request->ajax()) {
            $html = '';
            foreach ($results['conversations']['data']['list'] as $conversation) {
                $html .= View::make('components.message.conversation', compact('conversation'))->render();
            }

            return response()->json([
                'pagination' => $results['conversations']['data']['pagination'],
                'html' => $html,
            ]);
        }

        // view
        $conversations = QueryHelper::convertApiDataToPaginate(
            items: $results['conversations']['data']['list'],
            pagination: $results['conversations']['data']['pagination'],
        );

        $pinConversations = $results['pinConversations']['data']['list'];

        return view('messages.index', compact('conversations', 'pinConversations'));
    }

    // conversation
    public function conversation(Request $request, int|string $uidOrUsername)
    {
        $query = $request->all();
        $query['pageListDirection'] = 'oldest';

        $results = MessageInterface::conversation($uidOrUsername, $query);

        $uid = fs_user('detail.uid');

        CacheHelper::forgetFresnsMultilingual("fresns_web_user_panel_{$uid}", 'fresnsWeb');

        // ajax
        if ($request->ajax()) {
            $html = '';
            foreach ($results['messages']['data']['list'] as $message) {
                $html .= View::make('components.message.message', compact('message'))->render();
            }

            return response()->json([
                'pagination' => $results['messages']['data']['pagination'],
                'html' => $html,
            ]);
        }

        // view
        $conversation = $results['conversation']['data'];

        $messages = QueryHelper::convertApiDataToPaginate(
            items: $results['messages']['data']['list'],
            pagination: $results['messages']['data']['pagination'],
        );

        return view('messages.conversation', compact('conversation', 'messages'));
    }

    // notification
    public function notifications(Request $request, ?string $types = null)
    {
        $query = $request->all();
        $query['types'] = $types;

        $result = MessageInterface::notifications($query);

        // ajax
        if ($request->ajax()) {
            $html = '';
            foreach ($result['data']['list'] as $notification) {
                $html .= View::make('components.message.notification', compact('notification'))->render();
            }

            return response()->json([
                'pagination' => $result['data']['pagination'],
                'html' => $html,
            ]);
        }

        // view
        $notifications = QueryHelper::convertApiDataToPaginate(
            items: $result['data']['list'],
            pagination: $result['data']['pagination'],
        );

        return view('messages.notifications', compact('notifications', 'types'));
    }
}
