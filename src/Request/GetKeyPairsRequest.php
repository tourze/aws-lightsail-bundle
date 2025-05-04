<?php

namespace AwsLightsailBundle\Request;

/**
 * 获取Lightsail密钥对的请求
 * @see https://docs.aws.amazon.com/lightsail/2016-11-28/api-reference/API_GetKeyPairs.html
 */
class GetKeyPairsRequest extends LightsailRequest
{
    /**
     * @param bool $includeDefaultKeyPair 是否包含默认密钥对
     */
    public function __construct(
        private readonly bool $includeDefaultKeyPair = true
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
                'includeDefaultKeyPair' => $this->includeDefaultKeyPair
            ],
            'headers' => [
                'X-Amz-Target' => 'Lightsail_20161128.GetKeyPairs'
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
