<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jevan Tang
 * Released under the Apache-2.0 License.
 */

namespace Fresns\WebEngine\Helpers;

use App\Helpers\CacheHelper;
use Illuminate\Support\Facades\Cookie;

class LoginHelper
{
    // Account Login
    public static function account(array $data)
    {
        // cookie key name
        $cookiePrefix = fs_config('website_cookie_prefix', 'fresns_');

        $cookieAid = "{$cookiePrefix}aid";
        $cookieAidToken = "{$cookiePrefix}aid_token";

        $cookieUlid = Cookie::get("{$cookiePrefix}ulid");

        $cacheKey = 'fresns_web_'.$cookieUlid;
        $cacheTags = ['fresnsWeb', 'fresnsWebAccountTokens',];

        if ($cacheKey) {
            $cacheData = [
                'aid' => data_get($data, 'authToken.aid'),
                'aidToken' => data_get($data, 'authToken.token'),
            ];

            CacheHelper::put($cacheData, $cacheKey, $cacheTags, 3, now()->addMinutes(3));
        }

        $accountExpiredHours = data_get($data, 'authToken.expiredHours') ?? 8760;
        $accountTokenMinutes = $accountExpiredHours * 60;

        $aid = data_get($data, 'authToken.aid');
        $aidToken = data_get($data, 'authToken.token');

        CacheHelper::forgetFresnsMultilingual("fresns_web_account_{$aid}", 'fresnsWeb');

        Cookie::queue($cookieAid, $aid, $accountTokenMinutes);
        Cookie::queue($cookieAidToken, $aidToken, $accountTokenMinutes);

        $users = data_get($data, 'detail.users', []) ?? [];
        $user = $users[0];
        $userCount = count($users);

        DataHelper::cacheForgetAccountAndUser();

        if ($userCount == 1 && ! $user['hasPin']) {
            DataHelper::cacheForgetAccountAndUser();

            $result = ApiHelper::make()->post('/api/fresns/v1/user/auth-token', [
                'json' => [
                    'uidOrUsername' => $user['uid'],
                    'password' => null,
                    'deviceToken' => null,
                ]
            ]);

            if ($result['code'] != 0) {
                return back()->with([
                    'code' => $result['code'],
                    'failure' => $result['message'],
                ]);
            };

            LoginHelper::user($result['data']);
        }

        return;
    }

    // User Login
    public static function user(array $data)
    {
        // cookie key name
        $cookiePrefix = fs_config('website_cookie_prefix', 'fresns_');

        $cookieUid = "{$cookiePrefix}uid";
        $cookieUidToken = "{$cookiePrefix}uid_token";

        $userExpiredHours = data_get($data, 'authToken.expiredHours') ?? 8760;
        $userTokenMinutes = $userExpiredHours * 60;

        Cookie::queue($cookieUid, data_get($data, 'authToken.uid'), $userTokenMinutes);
        Cookie::queue($cookieUidToken, data_get($data, 'authToken.token'), $userTokenMinutes);
    }
}
