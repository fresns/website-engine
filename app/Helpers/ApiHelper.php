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
use App\Helpers\PluginHelper;
use App\Helpers\SignHelper;
use App\Models\SessionKey;
use Browser;
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
        if (! data_get($this->result, 'data.paginate', false)) {
            return null;
        }

        $paginate = new LengthAwarePaginator(
            items: data_get($this->result, 'data.list'),
            total: data_get($this->result, 'data.paginate.total'),
            perPage: data_get($this->result, 'data.paginate.pageSize'),
            currentPage: data_get($this->result, 'data.paginate.currentPage'),
        );

        $paginate->withPath(Str::of(request()->path())->start('/'))->withQueryString();

        return $paginate;
    }

    public function getBaseUri(): ?string
    {
        $isLocal = is_local_api();

        $localApiHost = config('app.url');
        $remoteApiHost = ConfigHelper::fresnsConfigByItemKey('webengine_api_host');

        $apiHost = $isLocal ? $localApiHost : $remoteApiHost;

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

            if ($data['trace']) {
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

        if (empty($keyConfig['platformId']) || empty($keyConfig['appId']) || empty($keyConfig['appSecret'])) {
            if (is_local_api()) {
                $keyId = ConfigHelper::fresnsConfigByItemKey('webengine_key_id');
                $keyInfo = SessionKey::find($keyId);

                $platformId = $keyInfo?->platform_id;
                $appId = $keyInfo?->app_id;
                $appSecret = $keyInfo?->app_secret;
            } else {
                $platformId = 4;
                $appId = ConfigHelper::fresnsConfigByItemKey('webengine_api_app_id');
                $appSecret = ConfigHelper::fresnsConfigByItemKey('webengine_api_app_secret');
            }

            $keyConfig = [
                'platformId' => $platformId,
                'appId' => $appId,
                'appSecret' => $appSecret,
            ];

            CacheHelper::put($keyConfig, $cacheKey, $cacheTags);
        }

        // cookie key name
        $cookiePrefix = fs_db_config('website_cookie_prefix', 'fresns_');
        $fresnsAid = "{$cookiePrefix}aid";
        $fresnsAidToken = "{$cookiePrefix}aid_token";
        $fresnsUid = "{$cookiePrefix}uid";
        $fresnsUidToken = "{$cookiePrefix}uid_token";

        $ulid = Cookie::get("{$cookiePrefix}ulid");
        $aidAndToken = [];
        if ($ulid) {
            $aidAndToken = CacheHelper::get("fresns_web_{$ulid}", ['fresnsWeb', 'fresnsWebAccountTokens']);
        }

        $clientFskey = Browser::isMobile() ? fs_db_config('webengine_view_mobile') : fs_db_config('webengine_view_desktop');
        $clientVersion = PluginHelper::fresnsPluginVersionByFskey($clientFskey);

        $now = now('UTC');
        $nowTimestamp = strtotime($now);

        // headers
        $headers = [
            'Accept' => 'application/json',
            'X-Fresns-App-Id' => $keyConfig['appId'],
            'X-Fresns-Client-Platform-Id' => $keyConfig['platformId'],
            'X-Fresns-Client-Version' => $clientVersion,
            'X-Fresns-Client-Device-Info' => base64_encode(json_encode(AppHelper::getDeviceInfo())),
            'X-Fresns-Client-Timezone' => $_COOKIE['fresns_timezone'] ?? null,
            'X-Fresns-Client-Lang-Tag' => current_lang_tag(),
            'X-Fresns-Client-Content-Format' => null,
            'X-Fresns-Aid' => Cookie::get($fresnsAid) ?? $aidAndToken['aid'] ?? null,
            'X-Fresns-Aid-Token' => Cookie::get($fresnsAidToken) ?? $aidAndToken['aidToken'] ?? null,
            'X-Fresns-Uid' => Cookie::get($fresnsUid),
            'X-Fresns-Uid-Token' => Cookie::get($fresnsUidToken),
            'X-Fresns-Signature' => null,
            'X-Fresns-Signature-Timestamp' => $nowTimestamp,
        ];
        $headers['X-Fresns-Signature'] = SignHelper::makeSign($headers, $keyConfig['appSecret']);

        return $headers;
    }
}
