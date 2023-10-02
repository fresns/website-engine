<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jevan Tang
 * Released under the Apache-2.0 License.
 */

namespace Fresns\WebEngine\Http\Controllers;

use Browser;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    protected $viewNamespace;

    public function __construct()
    {
        $viewNamespace = Browser::isMobile() ? fs_db_config('engine_view_mobile') : fs_db_config('engine_view_desktop');

        $this->viewNamespace = $viewNamespace;
    }
}
