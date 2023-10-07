<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jevan Tang
 * Released under the Apache-2.0 License.
 */

namespace Fresns\WebEngine\Http\Controllers;

use App\Fresns\Panel\Http\Controllers\Controller as PanelController;
use App\Helpers\AppHelper;
use App\Models\Config;
use App\Models\SessionKey;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AdminController extends PanelController
{
    public function index()
    {
        // config keys
        $configKeys = [
            'webengine_api_type',
            'webengine_api_host',
            'webengine_api_app_id',
            'webengine_api_app_secret',
            'webengine_key_id',
            'webengine_status',
            'webengine_interaction_status',
            'webengine_interaction_number',
            'webengine_interaction_percentage',
        ];
        $configs = Config::whereIn('item_key', $configKeys)->get();

        foreach ($configs as $config) {
            $params[$config->item_key] = $config->item_value;
        }

        $keyData = SessionKey::where('type', 1)->whereIn('platform_id', [2, 3, 4])->isEnabled()->get();
        $keys = [];
        foreach ($keyData as $key) {
            $item['id'] = $key->id;
            $item['name'] = $key->name;
            $item['appId'] = $key->app_id;

            $keys[] = $item;
        }

        $versionMd5 = AppHelper::VERSION_MD5_16BIT;

        return view('WebEngine::clients.website', compact('keys', 'params', 'versionMd5'));
    }

    public function update(Request $request)
    {
        // config keys
        $configKeys = [
            'webengine_api_type',
            'webengine_api_host',
            'webengine_api_app_id',
            'webengine_api_app_secret',
            'webengine_key_id',
            'webengine_status',
            'webengine_interaction_status',
            'webengine_interaction_number',
            'webengine_interaction_percentage',
        ];

        $configs = Config::whereIn('item_key', $configKeys)->get();

        foreach ($configKeys as $configKey) {
            $config = $configs->where('item_key', $configKey)->first();
            if (! $config) {
                continue;
            }

            if (! $request->has($configKey)) {
                $config->setDefaultValue();
                $config->save();
                continue;
            }

            if ($configKey == 'engine_api_host' && $request->get('engine_api_host')) {
                $url = Str::of($request->get('engine_api_host'))->trim();
                $url = Str::of($url)->rtrim('/');

                $request->$configKey = $url;
            }

            $config->item_value = $request->$configKey;
            $config->save();
        }

        return $this->updateSuccess();
    }
}
