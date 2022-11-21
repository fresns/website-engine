<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jarvis Tang
 * Released under the Apache-2.0 License.
 */

namespace Plugins\FresnsEngine\Sdk\Editor;

use Plugins\FresnsEngine\Sdk\Editor\Draft\Client;
use Plugins\FresnsEngine\Sdk\Kernel\ServiceContainer;

/**
 * Class Application.
 *
 * @property Client $draft
 * @property Upload\Client $upload
 */
class Application extends ServiceContainer
{
    protected $defaultConfig = [
        'http' => [
            'timeout' => 600,
            'connect_timeout' => 600,
            'read_timeout' => 600,
        ],
    ];
    protected $providers = [
        Draft\ServiceProvider::class,
        Upload\ServiceProvider::class,
    ];
}
