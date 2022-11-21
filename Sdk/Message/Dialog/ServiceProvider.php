<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jarvis Tang
 * Released under the Apache-2.0 License.
 */

namespace Plugins\FresnsEngine\Sdk\Message\Dialog;

use Illuminate\Container\Container;
use Plugins\FresnsEngine\Sdk\Kernel\Contracts\ServiceProviderInterface;

class ServiceProvider implements ServiceProviderInterface
{
    public function register(Container $container): void
    {
        $container->singleton('dialog', function (Container $container) {
            return new Client($container);
        });
    }
}
