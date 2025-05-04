<?php

namespace AwsLightsailBundle\Request;

/**
 * 创建Lightsail实例的请求
 * @see https://docs.aws.amazon.com/lightsail/2016-11-28/api-reference/API_CreateInstances.html
 */
class CreateInstanceRequest extends LightsailRequest
{
    /**
     * @param array $instanceNames 实例名称列表
     * @param string $availabilityZone 可用区
     * @param string $blueprintId 系统镜像ID
     * @param string $bundleId 实例规格
     * @param string $keyPairName SSH密钥名称
     * @param string $ipAddressType IP地址类型
     */
    public function __construct(
        private readonly array $instanceNames,
        private readonly string $availabilityZone,
        private readonly string $blueprintId,
        private readonly string $bundleId,
        private readonly string $keyPairName,
        private readonly string $ipAddressType = 'ipv4'
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
            'blueprintId' => $this->blueprintId,
            'bundleId' => $this->bundleId,
            'keyPairName' => $this->keyPairName,
            'ipAddressType' => $this->ipAddressType,
        ];

        return [
            'json' => $data,
            'headers' => [
                'X-Amz-Target' => 'Lightsail_20161128.CreateInstances'
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
