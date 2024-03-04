<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jevan Tang
 * Released under the Apache-2.0 License.
 */

namespace Fresns\WebEngine\Http\Controllers;

use App\Helpers\CacheHelper;
use App\Models\File;
use App\Utilities\ConfigUtility;
use Fresns\WebEngine\Exceptions\ErrorException;
use Fresns\WebEngine\Helpers\ApiHelper;
use Fresns\WebEngine\Helpers\DataHelper;
use Fresns\WebEngine\Interfaces\MeInterface;
use Illuminate\Http\Request;

class EditorController extends Controller
{
    // post
    public function post(Request $request)
    {
        $did = $request->did;
        $pid = $request->pid;

        // edit draft
        if ($did) {
            $result = MeInterface::getDraftDetail('post', $did);

            if ($result['code'] != 0) {
                throw new ErrorException($result['message'], $result['code']);
            }

            // post_editor_service
            if (fs_config('post_editor_service')) {
                $pluginUrl = DataHelper::getEditorUrl(fs_config('post_editor_service'), 'post', $did, $pid);

                return redirect()->to($pluginUrl);
            }

            return redirect()->to(fs_route(route('fresns.editor.edit', [
                'type' => 'post',
                'did' => $did,
            ])));
        }

        // edit published post
        if ($pid) {
            $postResponse = ApiHelper::make()->post("/api/fresns/v1/editor/post/edit/{$pid}");

            if (data_get($postResponse, 'code') !== 0) {
                throw new ErrorException($postResponse['message'], $postResponse['code']);
            }

            $did = $postResponse['data']['detail']['did'];

            // post_editor_service
            if (fs_config('post_editor_service')) {
                $pluginUrl = DataHelper::getEditorUrl(fs_config('post_editor_service'), 'post', $did, $pid);

                return redirect()->to($pluginUrl);
            }

            return redirect()->to(fs_route(route('fresns.editor.edit', [
                'type' => 'post',
                'did' => $did,
            ])));
        }

        // drafts
        $drafts = MeInterface::drafts('post');
        $skipDrafts = $request->skipDrafts;

        if (empty($drafts) || $skipDrafts) {
            $response = ApiHelper::make()->post('/api/fresns/v1/editor/post/draft', [
                'json' => [
                    'createType' => 2,
                    'gid' => $request->gid,
                    'quotePid' => $request->quotePid,
                    'gtid' => $request->gtid,
                ],
            ]);

            if (data_get($response, 'code') !== 0) {
                throw new ErrorException($response['message'], $response['code']);
            }

            $did = $response['data']['detail']['did'];

            // post_editor_service
            if (fs_config('post_editor_service')) {
                $pluginUrl = DataHelper::getEditorUrl(fs_config('post_editor_service'), 'post', $did, $pid);

                return redirect()->to($pluginUrl);
            }

            return redirect()->to(fs_route(route('fresns.editor.edit', [
                'type' => 'post',
                'did' => $did,
            ])));
        }

        $type = 'post';

        // editor configs
        $uid = fs_user('detail.uid');
        $langTag = fs_theme('lang');

        $cacheKey = "fresns_web_post_editor_configs_{$uid}_{$langTag}";
        $cacheTags = ['fresnsWeb', 'fresnsWebConfigs'];

        // get cache
        $configs = CacheHelper::get($cacheKey, $cacheTags);

        if (empty($configs)) {
            $result = ApiHelper::make()->get('/api/fresns/v1/editor/post/configs');

            $configs = data_get($result, 'data');

            $cacheTime = CacheHelper::fresnsCacheTimeByFileType(File::TYPE_IMAGE);
            CacheHelper::put($configs, $cacheKey, $cacheTags, null, $cacheTime);
        }

        return view('editor.index', compact('type', 'configs', 'drafts'));
    }

    // comment
    public function comment(Request $request)
    {
        $did = $request->did;
        $cid = $request->cid;
        $pid = $request->pid;

        // edit draft
        if ($did) {
            $result = MeInterface::getDraftDetail('comment', $did);

            if ($result['code'] != 0) {
                throw new ErrorException($result['message'], $result['code']);
            }

            // comment_editor_service
            if (fs_config('comment_editor_service')) {
                $pluginUrl = DataHelper::getEditorUrl(fs_config('comment_editor_service'), 'comment', $did, $pid);

                return redirect()->to($pluginUrl);
            }

            return redirect()->to(fs_route(route('fresns.editor.edit', [
                'type' => 'comment',
                'did' => $did,
            ])));
        }

        // edit published comment
        if ($cid) {
            $commentResponse = ApiHelper::make()->post("/api/fresns/v1/editor/comment/edit/{$cid}");

            if (data_get($commentResponse, 'code') !== 0) {
                throw new ErrorException($commentResponse['message'], $commentResponse['code']);
            }

            $did = $commentResponse['data']['detail']['did'];

            // comment_editor_service
            if (fs_config('comment_editor_service')) {
                $pluginUrl = DataHelper::getEditorUrl(fs_config('comment_editor_service'), 'comment', $did, $cid);

                return redirect()->to($pluginUrl);
            }

            return redirect()->to(fs_route(route('fresns.editor.edit', [
                'type' => 'comment',
                'did' => $did,
            ])));
        }

        // new draft
        if (empty($pid)) {
            $errorMessage = ConfigUtility::getCodeMessage(30001, 'Fresns', fs_theme('lang'));

            throw new ErrorException($errorMessage, 30001);
        }

        $response = ApiHelper::make()->post('/api/fresns/v1/editor/comment/draft', [
            'json' => [
                'createType' => 2,
                'commentPid' => $pid,
                'gtid' => $request->gtid,
            ],
        ]);

        if (data_get($response, 'code') !== 0) {
            throw new ErrorException($response['message'], $response['code']);
        }

        $did = $response['data']['detail']['did'];

        // comment_editor_service
        if (fs_config('comment_editor_service')) {
            $pluginUrl = DataHelper::getEditorUrl(fs_config('comment_editor_service'), 'comment', $did, $pid);

            return redirect()->to($pluginUrl);
        }

        return redirect()->to(fs_route(route('fresns.editor.edit', [
            'type' => 'comment',
            'did' => $did,
        ])));
    }

    // edit
    public function edit(string $type, string $did)
    {
        $type = match ($type) {
            'posts' => 'post',
            'comments' => 'comment',
            'post' => 'post',
            'comment' => 'comment',
            default => 'post',
        };

        // editor configs
        $uid = fs_user('detail.uid');
        $langTag = fs_theme('lang');

        $cacheKey = "fresns_web_{$type}_editor_configs_{$uid}_{$langTag}";
        $cacheTags = ['fresnsWeb', 'fresnsWebConfigs'];

        // get cache
        $configs = CacheHelper::get($cacheKey, $cacheTags);

        if (empty($configs)) {
            $result = ApiHelper::make()->get("/api/fresns/v1/editor/{$type}/configs");

            $configs = data_get($result, 'data');

            $cacheTime = CacheHelper::fresnsCacheTimeByFileType(File::TYPE_IMAGE);
            CacheHelper::put($configs, $cacheKey, $cacheTags, null, $cacheTime);
        }

        // draft
        $result = MeInterface::getDraftDetail($type, $did);

        if ($result['code'] != 0) {
            throw new ErrorException($result['message'], $result['code']);
        }

        $draft = $result['data'];

        return view('editor.edit', compact('type', 'configs', 'draft'));
    }
}
