<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jevan Tang
 * Released under the Apache-2.0 License.
 */

namespace Fresns\WebEngine\Interfaces;

use App\Fresns\Api\Http\Controllers\SearchController;
use Fresns\WebEngine\Exceptions\ErrorException;
use Fresns\WebEngine\Helpers\ApiHelper;
use Fresns\WebEngine\Helpers\DataHelper;
use Illuminate\Http\Request;

class SearchInterface
{
    public static function search(string $type, ?array $query = []): array
    {
        if (fs_config('site_mode') == 'private' && fs_config('site_private_end_after') == 1 && fs_user('detail.expired')) {
            return DataHelper::getApiDataTemplate();
        }

        if (is_remote_api()) {
            return ApiHelper::make()->get("/api/fresns/v1/search/{$type}", [
                'query' => $query,
            ]);
        }

        try {
            $request = Request::create("/api/fresns/v1/search/{$type}", 'GET', $query);

            $apiController = new SearchController();
            $response = $apiController->$type($request);

            if (is_array($response)) {
                $result = $response;
            } else {
                $resultContent = $response->getContent();
                $result = json_decode($resultContent, true);
            }
        } catch (\Exception $e) {
            $code = (int) $e->getCode();

            throw new ErrorException($e->getMessage(), $code);
        }

        return $result;
    }
}
