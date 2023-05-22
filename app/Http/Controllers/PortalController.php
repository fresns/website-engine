<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jevan Tang
 * Released under the Apache-2.0 License.
 */

namespace Plugins\FresnsEngine\Http\Controllers;

use Browser;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Response;

class PortalController extends Controller
{
    public function index()
    {
        $content = fs_db_config('portal_4') ?? (
            Browser::isMobile() ? fs_db_config('portal_3') : fs_db_config('portal_2')
        );

        return view('portal.index', compact('content'));
    }

    public function policies()
    {
        return view('portal.policies');
    }

    public function customPage(string $name)
    {
        $checkName = in_array($name, [
            'index',
            'private',
            'policies',
        ]);

        $viewName = "portal.{$name}";

        if ($checkName || ! View::exists($viewName)) {
            return Response::view('error', [
                'code' => 404,
                'message' => 'Page Not Found',
            ], 404);
        }

        return view($viewName);
    }
}
