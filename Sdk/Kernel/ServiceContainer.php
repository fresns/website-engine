<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jarvis Tang
 * Released under the Apache-2.0 License.
 */

namespace Plugins\FresnsEngine\Sdk\Kernel;

use App\Helpers\SignHelper;
use App\Utilities\AppUtility;
use Illuminate\Container\Container;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cookie;
use Plugins\FresnsEngine\Sdk\Kernel\Providers\HttpClientServiceProvider;

/**
 * Class ServiceContainer.
 */
class ServiceContainer extends Container
{
    /**
     * @var string
     */
    protected $id;

    /**
     * @var array
     */
    protected $providers = [];

    /**
     * @var array
     */
    protected $defaultConfig = [];

    /**
     * @var array
     */
    protected $accountConfig = [];

    /**
     * Constructor.
     */
    public function __construct(array $config = [], string $id = null)
    {
        $this->accountConfig = $config;
        $this->id = $id;
        $this->registerProviders($this->getProviders());
    }

    /**
     * @return string
     */
    public function getId(): string
    {
        return $this->id ?? $this->id = md5(json_encode($this->accountConfig));
    }

    /**
     * Return all providers.
     *
     * @return array
     */
    public function getProviders(): array
    {
        return array_merge([
            HttpClientServiceProvider::class,
        ], $this->providers);
    }

    /**
     * @return Collection
     */
    public function getConfig(): Collection
    {
        $apiHostUrl = fresnsengine_config('fresnsengine_apihost');

        $base = [
            'http' => [
                'timeout' => 30,
                'base_uri' => rtrim($apiHostUrl, '/'),
                'headers' => $this->getHeaders(),
                'verify' => false,
            ],
            'response_type' => 'array',
        ];

        return collect(array_replace_recursive($base, $this->defaultConfig, $this->accountConfig));
    }

    /**
     * @return array
     */
    public function getHeaders(): array
    {
        $headers = [
            'platform' => 4,
            'version' => '1.0.0',
            'appId' => fresnsengine_config('fresnsengine_appid'),
            'timestamp' => now()->unix(),
            'aid' => Cookie::get('aid') ?? request()->post('account_id'),
            'uid' => Cookie::get('uid') ?? request()->post('user_id'),
            'langTag' => current_lang(),
            'timezone' => Cookie::get('timezone') ?? '',
            'token' => Cookie::get('token') ?? request()->post('request_token'),
            'deviceInfo' => json_encode(AppUtility::getDeviceInfo()),
        ];

        $sign = SignHelper::makeSign($headers, fresnsengine_config('fresnsengine_appsecret') ?? '');

        $headers['sign'] = $sign;

        return collect($headers)->filter()->toArray();
    }

    /**
     * @param  array  $providers
     */
    public function registerProviders(array $providers): void
    {
        foreach ($providers as $provider) {
            (new $provider)->register($this);
        }
    }
}
