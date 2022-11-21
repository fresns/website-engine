<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jarvis Tang
 * Released under the Apache-2.0 License.
 */

namespace Plugins\FresnsEngine\Sdk\User\Auth;

use Carbon\Carbon;
use GuzzleHttp\Exception\GuzzleException;
use Plugins\FresnsEngine\Sdk\Kernel\BaseClient;

class Client extends BaseClient
{
    /**
     * @param  int  $uid
     * @param  string|null  $password
     * @return array
     *
     * @throws GuzzleException
     */
    public function login(
        int $uid,
        ?string $password = null
    ): array {
        return $this->httpPostJson('/api/v1/user/auth', [
            'uid' => $uid,
            'password' => $password ? base64_encode($password) : null,
        ]);
    }

    /**
     * @param  string|null  $viewUid
     * @param  string|null  $viewUsername
     * @return array
     *
     * @throws GuzzleException
     */
    public function detail(
        ?string $viewUid = null,
        ?string $viewUsername = null
    ): array {
        return $this->httpPostJson('/api/v1/user/detail', [
            'viewUid' => $viewUid,
            'viewUsername' => $viewUsername,
        ]);
    }

    /**
     * @param  string|null  $searchKey
     * @param  int|null  $gender
     * @param  string|null  $createdTimeGt
     * @param  string|null  $createdTimeLt
     * @param  string|null  $sortType
     * @param  int|null  $sortDirection
     * @param  int|null  $pageSize
     * @param  int|null  $page
     * @return array
     *
     * @throws GuzzleException
     */
    public function lists(
        ?string $searchKey = null,
        ?int $gender = null,
        ?string $createdTimeGt = null,
        ?string $createdTimeLt = null,
        ?string $sortType = null,
        ?int $sortDirection = null,
        ?int $pageSize = null,
        ?int $page = null
    ): array {
        return $this->httpPostJson('/api/v1/user/lists', compact(
            'searchKey',
            'gender',
            'createdTimeGt',
            'createdTimeLt',
            'sortType',
            'sortDirection',
            'pageSize',
            'page'
        ));
    }

    /**
     * @param  string|null  $username
     * @param  string|null  $nickname
     * @param  string|null  $avatarFid
     * @param  string|null  $avatarUrl
     * @param  int|null  $gender
     * @param  string|null  $birthday
     * @param  string|null  $bio
     * @param  int|null  $dialogLimit
     * @param  string|null  $timezone
     * @return array
     *
     * @throws GuzzleException
     */
    public function edit(
        ?string $username = null,
        ?string $nickname = null,
        ?string $avatarFid = null,
        ?string $avatarUrl = null,
        ?int $gender = null,
        ?string $birthday = null,
        ?string $bio = null,
        ?int $dialogLimit = null,
        ?string $timezone = null,
    ): array {
        $birthday = $birthday ? Carbon::parse($birthday)->format('Y-m-d H:i:s') : null;

        return $this->httpPostJson('/api/v1/user/edit', compact(
            'username',
            'nickname',
            'avatarFid',
            'avatarUrl',
            'gender',
            'birthday',
            'bio',
            'dialogLimit',
            'timezone',
        ));
    }
}
