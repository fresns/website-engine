<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jevan Tang
 * Released under the Apache-2.0 License.
 */

namespace Fresns\WebsiteEngine\Http\Middleware;

use App\Helpers\ConfigHelper;
use App\Utilities\ConfigUtility;
use Closure;
use Illuminate\Http\Request;

class UserAuthorize
{
    public function handle(Request $request, Closure $next)
    {
        try {
            if (fs_user()->check()) {
                return $next($request);
            } else {
                $cookiePrefix = ConfigHelper::fresnsConfigByItemKey('website_cookie_prefix') ?? 'fresns_';
                $langTag = "{$cookiePrefix}lang_tag";

                $userLoginTip = ConfigUtility::getCodeMessage(31601, 'Fresns', \request()->cookie($langTag));

                $redirectURL = url()->current();

                return redirect()->to(fs_route(route('fresns.login', ['redirectURL' => $redirectURL])))->withErrors($userLoginTip); //FsLang
            }
        } catch (\Exception $exception) {
            $redirectURL = url()->current();

            return redirect()->to(fs_route(route('fresns.login', ['redirectURL' => $redirectURL])))->withErrors($exception->getMessage());
        }
    }
}
