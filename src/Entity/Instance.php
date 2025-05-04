<?php

namespace AwsLightsailBundle\Entity;

/**
 * AWS Lightsail 实例实体
 */
class Instance implements \Stringable
{
    public function __construct(
        private readonly string $name,
        private readonly string $arn,
        private readonly string $supportCode,
        private readonly string $createdAt,
        private readonly string $location,
        private readonly string $resourceType,
        private readonly string $blueprintId,
        private readonly string $blueprintName,
        private readonly string $bundleId,
        private readonly bool $isStaticIp,
        private readonly string $privateIpAddress,
        private readonly string $publicIpAddress,
        private readonly string $ipv6Address,
        private readonly array $hardware,
        private readonly array $networking,
        private readonly array $state,
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

    public function getLocation(): string
    {
        return $this->location;
    }

    public function getResourceType(): string
    {
        return $this->resourceType;
    }

    public function getBlueprintId(): string
    {
        return $this->blueprintId;
    }

    public function getBlueprintName(): string
    {
        return $this->blueprintName;
    }

    public function getBundleId(): string
    {
        return $this->bundleId;
    }

    public function isStaticIp(): bool
    {
        return $this->isStaticIp;
    }

    public function getPrivateIpAddress(): string
    {
        return $this->privateIpAddress;
    }

    public function getPublicIpAddress(): string
    {
        return $this->publicIpAddress;
    }

    public function getIpv6Address(): string
    {
        return $this->ipv6Address;
    }

    public function getHardware(): array
    {
        return $this->hardware;
    }

    public function getNetworking(): array
    {
        return $this->networking;
    }

    public function getState(): array
    {
        return $this->state;
    }

    public function getTags(): array
    {
        return $this->tags;
    }

    /**
     * 从API响应数据创建实例
     */
    public static function fromApiResponse(array $data): self
    {
        $instance = $data['instance'] ?? $data;

        return new self(
            $instance['name'] ?? '',
            $instance['arn'] ?? '',
            $instance['supportCode'] ?? '',
            $instance['createdAt'] ?? '',
            $instance['location'] ?? [],
            $instance['resourceType'] ?? '',
            $instance['blueprintId'] ?? '',
            $instance['blueprintName'] ?? '',
            $instance['bundleId'] ?? '',
            $instance['isStaticIp'] ?? false,
            $instance['privateIpAddress'] ?? '',
            $instance['publicIpAddress'] ?? '',
            $instance['ipv6Address'] ?? '',
            $instance['hardware'] ?? [],
            $instance['networking'] ?? [],
            $instance['state'] ?? [],
            $instance['tags'] ?? [],
        );
    }

    /**
     * 返回实例的字符串表示
     */
    public function __toString(): string
    {
        return $this->name;
    }
}
