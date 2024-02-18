<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jevan Tang
 * Released under the Apache-2.0 License.
 */

namespace Fresns\WebEngine\Helpers;

use App\Helpers\CacheHelper;
use App\Helpers\ConfigHelper;
use App\Models\File;
use App\Utilities\ConfigUtility;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Str;

class DataHelper
{
    // get api data
    public static function getApiDataTemplate(?string $type = 'list'): array
    {
        $message = ConfigUtility::getCodeMessage(35303, 'Fresns', current_lang_tag()) ?? 'Unknown Warning';

        $data = [
            'code' => 0,
            'message' => "[35303] {$message}",
            'data' => [
                'pagination' => [
                    'total' => 0,
                    'pageSize' => 15,
                    'currentPage' => 1,
                    'lastPage' => 1,
                ],
                'list' => [],
            ],
        ];

        if ($type == 'list') {
            return $data;
        }

        return [
            'code' => 35303,
            'message' => $message,
            'data' => [],
        ];
    }

    // get editor url
    public static function getEditorUrl(string $url, string $type, ?int $draftId = null, ?string $fsid = null)
    {
        $headers = Arr::except(ApiHelper::getHeaders(), ['Accept']);

        $authorization = urlencode(base64_encode(json_encode($headers)));

        $scene = match ($type) {
            'post' => 'postEditor',
            'comment' => 'commentEditor',
            default => 'postEditor',
        };

        $pluginUrl = Str::replace('{authorization}', $authorization, $url);
        $pluginUrl = Str::replace('{type}', $type, $pluginUrl);
        $pluginUrl = Str::replace('{scene}', $scene, $pluginUrl);
        if ($draftId) {
            $logIdName = match ($type) {
                'post' => '{plid}',
                'comment' => '{clid}',
                default => '{plid}',
            };
            $pluginUrl = Str::replace($logIdName, $draftId, $pluginUrl);
        }
        if ($fsid) {
            $fsidName = match ($type) {
                'post' => '{pid}',
                'comment' => '{cid}',
                default => '{pid}',
            };

            $pluginUrl = Str::replace($fsidName, $fsid, $pluginUrl);
        }

        return $pluginUrl;
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
                    $result = ApiHelper::make()->get('/api/fresns/v1/group/categories', [
                        'query' => [
                            'pageSize' => 100,
                            'page' => 1,
                        ],
                    ]);

                    $listArr = data_get($result, 'data.list', []);
                    break;

                    // tree
                case 'tree':
                    $result = ApiHelper::make()->get('/api/fresns/v1/group/tree');

                    $listArr = data_get($result, 'data', []);
                    break;
            }

            $cacheTime = CacheHelper::fresnsCacheTimeByFileType(File::TYPE_ALL, 60);
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
            if (fs_config('site_mode') == 'private' && $listKey != 'groups' && ! fs_user('detail.expiryDateTime')) {
                return [];
            }

            switch ($listKey) {
                // users
                case 'users':
                    $userQuery = QueryHelper::configToQuery(QueryHelper::TYPE_USER);
                    $result = ApiHelper::make()->get('/api/fresns/v1/user/list', [
                        'query' => $userQuery,
                    ]);
                    break;

                    // groups
                case 'groups':
                    $groupQuery = QueryHelper::configToQuery(QueryHelper::TYPE_GROUP);
                    $result = ApiHelper::make()->get('/api/fresns/v1/group/list', [
                        'query' => $groupQuery,
                    ]);
                    break;

                    // hashtags
                case 'hashtags':
                    $hashtagQuery = QueryHelper::configToQuery(QueryHelper::TYPE_HASHTAG);
                    $result = ApiHelper::make()->get('/api/fresns/v1/hashtag/list', [
                        'query' => $hashtagQuery,
                    ]);
                    break;

                    // posts
                case 'posts':
                    $postQuery = QueryHelper::configToQuery(QueryHelper::TYPE_POST);
                    $result = ApiHelper::make()->get('/api/fresns/v1/post/list', [
                        'query' => $postQuery,
                    ]);
                    break;

                    // comments
                case 'comments':
                    $commentQuery = QueryHelper::configToQuery(QueryHelper::TYPE_COMMENT);
                    $result = ApiHelper::make()->get('/api/fresns/v1/comment/list', [
                        'query' => $commentQuery,
                    ]);
                    break;
            }

            $listArr = data_get($result, 'data.list', []);

            $cacheTime = CacheHelper::fresnsCacheTimeByFileType(File::TYPE_ALL, 60);
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
            if (fs_config('site_mode') == 'private' && $listKey != 'groups' && ! fs_user('detail.expiryDateTime')) {
                return [];
            }

            switch ($listKey) {
                // users
                case 'users':
                    $userQuery = QueryHelper::configToQuery(QueryHelper::TYPE_USER_LIST);
                    $result = ApiHelper::make()->get('/api/fresns/v1/user/list', [
                        'query' => $userQuery,
                    ]);
                    break;

                    // groups
                case 'groups':
                    $groupQuery = QueryHelper::configToQuery(QueryHelper::TYPE_GROUP_LIST);
                    $result = ApiHelper::make()->get('/api/fresns/v1/group/list', [
                        'query' => $groupQuery,
                    ]);
                    break;

                    // hashtags
                case 'hashtags':
                    $hashtagQuery = QueryHelper::configToQuery(QueryHelper::TYPE_HASHTAG_LIST);
                    $result = ApiHelper::make()->get('/api/fresns/v1/hashtag/list', [
                        'query' => $hashtagQuery,
                    ]);
                    break;

                    // posts
                case 'posts':
                    $postQuery = QueryHelper::configToQuery(QueryHelper::TYPE_POST_LIST);
                    $result = ApiHelper::make()->get('/api/fresns/v1/post/list', [
                        'query' => $postQuery,
                    ]);
                    break;

                    // comments
                case 'comments':
                    $commentQuery = QueryHelper::configToQuery(QueryHelper::TYPE_COMMENT_LIST);
                    $result = ApiHelper::make()->get('/api/fresns/v1/comment/list', [
                        'query' => $commentQuery,
                    ]);
                    break;
            }

            $listArr = data_get($result, 'data.list', []);

            $cacheTime = CacheHelper::fresnsCacheTimeByFileType(File::TYPE_ALL, 60);
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
            if (fs_config('site_mode') == 'private' && ! fs_user('detail.expiryDateTime')) {
                return [];
            }

            $result = ApiHelper::make()->get('/api/fresns/v1/post/list', [
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
            if (fs_config('site_mode') == 'private' && ! fs_user('detail.expiryDateTime')) {
                return [];
            }

            $result = ApiHelper::make()->get('/api/fresns/v1/comment/list', [
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

    // cache forget account and user
    public static function cacheForgetAccountAndUser()
    {
        $cookiePrefix = ConfigHelper::fresnsConfigByItemKey('website_cookie_prefix') ?? 'fresns_';

        $aid = Cookie::get("{$cookiePrefix}aid");
        $uid = Cookie::get("{$cookiePrefix}uid");

        CacheHelper::forgetFresnsMultilingual("fresns_web_account_{$aid}", 'fresnsWeb');
        CacheHelper::forgetFresnsMultilingual("fresns_web_user_{$uid}", 'fresnsWeb');
    }
}
