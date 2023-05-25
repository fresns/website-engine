<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jevan Tang
 * Released under the Apache-2.0 License.
 */

namespace Plugins\FresnsEngine\Interfaces;

use App\Fresns\Api\Http\Controllers\CommentController;
use App\Fresns\Api\Http\Controllers\PostController;
use App\Fresns\Api\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Plugins\FresnsEngine\Exceptions\ErrorException;
use Plugins\FresnsEngine\Helpers\ApiHelper;
use Plugins\FresnsEngine\Helpers\DataHelper;

class UserInterface
{
    public static function list(?array $query = []): array
    {
        if (fs_api_config('site_mode') == 'private' && fs_api_config('site_private_end_after') == 1 && fs_user('detail.expired')) {
            return DataHelper::getApiDataTemplate();
        }

        if (is_remote_api()) {
            return ApiHelper::make()->get('/api/v2/user/list', [
                'query' => $query,
            ]);
        }

        try {
            $request = Request::create('/api/v2/user/list', 'GET', $query);

            $apiController = new UserController();
            $response = $apiController->list($request);

            $resultContent = $response->getContent();
            $result = json_decode($resultContent, true);
        } catch (\Exception $e) {
            throw new ErrorException($e->getMessage(), $e->getCode());
        }

        return $result;
    }

    public static function markList(int $uid, string $markType, string $listType, ?array $query = []): array
    {
        if (fs_api_config('site_mode') == 'private' && fs_api_config('site_private_end_after') == 1 && fs_user('detail.expired')) {
            return DataHelper::getApiDataTemplate();
        }

        if (is_remote_api()) {
            return ApiHelper::make()->get("/api/v2/user/{$uid}/mark/{$markType}/{$listType}", [
                'query' => $query,
            ]);
        }

        try {
            $request = Request::create("/api/v2/user/{$uid}/mark/{$markType}/{$listType}", 'GET', $query);

            $apiController = new UserController();
            $response = $apiController->markList($uid, $markType, $listType, $request);

            $resultContent = $response->getContent();
            $result = json_decode($resultContent, true);
        } catch (\Exception $e) {
            throw new ErrorException($e->getMessage(), $e->getCode());
        }

        return $result;
    }

