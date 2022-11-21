<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jarvis Tang
 * Released under the Apache-2.0 License.
 */

namespace Plugins\FresnsEngine\Http\Controllers;

use GuzzleHttp\Client;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Response;
use Plugins\FresnsEngine\Sdk\Factory;

class FilesController extends Controller
{
    public function download(int $type, string $fsid, string $fid)
    {
        try {
            $result = Factory::information()->files->download($type, $fsid, $fid);

            if (Arr::get($result, 'code') !== 0) {
                return back()->with(['failure' => $result['message'], 'code' => $result['code']]);
            }

            $downloadUrl = Arr::get($result, 'data.downloadUrl');

            return Response::streamDownload(function () use ($downloadUrl) {
                echo (new Client())->get($downloadUrl, [
                    'timeout' => 600,
                    'connect_timeout' => 600,
                    'read_timeout' => 600,
                ])->getBody();
            }, pathinfo($downloadUrl, PATHINFO_BASENAME));
        } catch (\Exception $exception) {
            return back()->withErrors($exception->getMessage());
        }
    }
}
