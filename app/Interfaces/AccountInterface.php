<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jevan Tang
 * Released under the Apache-2.0 License.
 */

namespace Fresns\WebEngine\Interfaces;

use App\Fresns\Api\Http\Controllers\AccountController;
use App\Fresns\Api\Http\Controllers\UserController;
use Fresns\WebEngine\Exceptions\ErrorException;
use Fresns\WebEngine\Helpers\ApiHelper;
use Illuminate\Http\Request;

class AccountInterface
{
    public static function walletLogs(?array $query = []): array
    {
        if (is_remote_api()) {
            return ApiHelper::make()->get('/api/fresns/v1/account/wallet-logs', [
                'query' => $query,
            ]);
        }

        try {
            $request = Request::create('/api/fresns/v1/account/wallet-logs', 'GET', $query);

            $apiController = new AccountController();
            $response = $apiController->walletLogs($request);

            $resultContent = $response->getContent();
            $result = json_decode($resultContent, true);
        } catch (\Exception $e) {
            $code = (int) $e->getCode();

            throw new ErrorException($e->getMessage(), $code);
        }

        return $result;
    }

    public static function extcreditsLogs(?array $query = []): array
    {
        if (is_remote_api()) {
            return ApiHelper::make()->get('/api/fresns/v1/user/extcredits-logs', [
                'query' => $query,
            ]);
        }

        try {
            $request = Request::create('/api/fresns/v1/user/extcredits-logs', 'GET', $query);

            $apiController = new UserController();
            $response = $apiController->extcreditsLogs($request);

            $resultContent = $response->getContent();
            $result = json_decode($resultContent, true);
        } catch (\Exception $e) {
            $code = (int) $e->getCode();

            throw new ErrorException($e->getMessage(), $code);
        }

        return $result;
    }
}
