<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jarvis Tang
 * Released under the Apache-2.0 License.
 */

namespace Plugins\FresnsEngine\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * Class Account.
 *
 * @method static account();
 * @method static bool check();
 * @method static null|array|string get(?string $key = null);
 * @method static bool guest();
 *
 * @see \Plugins\FresnsEngine\Auth\UserGuard
 */
class User extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'fresns.user';
    }
}
