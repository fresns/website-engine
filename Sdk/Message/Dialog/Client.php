<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jarvis Tang
 * Released under the Apache-2.0 License.
 */

namespace Plugins\FresnsEngine\Sdk\Message\Dialog;

use GuzzleHttp\Exception\GuzzleException;
use Plugins\FresnsEngine\Sdk\Kernel\BaseClient;

class Client extends BaseClient
{
    /**
     * @param $pageSize
     * @param $page
     * @return array
     *
     * @throws GuzzleException
     */
    public function lists(
        $pageSize,
        $page
    ): array {
        return $this->httpPostJson('/api/v1/dialog/lists', compact('pageSize', 'page'));
    }
}
