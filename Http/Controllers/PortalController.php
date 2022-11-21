<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jarvis Tang
 * Released under the Apache-2.0 License.
 */

namespace Plugins\FresnsEngine\Http\Controllers;

use Browser;
use Illuminate\Contracts\View\View;

class PortalController extends Controller
{
    public function index(): View
    {
        $content = fresnsengine_config('portal_4') ?? (
            Browser::isMobile() ? fresnsengine_config('portal_3') : fresnsengine_config('portal_2')
        );

        return view('portal.index', compact('content'));
    }
}
