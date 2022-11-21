<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jarvis Tang
 * Released under the Apache-2.0 License.
 */

namespace Plugins\FresnsEngine\Http\Controllers;

use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Validator;
use Plugins\FresnsEngine\Http\Services\ErrorCodeService;
use Plugins\FresnsEngine\Sdk\Factory;

class EditorController extends Controller
{
    /**
     * @return View|RedirectResponse
     *
     * @throws GuzzleException
     */
    public function index()
    {
        // default post
        $groupCategories = Arr::get(Factory::content()->group->lists(1), 'data.list');

        $type = request('type', 1);

        $editorConfig = Arr::get(Factory::editor()->upload->configs($type), 'data');

        $stickers = stickers();

        $drafts = Arr::get(Factory::editor()->draft->lists(1, 1), 'data.list');

        $draft = [];

        if (! $drafts) {
            $result = Factory::editor()->draft->create($type);

            if ($result['code']) {
                return back()->with(['failure' => $result['message'], 'code' => $result['code']]);
            }
            $draft = Arr::get(Factory::editor()->draft->create($type), 'data.detail');

            return redirect(fs_route(route('fresns.editor.edit', $draft['id'])));
        }

        return view('editor.index', compact('stickers', 'editorConfig', 'type', 'draft', 'drafts', 'groupCategories'));
    }

    /**
     * @return View|RedirectResponse
     *
     * @throws GuzzleException
     */
    public function store()
    {
        $type = request('type', 1);
        $fsid = request('fsid');

        try {
            $result = Factory::editor()->draft->create($type, $fsid);

            if (Arr::get($result, 'code') !== 0) {
                return back()->with(['failure' => $result['message'], 'code' => $result['code']]);
            }

            $draft = Arr::get($result, 'data.detail');

            return redirect(fs_route(route('fresns.editor.edit', ['editor' => $draft['id'], 'type' => $type])));
        } catch (\Exception $exception) {
            return back()->withErrors($exception->getMessage());
        }
    }

    /**
     * @param  int  $id
     * @return View|RedirectResponse
     *
     * @throws GuzzleException
     */
    public function edit(int $id)
    {
        $type = request('type', 1);

        $editorConfig = Arr::get(Factory::editor()->upload->configs($type), 'data');

        $stickers = stickers();

        $draft = Arr::get(Factory::editor()->draft->detail($type, $id), 'data.detail');

        return view('editor.editor', compact('stickers', 'editorConfig', 'type', 'draft'));
    }

    /**
     * @param  Request  $request
     * @return RedirectResponse
     *
     * @throws GuzzleException
     */
    public function update(Request $request)
    {
        $validator = Validator::make($request->post(),
            [
                'logType' => 'required|in:1,2|integer',
                'logId' => 'required|integer',
            ],
        );
        if ($validator->fails()) {
            return back()->withErrors(fs_lang('errorEmpty'));
        }

        $types = 'text';
        if ($request->post('filesJsons') && $filesArray = json_decode($request->post('filesJsons'), true)) {
            $types = implode(',', get_filetypes(Arr::pluck($filesArray, 'type')));
        }

        try {
            // Modify the draft once before submitting
            $result = Factory::editor()->draft->update(
                $request->post('logType'),
                $request->post('logId'),
                $types,
                $request->post('gid'),
                $request->has('title') ? $request->post('title') : null,
                $request->has('content') ? $request->post('content') : null,
                $request->has('filesJsons') ? $request->post('filesJsons') : null,
                null,
                (int) $request->post('isAnonymous')
            );

            if (Arr::get($result, 'code') !== 0) {
                return back()->with(['failure' => $result['message'], 'code' => $result['code']]);
            }

            $result = Factory::editor()->draft->submit($request->post('logType'), $request->post('logId'));

            if (Arr::get($result, 'code') === 0) {
                return redirect(fs_route(route('fresns.posts.list')))->with('success', fs_config('publish_post_name').': '.fs_lang('success'));
            }

            if (Arr::get($result, 'message')) {
                return back()->with(['failure' => $result['message'], 'code' => $result['code']]);
            }
        } catch (\Exception $exception) {
            return back()->withErrors($exception->getMessage());
        }
    }

    /**
     * @return View
     */
    public function drafts(): View
    {
        return view('editor.drafts');
    }

    public function publish(Request $request)
    {
        $validator = Validator::make($request->post(),
            [
                'type' => 'required',
                'content' => 'required',
            ],
            [
                'content.required' => fs_lang('pleaseEnter').': '.fs_lang('editorContent'),
            ]
        );

        if ($validator->fails()) {
            return back()->withErrors($validator);
        }

        try {
            $result = Factory::editor()->upload->publish(
                (int) $request->post('type'),
                $request->post('title'),
                $request->post('content'),
                0,
                (int) $request->post('anonymous', 0),
                $request->file('formFile'),
                $request->post('gid'),
                $request->post('commentPid'),
                $request->post('commentCid')
            );

            if (Arr::get($result, 'code') === 0) {
                if ((int) $request->post('type') === 1) {
                    return redirect(fs_route(route('fresns.posts.list')))->with('success', fs_config('publish_post_name').': '.fs_lang('success'));
                } else {
                    return back()->with('success', fs_config('publish_comment_name').': '.fs_lang('success'));
                }
            }

            if (Arr::get($result, 'message')) {
                return back()->with(['failure' => $result['message'], 'code' => $result['code']]);
            }
        } catch (\Exception $exception) {
            return back()->withErrors($exception->getMessage());
        }
    }

    /**
     * @return View
     */
    public function upload(Request $request): View
    {
        $extensions = $request->get('extensions');
        $acceptExtensions = implode(',', array_map(function ($extension) {
            return '.'.$extension;
        }, explode(',', $extensions)));
        $maxSize = $request->get('maxSize');

        return view('editor.upload', compact('extensions', 'maxSize', 'acceptExtensions'));
    }

    /**
     * @param  Request  $request
     * @return JsonResponse
     *
     * @throws GuzzleException
     */
    public function postUpload(Request $request): JsonResponse
    {
        $tableName = $request->post('logType') === '1' ? 'post_logs' : 'comment_logs';
        $tableType = $request->post('logType') === '1' ? 8 : 9;

        try {
            $result = Factory::editor()->upload->upload(
                $request->post('type'),
                request('tableType', $tableType),
                request('tableName', $tableName),
                request('tableColumn', 'files_json'),
                request('tableId'),
                request('tableKey'),
                request('mode', 1),
                $request->file('formFile')
            );

            if (Arr::get($result, 'code') === 0) {
                return Response::json(['message' => 'Upload Successful！', 'data'=> Arr::get($result, 'data.files.0')]);
            }

            return Response::json(['message' => $result['message']], 400);
        } catch (\Exception $exception) {
            return Response::json(['message' => $exception->getMessage()], 400);
        }
    }
}
