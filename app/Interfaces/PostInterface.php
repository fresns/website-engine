<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jevan Tang
 * Released under the Apache-2.0 License.
 */

namespace Fresns\WebEngine\Interfaces;

use App\Fresns\Api\Http\Controllers\PostController;
use Illuminate\Http\Request;
use Fresns\WebEngine\Exceptions\ErrorException;
use Fresns\WebEngine\Helpers\ApiHelper;
use Fresns\WebEngine\Helpers\DataHelper;

class PostInterface
{
    public static function list(?array $query = []): array
    {
        if (fs_api_config('site_mode') == 'private' && fs_api_config('site_private_end_after') == 1 && fs_user('detail.expired')) {
            return DataHelper::getApiDataTemplate();
        }

        if (is_remote_api()) {
            return ApiHelper::make()->get('/api/v2/post/list', [
                'query' => $query,
            ]);
        }

        try {
            $request = Request::create('/api/v2/post/list', 'GET', $query);

            $apiController = new PostController();
            $response = $apiController->list($request);

            if (is_array($response)) {
                $result = $response;
            } else {
                $resultContent = $response->getContent();
                $result = json_decode($resultContent, true);
            }
        } catch (\Exception $e) {
            throw new ErrorException($e->getMessage(), $e->getCode());
        }

        return $result;
    }

    public static function nearby(?array $query = []): array
    {
        if (fs_api_config('site_mode') == 'private' && fs_api_config('site_private_end_after') == 1 && fs_user('detail.expired')) {
            return DataHelper::getApiDataTemplate();
        }

        if (is_remote_api()) {
            return ApiHelper::make()->get('/api/v2/post/nearby', [
                'query' => $query,
            ]);
        }

        try {
            $request = Request::create('/api/v2/post/nearby', 'GET', $query);

            $apiController = new PostController();
            $response = $apiController->nearby($request);

            if (is_array($response)) {
                $result = $response;
            } else {
                $resultContent = $response->getContent();
                $result = json_decode($resultContent, true);
            }
        } catch (\Exception $e) {
            throw new ErrorException($e->getMessage(), $e->getCode());
        }

        return $result;
    }

    public static function detail(string $pid, ?array $query = []): array
    {
        if (fs_api_config('site_mode') == 'private' && fs_api_config('site_private_end_after') == 1 && fs_user('detail.expired')) {
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
                'post' => $client->getAsync("/api/v2/post/{$pid}/detail"),
                'comments' => $client->getAsync('/api/v2/comment/list', [
                    'query' => $query,
                ]),
                'stickies' => $client->getAsync('/api/v2/comment/list', [
                    'query' => [
                        'pid' => $pid,
                        'sticky' => true,
                    ],
                ]),
            ]);

            return $results;
        }

        try {
            $request = Request::create("/api/v2/post/{$pid}/detail", 'GET', $query);

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
            throw new ErrorException($e->getMessage(), $e->getCode());
        }

        return $results;
    }
}
