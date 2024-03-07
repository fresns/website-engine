<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jevan Tang
 * Released under the Apache-2.0 License.
 */

namespace Fresns\WebsiteEngine\Interfaces;

use App\Fresns\Api\Http\Controllers\CommentController;
use App\Fresns\Api\Http\Controllers\GeotagController;
use App\Fresns\Api\Http\Controllers\PostController;
use Fresns\WebsiteEngine\Exceptions\ErrorException;
use Fresns\WebsiteEngine\Helpers\ApiHelper;
use Fresns\WebsiteEngine\Helpers\DataHelper;
use Illuminate\Http\Request;

class GeotagInterface
{
    public static function list(?array $query = []): array
    {
        if (is_remote_api()) {
            return ApiHelper::make()->get('/api/fresns/v1/geotag/list', [
                'query' => $query,
            ]);
        }

        if (fs_config('site_mode') == 'private' && fs_config('site_private_end_after') == 1 && fs_user('detail.expired')) {
            return DataHelper::getApiDataTemplate();
        }

        try {
            $request = Request::create('/api/fresns/v1/geotag/list', 'GET', $query);

            $apiController = new GeotagController();
            $response = $apiController->list($request);

            $resultContent = $response->getContent();
            $result = json_decode($resultContent, true);
        } catch (\Exception $e) {
            $code = (int) $e->getCode();

            throw new ErrorException($e->getMessage(), $code);
        }

        return $result;
    }

    public static function detail(string $gtid, ?string $type = null, ?array $query = []): array
    {
        $type = match ($type) {
            'posts' => 'posts',
            'comments' => 'comments',
            default => 'posts',
        };

        if (fs_config('site_mode') == 'private' && fs_config('site_private_end_after') == 1 && fs_user('detail.expired')) {
            $results = [
                'geotag' => DataHelper::getApiDataTemplate('detail'),
                'posts' => CommentInterface::list($query),
                'comments' => CommentInterface::list($query),
            ];

            return $results;
        }

        if (is_remote_api()) {
            $client = ApiHelper::make();

            switch ($type) {
                case 'posts':
                    $results = $client->unwrapRequests([
                        'geotag' => $client->getAsync("/api/fresns/v1/geotag/{$gtid}/detail"),
                        'posts' => $client->getAsync('/api/fresns/v1/post/list', [
                            'query' => $query,
                        ]),
                    ]);
                    break;

                case 'comments':
                    $results = $client->unwrapRequests([
                        'geotag' => $client->getAsync("/api/fresns/v1/geotag/{$gtid}/detail"),
                        'comments' => $client->getAsync('/api/fresns/v1/comment/list', [
                            'query' => $query,
                        ]),
                    ]);
                    break;
            }

            return $results;
        }

        try {
            $apiController = new GeotagController();

            $request = Request::create("/api/fresns/v1/geotag/{$gtid}/detail", 'GET', []);
            $response = $apiController->detail($gtid, $request);

            $resultContent = $response->getContent();
            $result = json_decode($resultContent, true);

            switch ($type) {
                case 'posts':
                    $postRequest = Request::create('/api/fresns/v1/post/list', 'GET', $query);
                    $apiPostController = new PostController();

                    $response = $apiPostController->list($postRequest);

                    $resultContent = $response->getContent();

                    $results = [
                        'geotag' => $result,
                        'posts' => json_decode($resultContent, true),
                    ];
                    break;

                case 'comments':
                    $commentRequest = Request::create('/api/fresns/v1/comment/list', 'GET', $query);
                    $apiCommentController = new CommentController();

                    $response = $apiCommentController->list($commentRequest);

                    $resultContent = $response->getContent();

                    $results = [
                        'geotag' => $result,
                        'comments' => json_decode($resultContent, true),
                    ];
                    break;
            }
        } catch (\Exception $e) {
            $code = (int) $e->getCode();

            throw new ErrorException($e->getMessage(), $code);
        }

        return $results;
    }

    public static function interaction(string $gtid, string $type, ?array $query = []): array
    {
        if (is_remote_api()) {
            $client = ApiHelper::make();

            $results = $client->unwrapRequests([
                'geotag' => $client->getAsync("/api/fresns/v1/geotag/{$gtid}/detail"),
                'users' => $client->getAsync("/api/fresns/v1/geotag/{$gtid}/interaction/{$type}", [
                    'query' => $query,
                ]),
            ]);

            return $results;
        }

        try {
            $apiController = new GeotagController();

            $detailRequest = Request::create("/api/fresns/v1/geotag/{$gtid}/detail", 'GET', []);
            $detailResponse = $apiController->detail($gtid, $detailRequest);

            $usersRequest = Request::create("/api/fresns/v1/geotag/{$gtid}/interaction/{$type}", 'GET', $query);
            $usersResponse = $apiController->interaction($gtid, $type, $usersRequest);

            $results = [
                'geotag' => json_decode($detailResponse->getContent(), true),
                'users' => json_decode($usersResponse->getContent(), true),
            ];
        } catch (\Exception $e) {
            $code = (int) $e->getCode();

            throw new ErrorException($e->getMessage(), $code);
        }

        return $results;
    }
}
