<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jevan Tang
 * Released under the Apache-2.0 License.
 */

namespace Plugins\FresnsEngine\Helpers;

use App\Helpers\ConfigHelper;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Str;

class QueryHelper
{
    const TYPE_USER = 'user';
    const TYPE_GROUP = 'group';
    const TYPE_HASHTAG = 'hashtag';
    const TYPE_POST = 'post';
    const TYPE_COMMENT = 'comment';
    const TYPE_USER_LIST = 'user_list';
    const TYPE_GROUP_LIST = 'group_list';
    const TYPE_HASHTAG_LIST = 'hashtag_list';
    const TYPE_POST_LIST = 'post_list';
    const TYPE_COMMENT_LIST = 'comment_list';

    public static function convertOptionToRequestParam(string $type, array $requestQuery)
    {
        $queryState = ConfigHelper::fresnsConfigByItemKey("menu_{$type}_query_state");
        $queryConfig = ConfigHelper::fresnsConfigByItemKey("menu_{$type}_query_config");

        // Convert to array parameters
        $params = [];
        if (! empty($queryConfig)) {
            $urlInfo = parse_url($queryConfig);

            if (! empty($urlInfo['path'])) {
                parse_str($urlInfo['path'], $params);
            }
        }

        $clientQuery = [];

        // Disable client incoming parameters
        if ($queryState == 1) {
            $clientQuery = [];
        }

        // Allow page flip parameters only
        if ($queryState == 2) {
            $clientQuery = [
                'pageSize' => $requestQuery['pageSize'] ?? $params['pageSize'] ?? 15,
                'page' => $requestQuery['page'] ?? $params['page'] ?? 1,
            ];
        }

        // Allow all parameters
        if ($queryState == 3) {
            $clientQuery = $requestQuery;
        }

        return array_merge($params, $clientQuery);
    }

    public static function configToQuery(string $type)
    {
        $queryConfig = ConfigHelper::fresnsConfigByItemKey("menu_{$type}_query_config");

        $params = [];
        if (! empty($queryConfig)) {
            $urlInfo = parse_url($queryConfig);

            if (! empty($urlInfo['path'])) {
                parse_str($urlInfo['path'], $params);
            }
        }

        return $params;
    }

    public static function convertApiDataToPaginate($items, $paginate)
    {
        $items = (array) $items;
        $total = $paginate['total'] ?? 0;
        $pageSize = $paginate['pageSize'] ?? 15;

        $paginate = new LengthAwarePaginator(
            items: $items,
            total: $total,
            perPage: $pageSize,
            currentPage: request('page', 1),
        );

        $paginate->withPath(Str::of(request()->path())->start('/'))->withQueryString();

        return $paginate;
    }
}
