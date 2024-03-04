<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jevan Tang
 * Released under the Apache-2.0 License.
 */

namespace Fresns\WebEngine\Http\Controllers;

use App\Utilities\ConfigUtility;
use Fresns\WebEngine\Helpers\ApiHelper;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Str;

class ApiController extends Controller
{
    // make access token
    public function makeAccessToken(): JsonResponse
    {
        $headers = Arr::except(ApiHelper::getHeaders(), ['Accept']);

        $accessToken = urlencode(base64_encode(json_encode($headers)));

        return Response::json([
            'code' => 0,
            'message' => 'ok',
            'data' => [
                'accessToken' => $accessToken,
            ],
        ]);
    }

    // api get
    public function apiGet(Request $request, string $path): JsonResponse
    {
        $endpointPath = Str::of($path)->start('/')->toString();

        if (in_array($endpointPath, [
            '/status.json',
            '/api/fresns/v1/global/status',
            '/api/fresns/v1/global/configs',
            '/api/fresns/v1/global/language-pack',
            '/api/fresns/v1/global/stickers',
        ])) {
            $data = match ($endpointPath) {
                '/status.json' => fs_status(),
                '/api/fresns/v1/global/status' => fs_status(),
                '/api/fresns/v1/global/configs' => fs_config(),
                '/api/fresns/v1/global/language-pack' => fs_lang(),
                '/api/fresns/v1/global/stickers' => fs_stickers(),
            };

            return Response::json([
                'code' => 0,
                'message' => 'ok',
                'data' => $data,
            ]);
        }

        $startsWith = Str::startsWith($endpointPath, [
            '/api/fresns/v1/user/',
            '/api/fresns/v1/hashtag/',
            '/api/fresns/v1/geotag/',
            '/api/fresns/v1/post/',
            '/api/fresns/v1/comment/',
            '/api/fresns/v1/search/',
        ]);

        $pattern = '/^\/api\/fresns\/v1\/group\/\d+\/interaction\/.*$/';

        if ($endpointPath == '/api/fresns/v1/common/ip-info' || $startsWith || preg_match($pattern, $endpointPath)) {
            $langTag = fs_theme('lang');

            return Response::json([
                'code' => 33100,
                'message' => ConfigUtility::getCodeMessage(33100, 'Fresns', $langTag),
                'data' => [],
            ]);
        }

        $result = ApiHelper::make()->get($endpointPath, [
            'query' => $request->all(),
        ]);

        return Response::json($result);
    }

    // api post
    public function apiPost(Request $request, string $path): JsonResponse|RedirectResponse
    {
        $endpointPath = Str::of($path)->start('/')->toString();

        if (in_array($endpointPath, [
            '/api/fresns/v1/common/file/uploads',
            '/api/fresns/v1/editor/post/publish',
            '/api/fresns/v1/editor/comment/publish',
        ])) {
            $multipart = [];
            foreach ($request->all() as $name => $contents) {
                if ($request->hasFile($name)) {
                    $file = $request->file($name);

                    $multipart[] = [
                        'name' => $name,
                        'contents' => fopen($file->getPathname(), 'r'),
                        'filename' => $file->getClientOriginalName(),
                        'headers' => [
                            'Content-Type' => $file->getMimeType(),
                        ],
                    ];

                    continue;
                }

                $multipart[] = compact('name', 'contents');
            }

            $result = ApiHelper::make()->post($endpointPath, [
                'multipart' => $multipart,
            ]);
        } else {
            $result = ApiHelper::make()->post($endpointPath, [
                'json' => $request->all(),
            ]);
        }

        // ajax
        if ($request->ajax()) {
            return Response::json($result);
        }

        $redirectURL = $request->redirectURL ?? fs_route(route('fresns.home'));

        return redirect()->intended($redirectURL);
    }

    // api put
    public function apiPut(Request $request, string $path): JsonResponse|RedirectResponse
    {
        $endpointPath = Str::of($path)->start('/')->toString();

        $result = ApiHelper::make()->put($endpointPath, [
            'json' => $request->all(),
        ]);

        // ajax
        if ($request->ajax()) {
            return Response::json($result);
        }

        $redirectURL = $request->redirectURL ?? fs_route(route('fresns.home'));

        return redirect()->intended($redirectURL);
    }

    // api patch
    public function apiPatch(Request $request, string $path): JsonResponse|RedirectResponse
    {
        $endpointPath = Str::of($path)->start('/')->toString();

        $result = ApiHelper::make()->patch($endpointPath, [
            'json' => $request->all(),
        ]);

        // ajax
        if ($request->ajax()) {
            return Response::json($result);
        }

        $redirectURL = $request->redirectURL ?? fs_route(route('fresns.home'));

        return redirect()->intended($redirectURL);
    }

    // api delete
    public function apiDelete(Request $request, string $path): JsonResponse|RedirectResponse
    {
        $endpointPath = Str::of($path)->start('/')->toString();

        $result = ApiHelper::make()->delete($endpointPath, [
            'json' => $request->all(),
        ]);

        // ajax
        if ($request->ajax()) {
            return Response::json($result);
        }

        $redirectURL = $request->redirectURL ?? fs_route(route('fresns.home'));

        return redirect()->intended($redirectURL);
    }
}
