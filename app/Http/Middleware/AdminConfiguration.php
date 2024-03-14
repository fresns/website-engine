<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jevan Tang
 * Released under the Apache-2.0 License.
 */

namespace Fresns\WebsiteEngine\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;

class AdminConfiguration
{
    public function handle(Request $request, Closure $next)
    {
        $themeFskey = $request->fskey;

        View::addLocation(base_path("themes/{$themeFskey}"));

        return $next($request);
    }
}
