<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jevan Tang
 * Released under the Apache-2.0 License.
 */

namespace Fresns\WebsiteEngine\Interfaces;

use App\Fresns\Api\Http\Controllers\AccountController;
use App\Fresns\Api\Http\Controllers\EditorController;
use App\Fresns\Api\Http\Controllers\GlobalController;
use App\Fresns\Api\Http\Controllers\UserController;
use Fresns\WebsiteEngine\Exceptions\ErrorException;
use Fresns\WebsiteEngine\Helpers\HttpHelper;
use Illuminate\Http\Request;

class MeInterface
{
    public static function extcreditsRecords(?array $query = []): array
    {
        if (is_remote_api()) {
            return HttpHelper::get('/api/fresns/v1/user/extcredits-records', $query);
        }

        try {
            $request = Request::create('/api/fresns/v1/user/extcredits-records', 'GET', $query);

            $apiController = new UserController();
            $response = $apiController->extcreditsRecords($request);

            if (is_array($response)) {
                $result = $response;
            } else {
                $resultContent = $response->getContent();
                $result = json_decode($resultContent, true);
            }

            if ($result['code'] != 0) {
                throw new ErrorException($result['message'], $result['code']);
            }
        } catch (\Exception $e) {
            $code = (int) $e->getCode();

            throw new ErrorException($e->getMessage(), $code);
        }

        return $result;
    }

    public static function walletRecords(?array $query = []): array
    {
        if (is_remote_api()) {
            return HttpHelper::get('/api/fresns/v1/account/wallet-records', $query);
        }

        try {
            $request = Request::create('/api/fresns/v1/account/wallet-records', 'GET', $query);

            $apiController = new AccountController();
            $response = $apiController->walletRecords($request);

            if (is_array($response)) {
                $result = $response;
            } else {
                $resultContent = $response->getContent();
                $result = json_decode($resultContent, true);
            }

            if ($result['code'] != 0) {
                throw new ErrorException($result['message'], $result['code']);
            }
        } catch (\Exception $e) {
            $code = (int) $e->getCode();

            throw new ErrorException($e->getMessage(), $code);
        }

        return $result;
    }

    public static function drafts(string $type, ?array $query = []): array
    {
        if (is_remote_api()) {
            return HttpHelper::get("/api/fresns/v1/editor/{$type}/drafts", $query);
        }

        try {
            $request = Request::create("/api/fresns/v1/editor/{$type}/drafts", 'GET', $query);

            $apiController = new EditorController();
            $response = $apiController->draftList($type, $request);

            if (is_array($response)) {
                $result = $response;
            } else {
                $resultContent = $response->getContent();
                $result = json_decode($resultContent, true);
            }

            if ($result['code'] != 0) {
                throw new ErrorException($result['message'], $result['code']);
            }
        } catch (\Exception $e) {
            $code = (int) $e->getCode();

            throw new ErrorException($e->getMessage(), $code);
        }

        return $result;
    }

    public static function getDraftDetail(string $type, string $did): array
    {
        if (is_remote_api()) {
            return HttpHelper::get("/api/fresns/v1/editor/{$type}/draft/{$did}");
        }

        try {
            $request = Request::create("/api/fresns/v1/editor/{$type}/draft/{$did}", 'GET');

            $apiController = new EditorController();
            $response = $apiController->draftDetail($type, $did, $request);

            if (is_array($response)) {
                $result = $response;
            } else {
                $resultContent = $response->getContent();
                $result = json_decode($resultContent, true);
            }

            if ($result['code'] != 0) {
                throw new ErrorException($result['message'], $result['code']);
            }
        } catch (\Exception $e) {
            $code = (int) $e->getCode();

            throw new ErrorException($e->getMessage(), $code);
        }

        return $result;
    }

    public static function archives(string $type): array
    {
        if (is_remote_api()) {
            return HttpHelper::get("/api/fresns/v1/global/{$type}/archives");
        }

        try {
            $request = Request::create("/api/fresns/v1/global/{$type}/archives", 'GET');

            $apiController = new GlobalController();
            $response = $apiController->archives('user', $request);

            if (is_array($response)) {
                $result = $response;
            } else {
                $resultContent = $response->getContent();
                $result = json_decode($resultContent, true);
            }

            if ($result['code'] != 0) {
                throw new ErrorException($result['message'], $result['code']);
            }
        } catch (\Exception $e) {
            $code = (int) $e->getCode();

            throw new ErrorException($e->getMessage(), $code);
        }

        return $result;
    }
}
