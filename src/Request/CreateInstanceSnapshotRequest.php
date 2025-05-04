<?php

namespace AwsLightsailBundle\Request;

/**
 * 创建Lightsail实例快照的请求
 * @see https://docs.aws.amazon.com/lightsail/2016-11-28/api-reference/API_CreateInstanceSnapshot.html
 */
class CreateInstanceSnapshotRequest extends LightsailRequest
{
    /**
     * @param string $instanceName 实例名称
     * @param string $instanceSnapshotName 快照名称
     * @param array<string, string> $tags 标签键值对
     */
    public function __construct(
        private readonly string $instanceName,
        private readonly string $instanceSnapshotName,
        private readonly array $tags = []
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
        $data = [
            'instanceName' => $this->instanceName,
            'instanceSnapshotName' => $this->instanceSnapshotName
        ];

        if (!empty($this->tags)) {
            $tags = [];
            foreach ($this->tags as $key => $value) {
                $tags[] = [
                    'key' => $key,
                    'value' => $value
                ];
            }
            $data['tags'] = $tags;
        }

        return [
            'json' => $data,
            'headers' => [
                'X-Amz-Target' => 'Lightsail_20161128.CreateInstanceSnapshot'
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
