<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jarvis Tang
 * Released under the Apache-2.0 License.
 */

namespace Plugins\FresnsEngine\Http\Middleware;

use App\Utilities\ConfigUtility;
use Closure;
use Illuminate\Http\Request;
use Plugins\FresnsEngine\Facades\User;

class UserAuthorize
{
    public function handle(Request $request, Closure $next)
    {
        try {
            if (User::check()) {
                return $next($request);
            } else {
                $langTag = current_lang() ?? '';
                $userLoginTip = ConfigUtility::getCodeMessage(31601, 'Fresns', $langTag);

                return redirect()->route('fresns.account')->withErrors($userLoginTip); //FsLang
            }
        } catch (\Exception $exception) {
            return redirect()->route('fresns.account.login')->withErrors($exception->getMessage());
        }
    }
}
