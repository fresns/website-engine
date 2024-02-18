<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jevan Tang
 * Released under the Apache-2.0 License.
 */

namespace Fresns\WebEngine\Interfaces;

use App\Fresns\Api\Http\Controllers\GroupController;
use Fresns\WebEngine\Exceptions\ErrorException;
use Fresns\WebEngine\Helpers\ApiHelper;
use Illuminate\Http\Request;

class GroupInterface
{
    public static function tree(): array
    {
        if (is_remote_api()) {
            return ApiHelper::make()->get('/api/fresns/v1/group/tree');
        }

        try {
            $apiController = new GroupController();
            $response = $apiController->tree();

            $resultContent = $response->getContent();
            $result = json_decode($resultContent, true);
        } catch (\Exception $e) {
            throw new ErrorException($e->getMessage(), $e->getCode());
        }

        return $result;
    }

    public static function list(?array $query = []): array
    {
        if (is_remote_api()) {
            return ApiHelper::make()->get('/api/fresns/v1/group/list', [
                'query' => $query,
            ]);
        }

        try {
            $request = Request::create('/api/fresns/v1/group/list', 'GET', $query);

            $apiController = new GroupController();
            $response = $apiController->list($request);

            $resultContent = $response->getContent();
            $result = json_decode($resultContent, true);
        } catch (\Exception $e) {
            throw new ErrorException($e->getMessage(), $e->getCode());
        }

        return $result;
    }

    public static function detail(string $gid, ?string $type = null, ?array $query = []): array
    {
        $type = match ($type) {
            'posts' => 'posts',
            'comments' => 'comments',
            default => 'posts',
        };

        if (is_remote_api()) {
            $client = ApiHelper::make();

            switch ($type) {
                case 'posts':
                    $results = $client->unwrapRequests([
                        'group' => $client->getAsync("/api/fresns/v1/group/{$gid}/detail"),
                        'posts' => $client->getAsync('/api/fresns/v1/post/list', [
                            'query' => $query,
                        ]),
                    ]);
                    break;

                case 'comments':
                    $results = $client->unwrapRequests([
                        'group' => $client->getAsync("/api/fresns/v1/group/{$gid}/detail"),
                        'comments' => $client->getAsync('/api/fresns/v1/comment/list', [
                            'query' => $query,
                        ]),
                    ]);
                    break;
            }

            return $results;
        }

        try {
            $apiController = new GroupController();
            $response = $apiController->detail($gid);

            $resultContent = $response->getContent();
            $result = json_decode($resultContent, true);

            switch ($type) {
                case 'posts':
                    $results = [
                        'group' => $result,
                        'posts' => PostInterface::list($query),
                    ];
                    break;

                case 'comments':
                    $results = [
                        'group' => $result,
                        'comments' => CommentInterface::list($query),
                    ];
                    break;
            }
        } catch (\Exception $e) {
            throw new ErrorException($e->getMessage(), $e->getCode());
        }

        return $results;
    }
}
