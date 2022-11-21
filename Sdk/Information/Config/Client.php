<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jarvis Tang
 * Released under the Apache-2.0 License.
 */

namespace Plugins\FresnsEngine\Sdk\Information\Config;

use GuzzleHttp\Exception\GuzzleException;
use Plugins\FresnsEngine\Sdk\Kernel\BaseClient;

class Client extends BaseClient
{
    /**
     * @param  string|null  $itemKey
     * @param  string|null  $itemTag
     * @param  int  $pageSize
     * @param  int  $page
     * @return array
     *
     * @throws GuzzleException
     */
    public function get(
        ?string $itemKey = null,
        ?string $itemTag = null,
        int $pageSize = 100,
        int $page = 1
    ): array {
        return $this->httpPostJson('/api/v1/info/configs', [
            'itemTag' => $itemTag,
            'itemKey' => $itemKey,
            'pageSize' => $pageSize,
            'page' => $page,
        ]);
    }
}
