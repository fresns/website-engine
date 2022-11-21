<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jarvis Tang
 * Released under the Apache-2.0 License.
 */

namespace Plugins\FresnsEngine\Sdk\Message;

use Plugins\FresnsEngine\Sdk\Kernel\ServiceContainer;

/**
 * Class Application.
 *
 * @property Dialog\Client $dialog
 * @property Notify\Client $notify
 */
class Application extends ServiceContainer
{
    protected $providers = [
        Dialog\ServiceProvider::class,
        Notify\ServiceProvider::class,
    ];
}
