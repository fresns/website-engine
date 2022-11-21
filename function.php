<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jarvis Tang
 * Released under the Apache-2.0 License.
 */

use App\Helpers\ConfigHelper;
use App\Helpers\LanguageHelper;
use App\Helpers\PluginHelper;
use App\Helpers\SignHelper;
use App\Models\Config;
use App\Utilities\AppUtility;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Str;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;
use Plugins\FresnsEngine\Auth\AccountGuard;
use Plugins\FresnsEngine\Auth\UserGuard;
use Plugins\FresnsEngine\Sdk\Factory;

if (! function_exists('account')) {
    /**
     * @return AccountGuard
     */
    function account()
    {
        return app('fresns.account');
    }
}

if (! function_exists('user')) {
    /**
     * @return UserGuard
     */
    function user()
    {
        return app('fresns.user');
    }
}

if (! function_exists('paginator')) {
    /**
     * @param  array  $data
     * @return LengthAwarePaginator
     */
    function paginator(array $data): LengthAwarePaginator
    {
        LengthAwarePaginator::useBootstrap();

        return new LengthAwarePaginator(
            Arr::get($data, 'list'),
            Arr::get($data, 'pagination.total'),
            Arr::get($data, 'pagination.pageSize'),
            Arr::get($data, 'pagination.current'),
            [
                'path' => Paginator::resolveCurrentPath(),
                'pageName' => 'page',
            ]
        );
    }
}

if (! function_exists('fs_config')) {
    function fs_config(string $itemKey, ?string $lang = null)
    {
        $key = md5(serialize([
            'itemKey' => $itemKey,
            'langTag' => $lang ?? current_lang(),
        ]));

        return Cache::remember($key, now()->addDays(1), function () use ($itemKey) {
            $result = Factory::information()->config->get($itemKey);
            // Preventing Cache Penetration
            return Arr::get($result, 'code') === 0 ? (Arr::get($result, 'data.list.0.itemValue') ?? '') : null;
        });
    }
}

if (! function_exists('current_lang')) {
    /**
     * @return array|Application|\Illuminate\Http\Request|mixed|string
     */
    function current_lang()
    {
        return \Illuminate\Support\Facades\App::getLocale();
    }
}

if (! function_exists('default_lang')) {
    /**
     * @return mixed
     */
    function default_lang()
    {
        return Cache::remember('fs_current_lang', now()->addDays(1), function () {
            return Config::query()->where('item_key', 'default_language')->value('item_value');
        });
    }
}

if (! function_exists('fresnsengine_config')) {
    /**
     * @param  string  $key
     * @return mixed
     */
    function fresnsengine_config(string $key)
    {
        $langTag = current_lang();
        $cacheKey = sprintf('%s-%s', $langTag, $key);

        return Cache::remember($cacheKey, now()->addDays(1), function () use ($key, $langTag) {
            $langTag = current_lang();
            $config = Config::query()->where('item_key', $key)->first();

            if (! $config) {
                return null;
            }

            $itemValue = $config->item_value;

            if (intval($config->is_multilingual) == 1) {
                $itemValue = LanguageHelper::fresnsLanguageByTableKey($config->item_key, $langTag);
            }

            if ($config->item_type == 'file') {
                if (is_numeric($itemValue)) {
                    $item['itemValue'] = ConfigHelper::fresnsConfigFileUrlByItemKey($itemValue);
                }
            }

            if ($config->item_type == 'plugin') {
                $itemValue = PluginHelper::fresnsPluginUrlByUnikey($itemValue);
            }

            if ($config->item_type == 'plugins') {
                if ($itemValue) {
                    foreach ($itemValue as $plugin) {
                        $item['code'] = $plugin['code'];
                        $item['url'] = PluginHelper::fresnsPluginUrlByUnikey($plugin['unikey']);
                        $itemArr[] = $item;
                    }
                    $itemValue = $itemArr;
                }
            }

            return $itemValue;
        });
    }
}

if (! function_exists('accept_file')) {
    /**
     * @return string
     */
    function accept_images(): string
    {
        $imagesExt = fs_config('images_ext');
        $exts = array_map(function ($ext) {
            return '.'.$ext;
        }, explode(',', $imagesExt));

        return implode(',', $exts);
    }
}

if (! function_exists('get_filetypes')) {
    function get_filetypes($types): array
    {
        return array_map(function (int $type) {
            switch ($type) {
                case '2':
                    $file_type = 'video';
                    break;
                case '3':
                    $file_type = 'audio';
                    break;
                case '4':
                    $file_type = 'document';
                    break;
                default:
                    $file_type = 'image';
                    break;
            }

            return $file_type;
        }, array_unique($types));
    }
}

if (! function_exists('stickers')) {
    /**
     * @return array
     */
    function stickers(): array
    {
        return Cache::remember('stickers', now()->addDays(1), function () {
            $result = Factory::information()->stickers->get(1000, 0);

            return Arr::get($result, 'code') === 0 ? Arr::get($result, 'data.list') : null;
        });
    }
}

if (! function_exists('fs_lang')) {
    /**
     * @param  string  $key
     * @return array|ArrayAccess|mixed|null
     */
    function fs_lang(string $key): ?string
    {
        $langTag = current_lang();
        $cacheKey = sprintf('fs-lang-%s-%s', $langTag, $key);

        return Cache::remember($cacheKey, now()->addDays(1), function () use ($key, $langTag) {
            $langArr = fs_config($langTag);
            $result = Arr::first($langArr, function (array $val) use ($key) {
                return $val['name'] === $key;
            });

            if ($result) {
                return Arr::get($result, 'content');
            }

            return null;
        });
    }
}

if (! function_exists('fmt')) {
    /**
     * @param $format
     * @param  mixed  ...$args
     * @return mixed|string
     */
    function fmt($format, ...$args)
    {
        if (count($args) > 0) {
            preg_match_all("/\{[^\{]+\}/", $format, $array);
            foreach ($array[0] as $key => $value) {
                $format = Str::replace($value, $args[$key] ?? null, $format);
            }

            return $format;
        } else {
            return $format;
        }
    }
}

if (! function_exists('sign')) {
    function sign()
    {
        $signParams = [
            'platform' => 4,
            'version' => '1.0.0',
            'appId' => fresnsengine_config('fresnsengine_appid'),
            'timestamp' => now()->unix(),
            'aid' => Cookie::get('aid') ?? null,
            'uid' => Cookie::get('uid') ?? null,
            'token' => Cookie::get('token') ?? null,
        ];
        $sign = SignHelper::makeSign($signParams, fresnsengine_config('fresnsengine_appsecret'));

        $urlHeader['platform'] = 4;
        $urlHeader['version'] = '1.0.0';
        $urlHeader['appId'] = fresnsengine_config('fresnsengine_appid');
        $urlHeader['timestamp'] = now()->unix();
        $urlHeader['sign'] = $sign;
        $urlHeader['langTag'] = current_lang() ?? '';
        $urlHeader['timezone'] = Cookie::get('timezone') ?? '';
        $urlHeader['aid'] = Cookie::get('aid') ?? null;
        $urlHeader['uid'] = Cookie::get('uid') ?? null;
        $urlHeader['token'] = Cookie::get('token') ?? null;
        $urlHeader['deviceInfo'] = json_encode(AppUtility::getDeviceInfo());

        return urlencode(base64_encode(json_encode($urlHeader)));
    }
}

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
