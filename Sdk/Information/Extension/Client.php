<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jarvis Tang
 * Released under the Apache-2.0 License.
 */

namespace Plugins\FresnsEngine\Sdk\Information\Extension;

use Plugins\FresnsEngine\Sdk\Kernel\BaseClient;

class Client extends BaseClient
{
    public function get(
        int $type,
        int $scene = 0,
        int $pageSize = 100,
        int $page = 1
    ): array {
        return $this->httpPostJson('/api/v1/info/extensions', [
            'type' => $type,
            'scene' => $scene,
            'pageSize' => $pageSize,
            'page' => $page,
        ]);
    }
}
