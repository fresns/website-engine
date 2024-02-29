<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jevan Tang
 * Released under the Apache-2.0 License.
 */

namespace Fresns\WebEngine\Http\Controllers;

use App\Helpers\AppHelper;
use App\Helpers\CacheHelper;
use App\Helpers\ConfigHelper;
use App\Helpers\PrimaryHelper;
use App\Models\App;
use App\Models\Config;
use App\Models\File as FileModel;
use App\Models\FileUsage;
use App\Models\SessionKey;
use App\Utilities\ConfigUtility;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class ThemeFunctionController extends Controller
{
    protected function getThemeConfig(string $fskey)
    {
        if (! $fskey) {
            abort(404, __('FsLang::tips.theme_error'));
        }

        $themeJsonFile = config('themes.paths.themes').'/'.$fskey.'/theme.json';

        if (! file_exists($themeJsonFile)) {
            abort(403, __('FsLang::tips.theme_json_file_error'));
        }

        $themeConfig = json_decode(File::get($themeJsonFile), true);

        return $themeConfig;
    }

    public function index(string $fskey)
    {
        $view = $fskey.'.functions';
        if (! view()->exists($view)) {
            abort(404, __('FsLang::tips.theme_functions_file_error'));
        }

        // theme configs
        $themeConfig = $this->getThemeConfig($fskey);
        $functionItems = collect($themeConfig['functionItems'] ?? []);

        $itemKeys = $functionItems->pluck('itemKey');
        $functionLang = $themeConfig['functionLang'] ?? null;

        // params
        $configs = Config::whereIn('item_key', $itemKeys)->get();
        $params = [];
        $fileUrls = [];
        foreach ($functionItems as $item) {
            $itemKey = $item['itemKey'] ?? null;

            if (! $itemKey) {
                continue;
            }

            $itemType = $item['itemType'] ? Str::lower($item['itemType']) : null;

            $defaultValue = match ($itemType) {
                'number' => null,
                'string' => null,
                'boolean' => false,
                'array' => [],
                'object' => [],
                'file' => null,
                'plugin' => null,
                'plugins' => [],
                default => null,
            };

            $params[$itemKey] = $configs->where('item_key', $itemKey)->first()?->item_value ?? $defaultValue;

            if ($itemType == 'file') {
                $fileUrls[$itemKey] = ConfigHelper::fresnsConfigFileUrlByItemKey($itemKey);
            }
        }

        // theme lang
        $lang = [];
        if ($functionLang) {
            $panelLang = Cookie::get('panel_lang');

            $lang = $functionLang["{$panelLang}"] ?? head($functionLang);
        }

        // apps
        $apps = App::whereNot('type', App::TYPE_THEME)->get();

        // languages
        $configKeys = [
            'language_status',
            'language_menus',
            'default_language',
        ];

        $langConfigs = Config::whereIn('item_key', $configKeys)->get();

        $languageStatus = $langConfigs->where('item_key', 'language_status')->first()?->item_value ?? false;
        $languageMenus = $langConfigs->where('item_key', 'language_menus')->first()?->item_value ?? [];
        $defaultLanguage = $langConfigs->where('item_key', 'default_language')->first()?->item_value ?? config('app.locale');

        // views
        $title = $themeConfig['name'] ?? '';
        $versionMd5 = AppHelper::VERSION_MD5_16BIT;

        return view($view, compact('params', 'fileUrls', 'lang', 'apps', 'languageStatus', 'languageMenus', 'defaultLanguage', 'title', 'versionMd5'));
    }

    public function functions(Request $request, string $fskey)
    {
        $themeConfig = $this->getThemeConfig($fskey);
        $functionItems = collect($themeConfig['functionItems'] ?? []);

        $fresnsConfigItems = [];
        foreach ($functionItems as $item) {
            $itemKey = $item['itemKey'] ?? null;
            $itemType = $item['itemType'] ? Str::lower($item['itemType']) : null;

            if (! $itemKey || ! $itemType || ! $request->$itemKey) {
                continue;
            }

            $itemValue = $request->$itemKey;

            if ($itemType == 'plugins') {
                // $itemValue = [
                //     [
                //         'order' => '',
                //         'code' => '',
                //         'fskey' => '',
                //     ]
                // ];

                usort($itemValue, function ($a, $b) {
                    $orderA = $a['order'] === '' ? 9 : (int) $a['order'];
                    $orderB = $b['order'] === '' ? 9 : (int) $b['order'];

                    return $orderA <=> $orderB;
                });
            }

            if ($itemType == 'file' && $request->hasFile($itemKey)) {
                $file = $request->file($itemKey);
                $mime = $file->getMimeType();

                $fileType = FileModel::TYPE_DOCUMENT;

                if (str_starts_with($mime, 'image/')) {
                    $fileType = FileModel::TYPE_IMAGE;
                }

                if (str_starts_with($mime, 'video/')) {
                    $fileType = FileModel::TYPE_VIDEO;
                }

                if (str_starts_with($mime, 'audio/')) {
                    $fileType = FileModel::TYPE_AUDIO;
                }

                $wordBody = [
                    'platformId' => SessionKey::PLATFORM_WEB_RESPONSIVE,
                    'usageType' => FileUsage::TYPE_SYSTEM,
                    'tableName' => 'configs',
                    'tableColumn' => 'item_value',
                    'tableKey' => $itemKey,
                    'type' => $fileType,
                    'file' => $file,
                ];
                $fresnsResp = \FresnsCmdWord::plugin('Fresns')->uploadFile($wordBody);

                if ($fresnsResp->isErrorResponse()) {
                    if (request()->ajax()) {
                        return $fresnsResp->errorResponse();
                    }

                    return back()->with('failure', $fresnsResp->getMessage());
                }

                $itemValue = PrimaryHelper::fresnsPrimaryId('file', $fresnsResp->getData('fid'));
            }

            $isMultilingual = $item['isMultilingual'] ?? false;

            $fresnsConfigItems[] = [
                'item_key' => $itemKey,
                'item_value' => $itemValue,
                'item_type' => $isMultilingual ? 'object' : $itemType,
                'is_multilingual' => $isMultilingual,
                'is_api' => true,
            ];

            CacheHelper::forgetFresnsConfigs($itemKey);
        }

        ConfigUtility::changeFresnsConfigItems($fresnsConfigItems);

        $successTip = __('FsLang::tips.upgradeSuccess');

        if (request()->ajax()) {
            return response()->json([
                'code' => 0,
                'message' => $successTip,
                'data' => [],
            ]);
        }

        return back()->with('success', $successTip);
    }
}
