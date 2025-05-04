<?php

namespace AwsLightsailBundle\Entity;

/**
 * AWS Lightsail 域名实体
 */
class Domain implements \Stringable
{
    public function __construct(
        private readonly string $name,
        private readonly string $arn,
        private readonly string $supportCode,
        private readonly string $createdAt,
        private readonly array $location,
        private readonly string $resourceType,
        private readonly array $domainEntries = [],
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

    public function getDomainEntries(): array
    {
        return $this->domainEntries;
    }

    public function getTags(): array
    {
        return $this->tags;
    }

    /**
     * 从API响应数据创建域名
     */
    public static function fromApiResponse(array $data): self
    {
        $domain = $data['domain'] ?? $data;

        return new self(
            $domain['name'] ?? '',
            $domain['arn'] ?? '',
            $domain['supportCode'] ?? '',
            $domain['createdAt'] ?? '',
            $domain['location'] ?? [],
            $domain['resourceType'] ?? '',
            $domain['domainEntries'] ?? [],
            $domain['tags'] ?? [],
        );
    }

    /**
     * 返回域名的字符串表示
     */
    public function __toString(): string
    {
        return $this->name;
    }
}
