<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jevan Tang
 * Released under the Apache-2.0 License.
 */

namespace Fresns\WebEngine\Interfaces;

use App\Fresns\Api\Http\Controllers\CommentController;
use Fresns\WebEngine\Exceptions\ErrorException;
use Fresns\WebEngine\Helpers\ApiHelper;
use Fresns\WebEngine\Helpers\DataHelper;
use Illuminate\Http\Request;

class CommentInterface
{
    public static function list(?array $query = []): array
    {
        if (fs_api_config('site_mode') == 'private' && fs_api_config('site_private_end_after') == 1 && fs_user('detail.expired')) {
            return DataHelper::getApiDataTemplate();
        }

        if (is_remote_api()) {
            return ApiHelper::make()->get('/api/v2/comment/list', [
                'query' => $query,
            ]);
        }

        try {
            $request = Request::create('/api/v2/comment/list', 'GET', $query);

            $apiController = new CommentController();
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
            return ApiHelper::make()->get('/api/v2/comment/nearby', [
                'query' => $query,
            ]);
        }

        try {
            $request = Request::create('/api/v2/comment/nearby', 'GET', $query);

            $apiController = new CommentController();
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

    public static function detail(string $cid, ?array $query = []): array
    {
        if (fs_api_config('site_mode') == 'private' && fs_api_config('site_private_end_after') == 1 && fs_user('detail.expired')) {
            $results = [
                'comment' => DataHelper::getApiDataTemplate('detail'),
                'comments' => CommentInterface::list($query),
            ];

            return $results;
        }

        if (is_remote_api()) {
            $client = ApiHelper::make();

            $results = $client->unwrapRequests([
                'comment' => $client->getAsync("/api/v2/comment/{$cid}/detail"),
                'comments' => $client->getAsync('/api/v2/comment/list', [
                    'query' => $query,
                ]),
            ]);

            return $results;
        }

        try {
            $request = Request::create("/api/v2/comment/{$cid}/detail", 'GET', $query);

            $apiController = new CommentController();
            $response = $apiController->detail($cid, $request);

            if (is_array($response)) {
                $result = $response;
            } else {
                $resultContent = $response->getContent();
                $result = json_decode($resultContent, true);
            }

            $results = [
                'comment' => $result,
                'comments' => CommentInterface::list($query),
            ];
        } catch (\Exception $e) {
            throw new ErrorException($e->getMessage(), $e->getCode());
        }

        return $results;
    }
}
