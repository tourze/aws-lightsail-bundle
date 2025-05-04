<?php

namespace AwsLightsailBundle\Request;

use HttpClientBundle\Request\RequestInterface;

class GetDomainRequest implements RequestInterface
{
    private string $accessKey;
    private string $secretKey;
    private string $region;

    public function __construct(
        private readonly string $domainName
    ) {
    }

    public function setCredentials(string $accessKey, string $secretKey, string $region): void
    {
        $this->accessKey = $accessKey;
        $this->secretKey = $secretKey;
        $this->region = $region;
    }

    public function getRequestMethod(): string
    {
        return 'POST';
    }

    public function getRequestPath(): string
    {
        return '/';
    }

    public function getRequestOptions(): array
    {
        return [
            'headers' => [
                'X-Amz-Target' => 'Lightsail_20161128.GetDomain',
                'Content-Type' => 'application/x-amz-json-1.1'
            ],
            'json' => [
                'domainName' => $this->domainName
            ]
        ];
    }

    public function getAccessKey(): string
    {
        return $this->accessKey;
    }

    public function getSecretKey(): string
    {
        return $this->secretKey;
    }

    public function getRegion(): string
    {
        return $this->region;
    }

    public function getService(): string
    {
        return 'lightsail';
    }
} 