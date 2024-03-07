<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jevan Tang
 * Released under the Apache-2.0 License.
 */

namespace Fresns\WebEngine\Interfaces;

use App\Fresns\Api\Http\Controllers\PostController;
use Fresns\WebEngine\Exceptions\ErrorException;
use Fresns\WebEngine\Helpers\ApiHelper;
use Fresns\WebEngine\Helpers\DataHelper;
use Illuminate\Http\Request;

class PostInterface
{
    public static function list(?array $query = []): array
    {
        if (fs_config('site_mode') == 'private' && fs_config('site_private_end_after') == 1 && fs_user('detail.expired')) {
            return DataHelper::getApiDataTemplate();
        }

        if (is_remote_api()) {
            return ApiHelper::make()->get('/api/fresns/v1/post/list', [
                'query' => $query,
            ]);
        }

        try {
            $request = Request::create('/api/fresns/v1/post/list', 'GET', $query);

            $apiController = new PostController();
            $response = $apiController->list($request);

            if (is_array($response)) {
                $result = $response;
            } else {
                $resultContent = $response->getContent();
                $result = json_decode($resultContent, true);
            }
        } catch (\Exception $e) {
            $code = (int) $e->getCode();

            throw new ErrorException($e->getMessage(), $code);
        }

        return $result;
    }

    public static function nearby(?array $query = []): array
    {
        if (fs_config('site_mode') == 'private' && fs_config('site_private_end_after') == 1 && fs_user('detail.expired')) {
            return DataHelper::getApiDataTemplate();
        }

        if (is_remote_api()) {
            return ApiHelper::make()->get('/api/fresns/v1/post/nearby', [
                'query' => $query,
            ]);
        }

        try {
            $request = Request::create('/api/fresns/v1/post/nearby', 'GET', $query);

            $apiController = new PostController();
            $response = $apiController->nearby($request);

            if (is_array($response)) {
                $result = $response;
            } else {
                $resultContent = $response->getContent();
                $result = json_decode($resultContent, true);
            }
        } catch (\Exception $e) {
            $code = (int) $e->getCode();

            throw new ErrorException($e->getMessage(), $code);
        }

        return $result;
    }

    public static function detail(string $pid, ?array $query = []): array
    {
        if (fs_config('site_mode') == 'private' && fs_config('site_private_end_after') == 1 && fs_user('detail.expired')) {
            $results = [
                'post' => DataHelper::getApiDataTemplate('detail'),
                'comments' => CommentInterface::list($query),
                'stickies' => CommentInterface::list($query),
            ];

            return $results;
        }

        if (is_remote_api()) {
            $client = ApiHelper::make();

            $results = $client->unwrapRequests([
                'post' => $client->getAsync("/api/fresns/v1/post/{$pid}/detail"),
                'comments' => $client->getAsync('/api/fresns/v1/comment/list', [
                    'query' => $query,
                ]),
                'stickies' => $client->getAsync('/api/fresns/v1/comment/list', [
                    'query' => [
                        'pid' => $pid,
                        'sticky' => true,
                    ],
                ]),
            ]);

            return $results;
        }

        try {
            $request = Request::create("/api/fresns/v1/post/{$pid}/detail", 'GET', $query);

            $apiController = new PostController();
            $response = $apiController->detail($pid, $request);

            if (is_array($response)) {
                $result = $response;
            } else {
                $resultContent = $response->getContent();
                $result = json_decode($resultContent, true);
            }

            $results = [
                'post' => $result,
                'comments' => CommentInterface::list($query),
                'stickies' => CommentInterface::list([
                    'pid' => $pid,
                    'sticky' => true,
                ]),
            ];
        } catch (\Exception $e) {
            $code = (int) $e->getCode();

            throw new ErrorException($e->getMessage(), $code);
        }

        return $results;
    }

    public static function interaction(string $pid, string $type, ?array $query = []): array
    {
        if (is_remote_api()) {
            $client = ApiHelper::make();

            $results = $client->unwrapRequests([
                'post' => $client->getAsync("/api/fresns/v1/post/{$pid}/detail"),
                'users' => $client->getAsync("/api/fresns/v1/post/{$pid}/interaction/{$type}", [
                    'query' => $query,
                ]),
            ]);

            return $results;
        }

        try {
            $apiController = new PostController();

            $detailRequest = Request::create("/api/fresns/v1/post/{$pid}/detail", 'GET', []);
            $detailResponse = $apiController->detail($pid, $detailRequest);

            $usersRequest = Request::create("/api/fresns/v1/post/{$pid}/interaction/{$type}", 'GET', $query);
            $usersResponse = $apiController->interaction($pid, $type, $usersRequest);

            $results = [
                'post' => json_decode($detailResponse->getContent(), true),
                'users' => json_decode($usersResponse->getContent(), true),
            ];
        } catch (\Exception $e) {
            $code = (int) $e->getCode();

            throw new ErrorException($e->getMessage(), $code);
        }

        return $results;
    }
}
