<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jevan Tang
 * Released under the Apache-2.0 License.
 */

namespace Fresns\WebEngine\Helpers;

use App\Helpers\AppHelper;
use App\Helpers\CacheHelper;
use App\Helpers\ConfigHelper;
use App\Helpers\SignHelper;
use App\Models\SessionKey;
use Fresns\WebEngine\Client\Clientable;
use Fresns\WebEngine\Exceptions\ErrorException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Str;

class ApiHelper
{
    use Clientable;

    protected array $result = [];

    public function caseForwardCallResult($result)
    {
        if ($result instanceof RedirectResponse) {
            throw new ErrorException(session('failure'), session('code'));
        }

        return $result;
    }

    public function caseUnwrapRequests(array $results)
    {
        if ($results instanceof RedirectResponse) {
            throw new ErrorException(session('failure'), (int) session('code'));
        }

        return $results;
    }

    public function paginate()
    {
        if (! data_get($this->result, 'data.pagination', false)) {
            return null;
        }

        $paginate = new LengthAwarePaginator(
            items: data_get($this->result, 'data.list'),
            total: data_get($this->result, 'data.pagination.total'),
            perPage: data_get($this->result, 'data.pagination.pageSize'),
            currentPage: data_get($this->result, 'data.pagination.currentPage'),
        );

        $paginate->withPath(Str::of(request()->path())->start('/'))->withQueryString();

        return $paginate;
    }

    public function getBaseUri(): ?string
    {
        $localApiHost = config('app.url');
        $remoteApiHost = ConfigHelper::fresnsConfigByItemKey('webengine_api_host');

        $apiHost = is_local_api() ? $localApiHost : $remoteApiHost;

        return $apiHost;
    }

    public function getOptions()
    {
        $cacheKey = 'fresns_web_api_host';
        $cacheTags = ['fresnsWeb', 'fresnsWebConfigs'];

        $apiHost = CacheHelper::get($cacheKey, $cacheTags);

        if (empty($apiHost)) {
            $apiHost = $this->getBaseUri();

            CacheHelper::put($apiHost, $cacheKey, $cacheTags);
        }

        return [
            'base_uri' => $apiHost,
            'verify' => false,
            'timeout' => 30000, // Request 5s timeout
            'http_errors' => false,
            'headers' => ApiHelper::getHeaders(),
        ];
    }

    public function castResponse($response)
    {
        $content = $response->getBody()->getContents();

        $data = json_decode($content, true) ?? [];

        if (empty($data)) {
            info('empty response, ApiException: '.var_export($content, true));
            throw new ErrorException($response?->getReasonPhrase(), $response?->getStatusCode());
        }

        if (! array_key_exists('code', $data)) {
            $code = 500;

            $message = $data['message'] ?? $data['exception'] ?? '';

            if ($data['trace'] ?? null) {
                $message = json_encode([
                    'file' => $data['file'] ?? null,
                    'line' => $data['line'] ?? null,
                    'message' => $message,
                ], \JSON_UNESCAPED_SLASHES | \JSON_UNESCAPED_UNICODE | \JSON_PRETTY_PRINT);

                $message = str_replace([base_path().'/', '\"'], '', $message);
                $message = str_replace([base_path().'/', '\\\\'], '\\', $message);
            }

            throw new ErrorException($message, $code);
        }

        if (array_key_exists('code', $data) && $data['code'] != 0) {
            info('error response, ApiException: '.var_export($content, true));

            $message = $data['message'] ?? $data['exception'] ?? '';
            if (empty($message)) {
                $message = 'Unknown api error';
            } elseif ($data['data'] ?? null) {
                $message = "{$message} ".head($data['data']) ?? '';
            }

            throw new ErrorException($message, $data['code']);
        }

        return $data;
    }

    public static function getHeaders()
    {
        $cacheKey = 'fresns_web_api_key';
        $cacheTags = ['fresnsWeb', 'fresnsWebConfigs'];

        $keyConfig = CacheHelper::get($cacheKey, $cacheTags);

        if (empty($keyConfig)) {
            $apiConfigs = ConfigHelper::fresnsConfigByItemKeys([
                'webengine_key_id',
                'webengine_api_type',
                'webengine_api_app_id',
                'webengine_api_app_key',
            ]);

            $keyInfo = SessionKey::find($apiConfigs['webengine_key_id']);

            $keyConfig = match ($apiConfigs['webengine_api_type']) {
                'local' => [
                    'platformId' => $keyInfo?->platform_id,
                    'appId' => $keyInfo?->app_id,
                    'appKey' => $keyInfo?->app_key,
                ],
                'remote' => [
                    'platformId' => SessionKey::PLATFORM_WEB_RESPONSIVE,
                    'appId' => $apiConfigs['webengine_api_app_id'],
                    'appKey' => $apiConfigs['webengine_api_app_key'],
                ],
                default => [
                    'platformId' => SessionKey::PLATFORM_WEB_RESPONSIVE,
                    'appId' => null,
                    'appKey' => null,
                ],
            };

            CacheHelper::put($keyConfig, $cacheKey, $cacheTags);
        }

        // cookie key name
        $cookiePrefix = ConfigHelper::fresnsConfigByItemKey('website_cookie_prefix') ?? 'fresns_';

        $cookieAid = "{$cookiePrefix}aid";
        $cookieAidToken = "{$cookiePrefix}aid_token";
        $cookieUid = "{$cookiePrefix}uid";
        $cookieUidToken = "{$cookiePrefix}uid_token";

        $ulid = Cookie::get("{$cookiePrefix}ulid");
        $cacheAidAndToken = [];
        if ($ulid) {
            $cacheAidAndToken = CacheHelper::get("fresns_web_{$ulid}", [
                'fresnsWeb',
                'fresnsWebAccountTokens',
            ]);
        }

        $now = now('UTC');
        $nowTimestamp = strtotime($now);

        // headers
        $headers = [
            'Accept' => 'application/json',
            'X-Fresns-App-Id' => $keyConfig['appId'],
            'X-Fresns-Client-Platform-Id' => $keyConfig['platformId'],
            'X-Fresns-Client-Version' => fs_theme('version'),
            'X-Fresns-Client-Device-Info' => base64_encode(json_encode(AppHelper::getDeviceInfo())),
            'X-Fresns-Client-Timezone' => $_COOKIE['fresns_timezone'] ?? null,
            'X-Fresns-Client-Lang-Tag' => fs_theme('lang'),
            'X-Fresns-Client-Content-Format' => null,
            'X-Fresns-Aid' => Cookie::get($cookieAid) ?? $cacheAidAndToken['aid'] ?? null,
            'X-Fresns-Aid-Token' => Cookie::get($cookieAidToken) ?? $cacheAidAndToken['aidToken'] ?? null,
            'X-Fresns-Uid' => Cookie::get($cookieUid),
            'X-Fresns-Uid-Token' => Cookie::get($cookieUidToken),
            'X-Fresns-Signature' => null,
            'X-Fresns-Signature-Timestamp' => $nowTimestamp,
        ];
        $headers['X-Fresns-Signature'] = SignHelper::makeSign($headers, $keyConfig['appKey']);

        return $headers;
    }
}
