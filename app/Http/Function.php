<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jevan Tang
 * Released under the Apache-2.0 License.
 */

use App\Helpers\CacheHelper;
use App\Helpers\ConfigHelper;
use App\Models\File;
use Fresns\WebEngine\Auth\UserGuard;
use Fresns\WebEngine\Helpers\ApiHelper;
use Fresns\WebEngine\Helpers\DataHelper;
use Illuminate\Support\Facades\App;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;

// is_local_api
if (! function_exists('is_local_api')) {
    function is_local_api()
    {
        $engineApiType = ConfigHelper::fresnsConfigByItemKey('webengine_api_type');

        return $engineApiType == 'local';
    }
}

// is_remote_api
if (! function_exists('is_remote_api')) {
    function is_remote_api()
    {
        $engineApiType = ConfigHelper::fresnsConfigByItemKey('webengine_api_type');

        return $engineApiType == 'remote';
    }
}

// current_lang_tag
if (! function_exists('current_lang_tag')) {
    function current_lang_tag()
    {
        return App::getLocale() ?? ConfigHelper::fresnsConfigByItemKey('default_language');
    }
}

// fs_status
if (! function_exists('fs_status')) {
    function fs_status(string $key)
    {
        $cacheKey = 'fresns_web_status';
        $cacheTags = ['fresnsWeb', 'fresnsWebConfigs'];

        $statusJson = CacheHelper::get($cacheKey, $cacheTags);

        if (empty($statusJson)) {
            $isLocal = is_local_api();

            $localApiHost = config('app.url');
            $remoteApiHost = ConfigHelper::fresnsConfigByItemKey('webengine_api_host');

            $apiHost = $isLocal ? $localApiHost : $remoteApiHost;

            $fileUrl = $apiHost.'/status.json';
            $client = new \GuzzleHttp\Client(['verify' => false]);

            try {
                $response = $client->request('GET', $fileUrl);
                $statusJson = json_decode($response->getBody(), true);
            } catch (\GuzzleHttp\Exception\RequestException $e) {
                $statusJson = [
                    'name' => 'Fresns',
                    'activate' => true,
                    'deactivateDescribe' => [
                        'default' => '',
                    ],
                ];
            }

            CacheHelper::put($statusJson, $cacheKey, $cacheTags, 10, now()->addMinutes(10));
        }

        return $statusJson[$key] ?? null;
    }
}

// fs_api_config
if (! function_exists('fs_api_config')) {
    function fs_api_config(string $itemKey, mixed $default = null)
    {
        $langTag = current_lang_tag();

        $cacheKey = "fresns_web_api_config_all_{$langTag}";
        $cacheTags = ['fresnsWeb', 'fresnsWebConfigs'];

        $apiConfig = CacheHelper::get($cacheKey, $cacheTags);

        if (empty($apiConfig)) {
            $result = ApiHelper::make()->get('/api/v2/global/configs');

            $apiConfig = data_get($result, 'data');

            $cacheTime = CacheHelper::fresnsCacheTimeByFileType(File::TYPE_ALL);
            CacheHelper::put($apiConfig, $cacheKey, $cacheTags, null, $cacheTime);
        }

        return $apiConfig[$itemKey] ?? $default;
    }
}

// fs_db_config
if (! function_exists('fs_db_config')) {
    function fs_db_config(string $itemKey, mixed $default = null)
    {
        $langTag = current_lang_tag();

        return ConfigHelper::fresnsConfigApiByItemKey($itemKey, $langTag) ?? $default;
    }
}

// fs_lang
if (! function_exists('fs_lang')) {
    function fs_lang(string $langKey, ?string $default = null): ?string
    {
        $langArr = fs_api_config('language_pack_contents');
        $result = $langArr[$langKey] ?? $default;

        return $result;
    }
}

// fs_code_message
if (! function_exists('fs_code_message')) {
    function fs_code_message(int $code, ?string $fskey = 'Fresns', ?string $default = null): ?string
    {
        $langTag = current_lang_tag();

        $cacheKey = "fresns_web_code_message_all_{$fskey}_{$langTag}";
        $cacheTags = ['fresnsWeb', 'fresnsWebConfigs'];

        $codeMessages = CacheHelper::get($cacheKey, $cacheTags);

        if (empty($codeMessages)) {
            $codeMessages = ApiHelper::make()->get('/api/v2/global/code-messages', [
                'query' => [
                    'fskey' => $fskey,
                    'isAll' => true,
                ],
            ]);

            CacheHelper::put($codeMessages, $cacheKey, $cacheTags);
        }

        return data_get($codeMessages, "data.{$code}") ?? $default;
    }
}

