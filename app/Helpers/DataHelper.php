<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jarvis Tang
 * Released under the Apache-2.0 License.
 */

namespace Plugins\FresnsEngine\Helpers;

use App\Helpers\CacheHelper;
use App\Helpers\ConfigHelper;
use App\Helpers\LanguageHelper;
use App\Helpers\PluginHelper;
use App\Models\Config;
use App\Models\File;
use Illuminate\Support\Facades\Cookie;

class DataHelper
{
    // get upload info
    public static function getConfigByItemKey(string $itemKey)
    {
        $langTag = current_lang_tag();

        $cacheKey = "fresns_web_db_config_{$itemKey}_{$langTag}";
        $cacheTags = ['fresnsWeb', 'fresnsWebConfigs'];

        // is known to be empty
        $isKnownEmpty = CacheHelper::isKnownEmpty($cacheKey);
        if ($isKnownEmpty) {
            return null;
        }

        // get cache
        $dbConfig = CacheHelper::get($cacheKey, $cacheTags);

        if (empty($dbConfig)) {
            $config = Config::where('item_key', $itemKey)->first();

            if (! $config) {
                return null;
            }

            $itemValue = $config->item_value;

            if ($config->is_multilingual == 1) {
                $itemValue = LanguageHelper::fresnsLanguageByTableKey($config->item_key, $config->item_type, $langTag);
            } elseif ($config->item_type == 'file') {
                $itemValue = ConfigHelper::fresnsConfigFileUrlByItemKey($config->item_key);
            } elseif ($config->item_type == 'plugin') {
                $itemValue = PluginHelper::fresnsPluginUrlByUnikey($config->item_value) ?? $config->item_value;
            } elseif ($config->item_type == 'plugins') {
                if ($config->item_value) {
                    foreach ($config->item_value as $plugin) {
                        $pluginItem['code'] = $plugin['code'];
                        $pluginItem['url'] = PluginHelper::fresnsPluginUrlByUnikey($plugin['unikey']);
                        $itemArr[] = $pluginItem;
                    }
                    $itemValue = $itemArr;
                }
            }

            $dbConfig = $itemValue;

            $cacheTime = CacheHelper::fresnsCacheTimeByFileType(File::TYPE_ALL);
            CacheHelper::put($dbConfig, $cacheKey, $cacheTags, null, $cacheTime);
        }

        return $dbConfig;
    }

    // get upload info
    public static function getUploadInfo(?int $usageType = null, ?string $tableName = null, ?string $tableColumn = null, ?int $tableId = null, ?string $tableKey = null)
    {
        $uploadInfo = [
            'image' => [
                'usageType' => $usageType,
                'tableName' => $tableName,
                'tableColumn' => $tableColumn,
                'tableId' => $tableId,
                'tableKey' => $tableKey,
                'type' => 'image',
            ],
            'video' => [
                'usageType' => $usageType,
                'tableName' => $tableName,
                'tableColumn' => $tableColumn,
                'tableId' => $tableId,
                'tableKey' => $tableKey,
                'type' => 'video',
            ],
            'audio' => [
                'usageType' => $usageType,
                'tableName' => $tableName,
                'tableColumn' => $tableColumn,
                'tableId' => $tableId,
                'tableKey' => $tableKey,
                'type' => 'audio',
            ],
            'document' => [
                'usageType' => $usageType,
                'tableName' => $tableName,
                'tableColumn' => $tableColumn,
                'tableId' => $tableId,
                'tableKey' => $tableKey,
                'type' => 'document',
            ],
        ];

        return $uploadInfo;
    }

    // get fresns user panel
    public static function getFresnsUserPanel(?string $key = null)
    {
        if (fs_user()->guest()) {
            return null;
        }

        $langTag = current_lang_tag();
        $uid = fs_user('detail.uid');

        $cacheKey = "fresns_web_user_panel_{$uid}_{$langTag}";
        $cacheTag = 'fresnsWeb';

        $userPanel = CacheHelper::get($cacheKey, $cacheTag);

        if (empty($userPanel)) {
            $result = ApiHelper::make()->get('/api/v2/user/panel');

            $userPanel = data_get($result, 'data');

            CacheHelper::put($userPanel, $cacheKey, $cacheTag, null, now()->addMinutes());
        }

        return data_get($userPanel, $key);
    }

