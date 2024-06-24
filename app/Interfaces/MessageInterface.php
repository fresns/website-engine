<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jevan Tang
 * Released under the Apache-2.0 License.
 */

namespace Fresns\WebsiteEngine\Interfaces;

use App\Fresns\Api\Http\Controllers\ConversationController;
use App\Fresns\Api\Http\Controllers\NotificationController;
use Fresns\WebsiteEngine\Exceptions\ErrorException;
use Fresns\WebsiteEngine\Helpers\ApiHelper;
use Illuminate\Http\Request;

class MessageInterface
{
    public static function list(): array
    {
        if (is_remote_api()) {
            $client = ApiHelper::make();

            $results = $client->unwrapRequests([
                'conversations' => $client->getAsync('/api/fresns/v1/conversation/list', [
                    'query' => [
                        'isPin' => false,
                    ],
                ]),
                'pinConversations' => $client->getAsync('/api/fresns/v1/conversation/list', [
                    'query' => [
                        'isPin' => true,
                    ],
                ]),
            ]);

            return $results;
        }

        try {
            $request = Request::create('/api/fresns/v1/conversation/list', 'GET', [
                'isPin' => false,
            ]);
            $pinRequest = Request::create('/api/fresns/v1/conversation/list', 'GET', [
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

            if ($results['conversations']['code'] != 0) {
                throw new ErrorException($results['conversations']['message'], $results['conversations']['code']);
            }
        } catch (\Exception $e) {
            $code = (int) $e->getCode();

            throw new ErrorException($e->getMessage(), $code);
        }

        return $results;
    }

    public static function conversation(int|string $uidOrUsername, ?array $query = []): array
    {
        if (is_remote_api()) {
            $client = ApiHelper::make();

            $results = $client->unwrapRequests([
                'conversation' => $client->getAsync("/api/fresns/v1/conversation/{$uidOrUsername}/detail"),
                'messages' => $client->getAsync("/api/fresns/v1/conversation/{$uidOrUsername}/messages", [
                    'query' => $query,
                ]),
                'markAllAsRead' => $client->patchAsync("/api/fresns/v1/conversation/{$uidOrUsername}/read-status"),
            ]);

            return $results;
        }

        try {
            $apiController = new ConversationController();

            $detailRequest = Request::create("/api/fresns/v1/conversation/{$uidOrUsername}/detail", 'GET', []);
            $response = $apiController->detail($uidOrUsername, $detailRequest);

            $resultContent = $response->getContent();
            $result = json_decode($resultContent, true);

            if ($result['code'] != 0) {
                throw new ErrorException($result['message'], $result['code']);
            }

            $messagesRequest = Request::create("/api/fresns/v1/conversation/{$uidOrUsername}/messages", 'GET', $query);
            $messagesResponse = $apiController->messages($uidOrUsername, $messagesRequest);

            $messagesResultContent = $messagesResponse->getContent();
            $messagesResult = json_decode($messagesResultContent, true);

            $results = [
                'conversation' => $result,
                'messages' => $messagesResult,
            ];

            $markAllAsReadRequest = Request::create("/api/fresns/v1/conversation/{$uidOrUsername}/read-status", 'PATCH');
            $apiController->readStatus($uidOrUsername, $markAllAsReadRequest);
        } catch (\Exception $e) {
            $code = (int) $e->getCode();

            throw new ErrorException($e->getMessage(), $code);
        }

        return $results;
    }

    public static function notifications(?array $query = []): array
    {
        if (is_remote_api()) {
            return ApiHelper::make()->get('/api/fresns/v1/notification/list', [
                'query' => $query,
            ]);
        }

        try {
            $request = Request::create('/api/fresns/v1/notification/list', 'GET', $query);

            $apiController = new NotificationController();
            $response = $apiController->list($request);

            $resultContent = $response->getContent();
            $result = json_decode($resultContent, true);

            if ($result['code'] != 0) {
                throw new ErrorException($result['message'], $result['code']);
            }
        } catch (\Exception $e) {
            $code = (int) $e->getCode();

            throw new ErrorException($e->getMessage(), $code);
        }

        return $result;
    }
}
