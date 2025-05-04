<?php

namespace AwsLightsailBundle\Entity;

/**
 * AWS Lightsail 分发实体
 */
class Distribution implements \Stringable
{
    public function __construct(
        private readonly string $name,
        private readonly string $arn,
        private readonly string $supportCode,
        private readonly string $createdAt,
        private readonly array $location,
        private readonly string $resourceType,
        private readonly array $alternativeDomainNames,
        private readonly string $status,
        private readonly bool $isEnabled,
        private readonly array $domainName,
        private readonly array $bundleId,
        private readonly array $certificateName,
        private readonly array $origin,
        private readonly array $originPublicDNS,
        private readonly array $defaultCacheBehavior,
        private readonly array $cacheBehaviors,
        private readonly array $ableToUpdateBundle,
        private readonly array $tags = [],
    ) {
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getArn(): string
    {
        return $this->arn;
    }

    public function getSupportCode(): string
    {
        return $this->supportCode;
    }

    public function getCreatedAt(): string
    {
        return $this->createdAt;
    }

    public function getLocation(): array
    {
        return $this->location;
    }

    public function getResourceType(): string
    {
        return $this->resourceType;
    }

    public function getAlternativeDomainNames(): array
    {
        return $this->alternativeDomainNames;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function isEnabled(): bool
    {
        return $this->isEnabled;
    }

    public function getDomainName(): array
    {
        return $this->domainName;
    }

    public function getBundleId(): array
    {
        return $this->bundleId;
    }

    public function getCertificateName(): array
    {
        return $this->certificateName;
    }

    public function getOrigin(): array
    {
        return $this->origin;
    }

    public function getOriginPublicDNS(): array
    {
        return $this->originPublicDNS;
    }

    public function getDefaultCacheBehavior(): array
    {
        return $this->defaultCacheBehavior;
    }

    public function getCacheBehaviors(): array
    {
        return $this->cacheBehaviors;
    }

    public function getAbleToUpdateBundle(): array
    {
        return $this->ableToUpdateBundle;
    }

    public function getTags(): array
    {
        return $this->tags;
    }

    /**
     * 从API响应数据创建分发
     */
    public static function fromApiResponse(array $data): self
    {
        $distribution = $data['distribution'] ?? $data;

        return new self(
            $distribution['name'] ?? '',
            $distribution['arn'] ?? '',
            $distribution['supportCode'] ?? '',
            $distribution['createdAt'] ?? '',
            $distribution['location'] ?? [],
            $distribution['resourceType'] ?? '',
            $distribution['alternativeDomainNames'] ?? [],
            $distribution['status'] ?? '',
            $distribution['isEnabled'] ?? false,
            $distribution['domainName'] ?? [],
            $distribution['bundleId'] ?? [],
            $distribution['certificateName'] ?? [],
            $distribution['origin'] ?? [],
            $distribution['originPublicDNS'] ?? [],
            $distribution['defaultCacheBehavior'] ?? [],
            $distribution['cacheBehaviors'] ?? [],
            $distribution['ableToUpdateBundle'] ?? [],
            $distribution['tags'] ?? [],
        );
    }

    /**
     * 返回分发的字符串表示
     */
    public function __toString(): string
    {
        return $this->name;
    }
}
