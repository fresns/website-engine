<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jevan Tang
 * Released under the Apache-2.0 License.
 */

namespace Fresns\WebEngine\Http\Controllers;

use App\Helpers\CacheHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;
use Fresns\WebEngine\Exceptions\ErrorException;
use Fresns\WebEngine\Helpers\QueryHelper;
use Fresns\WebEngine\Interfaces\MessageInterface;

class MessageController extends Controller
{
    // index
    public function index(Request $request)
    {
        $results = MessageInterface::list();

        if (data_get($results, 'conversations.code') !== 0) {
            throw new ErrorException($results['conversations']['message'], $results['conversations']['code']);
        }

        if (data_get($results, 'pinConversations.code') !== 0) {
            throw new ErrorException($results['pinConversations']['message'], $results['pinConversations']['code']);
        }

        $conversations = QueryHelper::convertApiDataToPaginate(
            items: $results['conversations']['data']['list'],
            paginate: $results['conversations']['data']['paginate'],
        );

        $pinConversations = $results['pinConversations']['data']['list'];

        // ajax
        if ($request->ajax()) {
            $html = '';
            foreach ($results['conversations']['data']['list'] as $conversation) {
                $html .= View::make('components.message.conversation', compact('conversation'))->render();
            }

            return response()->json([
                'paginate' => $results['conversations']['data']['paginate'],
                'html' => $html,
            ]);
        }

        // view
        return view("{$this->viewNamespace}::messages.index", compact('conversations', 'pinConversations'));
    }

    // conversation
    public function conversation(Request $request, int $conversationId)
    {
        $query = $request->all();
        $query['pageListDirection'] = 'oldest';

        $results = MessageInterface::conversation($conversationId, $query);

        if (data_get($results, 'conversation.code') !== 0) {
            throw new ErrorException($results['conversation']['message'], $results['conversation']['code']);
        }

        if (data_get($results, 'messages.code') !== 0) {
            throw new ErrorException($results['messages']['message'], $results['messages']['code']);
        }

        $uid = fs_user('detail.uid');

        CacheHelper::forgetFresnsMultilingual("fresns_web_user_panel_{$uid}", 'fresnsWeb');

        $conversation = $results['conversation']['data'];

        $messages = QueryHelper::convertApiDataToPaginate(
            items: $results['messages']['data']['list'],
            paginate: $results['messages']['data']['paginate'],
        );

        // ajax
        if ($request->ajax()) {
            $html = '';
            foreach ($results['messages']['data']['list'] as $message) {
                $html .= View::make('components.message.message', compact('message'))->render();
            }

            return response()->json([
                'paginate' => $results['messages']['data']['paginate'],
                'html' => $html,
            ]);
        }

        // view
        return view("{$this->viewNamespace}::messages.conversation", compact('conversation', 'messages'));
    }

    // notification
    public function notifications(Request $request, ?string $types = null)
    {
        $query = $request->all();
        $query['types'] = $types;

        $result = MessageInterface::notifications($query);

        if (data_get($result, 'code') !== 0) {
            throw new ErrorException($result['message'], $result['code']);
        }

        $notifications = QueryHelper::convertApiDataToPaginate(
            items: $result['data']['list'],
            paginate: $result['data']['paginate'],
        );

        // ajax
        if ($request->ajax()) {
            $html = '';
            foreach ($result['data']['list'] as $notification) {
                $html .= View::make('components.message.notification', compact('notification'))->render();
            }

            return response()->json([
                'paginate' => $result['data']['paginate'],
                'html' => $html,
            ]);
        }

        // view
        return view("{$this->viewNamespace}::notifications.index", compact('notifications', 'types'));
    }
}
