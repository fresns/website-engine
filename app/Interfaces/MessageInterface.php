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
use Fresns\WebsiteEngine\Helpers\HttpHelper;
use Illuminate\Http\Request;

class MessageInterface
{
    public static function list(): array
    {
        if (is_remote_api()) {
            $requests = [
                [
                    'name' => 'conversations',
                    'method' => 'GET',
                    'path' => '/api/fresns/v1/conversation/list',
                    'params' => [
                        'isPin' => false,
                    ],
                ],
                [
                    'name' => 'pinConversations',
                    'method' => 'GET',
                    'path' => '/api/fresns/v1/conversation/list',
                    'params' => [
                        'isPin' => true,
                    ],
                ],
            ];

            $results = HttpHelper::concurrentRequests($requests);

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

            if (is_array($response)) {
                $resultContent = $response;
            } else {
                $resultContent = json_decode($response->getContent(), true);
            }

            $pinResponse = $apiController->list($pinRequest);

            if (is_array($pinResponse)) {
                $pinResultContent = $pinResponse;
            } else {
                $pinResultContent = json_decode($pinResponse->getContent(), true);
            }

            $results = [
                'conversations' => $resultContent,
                'pinConversations' => $pinResultContent,
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
            $requests = [
                [
                    'name' => 'conversation',
                    'method' => 'GET',
                    'path' => "/api/fresns/v1/conversation/{$uidOrUsername}/detail",
                ],
                [
                    'name' => 'messages',
                    'method' => 'GET',
                    'path' => "/api/fresns/v1/conversation/{$uidOrUsername}/messages",
                    'params' => $query,
                ],
                [
                    'name' => 'markAllAsRead',
                    'method' => 'GET',
                    'path' => "/api/fresns/v1/conversation/{$uidOrUsername}/read-status",
                ],
            ];

            $results = HttpHelper::concurrentRequests($requests);

            return $results;
        }

        try {
            $apiController = new ConversationController();

            $detailRequest = Request::create("/api/fresns/v1/conversation/{$uidOrUsername}/detail", 'GET', []);
            $response = $apiController->detail($uidOrUsername, $detailRequest);

            if (is_array($response)) {
                $result = $response;
            } else {
                $resultContent = $response->getContent();
                $result = json_decode($resultContent, true);
            }

            if ($result['code'] != 0) {
                throw new ErrorException($result['message'], $result['code']);
            }

            $messagesRequest = Request::create("/api/fresns/v1/conversation/{$uidOrUsername}/messages", 'GET', $query);
            $messagesResponse = $apiController->messages($uidOrUsername, $messagesRequest);

            if (is_array($messagesResponse)) {
                $messagesResult = $messagesResponse;
            } else {
                $messagesResultContent = $messagesResponse->getContent();
                $messagesResult = json_decode($messagesResultContent, true);
            }

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
            return HttpHelper::get('/api/fresns/v1/notification/list', $query);
        }

        try {
            $request = Request::create('/api/fresns/v1/notification/list', 'GET', $query);

            $apiController = new NotificationController();
            $response = $apiController->list($request);

            if (is_array($response)) {
                $result = $response;
            } else {
                $resultContent = $response->getContent();
                $result = json_decode($resultContent, true);
            }

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
