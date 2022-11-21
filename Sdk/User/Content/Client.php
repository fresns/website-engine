<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jarvis Tang
 * Released under the Apache-2.0 License.
 */

namespace Plugins\FresnsEngine\Sdk\User\Content;

use GuzzleHttp\Exception\GuzzleException;
use Plugins\FresnsEngine\Sdk\Kernel\BaseClient;

class Client extends BaseClient
{
    /**
     * @param  int  $type
     * @param  int  $markType
     * @param  int  $markTarget
     * @param  string  $markId
     * @return array
     *
     * @throws GuzzleException
     */
    public function mark(
        int $type,
        int $markType,
        int $markTarget,
        string $markId
    ): array {
        return $this->httpPostJson('/api/v1/user/mark', [
            'type' => $type,
            'markType' => $markType,
            'markTarget' => $markTarget,
            'markId' => $markId,
        ]);
    }

    /**
     * @param  int  $viewType
     * @param  int  $viewTarget
     * @param  string|null  $viewUid
     * @param  string|null  $viewUsername
     * @param  int|null  $pageSize
     * @param  int|null  $page
     * @return array
     *
     * @throws GuzzleException
     */
    public function markLists(
        int $viewType,
        int $viewTarget,
        ?string $viewUid = null,
        ?string $viewUsername = null,
        ?int $pageSize = null,
        ?int $page = null
    ): array {
        return $this->httpPostJson('/api/v1/user/markLists', [
            'viewType' => $viewType,
            'viewTarget' => $viewTarget,
            'viewUid' => $viewUid,
            'viewUsername' => $viewUsername,
            'pageSize' => $pageSize,
            'page' => $page,
        ]);
    }

    /**
     * @param  int  $type
     * @param  int|null  $objectType
     * @param  string|null  $objectFsid
     * @param  string|null  $sortDirection
     * @param  int|null  $pageSize
     * @param  int|null  $page
     * @return array
     *
     * @throws GuzzleException
     */
    public function interactions(
        int $type,
        ?int $objectType = null,
        string $objectFsid = null,
        ?string $sortDirection = null,
        ?int $pageSize = null,
        ?int $page = null
    ): array {
        return $this->httpPostJson('/api/v1/user/interactions', compact(
            'type',
            'objectType',
            'objectFsid',
            'sortDirection',
            'pageSize',
            'page'
        ));
    }

    /**
     * @param  int  $type
     * @param  string  $fsid
     * @return array
     *
     * @throws GuzzleException
     */
    public function delete(
        int $type,
        string $fsid
    ): array {
        return $this->httpPostJson('/api/v1/user/delete', compact(
            'type',
            'fsid'
        ));
    }
}
