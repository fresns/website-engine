<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jevan Tang
 * Released under the Apache-2.0 License.
 */

namespace Fresns\WebsiteEngine\Interfaces;

use App\Fresns\Api\Http\Controllers\GroupController;
use Fresns\WebsiteEngine\Exceptions\ErrorException;
use Fresns\WebsiteEngine\Helpers\ApiHelper;
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
            return ApiHelper::make()->get('/api/fresns/v1/group/list', [
                'query' => $query,
            ]);
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
            $client = ApiHelper::make();

            $results = $client->unwrapRequests([
                'group' => $client->getAsync("/api/fresns/v1/group/{$gid}/detail"),
                'users' => $client->getAsync("/api/fresns/v1/group/{$gid}/interaction/{$type}", [
                    'query' => $query,
                ]),
            ]);

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
