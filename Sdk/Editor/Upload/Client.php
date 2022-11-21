<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jarvis Tang
 * Released under the Apache-2.0 License.
 */

namespace Plugins\FresnsEngine\Sdk\Editor\Upload;

use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection;
use Plugins\FresnsEngine\Sdk\Kernel\BaseClient;
use Plugins\FresnsEngine\Sdk\Kernel\Response;
use Psr\Http\Message\ResponseInterface;

class Client extends BaseClient
{
    /**
     * @param  int  $type
     * @param  int  $model
     * @param  int  $scene
     * @return array
     *
     * @throws GuzzleException
     */
    public function token(
        int $type,
        string $name,
        int $expireTime
    ): array {
        return $this->httpPostJson('/api/v1/editor/uploadToken', [
            'type' => $type,
            'name' => $name,
            'expireTime' => $expireTime,
        ]);
    }

    /**
     * @param  int  $type
     * @param  int  $tableType
     * @param  string  $tableName
     * @param  string  $tableColumn
     * @param  string|null  $tableId
     * @param  string|null  $tableKey
     * @param  int  $mode
     * @param  UploadedFile|null  $file
     * @param  string|null  $fileInfo
     * @return array
     *
     * @throws GuzzleException
     */
    public function upload(
        int $type,
        int $tableType,
        string $tableName,
        string $tableColumn,
        ?int $tableId = null,
        ?string $tableKey = null,
        int $mode,
        ?UploadedFile $file = null,
        ?string $fileInfo = null
    ): array {
        return $this->httpUpload('/api/v1/editor/upload',
            [
                'file' => $file,
            ],
            [
                'type' => $type,
                'tableType' => $tableType,
                'tableName' => $tableName,
                'tableColumn' => $tableColumn,
                'tableId' => $tableId,
                'tableKey' => $tableKey,
                'mode' => $mode,
                'fileInfo' => $fileInfo,
            ]);
    }

    /**
     * @param  int  $type
     * @param  string|null  $postTitle
     * @param  string  $content
     * @param  int  $isMarkdown
     * @param  int  $isAnonymous
     * @param  UploadedFile|null  $file
     * @param  string|null  $postGid
     * @param  string|null  $commentPid
     * @param  string|null  $commentCid
     * @param  string|null  $fileInfo
     * @param  string|null  $eid
     * @return Response|array|Collection|object|ResponseInterface
     *
     * @throws GuzzleException
     */
    public function publish(
        int $type,
        ?string $postTitle,
        string $content,
        int $isMarkdown = 0,
        int $isAnonymous = 0,
        ?UploadedFile $file = null,
        ?string $postGid = null,
        ?string $commentPid = null,
        ?string $commentCid = null,
        ?string $fileInfo = null,
        ?string $eid = null
    ) {
        return $this->httpUpload('/api/v1/editor/publish', [
            'file' => $file,
        ], [
            'type' => $type,
            'postGid' => $postGid,
            'postTitle' => $postTitle,
            'commentPid' => $commentPid,
            'commentCid' => $commentCid,
            'content' => $content,
            'isMarkdown' => $isMarkdown,
            'isAnonymous' => $isAnonymous,
            'fileInfo' => $fileInfo,
            'eid' => $eid,
        ]);
    }

    /**
     * @param  int  $type
     * @return array
     *
     * @throws GuzzleException
     */
    public function configs(
        int $type
    ): array {
        return $this->httpPostJson('/api/v1/editor/configs', compact('type'));
    }
}
