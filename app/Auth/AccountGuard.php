<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jevan Tang
 * Released under the Apache-2.0 License.
 */

namespace Fresns\WebsiteEngine\Auth;

use App\Helpers\CacheHelper;
use App\Models\File;
use Fresns\WebsiteEngine\Helpers\DataHelper;
use Fresns\WebsiteEngine\Helpers\HttpHelper;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Cookie;

class AccountGuard implements Guard
{
    /**
     * @var array
     */
    protected $account;

    /**
     * Indicates if the logout method has been called.
     *
     * @var bool
     */
    protected $loggedOut = false;

    /**
     * The application instance.
     *
     * @var Application
     */
    protected $app;

    public function __construct($app)
    {
        $this->app = $app;
    }

    /**
     * Determine if the current account is authenticated. If not, throw an exception.
     *
     * @return array
     *
     * @throws AuthenticationException|GuzzleException
     */
    public function authenticate(): array
    {
        if (! is_null($account = $this->get())) {
            return $account;
        }

        throw new AuthenticationException;
    }

    /**
     * Determine if the guard has a account instance.
     *
     * @return bool
     */
    public function has(): bool
    {
        return ! is_null($this->account);
    }

    /**
     * Determine if the current account is authenticated.
     *
     * @return bool
     *
     * @throws GuzzleException
     */
    public function check(): bool
    {
        try {
            return ! is_null($this->get());
        } catch (\Exception $exception) {
            return false;
        }
    }

    /**
     * Determine if the current account is a guest.
     *
     * @return bool
     */
    public function guest(): bool
    {
        return ! $this->check();
    }

    /**
     * @param  array  $account
     * @return $this
     */
    public function set(array $account): self
    {
        $this->account = $account;

        return $this;
    }

    /**
     * @param  string|null  $key
     * @return array|null
     *
     * @throws GuzzleException
     */
    public function get(?string $key = null): mixed
    {
        if ($this->loggedOut) {
            return null;
        }

        if (! is_null($this->account)) {
            return $key ? Arr::get($this->account, $key) : $this->account;
        }

        $aid = Cookie::get('fresns_aid');
        $token = Cookie::get('fresns_aid_token');

        if ($aid && $token) {
            try {
                $langTag = fs_theme('lang');

                $cacheKey = "fresns_web_account_{$aid}_{$langTag}";
                $cacheTag = 'fresnsWeb';

                $result = CacheHelper::get($cacheKey, $cacheTag);

                if (empty($result)) {
                    $result = HttpHelper::get('/api/fresns/v1/account/detail');

                    $cacheTime = CacheHelper::fresnsCacheTimeByFileType(File::TYPE_ALL);
                    CacheHelper::put($result, $cacheKey, $cacheTag, $cacheTime);
                }

                if ($result['code'] != 0) {
                    $this->account = null;
                    $this->loggedOut = true;

                    if (in_array($result['code'], [31103, 31501, 31502, 31503, 31504, 31505])) {
                        Cookie::queue(Cookie::forget('fresns_aid'));
                        Cookie::queue(Cookie::forget('fresns_aid_token'));
                        Cookie::queue(Cookie::forget('fresns_uid'));
                        Cookie::queue(Cookie::forget('fresns_uid_token'));

                        Cookie::queue(Cookie::forget('fresns_account_center_aid'));
                        Cookie::queue(Cookie::forget('fresns_account_center_aid_token'));
                        Cookie::queue(Cookie::forget('fresns_account_center_uid'));
                        Cookie::queue(Cookie::forget('fresns_account_center_uid_token'));
                    }

                    return null;
                }

                $this->account = data_get($result, 'data');
            } catch (\Throwable $e) {
                Cookie::queue(Cookie::forget('fresns_aid'));
                Cookie::queue(Cookie::forget('fresns_aid_token'));
                Cookie::queue(Cookie::forget('fresns_uid'));
                Cookie::queue(Cookie::forget('fresns_uid_token'));

                Cookie::queue(Cookie::forget('fresns_account_center_aid'));
                Cookie::queue(Cookie::forget('fresns_account_center_aid_token'));
                Cookie::queue(Cookie::forget('fresns_account_center_uid'));
                Cookie::queue(Cookie::forget('fresns_account_center_uid_token'));

                throw $e;
            }
        }

        if ($key) {
            return data_get($this->account, $key);
        }

        return $this->account;
    }

    /**
     * Account by api login.
     */
    public function logout(): void
    {
        DataHelper::cacheForgetAccountAndUser();

        Cookie::queue(Cookie::forget('fresns_aid'));
        Cookie::queue(Cookie::forget('fresns_aid_token'));
        Cookie::queue(Cookie::forget('fresns_uid'));
        Cookie::queue(Cookie::forget('fresns_uid_token'));

        Cookie::queue(Cookie::forget('fresns_account_center_aid'));
        Cookie::queue(Cookie::forget('fresns_account_center_aid_token'));
        Cookie::queue(Cookie::forget('fresns_account_center_uid'));
        Cookie::queue(Cookie::forget('fresns_account_center_uid_token'));

        $this->account = null;
        $this->loggedOut = true;
    }
}
