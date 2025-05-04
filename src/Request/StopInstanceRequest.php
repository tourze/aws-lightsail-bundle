<?php

namespace AwsLightsailBundle\Request;

/**
 * 停止Lightsail实例的请求
 * @see https://docs.aws.amazon.com/lightsail/2016-11-28/api-reference/API_StopInstance.html
 */
class StopInstanceRequest extends LightsailRequest
{
    /**
     * @param string $instanceName 实例名称
     * @param bool $force 是否强制停止
     */
    public function __construct(
        private readonly string $instanceName,
        private readonly bool $force = false
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
                'force' => $this->force
            ],
            'headers' => [
                'X-Amz-Target' => 'Lightsail_20161128.StopInstance'
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
