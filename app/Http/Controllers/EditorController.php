<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jevan Tang
 * Released under the Apache-2.0 License.
 */

namespace Fresns\WebsiteEngine\Http\Controllers;

use App\Helpers\CacheHelper;
use App\Models\File;
use App\Utilities\ConfigUtility;
use Fresns\WebsiteEngine\Exceptions\ErrorException;
use Fresns\WebsiteEngine\Helpers\ApiHelper;
use Fresns\WebsiteEngine\Helpers\DataHelper;
use Fresns\WebsiteEngine\Interfaces\MeInterface;
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
            // post_editor_service
            if (fs_config('post_editor_service')) {
                $pluginUrl = DataHelper::getEditorUrl(fs_config('post_editor_service'), 'post', $did, $pid);

                return redirect()->to($pluginUrl);
            }

            return redirect()->to(route('fresns.editor.edit', [
                'type' => 'post',
                'did' => $did,
            ]));
        }

        // edit published post
        if ($pid) {
            $postResponse = ApiHelper::make()->post("/api/fresns/v1/editor/post/edit/{$pid}");

            $did = $postResponse['data']['detail']['did'] ?? null;

            if (empty($did)) {
                $errorMessage = ConfigUtility::getCodeMessage(38100, 'Fresns', fs_theme('lang'));

                throw new ErrorException($errorMessage, 38100);
            }

            // post_editor_service
            if (fs_config('post_editor_service')) {
                $pluginUrl = DataHelper::getEditorUrl(fs_config('post_editor_service'), 'post', $did, $pid);

                return redirect()->to($pluginUrl);
            }

            return redirect()->to(route('fresns.editor.edit', [
                'type' => 'post',
                'did' => $did,
            ]));
        }

        // drafts
        $draftsResult = MeInterface::drafts('post');
        $drafts = $draftsResult['data']['list'];
        $skipDrafts = $request->skipDrafts;

        if (empty($drafts) || $skipDrafts) {
            $response = ApiHelper::make()->post('/api/fresns/v1/editor/post/draft', [
                'json' => [
                    'gid' => $request->gid,
                    'quotePid' => $request->quotePid,
                    'gtid' => $request->gtid,
                ],
            ]);

            $did = $response['data']['detail']['did'] ?? null;

            if (empty($did)) {
                $errorMessage = ConfigUtility::getCodeMessage(38100, 'Fresns', fs_theme('lang'));

                throw new ErrorException($errorMessage, 38100);
            }

            // post_editor_service
            if (fs_config('post_editor_service')) {
                $pluginUrl = DataHelper::getEditorUrl(fs_config('post_editor_service'), 'post', $did, $pid);

                return redirect()->to($pluginUrl);
            }

            return redirect()->to(route('fresns.editor.edit', [
                'type' => 'post',
                'did' => $did,
            ]));
        }

        $type = 'post';

        // editor configs
        $configs = DataHelper::getEditorConfigs('post');

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
            // comment_editor_service
            if (fs_config('comment_editor_service')) {
                $pluginUrl = DataHelper::getEditorUrl(fs_config('comment_editor_service'), 'comment', $did, $pid);

                return redirect()->to($pluginUrl);
            }

            return redirect()->to(route('fresns.editor.edit', [
                'type' => 'comment',
                'did' => $did,
            ]));
        }

        // edit published comment
        if ($cid) {
            $commentResponse = ApiHelper::make()->post("/api/fresns/v1/editor/comment/edit/{$cid}");

            $did = $commentResponse['data']['detail']['did'] ?? null;

            if (empty($did)) {
                $errorMessage = ConfigUtility::getCodeMessage(38100, 'Fresns', fs_theme('lang'));

                throw new ErrorException($errorMessage, 38100);
            }

            // comment_editor_service
            if (fs_config('comment_editor_service')) {
                $pluginUrl = DataHelper::getEditorUrl(fs_config('comment_editor_service'), 'comment', $did, $cid);

                return redirect()->to($pluginUrl);
            }

            return redirect()->to(route('fresns.editor.edit', [
                'type' => 'comment',
                'did' => $did,
            ]));
        }

        // new draft
        if (empty($pid)) {
            $errorMessage = ConfigUtility::getCodeMessage(30001, 'Fresns', fs_theme('lang'));

            throw new ErrorException($errorMessage, 30001);
        }

        $response = ApiHelper::make()->post('/api/fresns/v1/editor/comment/draft', [
            'json' => [
                'commentPid' => $pid,
                'gtid' => $request->gtid,
            ],
        ]);

        $did = $response['data']['detail']['did'] ?? null;

        if (empty($did)) {
            $errorMessage = ConfigUtility::getCodeMessage(38100, 'Fresns', fs_theme('lang'));

            throw new ErrorException($errorMessage, 38100);
        }

        // comment_editor_service
        if (fs_config('comment_editor_service')) {
            $pluginUrl = DataHelper::getEditorUrl(fs_config('comment_editor_service'), 'comment', $did, $pid);

            return redirect()->to($pluginUrl);
        }

        return redirect()->to(route('fresns.editor.edit', [
            'type' => 'comment',
            'did' => $did,
        ]));
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
            CacheHelper::put($configs, $cacheKey, $cacheTags, $cacheTime);
        }

        // draft
        $draftDetail = MeInterface::getDraftDetail($type, $did);

        $draft = $draftDetail['data'];

        $archiveResult = MeInterface::archives($type);

        $archives = $archiveResult['data'];

        return view('editor.edit', compact('type', 'configs', 'draft', 'archives'));
    }
}
