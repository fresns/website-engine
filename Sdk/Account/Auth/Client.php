<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jarvis Tang
 * Released under the Apache-2.0 License.
 */

namespace Plugins\FresnsEngine\Sdk\Account\Auth;

use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Str;
use Plugins\FresnsEngine\Sdk\Kernel\BaseClient;

class Client extends BaseClient
{
    /**
     * @param  int  $type
     * @param  string  $account
     * @param  int|null  $countryCode
     * @param  string  $verifyCode
     * @param  string  $password
     * @param  string  $nickname
     * @return array
     *
     * @throws GuzzleException
     */
    public function register(
        int $type,
        string $account,
        ?int $countryCode,
        string $verifyCode,
        string $password,
        string $nickname
    ): array {
        return $this->httpPostJson('/api/v1/account/register', [
            'type' => $type,
            'account' => $account,
            'countryCode' => $countryCode,
            'verifyCode' => $verifyCode,
            'password' => base64_encode($password),
            'nickname' => $nickname,
        ]);
    }

    /**
     * @param  int  $type
     * @param  string  $account
     * @param  int|null  $countryCode
     * @param  string|null  $verifyCode
     * @param  string|null  $password
     * @return array
     *
     * @throws GuzzleException
     */
    public function login(
        int $type,
        string $account,
        ?int $countryCode,
        ?string $verifyCode,
        ?string $password
    ): array {
        return $this->httpPostJson('/api/v1/account/login', [
            'type' => $type,
            'account' => $account,
            'countryCode' => $countryCode,
            'verifyCode' => $verifyCode,
            'password' => $password ? base64_encode($password) : null,
        ]);
    }

    /**
     * @return array
     *
     * @throws GuzzleException
     */
    public function detail(): array
    {
        return $this->httpPostJson('/api/v1/account/detail');
    }

    /**
     * @throws GuzzleException
     */
    public function logout(): array
    {
        return $this->httpPostJson('/api/v1/account/logout');
    }

    /**
     * @param  int  $type
     * @param  string  $account
     * @param  int|null  $countryCode
     * @param  string  $verifyCode
     * @param  string  $newPassword
     * @return array
     *
     * @throws GuzzleException
     */
    public function reset(
        int $type,
        string $account,
        ?int $countryCode,
        string $verifyCode,
        string $newPassword
    ): array {
        $newPassword = base64_encode($newPassword);

        return $this->httpPostJson('/api/v1/account/reset', compact(
            'type',
            'account',
            'countryCode',
            'verifyCode',
            'newPassword'
        ));
    }

    /**
     * @param  string|null  $verifyCode
     * @param  int|null  $codeType
     * @param  string|null  $password
     * @param  string|null  $walletPassword
     * @param  string|null  $editEmail
     * @param  string|null  $editPhone
     * @param  int|null  $editCountryCode
     * @param  string|null  $editPassword
     * @param  string|null  $editWalletPassword
     * @param  string|null  $editLastLoginTime
     * @param  int|null  $deleteConnectId
     * @param  string|null  $newVerifyCode
     * @return array
     *
     * @throws GuzzleException
     */
    public function edit(
        ?string $verifyCode = null,
        ?int $codeType = null,
        ?string $password = null,
        ?string $walletPassword = null,
        ?string $editEmail = null,
        ?string $editPhone = null,
        ?int $editCountryCode = null,
        ?string $editPassword = null,
        ?string $editWalletPassword = null,
        ?string $editLastLoginTime = null,
        ?int $deleteConnectId = null,
        ?string $newVerifyCode = null
    ): array {
        $editCountryCode = $editCountryCode ? Str::after($editCountryCode, '+') : null;
        $password = $password ? base64_encode($password) : null;
        $walletPassword = $walletPassword ? base64_encode($walletPassword) : null;
        $editPassword = $editPassword ? base64_encode($editPassword) : null;
        $editWalletPassword = $editWalletPassword ? base64_encode($editWalletPassword) : null;

        return $this->httpPostJson('/api/v1/account/edit', compact(
            'verifyCode',
            'codeType',
            'password',
            'walletPassword',
            'editEmail',
            'editPhone',
            'editCountryCode',
            'editPassword',
            'editWalletPassword',
            'editLastLoginTime',
            'deleteConnectId',
            'newVerifyCode'
        ));
    }

    /**
     * @param  int  $codeType
     * @param  string  $verifyCode
     * @return array
     *
     * @throws GuzzleException
     */
    public function verification(
        int $codeType,
        string $verifyCode
    ): array {
        return $this->httpPostJson('/api/v1/account/verification', compact(
            'codeType',
            'verifyCode'
        ));
    }
}
