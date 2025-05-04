<?php

namespace AwsLightsailBundle\Request;

/**
 * 获取Lightsail实例状态的请求
 * @see https://docs.aws.amazon.com/lightsail/2016-11-28/api-reference/API_GetInstanceState.html
 */
class GetInstanceStateRequest extends LightsailRequest
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
                'X-Amz-Target' => 'Lightsail_20161128.GetInstanceState'
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
