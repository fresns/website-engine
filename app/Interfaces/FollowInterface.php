<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jevan Tang
 * Released under the Apache-2.0 License.
 */

namespace Plugins\FresnsEngine\Interfaces;

use App\Fresns\Api\Http\Controllers\CommentController;
use App\Fresns\Api\Http\Controllers\PostController;
use Illuminate\Http\Request;
use Plugins\FresnsEngine\Exceptions\ErrorException;
use Plugins\FresnsEngine\Helpers\ApiHelper;
use Plugins\FresnsEngine\Helpers\DataHelper;

class FollowInterface
{
    public static function posts(string $type, ?array $query = []): array
    {
        if (fs_api_config('site_mode') == 'private' && fs_api_config('site_private_end_after') == 1 && fs_user('detail.expired')) {
            return DataHelper::getApiDataTemplate();
        }

        if (is_remote_api()) {
            return ApiHelper::make()->get("/api/v2/post/follow/{$type}", [
                'query' => $query,
            ]);
        }

        try {
            $request = Request::create("/api/v2/post/follow/{$type}", 'GET', $query);

            $apiController = new PostController();
            $response = $apiController->follow($type, $request);

            $resultContent = $response->getContent();
            $result = json_decode($resultContent, true);
        } catch (\Exception $e) {
            throw new ErrorException($e->getMessage(), $e->getCode());
        }

        return $result;
    }

    public static function comments(string $type, ?array $query = []): array
    {
        if (fs_api_config('site_mode') == 'private' && fs_api_config('site_private_end_after') == 1 && fs_user('detail.expired')) {
            return DataHelper::getApiDataTemplate();
        }

        if (is_remote_api()) {
            return ApiHelper::make()->get("/api/v2/comment/follow/{$type}", [
                'query' => $query,
            ]);
        }

        try {
            $request = Request::create("/api/v2/comment/follow/{$type}", 'GET', $query);

            $apiController = new CommentController();
            $response = $apiController->follow($type, $request);

            $resultContent = $response->getContent();
            $result = json_decode($resultContent, true);
        } catch (\Exception $e) {
            throw new ErrorException($e->getMessage(), $e->getCode());
        }

        return $result;
    }
}
