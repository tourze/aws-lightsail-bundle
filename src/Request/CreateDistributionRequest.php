<?php

namespace AwsLightsailBundle\Request;

use HttpClientBundle\Request\RequestInterface;

class CreateDistributionRequest implements RequestInterface
{
    private string $accessKey;
    private string $secretKey;
    private string $region;

    public function __construct(
        private readonly string $distributionName,
        private readonly string $bundleId,
        private readonly array $origin,
        private readonly array $defaultCacheBehavior,
        private readonly array $cacheBehaviors,
        private readonly bool $enableDistribution,
        private readonly array $tags = []
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
                'X-Amz-Target' => 'Lightsail_20161128.CreateDistribution',
                'Content-Type' => 'application/x-amz-json-1.1'
            ],
            'json' => $this->getRequestBody()
        ];
    }

    private function getRequestBody(): array
    {
        $params = [
            'distributionName' => $this->distributionName,
            'bundleId' => $this->bundleId,
            'origin' => $this->origin,
            'defaultCacheBehavior' => $this->defaultCacheBehavior,
            'cacheBehaviorSettings' => $this->cacheBehaviors,
            'tags' => $this->tags
        ];

        if ($this->enableDistribution) {
            $params['isEnabled'] = true;
        }

        return $params;
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