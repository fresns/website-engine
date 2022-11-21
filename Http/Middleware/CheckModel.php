<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jarvis Tang
 * Released under the Apache-2.0 License.
 */

namespace Plugins\FresnsEngine\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Plugins\FresnsEngine\Facades\Account;

class CheckModel
{
    public function handle(Request $request, Closure $next)
    {
        $siteMode = fs_config('site_mode');
        if ($siteMode === 'private' && Account::guest()) {
            return redirect()->route('fresns.private');
        }

        return $next($request);
    }
}
