<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jarvis Tang
 * Released under the Apache-2.0 License.
 */

namespace Plugins\FresnsEngine\Sdk\Content\Comment;

use GuzzleHttp\Exception\GuzzleException;
use Plugins\FresnsEngine\Sdk\Kernel\BaseClient;

class Client extends BaseClient
{
    public function lists(
        ?string $searchPid = null,
        ?string $searchCid = null,
        ?string $sortType = null,
        ?int $sortDirection = null,
        ?int $pageSize = null,
        ?int $page = null,
        ?string $searchType = null,
        ?string $searchKey = null,
        ?int $searchUid = null,
        ?int $searchSticky = null,
        ?int $likeCountGt = null,
        ?int $likeCountLt = null,
        ?int $followCountGt = null,
        ?int $followCountLt = null,
        ?int $blockCountGt = null,
        ?int $blockCountLt = null,
        ?int $commentCountGt = null,
        ?int $commentCountLt = null,
        ?string $createdTimeGt = null,
        ?string $createdTimeLt = null,
        ?int $mapId = null,
        ?string $longitude = null,
        ?string $latitude = null
    ): array {
        return $this->httpPostJson('/api/v1/comment/lists', [
            'searchType' => $searchType,
            'searchKey' => $searchKey,
            'searchUid' => $searchUid,
            'searchPid' => $searchPid,
            'searchCid' => $searchCid,
            'searchSticky' => $searchSticky,
            'likeCountGt' => $likeCountGt,
            'likeCountLt' => $likeCountLt,
            'followCountGt' => $followCountGt,
            'followCountLt' => $followCountLt,
            'blockCountGt' => $blockCountGt,
            'blockCountLt' => $blockCountLt,
            'commentCountGt' => $commentCountGt,
            'commentCountLt' => $commentCountLt,
            'createdTimeGt' => $createdTimeGt,
            'createdTimeLt' => $createdTimeLt,
            'mapId' => $mapId,
            'longitude' => $longitude,
            'latitude' => $latitude,
            'sortType' => $sortType,
            'sortDirection' => $sortDirection,
            'pageSize' => $pageSize,
            'page' => $page,
        ]);
    }

    /**
     * @param  string  $cid
     * @param  int|null  $mapId
     * @param  string|null  $longitude
     * @param  string|null  $latitude
     * @return array
     *
     * @throws GuzzleException
     */
    public function detail(
        string $cid,
        ?int $mapId = null,
        ?string $longitude = null,
        ?string $latitude = null
    ): array {
        return $this->httpPostJson('/api/v1/comment/detail', compact(
           'cid',
           'mapId',
           'longitude',
           'latitude'
        ));
    }
}
