<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jarvis Tang
 * Released under the Apache-2.0 License.
 */

namespace Plugins\FresnsEngine\Http\Controllers;

use App\Helpers\PluginHelper;
use App\Models\Config;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class SettingsController extends Controller
{
    /**
     * @return View
     */
    public function general(): View
    {
        $content = Config::query()->whereIn('item_key', [
            'web_stat_code',
            'web_stat_position',
            'web_status',
            'web_number',
            'web_proportion',
            'site_china_mode',
            'site_miit_beian',
            'site_miit_tsm',
            'site_miit_gongan',
        ])->pluck('item_value', 'item_key');

        $version = PluginHelper::fresnsPluginVersionByUnikey('FresnsEngine');

        return view('FresnsEngine::general', compact('content', 'version'));
    }

    /**
     * @param  Request  $request
     * @return RedirectResponse
     */
    public function postGeneral(Request $request): RedirectResponse
    {
        if (! fresnsengine_config('fresnsengine_apihost') || ! fresnsengine_config('fresnsengine_appid') || ! fresnsengine_config('fresnsengine_appsecret')) {
            return back()->with('failure', __('FresnsEngine::tips.generalSettingTip'));
        }

        collect($request->except('_token'))->each(function (?string $value, string $key) {
            $config = Config::query()->firstWhere('item_key', $key) ?: Config::query()->newModelInstance();
            $config->item_key = $key;
            $config->item_value = $value ?: '';
            $config->saveOrFail();
        });

        Cache::clear();

        return back()->with('success', fs_lang('success'));
    }

    public function key(): view
    {
        $content = Config::query()->whereIn('item_key', [
            'fresnsengine_apihost',
            'fresnsengine_appid',
            'fresnsengine_appsecret',
        ])->pluck('item_value', 'item_key');

        $version = PluginHelper::fresnsPluginVersionByUnikey('FresnsEngine');

        return view('FresnsEngine::key', compact('content', 'version'));
    }

    public function postKey(Request $request): RedirectResponse
    {
        collect($request->except('_token'))->each(function (?string $value, string $key) {
            $config = Config::query()->firstWhere('item_key', $key) ?: Config::query()->newModelInstance();
            $config->item_key = $key;
            $config->item_value = $value ?: '';
            $config->item_type = 'string';
            $config->item_tag = 'FresnsEngine';
            $config->saveOrFail();
        });

        Cache::clear();

        return back()->with('success', fs_lang('success'));
    }
}
