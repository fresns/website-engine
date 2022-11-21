<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jarvis Tang
 * Released under the Apache-2.0 License.
 */

namespace Plugins\FresnsEngine\Sdk\Information;

use Plugins\FresnsEngine\Sdk\Kernel\ServiceContainer;

/**
 * Class Application.
 *
 * @property Config\Client $config
 * @property Extension\Client $extension
 * @property VerifyCode\Client $verify_code
 * @property Stickers\Client $stickers
 * @property InputTips\Client $input_tips
 * @property Files\Client $files
 * @property Overview\Client $overview
 */
class Application extends ServiceContainer
{
    protected $providers = [
        Config\ServiceProvider::class,
        Extension\ServiceProvider::class,
        VerifyCode\ServiceProvider::class,
        Stickers\ServiceProvider::class,
        InputTips\ServiceProvider::class,
        Files\ServiceProvider::class,
        Overview\ServiceProvider::class,
    ];
}