// fs_route
if (! function_exists('fs_route')) {
    /**
     * @param  string|null  $url
     * @param  string|bool|null  $locale
     * @return string
     */
    function fs_route(string $url = null, string|bool $locale = null): string
    {
        return LaravelLocalization::localizeUrl($url, $locale);
    }
}

// fs_channels
if (! function_exists('fs_channels')) {
    function fs_channels()
    {
        $langTag = current_lang_tag();

        $uid = 'guest';
        if (fs_user()->check()) {
            $uid = fs_user('detail.uid');
        }

        $cacheKey = "fresns_web_channels_{$uid}_{$langTag}";
        $cacheTag = 'fresnsWeb';

        // is known to be empty
        $isKnownEmpty = CacheHelper::isKnownEmpty($cacheKey);
        if ($isKnownEmpty) {
            return [];
        }

        $channels = CacheHelper::get($cacheKey, $cacheTag);

        if (empty($channels)) {
            $result = ApiHelper::make()->get('/api/v2/global/channels');

            $channels = data_get($result, 'data');

            CacheHelper::put($channels, $cacheKey, $cacheTag, 5, now()->addMinutes(5));
        }

        return $channels ?? [];
    }
}

// fs_account
if (! function_exists('fs_account')) {
    /**
     * @return AccountGuard|mixin
     */
    function fs_account(?string $detailKey = null)
    {
        if ($detailKey) {
            return app('fresns.account')->get($detailKey);
        }

        return app('fresns.account');
    }
}

// fs_user
if (! function_exists('fs_user')) {
    /**
     * @return UserGuard|mixin
     */
    function fs_user(?string $detailKey = null)
    {
        if ($detailKey) {
            return app('fresns.user')->get($detailKey);
        }

        return app('fresns.user');
    }
}

// fs_user_panel
if (! function_exists('fs_user_panel')) {
    /**
     * @param  string|null  $key
     * @return array
     */
    function fs_user_panel(?string $key = null)
    {
        return DataHelper::getFresnsUserPanel($key);
    }
}

// fs_groups
if (! function_exists('fs_groups')) {
    /**
     * @param  string  $listKey
     * @return array
     */
    function fs_groups(string $listKey)
    {
        return DataHelper::getFresnsGroups($listKey);
    }
}

// fs_index_list
if (! function_exists('fs_index_list')) {
    /**
     * @param  string  $listKey
     * @return array
     */
    function fs_index_list(string $listKey)
    {
        if (fs_api_config('site_mode') == 'private' && fs_user()->guest()) {
            return [];
        }

        return DataHelper::getFresnsIndexList($listKey);
    }
}

// fs_list
if (! function_exists('fs_list')) {
    /**
     * @param  string  $listKey
     * @return array
     */
    function fs_list(string $listKey)
    {
        if (fs_api_config('site_mode') == 'private' && fs_user()->guest()) {
            return [];
        }

        return DataHelper::getFresnsList($listKey);
    }
}

// fs_sticky_posts
if (! function_exists('fs_sticky_posts')) {
    /**
     * @param  string|null  $gid
     * @return array
     */
    function fs_sticky_posts(?string $gid = null)
    {
        if (fs_api_config('site_mode') == 'private' && fs_user()->guest()) {
            return [];
        }

        return DataHelper::getFresnsStickyPosts($gid);
    }
}

// fs_sticky_comments
if (! function_exists('fs_sticky_comments')) {
    /**
     * @param  string  $pid
     * @return array
     */
    function fs_sticky_comments(string $pid)
    {
        if (fs_api_config('site_mode') == 'private' && fs_user()->guest()) {
            return [];
        }

        return DataHelper::getFresnsStickyComments($pid);
    }
}

// fs_content_types
if (! function_exists('fs_content_types')) {
    /**
     * @param  string  $type
     * @return array
     */
    function fs_content_types(string $type)
    {
        return DataHelper::getFresnsContentTypes($type);
    }
}

// fs_stickers
if (! function_exists('fs_stickers')) {
    /**
     * @return array
     */
    function fs_stickers()
    {
        if (fs_api_config('site_mode') == 'private' && fs_user()->guest()) {
            return [];
        }

        return DataHelper::getFresnsStickers();
    }
}
