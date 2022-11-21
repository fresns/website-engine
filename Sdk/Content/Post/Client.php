<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jarvis Tang
 * Released under the Apache-2.0 License.
 */

namespace Plugins\FresnsEngine\Sdk\Content\Post;

use GuzzleHttp\Exception\GuzzleException;
use Plugins\FresnsEngine\Sdk\Kernel\BaseClient;

class Client extends BaseClient
{
    /**
     * @param  int|null  $pageSize
     * @param  int|null  $page
     * @param  string|null  $searchType
     * @param  string|null  $searchKey
     * @param  int|null  $searchUid
     * @param  string|null  $searchGid
     * @param  string|null  $searchHuri
     * @param  int|null  $searchDigest
     * @param  int|null  $searchSticky
     * @param  int|null  $viewCountGt
     * @param  int|null  $viewCountLt
     * @param  int|null  $likeCountGt
     * @param  int|null  $likeCountLt
     * @param  int|null  $followCountGt
     * @param  int|null  $followCountLt
     * @param  int|null  $blockCountGt
     * @param  int|null  $blockCountLt
     * @param  int|null  $commentCountGt
     * @param  int|null  $commentCountLt
     * @param  string|null  $createdTimeGt
     * @param  string|null  $createdTimeLt
     * @param  int|null  $mapId
     * @param  string|null  $longitude
     * @param  string|null  $latitude
     * @param  string|null  $sortType
     * @param  int|null  $sortDirection
     * @param  int|null  $rankNumber
     * @return array
     *
     * @throws GuzzleException
     */
    public function lists(
        ?int $pageSize = null,
        ?int $page = null,
        ?string $searchType = null,
        ?string $searchKey = null,
        ?int $searchUid = null,
        ?string $searchGid = null,
        ?string $searchHuri = null,
        ?int $searchDigest = null,
        ?int $searchSticky = null,
        ?int $viewCountGt = null,
        ?int $viewCountLt = null,
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
        ?string $latitude = null,
        ?string $sortType = null,
        ?int $sortDirection = null,
        ?int $rankNumber = null
    ): array {
        return $this->httpPostJson('/api/v1/post/lists', [
            'searchType' => $searchType,
            'searchKey' => $searchKey,
            'searchUid' => $searchUid,
            'searchGid' => $searchGid,
            'searchHuri' => $searchHuri ? rawurlencode($searchHuri) : null,
            'searchDigest' => $searchDigest,
            'searchSticky' => $searchSticky,
            'viewCountGt' => $viewCountGt,
            'viewCountLt' => $viewCountLt,
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
            'rankNumber' => $rankNumber,
            'pageSize' => $pageSize,
            'page' => $page,
        ]);
    }

    /**
     * @param  string  $pid
     * @param  int|null  $mapId
     * @param  int|null  $longitude
     * @param  int|null  $latitude
     * @return array
     *
     * @throws GuzzleException
     */
    public function detail(
        string $pid,
        ?int $mapId = null,
        ?int $longitude = null,
        ?int $latitude = null
    ): array {
        return $this->httpPostJson('/api/v1/post/detail', compact(
            'pid',
            'mapId',
            'longitude',
            'latitude'
        ));
    }

    /**
     * @param  int|null  $pageSize
     * @param  int|null  $page
     * @param  string|null  $searchType
     * @param  string|null  $searchKey
     * @param  string|null  $followType
     * @param  int|null  $mapId
     * @param  string|null  $longitude
     * @param  string|null  $latitude
     * @param  int|null  $rankNumber
     * @return array
     *
     * @throws GuzzleException
     */
    public function follows(
        ?int $pageSize = null,
        ?int $page = null,
        ?string $searchType = null,
        ?string $searchKey = null,
        ?string $followType = null,
        ?int $mapId = null,
        ?string $longitude = null,
        ?string $latitude = null,
        ?int $rankNumber = null
    ): array {
        return $this->httpPostJson('/api/v1/post/follows', [
            'searchType' => $searchType,
            'searchKey' => $searchKey,
            'followType' => $followType,
            'mapId' => $mapId,
            'longitude' => $longitude,
            'latitude' => $latitude,
            'rankNumber' => $rankNumber,
            'pageSize' => $pageSize,
            'page' => $page,
        ]);
    }

    /**
     * @param  string  $longitude
     * @param  string  $latitude
     * @param  int  $mapId
     * @param  string|null  $searchType
     * @param  string|null  $searchKey
     * @param  int|null  $length
     * @param  string|null  $lengthUnits
     * @param  int|null  $pageSize
     * @param  int|null  $page
     * @param  int|null  $rankNumber
     * @return array
     *
     * @throws GuzzleException
     */
    public function nearbys(
        string $longitude,
        string $latitude,
        int $mapId,
        ?string $searchType = null,
        ?string $searchKey = null,
        ?int $length = null,
        ?string $lengthUnits = null,
        ?int $pageSize = null,
        ?int $page = null,
        ?int $rankNumber = null
    ): array {
        return $this->httpPostJson('/api/v1/post/nearbys', [
            'length' => $length,
            'lengthUnits' => $lengthUnits,
            'mapId' => $mapId,
            'searchType' => $searchType,
            'searchKey' => $searchKey,
            'longitude' => $longitude,
            'latitude' => $latitude,
            'rankNumber' => $rankNumber,
            'pageSize' => $pageSize,
            'page' => $page,
        ]);
    }
}
