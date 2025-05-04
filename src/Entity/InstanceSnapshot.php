<?php

namespace AwsLightsailBundle\Entity;

/**
 * AWS Lightsail 实例快照实体
 */
class InstanceSnapshot implements \Stringable
{
    public function __construct(
        private readonly string $name,
        private readonly string $arn,
        private readonly string $supportCode,
        private readonly string $createdAt,
        private readonly array $location,
        private readonly string $resourceType,
        private readonly array $fromAttachedDisks,
        private readonly array $fromInstanceName,
        private readonly array $fromInstanceArn,
        private readonly string $fromBlueprintId,
        private readonly string $fromBundleId,
        private readonly string $sizeInGb,
        private readonly string $state,
        private readonly array $progress,
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

    public function getFromAttachedDisks(): array
    {
        return $this->fromAttachedDisks;
    }

    public function getFromInstanceName(): array
    {
        return $this->fromInstanceName;
    }

    public function getFromInstanceArn(): array
    {
        return $this->fromInstanceArn;
    }

    public function getFromBlueprintId(): string
    {
        return $this->fromBlueprintId;
    }

    public function getFromBundleId(): string
    {
        return $this->fromBundleId;
    }

    public function getSizeInGb(): string
    {
        return $this->sizeInGb;
    }

    public function getState(): string
    {
        return $this->state;
    }

    public function getProgress(): array
    {
        return $this->progress;
    }

    public function getTags(): array
    {
        return $this->tags;
    }

    /**
     * 从API响应数据创建实例快照
     */
    public static function fromApiResponse(array $data): self
    {
        $snapshot = $data['instanceSnapshot'] ?? $data;

        return new self(
            $snapshot['name'] ?? '',
            $snapshot['arn'] ?? '',
            $snapshot['supportCode'] ?? '',
            $snapshot['createdAt'] ?? '',
            $snapshot['location'] ?? [],
            $snapshot['resourceType'] ?? '',
            $snapshot['fromAttachedDisks'] ?? [],
            $snapshot['fromInstanceName'] ?? [],
            $snapshot['fromInstanceArn'] ?? [],
            $snapshot['fromBlueprintId'] ?? '',
            $snapshot['fromBundleId'] ?? '',
            $snapshot['sizeInGb'] ?? '',
            $snapshot['state'] ?? '',
            $snapshot['progress'] ?? [],
            $snapshot['tags'] ?? [],
        );
    }

    /**
     * 返回实例快照的字符串表示
     */
    public function __toString(): string
    {
        return $this->name;
    }
}
