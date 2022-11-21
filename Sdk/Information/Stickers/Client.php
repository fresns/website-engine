<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jarvis Tang
 * Released under the Apache-2.0 License.
 */

namespace Plugins\FresnsEngine\Sdk\Information\Stickers;

use Plugins\FresnsEngine\Sdk\Kernel\BaseClient;

class Client extends BaseClient
{
    /**
     * @param  int  $pageSize
     * @param  int  $page
     * @return array
     *
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function get(
        int $pageSize = 10,
        int $page = 1
    ): array {
        return $this->httpPostJson('/api/v1/info/stickers', [
            'pageSize' => $pageSize,
            'page' => $page,
        ]);
    }
}
