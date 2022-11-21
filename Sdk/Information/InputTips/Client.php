<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jarvis Tang
 * Released under the Apache-2.0 License.
 */

namespace Plugins\FresnsEngine\Sdk\Information\InputTips;

use Plugins\FresnsEngine\Sdk\Kernel\BaseClient;

class Client extends BaseClient
{
    /**
     * @param  int  $queryType
     * @param  string  $queryKey
     * @return array
     *
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function get(
        int $queryType,
        string $queryKey
    ): array {
        return $this->httpPostJson('/api/v1/info/inputTips', [
            'queryType' =>$queryType,
            'queryKey' => $queryKey,
        ]);
    }
}
