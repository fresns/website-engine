<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jevan Tang
 * Released under the Apache-2.0 License.
 */

namespace Fresns\WebsiteEngine\Http\Middleware;

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
                $userLoginTip = ConfigUtility::getCodeMessage(31601, 'Fresns', \request()->cookie('fresns_lang_tag'));

                $redirectURL = url()->current();

                return redirect()->to(route('fresns.login', ['redirectURL' => $redirectURL]))->withErrors($userLoginTip); //FsLang
            }
        } catch (\Exception $exception) {
            $redirectURL = url()->current();

            return redirect()->to(route('fresns.login', ['redirectURL' => $redirectURL]))->withErrors($exception->getMessage());
        }
    }
}
