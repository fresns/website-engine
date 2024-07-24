<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jevan Tang
 * Released under the Apache-2.0 License.
 */

namespace Fresns\WebsiteEngine\Helpers;

use App\Helpers\AppHelper;
use App\Helpers\CacheHelper;
use App\Helpers\ConfigHelper;
use App\Helpers\SignHelper;
use App\Models\SessionKey;
use Fresns\WebsiteEngine\Exceptions\ErrorException;
use Illuminate\Http\Client\Pool;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Http;

class HttpHelper
{
    public static function concurrentRequests(array $requests): array
    {
        // $requests = [
        //     [
        //         'name' => 'detail',
        //         'method' => 'GET',
        //         'path' => '/api/fresns/v1/comment/cid/detail',
        //         'params' => ['query' => 'value1']
        //     ],
        //     [
        //         'name' => 'list',
        //         'method' => 'GET',
        //         'path' => '/api/fresns/v1/comment/list',
        //         'params' => ['data' => 'value2']
        //     ],
        // ];

        $baseUrl = self::getBaseUrl();
        $headers = self::getHeaders();

        $responses = Http::pool(function (Pool $pool) use ($baseUrl, $headers, $requests) {
            $poolRequests = [];
            foreach ($requests as $request) {
                $method = strtolower($request['method']);
                $apiUrl = $baseUrl.$request['path'];
                $params = $request['params'] ?? [];

                $poolRequests[] = $pool->withHeaders($headers)->$method($apiUrl, $params);
            }

            return $poolRequests;
        });

        $results = [];
        foreach ($requests as $key => $request) {
            if (! $responses[$key]->ok()) {
                throw new ErrorException('Operation failed, please try again', 30008);
            }

            $name = $request['name'];

            $results[$name] = $responses[$key]->json();
        }

        return $results;
    }

    public static function get(string $endpointPath, ?array $parameters = []): array
    {
        return self::sendRequest('get', $endpointPath, $parameters);
    }

    public static function post(string $endpointPath, array $parameters = []): array
    {
        return self::sendRequest('post', $endpointPath, $parameters);
    }

    public static function put(string $endpointPath, array $parameters = []): array
    {
        return self::sendRequest('put', $endpointPath, $parameters);
    }

    public static function patch(string $endpointPath, array $parameters = []): array
    {
        return self::sendRequest('patch', $endpointPath, $parameters);
    }

    public static function delete(string $endpointPath, array $parameters = []): array
    {
        return self::sendRequest('delete', $endpointPath, $parameters);
    }

    private static function sendRequest(string $method, string $endpointPath, ?array $parameters = []): array
    {
        $baseUrl = self::getBaseUrl();
        $headers = self::getHeaders();

        $apiUrl = $baseUrl.$endpointPath;

        $response = Http::withHeaders($headers)->$method($apiUrl, $parameters);

        if (! $response->ok()) {
            throw new ErrorException('Operation failed, please try again', 30008);
        }

        $result = $response->json();

        if ($result['code']) {
            throw new ErrorException($result['message'], $result['code']);
        }

        return $result;
    }

    public static function getBaseUrl(): string
    {
        $baseUrl = config('app.url');

        if (is_remote_api()) {
            $baseUrl = ConfigHelper::fresnsConfigByItemKey('website_engine_api_host');
        }

        return $baseUrl;
    }

    public static function getHeaders(): array
    {
        $cacheKey = 'fresns_web_api_key';
        $cacheTags = ['fresnsWeb', 'fresnsWebConfigs'];

        $keyConfig = CacheHelper::get($cacheKey, $cacheTags);

        if (empty($keyConfig)) {
            $apiConfigs = ConfigHelper::fresnsConfigByItemKeys([
                'website_engine_key_id',
                'website_engine_api_type',
                'website_engine_api_app_id',
                'website_engine_api_app_key',
            ]);

            $keyInfo = SessionKey::find($apiConfigs['website_engine_key_id']);

            $keyConfig = match ($apiConfigs['website_engine_api_type']) {
                'local' => [
                    'platformId' => $keyInfo?->platform_id,
                    'appId' => $keyInfo?->app_id,
                    'appKey' => $keyInfo?->app_key,
                ],
                'remote' => [
                    'platformId' => SessionKey::PLATFORM_WEB_RESPONSIVE,
                    'appId' => $apiConfigs['website_engine_api_app_id'],
                    'appKey' => $apiConfigs['website_engine_api_app_key'],
                ],
                default => [
                    'platformId' => SessionKey::PLATFORM_WEB_RESPONSIVE,
                    'appId' => null,
                    'appKey' => null,
                ],
            };

            CacheHelper::put($keyConfig, $cacheKey, $cacheTags);
        }

        // headers
        $headers = [
            'Accept' => 'application/json',
            'X-Fresns-App-Id' => $keyConfig['appId'],
            'X-Fresns-Client-Platform-Id' => $keyConfig['platformId'],
            'X-Fresns-Client-Version' => fs_theme('version'),
            'X-Fresns-Client-Device-Info' => base64_encode(json_encode(AppHelper::getDeviceInfo())),
            'X-Fresns-Client-Timezone' => Cookie::get('fresns_timezone'),
            'X-Fresns-Client-Lang-Tag' => request()->ajax() ? Cookie::get('fresns_lang_tag') : fs_theme('lang'),
            'X-Fresns-Client-Content-Format' => null,
            'X-Fresns-Aid' => Cookie::get('fresns_aid'),
            'X-Fresns-Aid-Token' => Cookie::get('fresns_aid_token'),
            'X-Fresns-Uid' => Cookie::get('fresns_uid'),
            'X-Fresns-Uid-Token' => Cookie::get('fresns_uid_token'),
            'X-Fresns-Signature' => null,
            'X-Fresns-Signature-Timestamp' => time(),
        ];
        $headers['X-Fresns-Signature'] = SignHelper::makeSign($headers, $keyConfig['appKey']);

        return $headers;
    }
}
