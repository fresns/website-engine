<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jevan Tang
 * Released under the Apache-2.0 License.
 */

namespace Fresns\WebEngine\Interfaces;

use App\Fresns\Api\Http\Controllers\ConversationController;
use App\Fresns\Api\Http\Controllers\NotificationController;
use Illuminate\Http\Request;
use Fresns\WebEngine\Exceptions\ErrorException;
use Fresns\WebEngine\Helpers\ApiHelper;

class MessageInterface
{
    public static function list(): array
    {
        if (is_remote_api()) {
            $client = ApiHelper::make();

            $results = $client->unwrapRequests([
                'conversations' => $client->getAsync('/api/v2/conversation/list', [
                    'query' => [
                        'isPin' => false,
                    ],
                ]),
                'pinConversations' => $client->getAsync('/api/v2/conversation/list', [
                    'query' => [
                        'isPin' => true,
                    ],
                ]),
            ]);

            return $results;
        }

        try {
            $request = Request::create('/api/v2/conversation/list', 'GET', [
                'isPin' => false,
            ]);
            $pinRequest = Request::create('/api/v2/conversation/list', 'GET', [
                'isPin' => true,
            ]);

            $apiController = new ConversationController();
            $response = $apiController->list($request);
            $pinResponse = $apiController->list($pinRequest);

            $resultContent = $response->getContent();
            $pinResultContent = $pinResponse->getContent();

            $results = [
                'conversations' => json_decode($resultContent, true),
                'pinConversations' => json_decode($pinResultContent, true),
            ];
        } catch (\Exception $e) {
            throw new ErrorException($e->getMessage(), $e->getCode());
        }

        return $results;
    }

    public static function conversation(int $conversationId, ?array $query = []): array
    {
        if (is_remote_api()) {
            $client = ApiHelper::make();

            $results = $client->unwrapRequests([
                'conversation' => $client->getAsync("/api/v2/conversation/{$conversationId}/detail"),
                'messages' => $client->getAsync("/api/v2/conversation/{$conversationId}/messages", [
                    'query' => $query,
                ]),
                'markAllAsRead' => $client->putAsync('/api/v2/conversation/mark-as-read', [
                    'json' => [
                        'type' => 'conversation',
                        'conversationId' => $conversationId,
                    ],
                ]),
            ]);

            return $results;
        }

        try {
            $apiController = new ConversationController();
            $response = $apiController->detail($conversationId);

            $resultContent = $response->getContent();
            $result = json_decode($resultContent, true);

            $request = Request::create("/api/v2/conversation/{$conversationId}/messages", 'GET', $query);
            $messagesResponse = $apiController->messages($conversationId, $request);

            $messagesResultContent = $messagesResponse->getContent();
            $messagesResult = json_decode($messagesResultContent, true);

            $results = [
                'conversation' => $result,
                'messages' => $messagesResult,
            ];

            $markAllAsReadRequest = Request::create('/api/v2/conversation/mark-as-read', 'GET', [
                'type' => 'conversation',
                'conversationId' => $conversationId,
            ]);
            $apiController->markAsRead($markAllAsReadRequest);
        } catch (\Exception $e) {
            throw new ErrorException($e->getMessage(), $e->getCode());
        }

        return $results;
    }

    public static function notifications(?array $query = []): array
    {
        if (is_remote_api()) {
            return ApiHelper::make()->get('/api/v2/notification/list', [
                'query' => $query,
            ]);
        }

        try {
            $request = Request::create('/api/v2/notification/list', 'GET', $query);

            $apiController = new NotificationController();
            $response = $apiController->list($request);

            $resultContent = $response->getContent();
            $result = json_decode($resultContent, true);
        } catch (\Exception $e) {
            throw new ErrorException($e->getMessage(), $e->getCode());
        }

        return $result;
    }
}