    // get fresns groups
    public static function getFresnsGroups(string $listKey): array
    {
        $listKeyArr = [
            'categories',
            'tree',
        ];

        if (! in_array($listKey, $listKeyArr)) {
            return [];
        }

        $langTag = current_lang_tag();

        if (fs_user()->check()) {
            $uid = fs_user('detail.uid');
            $cacheKey = "fresns_web_group_{$listKey}_by_{$uid}_{$langTag}";
        } else {
            $cacheKey = "fresns_web_group_{$listKey}_by_guest_{$langTag}";
        }

        $cacheTag = 'fresnsWeb';

        // is known to be empty
        $isKnownEmpty = CacheHelper::isKnownEmpty($cacheKey);
        if ($isKnownEmpty) {
            return [];
        }

        // get cache
        $listArr = CacheHelper::get($cacheKey, $cacheTag);

        if (empty($listArr)) {
            switch ($listKey) {
                // categories
                case 'categories':
                    $result = ApiHelper::make()->get('/api/v2/group/categories', [
                        'query' => [
                            'pageSize' => 100,
                            'page' => 1,
                        ],
                    ]);

                    $listArr = data_get($result, 'data.list', []);
                break;

                // tree
                case 'tree':
                    $result = ApiHelper::make()->get('/api/v2/group/tree');

                    $listArr = data_get($result, 'data', []);
                break;
            }

            $cacheTime = CacheHelper::fresnsCacheTimeByFileType(File::TYPE_ALL, 120);
            CacheHelper::put($listArr, $cacheKey, $cacheTag, null, $cacheTime);
        }

        return $listArr ?? [];
    }

    // get fresns index list
    public static function getFresnsIndexList(string $listKey): array
    {
        $listKeyArr = [
            'users',
            'groups',
            'hashtags',
            'posts',
            'comments',
        ];

        if (! in_array($listKey, $listKeyArr)) {
            return [];
        }

        $langTag = current_lang_tag();

        if (fs_user()->check()) {
            $uid = fs_user('detail.uid');
            $cacheKey = "fresns_web_{$listKey}_index_list_by_{$uid}_{$langTag}";
        } else {
            $cacheKey = "fresns_web_{$listKey}_index_list_by_guest_{$langTag}";
        }

        // is known to be empty
        $isKnownEmpty = CacheHelper::isKnownEmpty($cacheKey);
        if ($isKnownEmpty) {
            return [];
        }

        $cacheTag = 'fresnsWeb';

        // get cache
        $listArr = CacheHelper::get($cacheKey, $cacheTag);

        if (empty($listArr)) {
            switch ($listKey) {
                // users
                case 'users':
                    $userQuery = QueryHelper::configToQuery(QueryHelper::TYPE_USER);
                    $result = ApiHelper::make()->get('/api/v2/user/list', [
                        'query' => $userQuery,
                    ]);
                break;

                // groups
                case 'groups':
                    $groupQuery = QueryHelper::configToQuery(QueryHelper::TYPE_GROUP);
                    $result = ApiHelper::make()->get('/api/v2/group/list', [
                        'query' => $groupQuery,
                    ]);
                break;

                // hashtags
                case 'hashtags':
                    $hashtagQuery = QueryHelper::configToQuery(QueryHelper::TYPE_HASHTAG);
                    $result = ApiHelper::make()->get('/api/v2/hashtag/list', [
                        'query' => $hashtagQuery,
                    ]);
                break;

                // posts
                case 'posts':
                    $postQuery = QueryHelper::configToQuery(QueryHelper::TYPE_POST);
                    $result = ApiHelper::make()->get('/api/v2/post/list', [
                        'query' => $postQuery,
                    ]);
                break;

                // comments
                case 'comments':
                    $commentQuery = QueryHelper::configToQuery(QueryHelper::TYPE_COMMENT);
                    $result = ApiHelper::make()->get('/api/v2/comment/list', [
                        'query' => $commentQuery,
                    ]);
                break;
            }

            $listArr = data_get($result, 'data.list', []);

            $cacheTime = CacheHelper::fresnsCacheTimeByFileType(File::TYPE_ALL, 120);
            CacheHelper::put($listArr, $cacheKey, $cacheTag, null, $cacheTime);
        }

        return $listArr ?? [];
    }

