<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jevan Tang
 * Released under the Apache-2.0 License.
 */

namespace Fresns\WebsiteEngine\Interfaces;

use App\Fresns\Api\Http\Controllers\GeotagController;
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
                'posts' => PostInterface::list($query),
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
                        'geotag' => $result,
                        'posts' => PostInterface::list($query),
                    ];
                    break;

                case 'comments':
                    $results = [
                        'geotag' => $result,
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

            if (is_array($detailResponse)) {
                $results['geotag'] = $detailResponse;
            } else {
                $results['geotag'] = json_decode($detailResponse->getContent(), true);
            }

            $usersRequest = Request::create("/api/fresns/v1/geotag/{$gtid}/interaction/{$type}", 'GET', $query);
            $usersResponse = $apiController->interaction($gtid, $type, $usersRequest);

            if (is_array($usersResponse)) {
                $results['users'] = $usersResponse;
            } else {
                $results['users'] = json_decode($usersResponse->getContent(), true);
            }

            if ($results['geotag']['code'] != 0) {
                throw new ErrorException($results['geotag']['message'], $results['geotag']['code']);
            }
        } catch (\Exception $e) {
            $code = (int) $e->getCode();

            throw new ErrorException($e->getMessage(), $code);
        }

        return $results;
    }
}
