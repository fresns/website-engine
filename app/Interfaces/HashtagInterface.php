<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jevan Tang
 * Released under the Apache-2.0 License.
 */

namespace Fresns\WebsiteEngine\Interfaces;

use App\Fresns\Api\Http\Controllers\CommentController;
use App\Fresns\Api\Http\Controllers\HashtagController;
use App\Fresns\Api\Http\Controllers\PostController;
use Fresns\WebsiteEngine\Exceptions\ErrorException;
use Fresns\WebsiteEngine\Helpers\ApiHelper;
use Fresns\WebsiteEngine\Helpers\DataHelper;
use Illuminate\Http\Request;

class HashtagInterface
{
    public static function list(?array $query = []): array
    {
        if (is_remote_api()) {
            return ApiHelper::make()->get('/api/fresns/v1/hashtag/list', [
                'query' => $query,
            ]);
        }

        if (fs_config('site_mode') == 'private' && fs_config('site_private_end_after') == 1 && fs_user('detail.expired')) {
            return DataHelper::getApiDataTemplate();
        }

        try {
            $request = Request::create('/api/fresns/v1/hashtag/list', 'GET', $query);

            $apiController = new HashtagController();
            $response = $apiController->list($request);

            $resultContent = $response->getContent();
            $result = json_decode($resultContent, true);

            if ($result['code'] != 0) {
                throw new ErrorException($result['message'], $result['code']);
            }
        } catch (\Exception $e) {
            $code = (int) $e->getCode();

            throw new ErrorException($e->getMessage(), $code);
        }

        return $result;
    }

    public static function detail(string $htid, ?string $type = null, ?array $query = []): array
    {
        $type = match ($type) {
            'posts' => 'posts',
            'comments' => 'comments',
            default => 'posts',
        };

        if (fs_config('site_mode') == 'private' && fs_config('site_private_end_after') == 1 && fs_user('detail.expired')) {
            $results = [
                'hashtag' => DataHelper::getApiDataTemplate('detail'),
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
                        'hashtag' => $client->getAsync("/api/fresns/v1/hashtag/{$htid}/detail"),
                        'posts' => $client->getAsync('/api/fresns/v1/post/list', [
                            'query' => $query,
                        ]),
                    ]);
                    break;

                case 'comments':
                    $results = $client->unwrapRequests([
                        'hashtag' => $client->getAsync("/api/fresns/v1/hashtag/{$htid}/detail"),
                        'comments' => $client->getAsync('/api/fresns/v1/comment/list', [
                            'query' => $query,
                        ]),
                    ]);
                    break;
            }

            return $results;
        }

        try {
            $apiController = new HashtagController();

            $request = Request::create("/api/fresns/v1/hashtag/{$htid}/detail", 'GET', []);
            $response = $apiController->detail($htid, $request);

            $resultContent = $response->getContent();
            $result = json_decode($resultContent, true);

            if ($result['code'] != 0) {
                throw new ErrorException($result['message'], $result['code']);
            }

            switch ($type) {
                case 'posts':
                    $postRequest = Request::create('/api/fresns/v1/post/list', 'GET', $query);
                    $apiPostController = new PostController();

                    $response = $apiPostController->list($postRequest);

                    $resultContent = $response->getContent();

                    $results = [
                        'hashtag' => $result,
                        'posts' => json_decode($resultContent, true),
                    ];
                    break;

                case 'comments':
                    $commentRequest = Request::create('/api/fresns/v1/comment/list', 'GET', $query);
                    $apiCommentController = new CommentController();

                    $response = $apiCommentController->list($commentRequest);

                    $resultContent = $response->getContent();

                    $results = [
                        'hashtag' => $result,
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

    public static function interaction(string $htid, string $type, ?array $query = []): array
    {
        if (is_remote_api()) {
            $client = ApiHelper::make();

            $results = $client->unwrapRequests([
                'hashtag' => $client->getAsync("/api/fresns/v1/hashtag/{$htid}/detail"),
                'users' => $client->getAsync("/api/fresns/v1/hashtag/{$htid}/interaction/{$type}", [
                    'query' => $query,
                ]),
            ]);

            return $results;
        }

        try {
            $apiController = new HashtagController();

            $detailRequest = Request::create("/api/fresns/v1/hashtag/{$htid}/detail", 'GET', []);
            $detailResponse = $apiController->detail($htid, $detailRequest);

            $usersRequest = Request::create("/api/fresns/v1/hashtag/{$htid}/interaction/{$type}", 'GET', $query);
            $usersResponse = $apiController->interaction($htid, $type, $usersRequest);

            $results = [
                'hashtag' => json_decode($detailResponse->getContent(), true),
                'users' => json_decode($usersResponse->getContent(), true),
            ];

            if ($results['hashtag']['code'] != 0) {
                throw new ErrorException($results['hashtag']['message'], $results['hashtag']['code']);
            }
        } catch (\Exception $e) {
            $code = (int) $e->getCode();

            throw new ErrorException($e->getMessage(), $code);
        }

        return $results;
    }
}
