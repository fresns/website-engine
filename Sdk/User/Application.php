<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jarvis Tang
 * Released under the Apache-2.0 License.
 */

namespace Plugins\FresnsEngine\Sdk\User;

use Plugins\FresnsEngine\Sdk\Kernel\ServiceContainer;
use Plugins\FresnsEngine\Sdk\User\Auth\Client;

/**
 * Class Application.
 *
 * @property Client $auth
 * @property Content\Client $content
 */
class Application extends ServiceContainer
{
    protected $providers = [
        Auth\ServiceProvider::class,
        Content\ServiceProvider::class,
    ];
}
