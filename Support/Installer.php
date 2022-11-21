<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jarvis Tang
 * Released under the Apache-2.0 License.
 */

namespace Plugins\FresnsEngine\Support;

use App\Models\Config;

class Installer
{
    protected $fresnsConfigItems = [
        [
            'item_key' => 'fresnsengine_apihost',
            'item_value' => '',
            'item_type' => 'string',
            'item_tag' => 'FresnsEngine',
        ],
        [
            'item_key' => 'fresnsengine_appid',
            'item_value' => '',
            'item_type' => 'string',
            'item_tag' => 'FresnsEngine',
        ],
        [
            'item_key' => 'fresnsengine_appsecret',
            'item_value' => '',
            'item_type' => 'string',
            'item_tag' => 'FresnsEngine',
        ],
    ];

    protected function process(callable $callback)
    {
        foreach ($this->fresnsConfigItems as $item) {
            $callback($item);
        }
    }

    public function install()
    {
        $this->process(function ($item) {
            Config::firstOrCreate([
                'item_key' => $item['item_key'],
            ], $item);
        });
    }

    public function uninstall(bool $clearPluginData = false)
    {
        if (! $clearPluginData) {
            return;
        }

        $this->process(function ($item) {
            Config::query()->where('item_key', $item['item_key'])->forceDelete();
        });
    }
}
