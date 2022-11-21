<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jarvis Tang
 * Released under the Apache-2.0 License.
 */

namespace Plugins\FresnsEngine\Sdk\Message\Notify;

use GuzzleHttp\Exception\GuzzleException;
use Plugins\FresnsEngine\Sdk\Kernel\BaseClient;

class Client extends BaseClient
{
    /**
     * @param  string|null  $type
     * @param  int|null  $pageSize
     * @param  int|null  $page
     * @return array
     *
     * @throws GuzzleException
     */
    public function lists(
        ?string $type = null,
        ?int $pageSize = null,
        ?int $page = null
    ): array {
        return $this->httpPostJson('/api/v1/notify/lists', compact(
            'type',
            'pageSize',
            'page'
        ));
    }

    /**
     * @param  int  $type  1.system / 2.follow / 3.like / 4.comment / 5.mention / 6.recommend
     * @return array
     *
     * @throws GuzzleException
     */
    public function read(
        int $type
    ): array {
        return $this->httpPostJson('/api/v1/notify/read', compact('type'));
    }
}
