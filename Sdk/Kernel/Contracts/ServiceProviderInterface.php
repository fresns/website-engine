<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jarvis Tang
 * Released under the Apache-2.0 License.
 */

namespace Plugins\FresnsEngine\Sdk\Kernel\Contracts;

use Illuminate\Container\Container;

interface ServiceProviderInterface
{
    /**
     * @param  Container  $container
     */
    public function register(Container $container): void;
}
