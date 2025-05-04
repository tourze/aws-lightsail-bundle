<?php

namespace AwsLightsailBundle\Request;

/**
 * 获取Lightsail实例快照的请求
 * @see https://docs.aws.amazon.com/lightsail/2016-11-28/api-reference/API_GetInstanceSnapshot.html
 */
class GetInstanceSnapshotRequest extends LightsailRequest
{
    /**
     * @param string $instanceSnapshotName 快照名称
     */
    public function __construct(
        private readonly string $instanceSnapshotName
    ) {
    }

    /**
     * API 端点
     */
    public function getRequestPath(): string
    {
        return '/';
    }

    /**
     * 请求参数
     */
    public function getRequestOptions(): ?array
    {
        return [
            'json' => [
                'instanceSnapshotName' => $this->instanceSnapshotName
            ],
            'headers' => [
                'X-Amz-Target' => 'Lightsail_20161128.GetInstanceSnapshot'
            ]
        ];
    }

    /**
     * 请求方法
     */
    public function getRequestMethod(): ?string
    {
        return 'POST';
    }
}
