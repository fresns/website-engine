<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jarvis Tang
 * Released under the Apache-2.0 License.
 */

namespace Plugins\FresnsEngine\Sdk\Content;

use Plugins\FresnsEngine\Sdk\Content\Post\Client;
use Plugins\FresnsEngine\Sdk\Kernel\ServiceContainer;

/**
 * Class Application.
 *
 * @property Client $post
 * @property Group\Client $group
 * @property Comment\Client $comment
 * @property Hashtag\Client $hashtag
 */
class Application extends ServiceContainer
{
    protected $providers = [
        Post\ServiceProvider::class,
        Group\ServiceProvider::class,
        Comment\ServiceProvider::class,
        Hashtag\ServiceProvider::class,
    ];
}
