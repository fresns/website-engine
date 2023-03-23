<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jevan Tang
 * Released under the Apache-2.0 License.
 */

namespace Plugins\FresnsEngine\Http\Controllers;

use App\Models\FileUsage;
use Illuminate\Http\Request;
use Plugins\FresnsEngine\Exceptions\ErrorException;
use Plugins\FresnsEngine\Helpers\ApiHelper;
use Plugins\FresnsEngine\Helpers\DataHelper;
use Plugins\FresnsEngine\Helpers\QueryHelper;

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

        $result = ApiHelper::make()->get("/api/v2/editor/{$draftType}/drafts", [
            'query' => $query,
        ]);

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
        $editorPlugin = match ($type) {
            'posts' => fs_api_config('post_editor_service'),
            'comments' => fs_api_config('comment_editor_service'),
            'post' => fs_api_config('post_editor_service'),
            'comment' => fs_api_config('comment_editor_service'),
            default => null,
        };

        if ($editorPlugin) {
            return redirect()->to($editorPlugin."/{$type}");
        }

        $type = match ($type) {
            'posts' => 'post',
            'comments' => 'comment',
            'post' => 'post',
            'comment' => 'comment',
            default => 'post',
        };

        $client = ApiHelper::make();

        $results = $client->unwrapRequests([
            'config' => $client->getAsync("/api/v2/editor/{$type}/config"),
            'drafts' => $client->getAsync("/api/v2/editor/{$type}/drafts"),
        ]);

        $config = $results['config']['data'];
        $drafts = $results['drafts']['data']['list'];

        if (empty($drafts)) {
            $response = ApiHelper::make()->post("/api/v2/editor/{$type}/create", [
                'json' => [
                    'createType' => 2,
                    'postGid' => $request->postGid,
                    'commentPid' => $request->commentPid,
                    'commentCid' => $request->commentCid,
                ],
            ]);

            if (data_get($response, 'code') !== 0) {
                throw new ErrorException($response['message'], $response['code']);
            }

            return redirect()->to(fs_route(route('fresns.editor.edit', [$type, $response['data']['detail']['id']])));
        }

        if ($type == 'comment') {
            foreach ($drafts as $draft) {
                if ($draft['pid'] == $request->commentPid && $draft['parentCid'] == $request->commentCid) {
                    return redirect()->to(fs_route(route('fresns.editor.edit', [$type, $draft['id']])));
                }

                if ($draft['pid'] == $request->commentPid && empty($draft['parentCid']) && empty($request->commentCid)) {
                    return redirect()->to(fs_route(route('fresns.editor.edit', [$type, $draft['id']])));
                }
            }

            $response = ApiHelper::make()->post("/api/v2/editor/{$type}/create", [
                'json' => [
                    'createType' => 2,
                    'postGid' => $request->postGid,
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

    // edit
    public function edit(Request $request, string $type, int $draftId)
    {
        $editorPlugin = match ($type) {
            'posts' => fs_api_config('post_editor_service'),
            'comments' => fs_api_config('comment_editor_service'),
            'post' => fs_api_config('post_editor_service'),
            'comment' => fs_api_config('comment_editor_service'),
            default => null,
        };

        if ($editorPlugin) {
            return redirect()->to($editorPlugin."/{$type}/{$draftId}");
        }

        $type = match ($type) {
            'posts' => 'post',
            'comments' => 'comment',
            'post' => 'post',
            'comment' => 'comment',
            default => 'post',
        };

        $draftInfo = self::getDraft($type, $draftId);

        $config = $draftInfo['config'];
        $draft = $draftInfo['draft'];
        $group = data_get($draftInfo, 'group.detail') ?? data_get($draft, 'detail.group.0');

        $plid = null; // post log id
        $clid = null; // comment log id
        if ($type == 'post') {
            $plid = $draftId;
        } else {
            $clid = $draftId;
        }

        $usageType = match ($type) {
            'posts' => FileUsage::TYPE_POST,
            'comments' => FileUsage::TYPE_COMMENT,
            'post' => FileUsage::TYPE_POST,
            'comment' => FileUsage::TYPE_COMMENT,
        };

        $tableName = match ($type) {
            'posts' => 'post_logs',
            'comments' => 'comment_logs',
            'post' => 'post_logs',
            'comment' => 'comment_logs',
        };

        $uploadInfo = DataHelper::getUploadInfo($usageType, $tableName, 'id', $draftId, null);

        return view('editor.edit', compact('type', 'plid', 'clid', 'config', 'draft', 'group', 'uploadInfo'));
    }

    // request: create or edit
    public function store(Request $request, string $type)
    {
        $fsid = $request->input('fsid');

        $editorPlugin = match ($type) {
            'posts' => fs_api_config('post_editor_service'),
            'comments' => fs_api_config('comment_editor_service'),
            'post' => fs_api_config('post_editor_service'),
            'comment' => fs_api_config('comment_editor_service'),
            default => null,
        };

        if ($editorPlugin) {
            return redirect()->to($editorPlugin."/{$type}?fsid={$fsid}");
        }

        $type = match ($type) {
            'posts' => 'post',
            'comments' => 'comment',
            'post' => 'post',
            'comment' => 'comment',
            default => 'post',
        };

        if ($fsid) {
            $response = ApiHelper::make()->post("/api/v2/editor/{$type}/generate/{$fsid}");
        } else {
            $response = ApiHelper::make()->post("/api/v2/editor/{$type}/create", [
                'json' => [
                    'createType' => 2,
                    'editorUnikey' => $request->input('editorUnikey'),
                    'postGid' => $request->input('postGid'),
                    'postTitle' => $request->input('postTitle'),
                    'postIsComment' => $request->input('postIsComment'),
                    'postIsCommentPublic' => $request->input('postIsCommentPublic'),
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

        if (data_get($response, 'code') !== 0) {
            throw new ErrorException($response['message'], $response['code']);
        }

        DataHelper::cacheForgetAccountAndUser();

        return redirect()->to(fs_route(route('fresns.editor.edit', [$type, $response['data']['detail']['id']])));
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
                'postIsComment' => $request->post('postIsComment'),
                'postIsCommentPublic' => $request->post('postIsCommentPublic'),
                'commentPid' => $request->input('commentPid'),
                'commentCid' => $request->input('commentCid'),
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

    // get draft
    public static function getDraft(string $type, ?int $draftId = null)
    {
        $client = ApiHelper::make();

        $params['config'] = $client->getAsync("/api/v2/editor/{$type}/config");

        if ($draftId) {
            $params['draft'] = $client->getAsync("/api/v2/editor/{$type}/{$draftId}");
        }

        if ($gid = request('gid')) {
            $params['group'] = $client->getAsync("/api/v2/group/{$gid}/detail");
        }

        $results = $client->unwrapRequests($params);

        $draftInfo['config'] = data_get($results, 'config.data');
        $draftInfo['draft'] = data_get($results, 'draft.data');
        $draftInfo['group'] = data_get($results, 'group.data');

        return $draftInfo;
    }
}
