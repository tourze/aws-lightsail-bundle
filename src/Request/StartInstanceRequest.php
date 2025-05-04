<?php

namespace AwsLightsailBundle\Request;

/**
 * 启动Lightsail实例的请求
 * @see https://docs.aws.amazon.com/lightsail/2016-11-28/api-reference/API_StartInstance.html
 */
class StartInstanceRequest extends LightsailRequest
{
    /**
     * @param string $instanceName 实例名称
     */
    public function __construct(
        private readonly string $instanceName
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
                'instanceName' => $this->instanceName
            ],
            'headers' => [
                'X-Amz-Target' => 'Lightsail_20161128.StartInstance'
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
