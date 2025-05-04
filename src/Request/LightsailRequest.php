<?php

namespace AwsLightsailBundle\Request;

use HttpClientBundle\Request\ApiRequest;

/**
 * AWS Lightsail 请求基类
 */
abstract class LightsailRequest extends ApiRequest
{
    private string $accessKey = '';
    private string $secretKey = '';
    private string $region = 'us-east-1';

    /**
     * 设置AWS凭证
     */
    public function setCredentials(string $accessKey, string $secretKey, string $region): self
    {
        $this->accessKey = $accessKey;
        $this->secretKey = $secretKey;
        $this->region = $region;

        return $this;
    }

    /**
     * 获取AWS访问密钥
     */
    public function getAccessKey(): string
    {
        return $this->accessKey;
    }

    /**
     * 获取AWS秘密密钥
     */
    public function getSecretKey(): string
    {
        return $this->secretKey;
    }

    /**
     * 获取AWS区域
     */
    public function getRegion(): string
    {
        return $this->region;
    }

    /**
     * API 端点
     */
    public function getRequestPath(): string
    {
        return '/';
    }

    /**
     * 请求方法
     */
    public function getRequestMethod(): ?string
    {
        return 'POST';
    }
}