    public static function detail(string $uidOrUsername, string $type, string $listType, ?array $query = []): array
    {
        if (is_remote_api()) {
            $client = ApiHelper::make();

            switch ($type) {
                case 'posts':
                    $results = $client->unwrapRequests([
                        'profile' => $client->getAsync("/api/v2/user/{$uidOrUsername}/detail"),
                        'followersYouFollow' => $client->getAsync("/api/v2/user/{$uidOrUsername}/followers-you-follow", [
                            'query' => [
                                'pageSize' => 3,
                                'page' => 1,
                            ],
                        ]),
                        'posts' => $client->getAsync('/api/v2/post/list', [
                            'query' => $query,
                        ]),
                    ]);
                    break;

                case 'comments':
                    $results = $client->unwrapRequests([
                        'profile' => $client->getAsync("/api/v2/user/{$uidOrUsername}/detail"),
                        'followersYouFollow' => $client->getAsync("/api/v2/user/{$uidOrUsername}/followers-you-follow", [
                            'query' => [
                                'pageSize' => 3,
                                'page' => 1,
                            ],
                        ]),
                        'comments' => $client->getAsync('/api/v2/comment/list', [
                            'query' => $query,
                        ]),
                    ]);
                    break;

                case 'followersYouFollow':
                    $results = $client->unwrapRequests([
                        'profile' => $client->getAsync("/api/v2/user/{$uidOrUsername}/detail"),
                        'followersYouFollow' => $client->getAsync("/api/v2/user/{$uidOrUsername}/followers-you-follow", [
                            'query' => [
                                'pageSize' => 3,
                                'page' => 1,
                            ],
                        ]),
                        'users' => $client->getAsync("/api/v2/user/{$uidOrUsername}/followers-you-follow", [
                            'query' => $query,
                        ]),
                    ]);
                    break;

                case 'interaction':
                    $results = $client->unwrapRequests([
                        'profile' => $client->getAsync("/api/v2/user/{$uidOrUsername}/detail"),
                        'followersYouFollow' => $client->getAsync("/api/v2/user/{$uidOrUsername}/followers-you-follow", [
                            'query' => [
                                'pageSize' => 3,
                                'page' => 1,
                            ],
                        ]),
                        'users' => $client->getAsync("/api/v2/user/{$uidOrUsername}/interaction/{$listType}", [
                            'query' => $query,
                        ]),
                    ]);
                    break;

                default:
                    $results = $client->unwrapRequests([
                        'profile' => $client->getAsync("/api/v2/user/{$uidOrUsername}/detail"),
                        'followersYouFollow' => $client->getAsync("/api/v2/user/{$uidOrUsername}/followers-you-follow", [
                            'query' => [
                                'pageSize' => 3,
                                'page' => 1,
                            ],
                        ]),
                        $listType => $client->getAsync("/api/v2/user/{$uidOrUsername}/mark/{$type}/{$listType}", [
                            'query' => $query,
                        ]),
                    ]);
                    break;
            }

            return $results;
        }

        try {
            $request = Request::create("/api/v2/user/{$uidOrUsername}/followers-you-follow", 'GET', [
                'pageSize' => 3,
                'page' => 1,
            ]);

            $apiController = new UserController();
            $responseDetail = $apiController->detail($uidOrUsername);
            $responseFollowersYouFollow = $apiController->followersYouFollow($uidOrUsername, $request);

            $resultDetailContent = $responseDetail->getContent();
            $resultFollowersYouFollowContent = $responseFollowersYouFollow->getContent();

            if (fs_api_config('site_mode') == 'private' && fs_api_config('site_private_end_after') == 1 && fs_user('detail.expired')) {
                $results = [
                    'profile' => json_decode($resultDetailContent, true),
                    'followersYouFollow' => json_decode($resultFollowersYouFollowContent, true),
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
                        'profile' => json_decode($resultDetailContent, true),
                        'followersYouFollow' => json_decode($resultFollowersYouFollowContent, true),
                        'posts' => PostInterface::list($query),
                    ];
                    break;

                case 'comments':
                    $results = [
                        'profile' => json_decode($resultDetailContent, true),
                        'followersYouFollow' => json_decode($resultFollowersYouFollowContent, true),
                        'comments' => CommentInterface::list($query),
                    ];
                    break;

                case 'followersYouFollow':
                    $queryRequest = Request::create("/api/v2/user/{$uidOrUsername}/followers-you-follow", 'GET', $query);

                    $response = $apiController->followersYouFollow($uidOrUsername, $queryRequest);

                    $resultContent = $response->getContent();
                    $result = json_decode($resultContent, true);

                    $results = [
                        'profile' => json_decode($resultDetailContent, true),
                        'followersYouFollow' => json_decode($resultFollowersYouFollowContent, true),
                        'users' => $result,
                    ];
                    break;

                case 'interaction':
                    $queryRequest = Request::create("/api/v2/user/{$uidOrUsername}/interaction/{$listType}", 'GET', $query);

                    $response = $apiController->interaction($uidOrUsername, $listType, $queryRequest);

                    $resultContent = $response->getContent();
                    $result = json_decode($resultContent, true);

                    $results = [
                        'profile' => json_decode($resultDetailContent, true),
                        'followersYouFollow' => json_decode($resultFollowersYouFollowContent, true),
                        'users' => $result,
                    ];
                    break;

                default:
                    $queryRequest = Request::create("/api/v2/user/{$uidOrUsername}/mark/{$type}/{$listType}", 'GET', $query);

                    $response = $apiController->markList($uidOrUsername, $type, $listType, $queryRequest);

                    $resultContent = $response->getContent();
                    $result = json_decode($resultContent, true);

                    $results = [
                        'profile' => json_decode($resultDetailContent, true),
                        'followersYouFollow' => json_decode($resultFollowersYouFollowContent, true),
                        $listType => $result,
                    ];
                    break;
            }
        } catch (\Exception $e) {
            throw new ErrorException($e->getMessage(), $e->getCode());
        }

        return $results;
    }
}
