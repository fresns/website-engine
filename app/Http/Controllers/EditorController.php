<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jevan Tang
 * Released under the Apache-2.0 License.
 */

namespace Fresns\WebEngine\Http\Controllers;

use App\Models\FileUsage;
use Illuminate\Http\Request;
use Fresns\WebEngine\Exceptions\ErrorException;
use Fresns\WebEngine\Helpers\ApiHelper;
use Fresns\WebEngine\Helpers\DataHelper;
use Fresns\WebEngine\Helpers\QueryHelper;
use Fresns\WebEngine\Interfaces\EditorInterface;

class EditorController extends Controller
{
    // drafts
    public function drafts(Request $request, string $type)
    {
        $draftType = match ($type) {
            'posts' => 'post',
            'comments' => 'comment',
            'post' => 'post',
            'comment' => 'comment',
            default => 'post',
        };

        $query = $request->all();

        $result = EditorInterface::drafts($draftType, $query);

        if ($result['code'] != 0) {
            throw new ErrorException($result['message'], $result['code']);
        }

        $drafts = QueryHelper::convertApiDataToPaginate(
            items: $result['data']['list'],
            paginate: $result['data']['paginate'],
        );

        return view('editor.drafts', compact('drafts', 'type'));
    }

    // index
    public function index(Request $request, string $type)
    {
        // Content Type
        $type = match ($type) {
            'posts' => 'post',
            'comments' => 'comment',
            'post' => 'post',
            'comment' => 'comment',
            default => 'post',
        };

        // Editor Plugin Configuration
        $editorPlugin = match ($type) {
            'post' => fs_api_config('post_editor_service'),
            'comment' => fs_api_config('comment_editor_service'),
            default => null,
        };

        // If the editor plugin is configured, jump to the plugin page
        if ($editorPlugin) {
            $pluginUrl = DataHelper::getEditorUrl($editorPlugin, $type);

            // Get the query parameters of the original request
            $queryParams = $request->query();

            // If query parameters exist, append them to $pluginUrl
            if ($queryParams) {
                $pluginUrl .= '&'.http_build_query($queryParams);
            }

            return redirect()->to($pluginUrl);
        }

        // If it is a comment ignore the draft logic
        if ($type == 'comment') {
            $response = ApiHelper::make()->post('/api/v2/editor/comment/create', [
                'json' => [
                    'createType' => 2,
                    'commentPid' => $request->commentPid,
                    'commentCid' => $request->commentCid,
                ],
            ]);

            if (data_get($response, 'code') !== 0) {
                throw new ErrorException($response['message'], $response['code']);
            }

            return redirect()->to(fs_route(route('fresns.editor.edit', [
                'type' => 'comment',
                'draftId' => $response['data']['detail']['id'],
            ])));
        }

        // Editor request data
        $client = ApiHelper::make();
        $results = $client->unwrapRequests([
            'config' => $client->getAsync("/api/v2/editor/{$type}/config"),
            'drafts' => $client->getAsync("/api/v2/editor/{$type}/drafts"),
        ]);

        $config = $results['config']['data'];
        $drafts = $results['drafts']['data']['list'];

        // User without drafts, automatically create drafts and enter the editor
        if (empty($drafts)) {
            $response = ApiHelper::make()->post("/api/v2/editor/{$type}/create", [
                'json' => [
                    'createType' => 2,
                    'postGid' => $request->postGid,
                    'postQuotePid' => $request->postQuotePid,
                    'commentPid' => $request->commentPid,
                    'commentCid' => $request->commentCid,
                ],
            ]);

            if (data_get($response, 'code') !== 0) {
                throw new ErrorException($response['message'], $response['code']);
            }

            return redirect()->to(fs_route(route('fresns.editor.edit', [$type, $response['data']['detail']['id']])));
        }

        $uploadInfo = DataHelper::getUploadInfo();

        return view('editor.index', compact('type', 'config', 'drafts', 'uploadInfo'));
    }

