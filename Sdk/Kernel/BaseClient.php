<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jarvis Tang
 * Released under the Apache-2.0 License.
 */

namespace Plugins\FresnsEngine\Sdk\Kernel;

use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Container\Container;
use Illuminate\Http\UploadedFile;
use Plugins\FresnsEngine\Sdk\Kernel\Traits\HasHttpRequests;
use Psr\Http\Message\ResponseInterface;

/**
 * Class BaseClient.
 */
class BaseClient
{
    use HasHttpRequests { request as performRequest; }

    /**
     * @var ServiceContainer
     */
    protected $app;

    /**
     * @var string
     */
    protected $baseUri;

    /**
     * BaseClient constructor.
     *
     * @param  Container  $app
     */
    public function __construct(Container $app)
    {
        $this->app = $app;
    }

    /**
     * @param  string  $url
     * @param  array  $query
     * @return ResponseInterface
     *
     * @throws GuzzleException
     */
    public function httpGet(string $url, array $query = [])
    {
        return $this->request($url, 'GET', ['query' => $query]);
    }

    /**
     * @param  string  $url
     * @param  array  $data
     * @return ResponseInterface
     *
     * @throws GuzzleException
     */
    public function httpPost(string $url, array $data = [])
    {
        return $this->request($url, 'POST', ['form_params' => $data]);
    }

    /**
     * @param  string  $url
     * @param  array  $data
     * @param  array  $query
     * @return ResponseInterface
     *
     * @throws GuzzleException
     */
    public function httpPostJson(string $url, array $data = [], array $query = [])
    {
        $data = collect($data)->reject(function ($val) {
            return $val === null;
        })->toArray();

        return $this->request($url, 'POST', ['query' => $query, 'json' => $data]);
    }

    /**
     * @param  string  $url
     * @param  array  $files
     * @param  array  $form
     * @param  array  $query
     * @return ResponseInterface
     *
     * @throws GuzzleException
     */
    public function httpUpload(string $url, array $files = [], array $form = [], array $query = [])
    {
        $form = collect($form)->filter(function ($param) {
            return $param !== null;
        })->toArray();

        $multipart = [];

        foreach ($files as $name => $file) {
            if ($file instanceof UploadedFile) {
                /** @var UploadedFile $file */
                $multipart[] = [
                    'name' => $name,
                    'filename' => $file->getClientOriginalName(),
                    'contents' => $file->getContent(),
                    'headers' => ['Content-Type' => $file->getClientMimeType()],
                ];
            }
        }

        foreach ($form as $name => $contents) {
            $headers = ['Content-Type' => 'application/x-www-form-urlencoded'];
            $multipart[] = compact('name', 'contents', 'headers');
        }

        return $this->request($url, 'POST', ['query' => $query, 'multipart' => $multipart]);
    }

    /**
     * @param  string  $url
     * @param  string  $method
     * @param  array  $options
     * @param  false  $returnRaw
     * @return Response|array|\Illuminate\Support\Collection|mixed|object|ResponseInterface
     *
     * @throws GuzzleException
     */
    public function request(string $url, string $method = 'GET', array $options = [], $returnRaw = false)
    {
        $url = ltrim($url, '/');

        $response = $this->performRequest($url, $method, $options);

        return $returnRaw ? $response : $this->castResponseToType($response, $this->app->getConfig()->get('response_type'));
    }
}
