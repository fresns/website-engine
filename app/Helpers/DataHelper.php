<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jevan Tang
 * Released under the Apache-2.0 License.
 */

namespace Fresns\WebsiteEngine\Helpers;

use App\Helpers\CacheHelper;
use App\Helpers\ConfigHelper;
use App\Models\File;
use App\Utilities\ConfigUtility;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Str;

class DataHelper
{
    // account and user login
    public static function accountAndUserCookie(array $authToken): void
    {
        $aid = $authToken['aid'];
        $aidToken = $authToken['aidToken'];
        $uid = $authToken['uid'];
        $uidToken = $authToken['uidToken'];
        $expiredHours = $authToken['expiredHours'];

        DataHelper::cacheForgetAccountAndUser($aid, $uid);

        if (empty($expiredHours)) {
            Cookie::queue(Cookie::forever('fresns_aid', $aid, '/'));
            Cookie::queue(Cookie::forever('fresns_aid_token', $aidToken, '/'));
            Cookie::queue(Cookie::forever('fresns_uid', $uid, '/'));
            Cookie::queue(Cookie::forever('fresns_uid_token', $uidToken, '/'));

            return;
        }

        $cookieMinutes = $expiredHours * 60;

        Cookie::queue('fresns_aid', $aid, $cookieMinutes);
        Cookie::queue('fresns_aid_token', $aidToken, $cookieMinutes);
        Cookie::queue('fresns_uid', $uid, $cookieMinutes);
        Cookie::queue('fresns_uid_token', $uidToken, $cookieMinutes);
    }

