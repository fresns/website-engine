<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jarvis Tang
 * Released under the Apache-2.0 License.
 */

namespace Plugins\FresnsEngine\Sdk\Information\Files;

use GuzzleHttp\Exception\GuzzleException;
use Plugins\FresnsEngine\Sdk\Kernel\BaseClient;

class Client extends BaseClient
{
    /**
     * @param  int  $type
     * @param  string  $fsid
     * @param  string  $fid
     * @return array
     *
     * @throws GuzzleException
     */
    public function download(
        int $type,
        string $fsid,
        string $fid
    ): array {
        return $this->httpPostJson('/api/v1/info/downloadFile', compact(
            'type',
            'fsid',
            'fid'
        ));
    }
}
