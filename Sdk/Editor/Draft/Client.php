<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jarvis Tang
 * Released under the Apache-2.0 License.
 */

namespace Plugins\FresnsEngine\Sdk\Editor\Draft;

use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Http\File;
use Plugins\FresnsEngine\Sdk\Kernel\BaseClient;

class Client extends BaseClient
{
    /**
     * @param  int  $type
     * @param  int  $status
     * @param  int|null  $class
     * @param  int  $pageSize
     * @param  int  $page
     * @return array
     *
     * @throws GuzzleException
     */
    public function lists(
        int $type,
        int $status,
        ?int $class = null,
        int $pageSize = 30,
        int $page = 1
    ): array {
        return $this->httpPostJson('/api/v1/editor/lists', [
            'type' => $type,
            'status' => $status,
            'class' => $class,
            'pageSize' => $pageSize,
            'page' => $page,
        ]);
    }

    /**
     * @param  int  $type
     * @param  int  $logId
     * @return array
     *
     * @throws GuzzleException
     */
    public function detail(
        int $type,
        int $logId
    ): array {
        return $this->httpPostJson('/api/v1/editor/detail', [
            'type' => $type,
            'logId' => $logId,
        ]);
    }

    /**
     * @param  int  $type
     * @param  string  $fsid
     * @param  string  $pid
     * @return array
     *
     * @throws GuzzleException
     */
    public function create(
        int $type,
        ?string $fsid = null,
        ?string $pid = null
    ): array {
        return $this->httpPostJson('/api/v1/editor/create', [
            'type' => $type,
            'fsid' => $fsid,
            'pid' => $pid,
        ]);
    }

    /**
     * @param  int  $logType
     * @param  int  $logId
     * @param  string|null  $types
     * @param  string|null  $gid
     * @param  string|null  $title
     * @param  string|null  $content
     * @param  string|null  $filesJson
     * @param  int|null  $isMarkdown
     * @param  int|null  $isAnonymous
     * @param  int|null  $isPluginEdit
     * @param  string|null  $pluginUnikey
     * @param  string|null  $userListJson
     * @param  string|null  $commentSetJson
     * @param  string|null  $allowJson
     * @param  string|null  $locationJson
     * @param  string|null  $extendsJson
     * @return array
     *
     * @throws GuzzleException
     */
    public function update(
        int $logType,
        int $logId,
        ?string $types = null,
        ?string $gid = null,
        ?string $title = null,
        ?string $content = null,
        ?string $filesJson = null,
        ?int $isMarkdown = null,
        ?int $isAnonymous = null,
        ?int $isPluginEdit = null,
        ?string $pluginUnikey = null,
        ?string $userListJson = null,
        ?string $commentSetJson = null,
        ?string $allowJson = null,
        ?string $locationJson = null,
        ?string $extendsJson = null
    ): array {
        return $this->httpPostJson('/api/v1/editor/update', [
            'logType' => $logType,
            'logId' => $logId,
            'types' => $types,
            'gid' => $gid,
            'title' => $title,
            'content' => $content,
            'isMarkdown' => $isMarkdown,
            'isAnonymous' => $isAnonymous,
            'isPluginEdit' => $isPluginEdit,
            'pluginUnikey' => $pluginUnikey,
            'userListJson' => $userListJson,
            'commentSetJson' => $commentSetJson,
            'allowJson' => $allowJson,
            'locationJson' => $locationJson,
            'filesJson' => $filesJson,
            'extendsJson' =>  $extendsJson,
        ]);
    }

    /**
     * @param  int  $type
     * @param  int  $logId
     * @param  int  $deleteType
     * @param  string|null  $deleteFsid
     * @return array
     *
     * @throws GuzzleException
     */
    public function delete(
        int $type,
        int $logId,
        int $deleteType,
        ?string $deleteFsid = null
    ): array {
        return $this->httpPostJson('/api/v1/editor/delete', [
            'type' => $type,
            'logId' => $logId,
            'deleteType' => $deleteType,
            'deleteFsid' => $deleteFsid,
        ]);
    }

    /**
     * @param  int  $type
     * @param  string|null  $postGid
     * @param  string|null  $postTitle
     * @param  string|null  $commentPid
     * @param  string|null  $commentCid
     * @param  string|null  $content
     * @param  string|null  $isMarkdown
     * @param  string|null  $isAnonymous
     * @param  File|null  $file
     * @param  string|null  $fileInfo
     * @param  string|null  $eid
     * @return array
     *
     * @throws GuzzleException
     */
    public function publish(
        int $type,
        ?string $postGid = null,
        ?string $postTitle = null,
        ?string $commentPid = null,
        ?string $commentCid = null,
        ?string $content = null,
        ?string $isMarkdown = null,
        ?string $isAnonymous = null,
        ?File $file = null,
        ?string $fileInfo = null,
        ?string $eid = null
    ): array {
        return $this->httpPostJson('/api/v1/editor/publish', [
            'type' => $type,
            'postGid' => $postGid,
            'postTitle' => $postTitle,
            'commentPid' => $commentPid,
            'commentCid' => $commentCid,
            'content' => $content,
            'isMarkdown' => $isMarkdown,
            'isAnonymous' => $isAnonymous,
            'file' => $file,
            'fileInfo' => $fileInfo,
            'eid' => $eid,
        ]);
    }

    /**
     * @param  int  $type
     * @param  int  $logId
     * @return array
     *
     * @throws GuzzleException
     */
    public function submit(
        int $type,
        int $logId
    ): array {
        return $this->httpPostJson('/api/v1/editor/submit', [
            'type' => $type,
            'logId' => $logId,
        ]);
    }

    /**
     * @param  int  $type
     * @param  int  $logId
     * @return array
     *
     * @throws GuzzleException
     */
    public function revoke(
        int $type,
        int $logId
    ): array {
        return $this->httpPostJson('/api/v1/editor/revoke', [
            'type' => $type,
            'logId' => $logId,
        ]);
    }
}