    // get api data
    public static function getApiDataTemplate(?string $type = 'list'): array
    {
        $message = ConfigUtility::getCodeMessage(35303, 'Fresns', fs_theme('lang')) ?? 'Unknown Warning';

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
    public static function getEditorUrl(string $url, string $type, ?string $did = null, ?string $fsid = null): string
    {
        $headers = Arr::except(ApiHelper::getHeaders(), ['Accept']);

        $accessToken = urlencode(base64_encode(json_encode($headers)));

        $scene = match ($type) {
            'post' => 'postEditor',
            'comment' => 'commentEditor',
            default => 'postEditor',
        };

        $pluginUrl = Str::replace('{accessToken}', $accessToken, $url);
        $pluginUrl = Str::replace('{type}', $type, $pluginUrl);
        $pluginUrl = Str::replace('{scene}', $scene, $pluginUrl);

        if ($did) {
            $pluginUrl = Str::replace('{did}', $did, $pluginUrl);
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

    // get editor configs
    public static function getEditorConfigs(string $type): array
    {
        $uid = fs_user('detail.uid');
        $langTag = fs_theme('lang');

        $cacheKey = "fresns_web_editor_{$type}_configs_{$uid}_{$langTag}";
        $cacheTags = ['fresnsWeb', 'fresnsWebConfigs'];

        // get cache
        $configs = CacheHelper::get($cacheKey, $cacheTags);

        if (empty($configs)) {
            $result = ApiHelper::make()->get("/api/fresns/v1/editor/{$type}/configs");

            $configs = data_get($result, 'data');

            $cacheTime = CacheHelper::fresnsCacheTimeByFileType(File::TYPE_IMAGE);
            CacheHelper::put($configs, $cacheKey, $cacheTags, $cacheTime);
        }

        return $configs;
    }

    // get fresns content list
    public static function getFresnsContentList(string $channel, string $type): ?array
    {
        $channelArr = [
            'user',
            'group',
            'hashtag',
            'geotag',
            'post',
            'comment',
        ];

        $typeArr = [
            'home',
            'list',
        ];

        if (! in_array($channel, $channelArr) || ! in_array($type, $typeArr)) {
            return [];
        }

        $langTag = fs_theme('lang');

        if (fs_user()->check()) {
            $uid = fs_user('detail.uid');
            $cacheKey = "fresns_web_content_{$channel}_{$type}_by_{$uid}_{$langTag}";
        } else {
            $cacheKey = "fresns_web_content_{$channel}_{$type}_by_guest_{$langTag}";
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
            $queryType = $channel;
            if ($type == 'list') {
                $queryType = $channel.'_list';
            }

            $channelGroupType = ConfigHelper::fresnsConfigByItemKey('channel_group_type');

            if ($channel == 'group' && $type == 'home' && $channelGroupType == 'tree') {
                $result = ApiHelper::make()->get('/api/fresns/v1/group/tree');

                $listArr = data_get($result, 'data', []);
            } else {
                $queryConfig = QueryHelper::configToQuery($queryType);

                $result = ApiHelper::make()->get("/api/fresns/v1/{$channel}/list", [
                    'query' => $queryConfig,
                ]);

                $listArr = data_get($result, 'data.list', []);
            }

            $cacheTime = CacheHelper::fresnsCacheTimeByFileType(File::TYPE_ALL, 60);
            CacheHelper::put($listArr, $cacheKey, $cacheTag, $cacheTime);
        }

        return $listArr ?? [];
    }

    // get fresns sticky posts
    public static function getFresnsStickyPosts(?string $gid = null): array
    {
        $langTag = fs_theme('lang');

        if (empty($gid)) {
            $cacheKey = "fresns_web_sticky_posts_by_global_{$langTag}";
            $query = [
                'stickyState' => 3,
            ];
        } else {
            $cacheKey = "fresns_web_sticky_posts_by_group_{$gid}_{$langTag}";
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
            $result = ApiHelper::make()->get('/api/fresns/v1/post/list', [
                'query' => $query,
            ]);

            $listArr = data_get($result, 'data.list', []);

            $cacheTime = CacheHelper::fresnsCacheTimeByFileType(File::TYPE_ALL, 360);
            CacheHelper::put($listArr, $cacheKey, $cacheTag, $cacheTime);
        }

        return $listArr ?? [];
    }

    // get fresns sticky comments
    public static function getFresnsStickyComments(string $pid): array
    {
        $langTag = fs_theme('lang');

        $cacheKey = "fresns_web_sticky_comments_by_{$pid}_{$langTag}";
        $cacheTag = 'fresnsWeb';

        // is known to be empty
        $isKnownEmpty = CacheHelper::isKnownEmpty($cacheKey);
        if ($isKnownEmpty) {
            return [];
        }

        // get cache
        $listArr = CacheHelper::get($cacheKey, $cacheTag);

        if (empty($listArr)) {
            $result = ApiHelper::make()->get('/api/fresns/v1/comment/list', [
                'query' => [
                    'pid' => $pid,
                    'sticky' => 1,
                ],
            ]);

            $listArr = data_get($result, 'data.list', []);

            $cacheTime = CacheHelper::fresnsCacheTimeByFileType(File::TYPE_ALL, 360);
            CacheHelper::put($listArr, $cacheKey, $cacheTag, $cacheTime);
        }

        return $listArr ?? [];
    }

    // cache forget account and user
    public static function cacheForgetAccountAndUser(?string $aid = null, ?int $uid = null)
    {
        $currentAid = $aid ?: Cookie::get('fresns_aid');
        $currentUid = $uid ?: Cookie::get('fresns_uid');

        CacheHelper::forgetFresnsMultilingual("fresns_web_account_{$currentAid}", 'fresnsWeb');
        CacheHelper::forgetFresnsMultilingual("fresns_web_user_{$currentUid}", 'fresnsWeb');
        CacheHelper::forgetFresnsMultilingual("fresns_web_user_overview_{$currentUid}", 'fresnsWeb');

        CacheHelper::forgetFresnsMultilingual("fresns_web_channels_{$currentUid}", 'fresnsWeb');

        CacheHelper::forgetFresnsMultilingual("fresns_web_editor_post_configs_{$currentUid}", ['fresnsWeb', 'fresnsWebConfigs']);
        CacheHelper::forgetFresnsMultilingual("fresns_web_editor_comment_configs_{$currentUid}", ['fresnsWeb', 'fresnsWebConfigs']);

        $channelArr = [
            'user',
            'group',
            'hashtag',
            'geotag',
            'post',
            'comment',
        ];

        foreach ($channelArr as $channel) {
            // fresns_web_content_{$channel}_{$type}_by_{$uid}_{$langTag}
            CacheHelper::forgetFresnsMultilingual("fresns_web_content_{$channel}_home_by_{$currentUid}", 'fresnsWeb');
            CacheHelper::forgetFresnsMultilingual("fresns_web_content_{$channel}_list_by_{$currentUid}", 'fresnsWeb');
        }
    }
}
