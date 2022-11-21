<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jarvis Tang
 * Released under the Apache-2.0 License.
 */

namespace Plugins\FresnsEngine\Http\Controllers;

use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Arr;
use Plugins\FresnsEngine\Http\Services\ErrorCodeService;
use Plugins\FresnsEngine\Sdk\Factory;

class MessagesController extends Controller
{
    /**
     * @return View
     */
    public function index(): View
    {
        return view('messages.index');
    }

    /**
     * @param  int  $dialogId
     * @return View
     */
    public function dialog(int $dialogId): View
    {
        return view('messages.dialog');
    }

    /**
     * @param  int|null  $type
     * @return Application|\Illuminate\Contracts\View\Factory|View|RedirectResponse
     *
     * @throws GuzzleException
     */
    public function notify(?int $type = null)
    {
        try {
            $result = Factory::message()->notify->lists($type);
            if (Arr::get($result, 'code') === 0) {
                $notifies = paginator(Arr::get($result, 'data'));

                return view('messages.notify', compact('notifies', 'type'));
            }

            if (Arr::get($result, 'message')) {
                return back()->with(['failure' => $result['message'], 'code' => $result['code']]);
            }
        } catch (\Exception $exception) {
            return back()->withErrors($exception->getMessage());
        }
    }

    /**
     * @param  int  $type
     * @return RedirectResponse
     *
     * @throws GuzzleException
     */
    public function read(int $type): RedirectResponse
    {
        try {
            $result = Factory::message()->notify->read($type);

            if (Arr::get($result, 'code') === 0) {
                return back()->with('success', fs_lang('success'));
            }
            if (Arr::get($result, 'message')) {
                return back()->with(['failure' => $result['message'], 'code' => $result['code']]);
            }
        } catch (\Exception $exception) {
            return back()->withErrors($exception->getMessage());
        }
    }
}
