<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jevan Tang
 * Released under the Apache-2.0 License.
 */

namespace Fresns\WebEngine\Http\Controllers;

use App\Fresns\Panel\Http\Controllers\Controller as PanelController;
use App\Helpers\AppHelper;
use App\Helpers\CacheHelper;
use App\Models\Config;
use App\Models\Plugin;
use App\Models\SessionKey;
use App\Utilities\ConfigUtility;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AdminController extends PanelController
{
    public function index(Request $request)
    {
        // config keys
        $configKeys = [
            'webengine_status',
            'webengine_api_type',
            'webengine_api_host',
            'webengine_api_app_id',
            'webengine_api_app_secret',
            'webengine_key_id',
            'webengine_view_desktop',
            'webengine_view_mobile',
            'webengine_interaction_status',
            'webengine_interaction_number',
            'webengine_interaction_percentage',
        ];
        $configs = Config::whereIn('item_key', $configKeys)->get();

        foreach ($configs as $config) {
            $params[$config->item_key] = $config->item_value;
        }

        $keys = SessionKey::where('type', SessionKey::TYPE_CORE)->where('platform_id', 4)->isEnabled()->get();

        $plugins = Plugin::all();

        $plugins = $plugins->filter(function ($plugin) {
            return in_array('website', $plugin->scene ?: []);
        });

        $versionMd5 = AppHelper::VERSION_MD5_16BIT;

        $redirectURL = $request->redirectURL;

        return view('WebEngine::admin', compact('keys', 'params', 'plugins', 'versionMd5', 'redirectURL'));
    }

    public function update(Request $request)
    {
        $apiHost = Str::of($request->get('webengine_api_host'))->trim();
        $apiHost = Str::of($apiHost)->rtrim('/');

        $fresnsConfigItems = [
            [
                'item_key' => 'webengine_status',
                'item_value' => $request->webengine_status,
                'item_type' => 'boolean', // number, string, boolean, array, object, file, plugin, plugins
                'item_tag' => 'webengine',
            ],
            [
                'item_key' => 'webengine_api_type',
                'item_value' => $request->webengine_api_type,
                'item_type' => 'string',
                'item_tag' => 'webengine',
            ],
            [
                'item_key' => 'webengine_api_host',
                'item_value' => $apiHost,
                'item_type' => 'string',
                'item_tag' => 'webengine',
            ],
            [
                'item_key' => 'webengine_api_app_id',
                'item_value' => $request->webengine_api_app_id,
                'item_type' => 'string',
                'item_tag' => 'webengine',
            ],
            [
                'item_key' => 'webengine_api_app_secret',
                'item_value' => $request->webengine_api_app_secret,
                'item_type' => 'string',
                'item_tag' => 'webengine',
            ],
            [
                'item_key' => 'webengine_key_id',
                'item_value' => $request->webengine_key_id,
                'item_type' => 'number',
                'item_tag' => 'webengine',
            ],
            [
                'item_key' => 'webengine_view_desktop',
                'item_value' => $request->webengine_view_desktop,
                'item_type' => 'plugin',
                'item_tag' => 'webengine',
            ],
            [
                'item_key' => 'webengine_view_mobile',
                'item_value' => $request->webengine_view_mobile,
                'item_type' => 'plugin',
                'item_tag' => 'webengine',
            ],
            [
                'item_key' => 'webengine_interaction_status',
                'item_value' => $request->webengine_interaction_status,
                'item_type' => 'boolean',
                'item_tag' => 'webengine',
            ],
            [
                'item_key' => 'webengine_interaction_number',
                'item_value' => $request->webengine_interaction_number,
                'item_type' => 'number',
                'item_tag' => 'webengine',
            ],
            [
                'item_key' => 'webengine_interaction_percentage',
                'item_value' => $request->webengine_interaction_percentage,
                'item_type' => 'number',
                'item_tag' => 'webengine',
            ],
        ];

        ConfigUtility::changeFresnsConfigItems($fresnsConfigItems);

        // config keys
        $configKeys = [
            'webengine_status',
            'webengine_api_type',
            'webengine_api_host',
            'webengine_api_app_id',
            'webengine_api_app_secret',
            'webengine_key_id',
            'webengine_view_desktop',
            'webengine_view_mobile',
            'webengine_interaction_status',
            'webengine_interaction_number',
            'webengine_interaction_percentage',
        ];

        CacheHelper::forgetFresnsConfigs($configKeys);

        return $this->updateSuccess();
    }
}
