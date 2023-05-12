<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jevan Tang
 * Released under the Apache-2.0 License.
 */

namespace Plugins\FresnsEngine\Interfaces;

use App\Fresns\Api\Http\Controllers\CommentController;
use App\Fresns\Api\Http\Controllers\GroupController;
use App\Fresns\Api\Http\Controllers\PostController;
use Illuminate\Http\Request;
use Plugins\FresnsEngine\Exceptions\ErrorException;
use Plugins\FresnsEngine\Helpers\ApiHelper;

class GroupInterface
{
    public static function tree(): array
    {
        if (is_remote_api()) {
            return ApiHelper::make()->get('/api/v2/group/tree');
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
            return ApiHelper::make()->get('/api/v2/group/list', [
                'query' => $query,
            ]);
        }

        try {
            $request = Request::create('/api/v2/group/list', 'GET', $query);

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
                        'group' => $client->getAsync("/api/v2/group/{$gid}/detail"),
                        'posts' => $client->getAsync('/api/v2/post/list', [
                            'query' => $query,
                        ]),
                    ]);
                    break;

                case 'comments':
                    $results = $client->unwrapRequests([
                        'group' => $client->getAsync("/api/v2/group/{$gid}/detail"),
                        'comments' => $client->getAsync('/api/v2/comment/list', [
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
                    $request = Request::create('/api/v2/post/list', 'GET', $query);
                    $apiPostController = new PostController();

                    $response = $apiPostController->list($request);

                    $resultContent = $response->getContent();

                    $results = [
                        'group' => $result,
                        'posts' => json_decode($resultContent, true),
                    ];
                    break;

                case 'comments':
                    $request = Request::create('/api/v2/comment/list', 'GET', $query);
                    $apiCommentController = new CommentController();

                    $response = $apiCommentController->list($request);

                    $resultContent = $response->getContent();

                    $results = [
                        'group' => $result,
                        'comments' => json_decode($resultContent, true),
                    ];
                    break;
            }
        } catch (\Exception $e) {
            throw new ErrorException($e->getMessage(), $e->getCode());
        }

        return $results;
    }
}
