<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jarvis Tang
 * Released under the Apache-2.0 License.
 */

namespace Plugins\FresnsEngine\Sdk\Information\VerifyCode;

use GuzzleHttp\Exception\GuzzleException;
use Plugins\FresnsEngine\Sdk\Kernel\BaseClient;

class Client extends BaseClient
{
    /**
     * @param  int  $type
     * @param  int  $useType
     * @param  int  $templateId
     * @param  string|null  $account
     * @param  int|null  $countryCode
     * @return array
     *
     * @throws GuzzleException
     */
    public function send(
        int $type,
        int $useType,
        int $templateId,
        ?string $account = null,
        ?int $countryCode = null
    ): array {
        return $this->httpPostJson('/api/v1/info/sendVerifyCode', [
            'type' => $type,
            'useType' => $useType,
            'templateId' => $templateId,
            'account' => $account,
            'countryCode' => $countryCode,
        ]);
    }
}
