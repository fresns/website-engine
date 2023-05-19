<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jevan Tang
 * Released under the Apache-2.0 License.
 */

namespace Plugins\FresnsEngine\Interfaces;

use App\Fresns\Api\Http\Controllers\SearchController;
use Illuminate\Http\Request;
use Plugins\FresnsEngine\Exceptions\ErrorException;
use Plugins\FresnsEngine\Helpers\ApiHelper;
use Plugins\FresnsEngine\Helpers\DataHelper;

class SearchInterface
{
    public static function search(string $type, ?array $query = []): array
    {
        if (fs_api_config('site_mode') == 'private' && fs_api_config('site_private_end_after') == 1 && fs_user('detail.expired')) {
            return DataHelper::getApiDataTemplate();
        }

        if (is_remote_api()) {
            return ApiHelper::make()->get("/api/v2/search/{$type}", [
                'query' => $query,
            ]);
        }

        try {
            $request = Request::create("/api/v2/search/{$type}", 'GET', $query);

            $apiController = new SearchController();
            $response = $apiController->$type($request);

            if (is_array($response)) {
                $result = $response;
            } else {
                $resultContent = $response->getContent();
                $result = json_decode($resultContent, true);
            }
        } catch (\Exception $e) {
            throw new ErrorException($e->getMessage(), $e->getCode());
        }

        return $result;
    }
}
