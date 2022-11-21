<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jarvis Tang
 * Released under the Apache-2.0 License.
 */

namespace Plugins\FresnsEngine\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;

class ChangeLanguage
{
    /**
     * @param  Request  $request
     * @param  Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        if ($request->lang) {
            Cookie::queue('lang', $request->lang);

            return back()->exceptInput('lang');
        }

        \App::setLocale(Cookie::get('lang', config('app.locale')));

        \View::share('locale', \App::getLocale());

        return $next($request);
    }
}
