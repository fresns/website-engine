<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jarvis Tang
 * Released under the Apache-2.0 License.
 */

namespace Plugins\FresnsEngine\Sdk\Account;

use Plugins\FresnsEngine\Sdk\Account\Auth\Client;
use Plugins\FresnsEngine\Sdk\Kernel\ServiceContainer;

/**
 * Class Application.
 *
 * @property Client $auth
 */
class Application extends ServiceContainer
{
    protected $providers = [
        Auth\ServiceProvider::class,
    ];
}
