<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jevan Tang
 * Released under the Apache-2.0 License.
 */

namespace Fresns\WebEngine\Auth;

use App\Helpers\CacheHelper;
use App\Models\File;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Cookie;
use Fresns\WebEngine\Helpers\ApiHelper;
use Fresns\WebEngine\Helpers\DataHelper;

class UserGuard implements Guard
{
    /**
     * @var array
     */
    protected $user;

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
     * Determine if the current user is authenticated. If not, throw an exception.
     *
     * @return array
     *
     * @throws AuthenticationException|GuzzleException
     */
    public function authenticate(): array
    {
        if (! is_null($user = $this->get())) {
            return $user;
        }

        throw new AuthenticationException;
    }

    /**
     * Determine if the guard has user instance.
     *
     * @return bool
     */
    public function has(): bool
    {
        return ! is_null($this->user);
    }

    /**
     * Determine if the current user is authenticated.
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
     * Determine if the current user is a guest.
     *
     * @return bool
     */
    public function guest(): bool
    {
        return ! $this->check();
    }

    /**
     * @param  array  $user
     * @return $this
     */
    public function set(array $user): self
    {
        $this->user = $user;

        return $this;
    }

    /**
     * @param  string|null  $key
     *
     * @throws GuzzleException
     */
    public function get(?string $key = null)
    {
        if ($this->loggedOut) {
            return null;
        }

        if (! is_null($this->user)) {
            return $key ? Arr::get($this->user, $key) : $this->user;
        }

        $cookiePrefix = fs_db_config('engine_cookie_prefix', 'fresns_');

        $uid = Cookie::get("{$cookiePrefix}uid");
        $token = Cookie::get("{$cookiePrefix}uid_token");
        $langTag = current_lang_tag();

        if ($uid && $token) {
            try {
                $cacheKey = "fresns_web_user_{$uid}_{$langTag}";
                $cacheTag = 'fresnsWeb';

                $result = CacheHelper::get($cacheKey, $cacheTag);

                if (empty($result)) {
                    $result = ApiHelper::make()->get("/api/v2/user/{$uid}/detail");

                    $cacheTime = CacheHelper::fresnsCacheTimeByFileType(File::TYPE_ALL);
                    CacheHelper::put($result, $cacheKey, $cacheTag, null, $cacheTime);
                }

                if ($result['code'] != 0) {
                    $this->user = null;
                    $this->loggedOut = true;

                    return null;
                }

                $this->user = data_get($result, 'data');
            } catch (\Throwable $e) {
                throw $e;
            }
        }

        if ($key) {
            return data_get($this->user, $key);
        }

        return $this->user;
    }

    public function logout(): void
    {
        DataHelper::cacheForgetAccountAndUser();

        $cookiePrefix = fs_db_config('engine_cookie_prefix', 'fresns_');

        Cookie::queue(Cookie::forget("{$cookiePrefix}uid"));
        Cookie::queue(Cookie::forget("{$cookiePrefix}uid_token"));

        $this->user = null;
        $this->loggedOut = true;
    }
}
