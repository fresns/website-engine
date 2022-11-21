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
 * @method static null|array get(null|string $key);
 *
 * @see \Plugins\FresnsEngine\Auth\AccountGuard
 */
class Account extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'fresns.account';
    }
}
