<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jarvis Tang
 * Released under the Apache-2.0 License.
 */

namespace Plugins\FresnsEngine\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class Register
{
    public function handle(Request $request, Closure $next)
    {
        try {
            return $next($request);
        } catch (\Exception $exception) {
            return redirect()->route('fresns.account.login')->withErrors($exception->getMessage());
        }
    }
}
