<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jevan Tang
 * Released under the Apache-2.0 License.
 */

namespace Fresns\WebsiteEngine\Interfaces;

use App\Fresns\Api\Http\Controllers\UserController;
use Fresns\WebsiteEngine\Exceptions\ErrorException;
use Fresns\WebsiteEngine\Helpers\DataHelper;
use Fresns\WebsiteEngine\Helpers\HttpHelper;
use Illuminate\Http\Request;

class UserInterface
{
    public static function list(?array $query = []): array
    {
        if (fs_config('site_mode') == 'private' && fs_config('site_private_end_after') == 1 && fs_user('detail.expired')) {
            return DataHelper::getApiDataTemplate();
        }

        if (is_remote_api()) {
            return HttpHelper::get('/api/fresns/v1/user/list', $query);
        }

        try {
            $request = Request::create('/api/fresns/v1/user/list', 'GET', $query);

            $apiController = new UserController();
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

    public static function markList(int $uid, string $markType, string $listType, ?array $query = []): array
    {
        if (fs_config('site_mode') == 'private' && fs_config('site_private_end_after') == 1 && fs_user('detail.expired')) {
            return DataHelper::getApiDataTemplate();
        }

        if (is_remote_api()) {
            return HttpHelper::get("/api/fresns/v1/user/{$uid}/mark/{$markType}/{$listType}", $query);
        }

        try {
            $request = Request::create("/api/fresns/v1/user/{$uid}/mark/{$markType}/{$listType}", 'GET', $query);

            $apiController = new UserController();
            $response = $apiController->markList($uid, $markType, $listType, $request);

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

    public static function detail(int|string $uidOrUsername, string $type, string $listType, ?array $query = []): array
    {
        if (is_remote_api()) {
            $requests = [
                [
                    'name' => 'profile',
                    'method' => 'GET',
                    'path' => "/api/fresns/v1/user/{$uidOrUsername}/detail",
                ],
                [
                    'name' => 'followersYouFollow',
                    'method' => 'GET',
                    'path' => "/api/fresns/v1/user/{$uidOrUsername}/followers-you-follow",
                    'params' => [
                        'pageSize' => 3,
                        'page' => 1,
                    ],
                ],
            ];

            switch ($type) {
                case 'posts':
                    $requests[] = [
                        'name' => 'posts',
                        'method' => 'GET',
                        'path' => '/api/fresns/v1/post/list',
                        'params' => $query,
                    ];
                    break;

                case 'comments':
                    $requests[] = [
                        'name' => 'comments',
                        'method' => 'GET',
                        'path' => '/api/fresns/v1/comment/list',
                        'params' => $query,
                    ];
                    break;

                case 'followersYouFollow':
                    $requests[] = [
                        'name' => 'users',
                        'method' => 'GET',
                        'path' => "/api/fresns/v1/user/{$uidOrUsername}/followers-you-follow",
                        'params' => $query,
                    ];
                    break;

                case 'interaction':
                    $requests[] = [
                        'name' => 'users',
                        'method' => 'GET',
                        'path' => "/api/fresns/v1/user/{$uidOrUsername}/interaction/{$listType}",
                        'params' => $query,
                    ];
                    break;

                default:
                    $requests[] = [
                        'name' => $listType,
                        'method' => 'GET',
                        'path' => "/api/fresns/v1/user/{$uidOrUsername}/mark/{$type}/{$listType}",
                        'params' => $query,
                    ];
                    break;
            }

            $results = HttpHelper::concurrentRequests($requests);

            return $results;
        }

        try {
            $request = Request::create("/api/fresns/v1/user/{$uidOrUsername}/detail", 'GET', []);

            $followersRequest = Request::create("/api/fresns/v1/user/{$uidOrUsername}/followers-you-follow", 'GET', [
                'pageSize' => 3,
                'page' => 1,
            ]);

            $apiController = new UserController();

            $responseDetail = $apiController->detail($uidOrUsername, $request);

            if (is_array($responseDetail)) {
                $resultDetailContent = $responseDetail;
            } else {
                $resultDetailContent = json_decode($responseDetail->getContent(), true);
            }

            $responseFollowersYouFollow = $apiController->followersYouFollow($uidOrUsername, $followersRequest);

            if (is_array($responseFollowersYouFollow)) {
                $resultFollowersYouFollowContent = $responseFollowersYouFollow;
            } else {
                $resultFollowersYouFollowContent = json_decode($responseFollowersYouFollow->getContent(), true);
            }

            if (fs_config('site_mode') == 'private' && fs_config('site_private_end_after') == 1 && fs_user('detail.expired')) {
                $results = [
                    'profile' => $resultDetailContent,
                    'followersYouFollow' => $resultFollowersYouFollowContent,
                    'posts' => DataHelper::getApiDataTemplate(),
                    'comments' => DataHelper::getApiDataTemplate(),
                    'users' => DataHelper::getApiDataTemplate(),
                    $listType => DataHelper::getApiDataTemplate(),
                ];

                return $results;
            }

            switch ($type) {
                case 'posts':
                    $results = [
                        'profile' => $resultDetailContent,
                        'followersYouFollow' => $resultFollowersYouFollowContent,
                        'posts' => PostInterface::list($query),
                    ];
                    break;

                case 'comments':
                    $results = [
                        'profile' => $resultDetailContent,
                        'followersYouFollow' => $resultFollowersYouFollowContent,
                        'comments' => CommentInterface::list($query),
                    ];
                    break;

                case 'followersYouFollow':
                    $queryRequest = Request::create("/api/fresns/v1/user/{$uidOrUsername}/followers-you-follow", 'GET', $query);

                    $response = $apiController->followersYouFollow($uidOrUsername, $queryRequest);

                    if (is_array($response)) {
                        $result = $response;
                    } else {
                        $resultContent = $response->getContent();
                        $result = json_decode($resultContent, true);
                    }

                    $results = [
                        'profile' => $resultDetailContent,
                        'followersYouFollow' => $resultFollowersYouFollowContent,
                        'users' => $result,
                    ];
                    break;

                case 'interaction':
                    $queryRequest = Request::create("/api/fresns/v1/user/{$uidOrUsername}/interaction/{$listType}", 'GET', $query);

                    $response = $apiController->interaction($uidOrUsername, $listType, $queryRequest);

                    if (is_array($response)) {
                        $result = $response;
                    } else {
                        $resultContent = $response->getContent();
                        $result = json_decode($resultContent, true);
                    }

                    $results = [
                        'profile' => $resultDetailContent,
                        'followersYouFollow' => $resultFollowersYouFollowContent,
                        'users' => $result,
                    ];
                    break;

                default:
                    $queryRequest = Request::create("/api/fresns/v1/user/{$uidOrUsername}/mark/{$type}/{$listType}", 'GET', $query);

                    $response = $apiController->markList($uidOrUsername, $type, $listType, $queryRequest);

                    if (is_array($response)) {
                        $result = $response;
                    } else {
                        $resultContent = $response->getContent();
                        $result = json_decode($resultContent, true);
                    }

                    $results = [
                        'profile' => $resultDetailContent,
                        'followersYouFollow' => $resultFollowersYouFollowContent,
                        $listType => $result,
                    ];
                    break;
            }

            if ($results['profile']['code'] != 0) {
                throw new ErrorException($results['profile']['message'], $results['profile']['code']);
            }
        } catch (\Exception $e) {
            $code = (int) $e->getCode();

            throw new ErrorException($e->getMessage(), $code);
        }

        return $results;
    }
}
