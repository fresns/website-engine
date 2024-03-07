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
use Illuminate\Support\Facades\Response;

class AccountAuthorize
{
    public function handle(Request $request, Closure $next)
    {
        try {
            if (fs_account()->check()) {
                return $next($request);
            } else {
                $cookiePrefix = ConfigHelper::fresnsConfigByItemKey('website_cookie_prefix') ?? 'fresns_';
                $langTag = "{$cookiePrefix}lang_tag";

                $accountLoginTip = ConfigUtility::getCodeMessage(31501, 'Fresns', \request()->cookie($langTag));

                return $this->shouldLoginRender($accountLoginTip);
            }
        } catch (\Exception $exception) {
            return $this->shouldLoginRender($exception->getMessage());
        }
    }

    public function shouldLoginRender(string $message, int $code = 401)
    {
        if (request()->ajax()) {
            return Response::json(compact('message', 'code'), $code);
        } else {
            $redirectURL = url()->current();

            return redirect(fs_route(route('fresns.login', ['redirectURL' => $redirectURL])))->withErrors($message);
        }
    }
}