    // get fresns list
    public static function getFresnsList(string $listKey): array
    {
        $listKeyArr = [
            'users',
            'groups',
            'hashtags',
            'posts',
            'comments',
        ];

        if (! in_array($listKey, $listKeyArr)) {
            return [];
        }

        $langTag = current_lang_tag();

        if (fs_user()->check()) {
            $uid = fs_user('detail.uid');
            $cacheKey = "fresns_web_{$listKey}_list_by_{$uid}_{$langTag}";
        } else {
            $cacheKey = "fresns_web_{$listKey}_list_by_guest_{$langTag}";
        }

        // is known to be empty
        $isKnownEmpty = CacheHelper::isKnownEmpty($cacheKey);
        if ($isKnownEmpty) {
            return [];
        }

        $cacheTag = 'fresnsWeb';

        // get cache
        $listArr = CacheHelper::get($cacheKey, $cacheTag);

        if (empty($listArr)) {
            switch ($listKey) {
                // users
                case 'users':
                    $userQuery = QueryHelper::configToQuery(QueryHelper::TYPE_USER_LIST);
                    $result = ApiHelper::make()->get('/api/v2/user/list', [
                        'query' => $userQuery,
                    ]);
                break;

                // groups
                case 'groups':
                    $groupQuery = QueryHelper::configToQuery(QueryHelper::TYPE_GROUP_LIST);
                    $result = ApiHelper::make()->get('/api/v2/group/list', [
                        'query' => $groupQuery,
                    ]);
                break;

                // hashtags
                case 'hashtags':
                    $hashtagQuery = QueryHelper::configToQuery(QueryHelper::TYPE_HASHTAG_LIST);
                    $result = ApiHelper::make()->get('/api/v2/hashtag/list', [
                        'query' => $hashtagQuery,
                    ]);
                break;

                // posts
                case 'posts':
                    $postQuery = QueryHelper::configToQuery(QueryHelper::TYPE_POST_LIST);
                    $result = ApiHelper::make()->get('/api/v2/post/list', [
                        'query' => $postQuery,
                    ]);
                break;

                // comments
                case 'comments':
                    $commentQuery = QueryHelper::configToQuery(QueryHelper::TYPE_COMMENT_LIST);
                    $result = ApiHelper::make()->get('/api/v2/comment/list', [
                        'query' => $commentQuery,
                    ]);
                break;
            }

            $listArr = data_get($result, 'data.list', []);

            $cacheTime = CacheHelper::fresnsCacheTimeByFileType(File::TYPE_ALL, 120);
            CacheHelper::put($listArr, $cacheKey, $cacheTag, null, $cacheTime);
        }

        return $listArr ?? [];
    }

