<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jarvis Tang
 * Released under the Apache-2.0 License.
 */

namespace Plugins\FresnsEngine\Sdk\Content\Hashtag;

use GuzzleHttp\Exception\GuzzleException;
use Plugins\FresnsEngine\Sdk\Kernel\BaseClient;

class Client extends BaseClient
{
    /**
     * @param  int|null  $pageSize
     * @param  int|null  $page
     * @param  string|null  $searchKey
     * @param  int|null  $viewCountGt
     * @param  int|null  $viewCountLt
     * @param  int|null  $likeCountGt
     * @param  int|null  $likeCountLt
     * @param  int|null  $followCountGt
     * @param  int|null  $followCountLt
     * @param  int|null  $blockCountGt
     * @param  int|null  $blockCountLt
     * @param  int|null  $postCountGt
     * @param  int|null  $postCountLt
     * @param  int|null  $digestCountGt
     * @param  int|null  $digestCountLt
     * @param  string|null  $createdTimeGt
     * @param  string|null  $createdTimeLt
     * @param  string|null  $sortType
     * @param  int|null  $sortDirection
     * @return array
     *
     * @throws GuzzleException
     */
    public function lists(
        ?int $pageSize = null,
        ?int $page = null,
        ?string $searchKey = null,
        ?int $viewCountGt = null,
        ?int $viewCountLt = null,
        ?int $likeCountGt = null,
        ?int $likeCountLt = null,
        ?int $followCountGt = null,
        ?int $followCountLt = null,
        ?int $blockCountGt = null,
        ?int $blockCountLt = null,
        ?int $postCountGt = null,
        ?int $postCountLt = null,
        ?int $digestCountGt = null,
        ?int $digestCountLt = null,
        ?string $createdTimeGt = null,
        ?string $createdTimeLt = null,
        ?string $sortType = null,
        ?int $sortDirection = null
    ): array {
        return $this->httpPostJson('/api/v1/hashtag/lists', [
            'searchKey' => $searchKey,
            'viewCountGt' => $viewCountGt,
            'viewCountLt' => $viewCountLt,
            'likeCountGt' => $likeCountGt,
            'likeCountLt' => $likeCountLt,
            'followCountGt' => $followCountGt,
            'followCountLt' => $followCountLt,
            'blockCountGt' => $blockCountGt,
            'blockCountLt' => $blockCountLt,
            'postCountGt' => $postCountGt,
            'postCountLt' => $postCountLt,
            'digestCountGt' => $digestCountGt,
            'digestCountLt' => $digestCountLt,
            'createdTimeGt' => $createdTimeGt,
            'createdTimeLt' => $createdTimeLt,
            'sortType' => $sortType,
            'sortDirection' => $sortDirection,
            'pageSize' => $pageSize,
            'page' => $page,
        ]);
    }

    /**
     * @param  string  $huri
     * @return array
     *
     * @throws GuzzleException
     */
    public function detail(
        string $huri
    ): array {
        $huri = rawurlencode($huri);

        return $this->httpPostJson('/api/v1/hashtag/detail', compact('huri'));
    }
}
