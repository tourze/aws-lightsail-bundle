<?php

namespace AwsLightsailBundle\Request;

/**
 * 删除Lightsail实例的请求
 * @see https://docs.aws.amazon.com/lightsail/2016-11-28/api-reference/API_DeleteInstance.html
 */
class DeleteInstanceRequest extends LightsailRequest
{
    /**
     * @param string $instanceName 实例名称
     * @param bool $forceDeleteAddOns 是否强制删除附加组件
     */
    public function __construct(
        private readonly string $instanceName,
        private readonly bool $forceDeleteAddOns = false
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
                'instanceName' => $this->instanceName,
                'forceDeleteAddOns' => $this->forceDeleteAddOns
            ],
            'headers' => [
                'X-Amz-Target' => 'Lightsail_20161128.DeleteInstance'
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
