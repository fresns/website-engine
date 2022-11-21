<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jarvis Tang
 * Released under the Apache-2.0 License.
 */

namespace Plugins\FresnsEngine\Sdk;

use Illuminate\Support\Str;
use Plugins\FresnsEngine\Sdk\Kernel\ServiceContainer;

/**
 * Class Factory.
 *
 * @method static \Plugins\FresnsEngine\Sdk\Information\Application    information()
 * @method static \Plugins\FresnsEngine\Sdk\Editor\Application         editor()
 * @method static \Plugins\FresnsEngine\Sdk\Account\Application        account()
 * @method static \Plugins\FresnsEngine\Sdk\User\Application         user()
 * @method static \Plugins\FresnsEngine\Sdk\Content\Application        content()
 * @method static \Plugins\FresnsEngine\Sdk\Message\Application        message()
 */
class Factory
{
    /**
     * @param $name
     * @return ServiceContainer
     */
    public static function make($name): ServiceContainer
    {
        $namespace = Str::studly($name);
        $application = "\\Plugins\\FresnsEngine\\Sdk\\{$namespace}\\Application";

        return new $application();
    }

    /**
     * @param $name
     * @param $arguments
     * @return ServiceContainer
     */
    public static function __callStatic($name, $arguments): ServiceContainer
    {
        return self::make($name, ...$arguments);
    }
}