    // get fresns sticky posts
    public static function getFresnsStickyPosts(?string $gid = null): array
    {
        $langTag = current_lang_tag();

        if (empty($gid)) {
            $cacheKey = "fresns_web_sticky_posts_{$langTag}";
            $query = [
                'stickyState' => 3,
            ];
        } else {
            $cacheKey = "fresns_web_group_{$gid}_sticky_posts_{$langTag}";
            $query = [
                'gid' => $gid,
                'stickyState' => 2,
            ];
        }

        // is known to be empty
        $isKnownEmpty = CacheHelper::isKnownEmpty($cacheKey);
        if ($isKnownEmpty) {
            return [];
        }

        $cacheTag = 'fresnsWeb';

        // get cache
        $listArr = CacheHelper::get($cacheKey, $cacheTag);

        if (empty($listArr)) {
            $result = ApiHelper::make()->get('/api/v2/post/list', [
                'query' => $query,
            ]);

            $listArr = data_get($result, 'data.list', []);

            $cacheTime = CacheHelper::fresnsCacheTimeByFileType(File::TYPE_ALL, 360);
            CacheHelper::put($listArr, $cacheKey, $cacheTag, null, $cacheTime);
        }

        return $listArr ?? [];
    }

    // get fresns sticky comments
    public static function getFresnsStickyComments(string $pid): array
    {
        $langTag = current_lang_tag();

        $cacheKey = "fresns_web_post_{$pid}_sticky_comments_{$langTag}";
        $cacheTag = 'fresnsWeb';

        // is known to be empty
        $isKnownEmpty = CacheHelper::isKnownEmpty($cacheKey);
        if ($isKnownEmpty) {
            return [];
        }

        // get cache
        $listArr = CacheHelper::get($cacheKey, $cacheTag);

        if (empty($listArr)) {
            $result = ApiHelper::make()->get('/api/v2/comment/list', [
                'query' => [
                    'pid' => $pid,
                    'sticky' => true,
                ],
            ]);

            $listArr = data_get($result, 'data.list', []);

            $cacheTime = CacheHelper::fresnsCacheTimeByFileType(File::TYPE_ALL, 360);
            CacheHelper::put($listArr, $cacheKey, $cacheTag, null, $cacheTime);
        }

        return $listArr ?? [];
    }

    // get fresns content types
    public static function getFresnsContentTypes(string $type): array
    {
        $langTag = current_lang_tag();

        $cacheKey = "fresns_web_{$type}_content_types_{$langTag}";
        $cacheTags = ['fresnsWeb', 'fresnsWebConfigs'];

        // is known to be empty
        $isKnownEmpty = CacheHelper::isKnownEmpty($cacheKey);
        if ($isKnownEmpty) {
            return [];
        }

        // get cache
        $listArr = CacheHelper::get($cacheKey, $cacheTags);

        if (empty($listArr)) {
            $result = ApiHelper::make()->get("/api/v2/global/{$type}/content-types");

            $listArr = data_get($result, 'data', []);

            CacheHelper::put($listArr, $cacheKey, $cacheTags);
        }

        return $listArr ?? [];
    }

    // get fresns stickers
    public static function getFresnsStickers(): array
    {
        $langTag = current_lang_tag();

        $cacheKey = "fresns_web_stickers_{$langTag}";
        $cacheTags = ['fresnsWeb', 'fresnsWebConfigs'];

        // is known to be empty
        $isKnownEmpty = CacheHelper::isKnownEmpty($cacheKey);
        if ($isKnownEmpty) {
            return [];
        }

        // get cache
        $listArr = CacheHelper::get($cacheKey, $cacheTags);

        if (empty($listArr)) {
            $result = ApiHelper::make()->get('/api/v2/global/stickers');

            $listArr = data_get($result, 'data', []);

            $cacheTime = CacheHelper::fresnsCacheTimeByFileType(File::TYPE_IMAGE);
            CacheHelper::put($listArr, $cacheKey, $cacheTags, null, $cacheTime);
        }

        return $listArr ?? [];
    }

    // cache forget account and user
    public static function cacheForgetAccountAndUser()
    {
        $cookiePrefix = fs_db_config('engine_cookie_prefix', 'fresns_');

        $aid = Cookie::get("{$cookiePrefix}aid");
        $uid = Cookie::get("{$cookiePrefix}uid");

        CacheHelper::forgetFresnsMultilingual("fresns_web_account_{$aid}", 'fresnsWeb');
        CacheHelper::forgetFresnsMultilingual("fresns_web_user_{$uid}", 'fresnsWeb');
    }
}
