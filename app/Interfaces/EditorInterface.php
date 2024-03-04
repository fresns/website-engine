<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jevan Tang
 * Released under the Apache-2.0 License.
 */

namespace Fresns\WebEngine\Interfaces;

use App\Fresns\Api\Http\Controllers\EditorController;
use Fresns\WebEngine\Exceptions\ErrorException;
use Fresns\WebEngine\Helpers\ApiHelper;
use Illuminate\Http\Request;

class EditorInterface
{
    public static function getDraft(string $type, ?int $draftId = null): array
    {
        if (is_remote_api()) {
            $client = ApiHelper::make();

            $params['configs'] = $client->getAsync("/api/fresns/v1/editor/{$type}/configs");

            if ($draftId) {
                $params['draft'] = $client->getAsync("/api/fresns/v1/editor/{$type}/{$draftId}");
            }

            $results = $client->unwrapRequests($params);

            $draftInfo['configs'] = data_get($results, 'configs.data');
            $draftInfo['draft'] = data_get($results, 'draft.data');

            return $draftInfo;
        }

        try {
            $apiController = new EditorController();
            $configResponse = $apiController->config($type);

            $resultContent = $configResponse->getContent();
            $result = json_decode($resultContent, true);

            $draftResult = null;
            if ($draftId) {
                $draftResponse = $apiController->detail($type, $draftId);

                $draftResultContent = $draftResponse->getContent();
                $draftResult = json_decode($draftResultContent, true);
            }

            $draftInfo['config'] = data_get($result, 'data');
            $draftInfo['draft'] = $draftResult ? data_get($draftResult, 'data') : null;
        } catch (\Exception $e) {
            $code = (int) $e->getCode();

            throw new ErrorException($e->getMessage(), $code);
        }

        return $draftInfo;
    }
}
