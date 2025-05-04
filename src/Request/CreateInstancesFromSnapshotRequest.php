<?php

namespace AwsLightsailBundle\Request;

/**
 * 从快照创建Lightsail实例的请求
 * @see https://docs.aws.amazon.com/lightsail/2016-11-28/api-reference/API_CreateInstancesFromSnapshot.html
 */
class CreateInstancesFromSnapshotRequest extends LightsailRequest
{
    /**
     * @param array $instanceNames 实例名称列表
     * @param string $availabilityZone 可用区
     * @param string $instanceSnapshotName 快照名称
     * @param string $bundleId 实例规格
     * @param array $tags 标签
     */
    public function __construct(
        private readonly array $instanceNames,
        private readonly string $availabilityZone,
        private readonly string $instanceSnapshotName,
        private readonly string $bundleId,
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
            'instanceNames' => $this->instanceNames,
            'availabilityZone' => $this->availabilityZone,
            'instanceSnapshotName' => $this->instanceSnapshotName,
            'bundleId' => $this->bundleId
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
                'X-Amz-Target' => 'Lightsail_20161128.CreateInstancesFromSnapshot'
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
