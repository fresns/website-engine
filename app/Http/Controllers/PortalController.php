<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jevan Tang
 * Released under the Apache-2.0 License.
 */

namespace Fresns\WebEngine\Http\Controllers;

use App\Helpers\ConfigHelper;
use Browser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\View;

class PortalController extends Controller
{
    public function index()
    {
        $portalContent = Browser::isMobile() ? ConfigHelper::fresnsConfigByItemKey('portal_3') : ConfigHelper::fresnsConfigByItemKey('portal_2');

        $content = ConfigHelper::fresnsConfigByItemKey('portal_4') ?? $portalContent;

        return view('portal.index', compact('content'));
    }

    public function about()
    {
        return view('portal.about');
    }

    public function policies()
    {
        return view('portal.policies');
    }

    public function login(Request $request)
    {
        $redirectURL = $request->redirectURL ?? fs_route(route('fresns.home'));

        if (fs_user()->check()) {
            return redirect()->intended($redirectURL);
        }

        return view('portal.login');
    }

    public function private()
    {
        return view('portal.private');
    }

    public function customPage(string $name)
    {
        if ($name == 'index') {
            return redirect(fs_route(route('fresns.portal')));
        }

        if (in_array($name, [
            'about',
            'policies',
            'login',
            'private',
        ])) {
            return redirect(fs_route(route("fresns.{$name}")));
        }

        $viewName = "portal.{$name}";

        if (! View::exists($viewName)) {
            return Response::view('error', [
                'code' => 404,
                'message' => 'Page Not Found',
            ], 404);
        }

        return view($viewName);
    }
}
