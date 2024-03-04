<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jevan Tang
 * Released under the Apache-2.0 License.
 */

use App\Helpers\CacheHelper;
use App\Helpers\ConfigHelper;
use App\Helpers\PluginHelper;
use App\Models\File;
use App\Utilities\ArrUtility;
use Fresns\WebEngine\Auth\UserGuard;
use Fresns\WebEngine\Helpers\ApiHelper;
use Fresns\WebEngine\Helpers\DataHelper;
use hisorange\BrowserDetect\Parser as Browser;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Str;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;

// is_local_api
if (! function_exists('is_local_api')) {
    function is_local_api(): bool
    {
        $engineApiType = ConfigHelper::fresnsConfigByItemKey('webengine_api_type');

        return $engineApiType == 'local';
    }
}

// is_remote_api
if (! function_exists('is_remote_api')) {
    function is_remote_api(): bool
    {
        $engineApiType = ConfigHelper::fresnsConfigByItemKey('webengine_api_type');

        return $engineApiType == 'remote';
    }
}

// fs_status
if (! function_exists('fs_status')) {
    function fs_status(?string $key = null): mixed
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

        if (empty($key)) {
            return $statusJson;
        }

        return $statusJson[$key] ?? null;
    }
}

// fs_route
if (! function_exists('fs_route')) {
    function fs_route(?string $url = null, string|bool $locale = null): string
    {
        return LaravelLocalization::localizeUrl($url, $locale);
    }
}

// fs_helpers
if (! function_exists('fs_helpers')) {
    function fs_helpers(string $helper, string $method, mixed $data = null, ?array $options = []): mixed
    {
        $helperData = null;

        $helper = Str::lower($helper);
        $method = Str::lower($method);

        switch ($helper) {
            case 'arr':
                $availableMethod = match ($method) {
                    'get' => 'get',
                    'forget' => 'forget',
                    'pull' => 'pull',
                    default => null,
                };

                $key = $options['key'] ?? null;
                $values = $options['values'] ?? null;
                $asArray = $options['asArray'] ?? true;

                if (empty($availableMethod) || empty($key) || empty($values)) {
                    return [];
                }

                $helperData = ArrUtility::$availableMethod($data, $key, $values, $asArray);
                break;

            default:
                $helperData = null;
        }

        return $helperData;
    }
}

// fs_theme
if (! function_exists('fs_theme')) {
    function fs_theme(string $type): ?string
    {
        $converted = Str::lower($type);

        $themeFskey = null;
        if ($converted != 'lang') {
            $themeFskey = Browser::isMobile() ? ConfigHelper::fresnsConfigByItemKey('webengine_view_mobile') : ConfigHelper::fresnsConfigByItemKey('webengine_view_desktop');
        }

        $info = match ($converted) {
            'fskey' => $themeFskey,
            'version' => PluginHelper::fresnsPluginVersionByFskey($themeFskey),
            'lang' => App::getLocale() ?? ConfigHelper::fresnsConfigByItemKey('default_language'),
            'assets' => "/assets/{$themeFskey}/",
            default => null,
        };

        return $info;
    }
}

// fs_config
if (! function_exists('fs_config')) {
    function fs_config(?string $itemKey = null, mixed $default = null): mixed
    {
        $langTag = fs_theme('lang');

        $cacheKey = "fresns_web_configs_{$langTag}";
        $cacheTags = ['fresnsWeb', 'fresnsWebConfigs'];

        $configs = CacheHelper::get($cacheKey, $cacheTags);

        if (empty($configs)) {
            $result = ApiHelper::make()->get('/api/fresns/v1/global/configs');

            $configs = data_get($result, 'data');

            $cacheTime = CacheHelper::fresnsCacheTimeByFileType(File::TYPE_ALL);
            CacheHelper::put($configs, $cacheKey, $cacheTags, null, $cacheTime);
        }

        if (empty($itemKey)) {
            return $configs;
        }

        return $configs[$itemKey] ?? $default;
    }
}

// fs_lang
if (! function_exists('fs_lang')) {
    function fs_lang(?string $langKey = null, ?string $default = null): mixed
    {
        $langTag = fs_theme('lang');

        $cacheKey = "fresns_web_languages_{$langTag}";
        $cacheTags = ['fresnsWeb', 'fresnsWebConfigs'];

        $languages = CacheHelper::get($cacheKey, $cacheTags);

        if (empty($languages)) {
            $result = ApiHelper::make()->get('/api/fresns/v1/global/language-pack');

            $languages = data_get($result, 'data');

            $cacheTime = CacheHelper::fresnsCacheTimeByFileType(File::TYPE_ALL);
            CacheHelper::put($languages, $cacheKey, $cacheTags, null, $cacheTime);
        }

        if (empty($langKey)) {
            return $languages;
        }

        return $languages[$langKey] ?? $default;
    }
}

// fs_channels
if (! function_exists('fs_channels')) {
    function fs_channels(): ?array
    {
        $langTag = fs_theme('lang');

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
            $result = ApiHelper::make()->get('/api/fresns/v1/global/channels');

            $channels = data_get($result, 'data');

            CacheHelper::put($channels, $cacheKey, $cacheTag, 5, now()->addMinutes(5));
        }

        return $channels ?? [];
    }
}