    // request: create or edit
    public function store(Request $request, string $type)
    {
        // Content Type
        $type = match ($type) {
            'posts' => 'post',
            'comments' => 'comment',
            'post' => 'post',
            'comment' => 'comment',
            default => 'post',
        };

        // Edit the pid or cid of the content
        $fsid = $request->input('fsid');

        // Editor Plugin Configuration
        $editorPlugin = match ($type) {
            'post' => fs_api_config('post_editor_service'),
            'comment' => fs_api_config('comment_editor_service'),
            default => null,
        };

        // If the editor plugin is configured, jump to the plugin page
        if ($editorPlugin) {
            $pluginUrl = DataHelper::getEditorUrl($editorPlugin, $type, null, $fsid);

            return redirect()->to($pluginUrl);
        }

        // Determine whether to edit content, or create a draft
        if ($fsid) {
            $response = ApiHelper::make()->post("/api/v2/editor/{$type}/generate/{$fsid}");
        } else {
            $response = ApiHelper::make()->post("/api/v2/editor/{$type}/create", [
                'json' => [
                    'createType' => 2,
                    'postQuotePid' => $request->input('postQuotePid'),
                    'postGid' => $request->input('postGid'),
                    'postTitle' => $request->input('postTitle'),
                    'postIsCommentDisabled' => $request->input('postIsCommentDisabled'),
                    'postIsCommentPrivate' => $request->input('postIsCommentPrivate'),
                    'commentPid' => $request->input('commentPid'),
                    'commentCid' => $request->input('commentCid'),
                    'content' => $request->input('content'),
                    'isMarkdown' => $request->input('isMarkdown'),
                    'isAnonymous' => $request->input('isAnonymous'),
                    'map' => $request->input('map'),
                    'extends' => $request->input('extends'),
                    'archives' => $request->input('archives'),
                ],
            ]);
        }

        // Process draft and enter the editor
        if (data_get($response, 'code') !== 0) {
            throw new ErrorException($response['message'], $response['code']);
        }

        DataHelper::cacheForgetAccountAndUser();

        return redirect()->to(fs_route(route('fresns.editor.edit', [$type, $response['data']['detail']['id']])));
    }

    // edit
    public function edit(Request $request, string $type, int $draftId)
    {
        // Content Type
        $type = match ($type) {
            'posts' => 'post',
            'comments' => 'comment',
            'post' => 'post',
            'comment' => 'comment',
            default => 'post',
        };

        // Editor Plugin Configuration
        $editorPlugin = match ($type) {
            'post' => fs_api_config('post_editor_service'),
            'comment' => fs_api_config('comment_editor_service'),
            default => null,
        };

        // If the editor plugin is configured, jump to the plugin page
        if ($editorPlugin) {
            $pluginUrl = DataHelper::getEditorUrl($editorPlugin, $type, $draftId);

            return redirect()->to($pluginUrl);
        }

        // Get draft data
        $draftInfo = EditorInterface::getDraft($type, $draftId);

        $config = $draftInfo['config'];
        $draft = $draftInfo['draft'];

        $plid = null; // post log id
        $clid = null; // comment log id
        if ($type == 'post') {
            $plid = $draftId;
        } else {
            $clid = $draftId;
        }

        $usageType = match ($type) {
            'post' => FileUsage::TYPE_POST,
            'comment' => FileUsage::TYPE_COMMENT,
        };

        $tableName = match ($type) {
            'post' => 'post_logs',
            'comment' => 'comment_logs',
        };

        $uploadInfo = DataHelper::getUploadInfo($usageType, $tableName, 'id', $draftId, null);

        return view('editor.edit', compact('type', 'plid', 'clid', 'config', 'draft', 'uploadInfo'));
    }

    // request: publish
    public function publish(Request $request, string $type, int $draftId)
    {
        $type = match ($type) {
            'posts' => 'post',
            'comments' => 'comment',
            'post' => 'post',
            'comment' => 'comment',
            default => 'post',
        };

        $response = ApiHelper::make()->put("/api/v2/editor/{$type}/{$draftId}", [
            'json' => [
                'postGid' => $request->post('postGid'),
                'postTitle' => $request->post('postTitle'),
                'postIsCommentDisabled' => $request->post('postIsCommentDisabled'),
                'postIsCommentPrivate' => $request->post('postIsCommentPrivate'),
                'postQuotePid' => $request->post('postQuotePid'),
                'commentPid' => $request->post('commentPid'),
                'commentCid' => $request->post('commentCid'),
                'content' => $request->post('content'),
                'isMarkdown' => $request->post('isMarkdown'),
                'isAnonymous' => $request->post('isAnonymous'),
                'map' => $request->post('map'),
                'extends' => $request->post('extends'),
                'archives' => $request->post('archives'),
                'deleteMap' => $request->post('deleteMap'),
                'deleteFile' => $request->post('deleteFile'),
                'deleteExtend' => $request->post('deleteExtend'),
                'deleteArchive' => $request->post('deleteArchive'),
            ],
        ]);

        DataHelper::cacheForgetAccountAndUser();

        if ($response['code'] !== 0) {
            throw new ErrorException($response['message'], $response['code']);
        }

        $response = ApiHelper::make()->post("/api/v2/editor/{$type}/{$draftId}");

        if ($response['code'] == 38200) {
            return redirect()->to(fs_route(route('fresns.post.index')))->with('success', $response['message']);
        }

        if ($response['code'] !== 0) {
            throw new ErrorException($response['message'], $response['code']);
        }

        return redirect()->to(fs_route(route('fresns.post.index')))->with('success', $response['message']);
    }
}
