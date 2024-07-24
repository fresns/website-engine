<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jevan Tang
 * Released under the Apache-2.0 License.
 */

namespace Fresns\WebsiteEngine\Interfaces;

use App\Fresns\Api\Http\Controllers\HashtagController;
use Fresns\WebsiteEngine\Exceptions\ErrorException;
use Fresns\WebsiteEngine\Helpers\DataHelper;
use Fresns\WebsiteEngine\Helpers\HttpHelper;
use Illuminate\Http\Request;

class HashtagInterface
{
    public static function list(?array $query = []): array
    {
        if (is_remote_api()) {
            return HttpHelper::get('/api/fresns/v1/hashtag/list', $query);
        }

        if (fs_config('site_mode') == 'private' && fs_config('site_private_end_after') == 1 && fs_user('detail.expired')) {
            return DataHelper::getApiDataTemplate();
        }

        try {
            $request = Request::create('/api/fresns/v1/hashtag/list', 'GET', $query);

            $apiController = new HashtagController();
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
                'posts' => PostInterface::list($query),
                'comments' => CommentInterface::list($query),
            ];

            return $results;
        }

        if (is_remote_api()) {
            switch ($type) {
                case 'posts':
                    $requests = [
                        [
                            'name' => 'hashtag',
                            'method' => 'GET',
                            'path' => "/api/fresns/v1/hashtag/{$htid}/detail",
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
                            'name' => 'hashtag',
                            'method' => 'GET',
                            'path' => "/api/fresns/v1/hashtag/{$htid}/detail",
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
            $apiController = new HashtagController();

            $request = Request::create("/api/fresns/v1/hashtag/{$htid}/detail", 'GET', []);
            $response = $apiController->detail($htid, $request);

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
                        'hashtag' => $result,
                        'posts' => PostInterface::list($query),
                    ];
                    break;

                case 'comments':
                    $results = [
                        'hashtag' => $result,
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

    public static function interaction(string $htid, string $type, ?array $query = []): array
    {
        if (is_remote_api()) {
            $requests = [
                [
                    'name' => 'hashtag',
                    'method' => 'GET',
                    'path' => "/api/fresns/v1/hashtag/{$htid}/detail",
                ],
                [
                    'name' => 'users',
                    'method' => 'GET',
                    'path' => "/api/fresns/v1/hashtag/{$htid}/interaction/{$type}",
                    'params' => $query,
                ],
            ];

            $results = HttpHelper::concurrentRequests($requests);

            return $results;
        }

        try {
            $apiController = new HashtagController();

            $detailRequest = Request::create("/api/fresns/v1/hashtag/{$htid}/detail", 'GET', []);
            $detailResponse = $apiController->detail($htid, $detailRequest);

            if (is_array($detailResponse)) {
                $results['hashtag'] = $detailResponse;
            } else {
                $results['hashtag'] = json_decode($detailResponse->getContent(), true);
            }

            $usersRequest = Request::create("/api/fresns/v1/hashtag/{$htid}/interaction/{$type}", 'GET', $query);
            $usersResponse = $apiController->interaction($htid, $type, $usersRequest);

            if (is_array($usersResponse)) {
                $results['users'] = $usersResponse;
            } else {
                $results['users'] = json_decode($usersResponse->getContent(), true);
            }

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
