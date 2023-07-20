<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jevan Tang
 * Released under the Apache-2.0 License.
 */

namespace Plugins\FresnsEngine\Http\Middleware;

use App\Helpers\AppHelper;
use App\Helpers\CacheHelper;
use App\Helpers\ConfigHelper;
use App\Helpers\PluginHelper;
use App\Helpers\PrimaryHelper;
use App\Helpers\SignHelper;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Response;

class SetHeaders
{
    public function handle(Request $request, Closure $next)
    {
        if (is_remote_api()) {
            return $next($request);
        }

        $keyId = ConfigHelper::fresnsConfigByItemKey('engine_key_id');
        $keyInfo = PrimaryHelper::fresnsModelById('key', $keyId);

        if (empty($keyInfo)) {
            return Response::view('error', [
                'message' => '<p>'.__('FsWeb::tips.errorKey').'</p><p>'.__('FsWeb::tips.settingApiTip').'</p>',
                'code' => 500,
            ], 500);
        }

        $engineVersion = PluginHelper::fresnsPluginVersionByFskey('FresnsEngine');

        // cookie key name
        $cookiePrefix = fs_db_config('engine_cookie_prefix', 'fresns_');
        $fresnsAid = "{$cookiePrefix}aid";
        $fresnsAidToken = "{$cookiePrefix}aid_token";
        $fresnsUid = "{$cookiePrefix}uid";
        $fresnsUidToken = "{$cookiePrefix}uid_token";

        $ulid = Cookie::get("{$cookiePrefix}ulid");
        $aidAndToken = [];
        if ($ulid) {
            $aidAndToken = CacheHelper::get("fresns_web_{$ulid}", ['fresnsWeb', 'fresnsWebAccountTokens']);
        }

        $now = now('UTC');
        $nowTimestamp = strtotime($now);

        $headers = [
            'X-Fresns-App-Id' => $keyInfo->app_id,
            'X-Fresns-Client-Platform-Id' => $keyInfo->platform_id,
            'X-Fresns-Client-Version' => $engineVersion,
            'X-Fresns-Client-Device-Info' => json_encode(AppHelper::getDeviceInfo()),
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
        $headers['X-Fresns-Signature'] = SignHelper::makeSign($headers, $keyInfo->app_secret);

        $request->headers->set('X-Fresns-App-Id', $headers['X-Fresns-App-Id']);
        $request->headers->set('X-Fresns-Client-Platform-Id', $headers['X-Fresns-Client-Platform-Id']);
        $request->headers->set('X-Fresns-Client-Version', $headers['X-Fresns-Client-Version']);
        $request->headers->set('X-Fresns-Client-Device-Info', $headers['X-Fresns-Client-Device-Info']);
        $request->headers->set('X-Fresns-Client-Lang-Tag', $headers['X-Fresns-Client-Lang-Tag']);
        $request->headers->set('X-Fresns-Client-Timezone', $headers['X-Fresns-Client-Timezone']);
        $request->headers->set('X-Fresns-Client-Content-Format', null);
        $request->headers->set('X-Fresns-Aid', $headers['X-Fresns-Aid']);
        $request->headers->set('X-Fresns-Aid-Token', $headers['X-Fresns-Aid-Token']);
        $request->headers->set('X-Fresns-Uid', $headers['X-Fresns-Uid']);
        $request->headers->set('X-Fresns-Uid-Token', $headers['X-Fresns-Uid-Token']);
        $request->headers->set('X-Fresns-Signature', $headers['X-Fresns-Signature']);
        $request->headers->set('X-Fresns-Signature-Timestamp', $headers['X-Fresns-Signature-Timestamp']);

        return $next($request);
    }
}
