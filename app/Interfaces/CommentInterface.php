<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jevan Tang
 * Released under the Apache-2.0 License.
 */

namespace Fresns\WebsiteEngine\Interfaces;

use App\Fresns\Api\Http\Controllers\CommentController;
use Fresns\WebsiteEngine\Exceptions\ErrorException;
use Fresns\WebsiteEngine\Helpers\ApiHelper;
use Fresns\WebsiteEngine\Helpers\DataHelper;
use Illuminate\Http\Request;

class CommentInterface
{
    public static function list(?array $query = []): array
    {
        if (fs_config('site_mode') == 'private' && fs_config('site_private_end_after') == 1 && fs_user('detail.expired')) {
            return DataHelper::getApiDataTemplate();
        }

        if (is_remote_api()) {
            return ApiHelper::make()->get('/api/fresns/v1/comment/list', [
                'query' => $query,
            ]);
        }

        try {
            $request = Request::create('/api/fresns/v1/comment/list', 'GET', $query);

            $apiController = new CommentController();
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

    public static function nearby(?array $query = []): array
    {
        if (fs_config('site_mode') == 'private' && fs_config('site_private_end_after') == 1 && fs_user('detail.expired')) {
            return DataHelper::getApiDataTemplate();
        }

        if (is_remote_api()) {
            return ApiHelper::make()->get('/api/fresns/v1/comment/nearby', [
                'query' => $query,
            ]);
        }

        try {
            $request = Request::create('/api/fresns/v1/comment/nearby', 'GET', $query);

            $apiController = new CommentController();
            $response = $apiController->nearby($request);

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

    public static function detail(string $cid, ?array $query = []): array
    {
        if (fs_config('site_mode') == 'private' && fs_config('site_private_end_after') == 1 && fs_user('detail.expired')) {
            $results = [
                'comment' => DataHelper::getApiDataTemplate('detail'),
                'comments' => CommentInterface::list($query),
            ];

            return $results;
        }

        if (is_remote_api()) {
            $client = ApiHelper::make();

            $results = $client->unwrapRequests([
                'comment' => $client->getAsync("/api/fresns/v1/comment/{$cid}/detail"),
                'comments' => $client->getAsync('/api/fresns/v1/comment/list', [
                    'query' => $query,
                ]),
            ]);

            return $results;
        }

        try {
            $request = Request::create("/api/fresns/v1/comment/{$cid}/detail", 'GET', $query);

            $apiController = new CommentController();
            $response = $apiController->detail($cid, $request);

            if (is_array($response)) {
                $result = $response;
            } else {
                $resultContent = $response->getContent();
                $result = json_decode($resultContent, true);
            }

            if ($result['code'] != 0) {
                throw new ErrorException($result['message'], $result['code']);
            }

            $results = [
                'comment' => $result,
                'comments' => CommentInterface::list($query),
            ];
        } catch (\Exception $e) {
            $code = (int) $e->getCode();

            throw new ErrorException($e->getMessage(), $code);
        }

        return $results;
    }

    public static function interaction(string $cid, string $type, ?array $query = []): array
    {
        if (is_remote_api()) {
            $client = ApiHelper::make();

            $results = $client->unwrapRequests([
                'comment' => $client->getAsync("/api/fresns/v1/comment/{$cid}/detail"),
                'users' => $client->getAsync("/api/fresns/v1/comment/{$cid}/interaction/{$type}", [
                    'query' => $query,
                ]),
            ]);

            return $results;
        }

        try {
            $apiController = new CommentController();

            $detailRequest = Request::create("/api/fresns/v1/comment/{$cid}/detail", 'GET', []);
            $detailResponse = $apiController->detail($cid, $detailRequest);

            $usersRequest = Request::create("/api/fresns/v1/comment/{$cid}/interaction/{$type}", 'GET', $query);
            $usersResponse = $apiController->interaction($cid, $type, $usersRequest);

            $results = [
                'comment' => json_decode($detailResponse->getContent(), true),
                'users' => json_decode($usersResponse->getContent(), true),
            ];

            if ($results['comment']['code'] != 0) {
                throw new ErrorException($results['comment']['message'], $results['comment']['code']);
            }
        } catch (\Exception $e) {
            $code = (int) $e->getCode();

            throw new ErrorException($e->getMessage(), $code);
        }

        return $results;
    }
}