// fs_post_editor
if (! function_exists('fs_post_editor')) {
    function fs_post_editor(?string $key = null): mixed
    {
        if (fs_user()->guest()) {
            return null;
        }

        $uid = fs_user('detail.uid');
        $langTag = fs_theme('lang');

        $cacheKey = "fresns_web_post_editor_configs_{$uid}_{$langTag}";
        $cacheTags = ['fresnsWeb', 'fresnsWebConfigs'];

        // get cache
        $configs = CacheHelper::get($cacheKey, $cacheTags);

        if (empty($configs)) {
            $result = ApiHelper::make()->get('/api/fresns/v1/editor/post/configs');

            $configs = data_get($result, 'data');

            $cacheTime = CacheHelper::fresnsCacheTimeByFileType(File::TYPE_IMAGE);
            CacheHelper::put($configs, $cacheKey, $cacheTags, null, $cacheTime);
        }

        if ($key) {
            return data_get($configs['editor'], $key);
        }

        return $configs['editor'];
    }
}

// fs_comment_editor
if (! function_exists('fs_comment_editor')) {
    function fs_comment_editor(?string $key = null): mixed
    {
        if (fs_user()->guest()) {
            return null;
        }

        $uid = fs_user('detail.uid');
        $langTag = fs_theme('lang');

        $cacheKey = "fresns_web_comment_editor_configs_{$uid}_{$langTag}";
        $cacheTags = ['fresnsWeb', 'fresnsWebConfigs'];

        // get cache
        $configs = CacheHelper::get($cacheKey, $cacheTags);

        if (empty($configs)) {
            $result = ApiHelper::make()->get('/api/fresns/v1/editor/comment/configs');

            $configs = data_get($result, 'data');

            $cacheTime = CacheHelper::fresnsCacheTimeByFileType(File::TYPE_IMAGE);
            CacheHelper::put($configs, $cacheKey, $cacheTags, null, $cacheTime);
        }

        if ($key) {
            return data_get($configs['editor'], $key);
        }

        return $configs['editor'];
    }
}

// fs_stickers
if (! function_exists('fs_stickers')) {
    function fs_stickers(): ?array
    {
        if (fs_config('site_mode') == 'private' && fs_user()->guest()) {
            return [];
        }

        $langTag = fs_theme('lang');

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
            $result = ApiHelper::make()->get('/api/fresns/v1/global/stickers');

            $listArr = data_get($result, 'data', []);

            $cacheTime = CacheHelper::fresnsCacheTimeByFileType(File::TYPE_IMAGE);
            CacheHelper::put($listArr, $cacheKey, $cacheTags, null, $cacheTime);
        }

        return $listArr ?? [];
    }
}

// fs_content_types
if (! function_exists('fs_content_types')) {
    function fs_content_types(string $type): ?array
    {
        $langTag = fs_theme('lang');

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
            $result = ApiHelper::make()->get("/api/fresns/v1/global/{$type}/content-types");

            $listArr = data_get($result, 'data', []);

            CacheHelper::put($listArr, $cacheKey, $cacheTags);
        }

        return $listArr ?? [];
    }
}

// fs_content_list
if (! function_exists('fs_content_list')) {
    function fs_content_list(string $channel, string $type): ?array
    {
        if (fs_config('site_mode') == 'private' && fs_user()->guest()) {
            return [];
        }

        return DataHelper::getFresnsContentList($channel, $type);
    }
}

// fs_sticky_posts
if (! function_exists('fs_sticky_posts')) {
    function fs_sticky_posts(?string $gid = null): ?array
    {
        if (fs_config('site_mode') == 'private' && fs_user()->guest()) {
            return [];
        }

        return DataHelper::getFresnsStickyPosts($gid);
    }
}

// fs_sticky_comments
if (! function_exists('fs_sticky_comments')) {
    function fs_sticky_comments(string $pid): ?array
    {
        if (fs_config('site_mode') == 'private' && fs_user()->guest()) {
            return [];
        }

        return DataHelper::getFresnsStickyComments($pid);
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

// fs_user_overview
if (! function_exists('fs_user_overview')) {
    function fs_user_overview(?string $key = null, mixed $uidOrUsername = null): mixed
    {
        if (fs_user()->guest()) {
            return null;
        }

        $langTag = fs_theme('lang');
        $uid = $uidOrUsername ?? fs_user('detail.uid');

        $cacheKey = "fresns_web_user_overview_{$uid}_{$langTag}";
        $cacheTag = 'fresnsWeb';

        $userOverview = CacheHelper::get($cacheKey, $cacheTag);

        if (empty($userOverview)) {
            $result = ApiHelper::make()->get('/api/fresns/v1/user/overview', [
                'query' => [
                    'uidOrUsername' => $uid,
                ],
            ]);

            $userOverview = data_get($result, 'data');

            CacheHelper::put($userOverview, $cacheKey, $cacheTag, null, now()->addMinutes());
        }

        if ($key) {
            return data_get($userOverview, $key);
        }

        return $userOverview;
    }
}
