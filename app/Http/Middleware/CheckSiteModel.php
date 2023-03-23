<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jevan Tang
 * Released under the Apache-2.0 License.
 */

namespace Plugins\FresnsEngine\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckSiteModel
{
    public function handle(Request $request, Closure $next)
    {
        if (fs_api_config('site_mode') == 'private' && fs_user()->guest()) {
            return \response()->view('portal.private');
        }

        return $next($request);
    }
}
