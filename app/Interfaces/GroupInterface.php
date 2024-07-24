<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jevan Tang
 * Released under the Apache-2.0 License.
 */

namespace Fresns\WebsiteEngine\Interfaces;

use App\Fresns\Api\Http\Controllers\GroupController;
use Fresns\WebsiteEngine\Exceptions\ErrorException;
use Fresns\WebsiteEngine\Helpers\HttpHelper;
use Illuminate\Http\Request;

class GroupInterface
{
    public static function tree(): array
    {
        if (is_remote_api()) {
            return HttpHelper::get('/api/fresns/v1/group/tree');
        }

        try {
            $apiController = new GroupController();

            $request = Request::create('/api/fresns/v1/group/tree', 'GET', []);
            $response = $apiController->tree($request);

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

    public static function list(?array $query = []): array
    {
        if (is_remote_api()) {
            return HttpHelper::get('/api/fresns/v1/group/list', $query);
        }

        try {
            $request = Request::create('/api/fresns/v1/group/list', 'GET', $query);

            $apiController = new GroupController();
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

    public static function detail(string $gid, ?string $type = null, ?array $query = []): array
    {
        $type = match ($type) {
            'posts' => 'posts',
            'comments' => 'comments',
            default => 'posts',
        };

        if (is_remote_api()) {
            switch ($type) {
                case 'posts':
                    $requests = [
                        [
                            'name' => 'group',
                            'method' => 'GET',
                            'path' => "/api/fresns/v1/group/{$gid}/detail",
                        ],
                        [
                            'name' => 'posts',
                            'method' => 'GET',
                            'path' => '/api/fresns/v1/post/list',
                            'params' => $query,
                        ],
                    ];

                    $results = HttpHelper::concurrentRequests($requests);
                    break;

                case 'comments':
                    $requests = [
                        [
                            'name' => 'group',
                            'method' => 'GET',
                            'path' => "/api/fresns/v1/group/{$gid}/detail",
                        ],
                        [
                            'name' => 'comments',
                            'method' => 'GET',
                            'path' => '/api/fresns/v1/comment/list',
                            'params' => $query,
                        ],
                    ];

                    $results = HttpHelper::concurrentRequests($requests);
                    break;
            }

            return $results;
        }

        try {
            $apiController = new GroupController();

            $request = Request::create("/api/fresns/v1/group/{$gid}/detail", 'GET', []);
            $response = $apiController->detail($gid, $request);

            if (is_array($response)) {
                $result = $response;
            } else {
                $resultContent = $response->getContent();
                $result = json_decode($resultContent, true);
            }

            if ($result['code'] != 0) {
                throw new ErrorException($result['message'], $result['code']);
            }

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
            $code = (int) $e->getCode();

            throw new ErrorException($e->getMessage(), $code);
        }

        return $results;
    }

    public static function interaction(string $gid, string $type, ?array $query = []): array
    {
        if (is_remote_api()) {
            $requests = [
                [
                    'name' => 'group',
                    'method' => 'GET',
                    'path' => "/api/fresns/v1/group/{$gid}/detail",
                ],
                [
                    'name' => 'users',
                    'method' => 'GET',
                    'path' => "/api/fresns/v1/group/{$gid}/interaction/{$type}",
                    'params' => $query,
                ],
            ];

            $results = HttpHelper::concurrentRequests($requests);

            return $results;
        }

        try {
            $apiController = new GroupController();

            $detailRequest = Request::create("/api/fresns/v1/group/{$gid}/detail", 'GET', []);
            $detailResponse = $apiController->detail($gid, $detailRequest);

            if (is_array($detailResponse)) {
                $results['group'] = $detailResponse;
            } else {
                $results['group'] = json_decode($detailResponse->getContent(), true);
            }

            $usersRequest = Request::create("/api/fresns/v1/group/{$gid}/interaction/{$type}", 'GET', $query);
            $usersResponse = $apiController->interaction($gid, $type, $usersRequest);

            if (is_array($usersResponse)) {
                $results['users'] = $usersResponse;
            } else {
                $results['users'] = json_decode($usersResponse->getContent(), true);
            }

            if ($results['group']['code'] != 0) {
                throw new ErrorException($results['group']['message'], $results['group']['code']);
            }
        } catch (\Exception $e) {
            $code = (int) $e->getCode();

            throw new ErrorException($e->getMessage(), $code);
        }

        return $results;
    }
}
