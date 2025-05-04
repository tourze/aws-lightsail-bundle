<?php

namespace AwsLightsailBundle\Entity;

/**
 * AWS Lightsail 密钥对实体
 */
class KeyPair implements \Stringable
{
    public function __construct(
        private readonly string $name,
        private readonly string $arn,
        private readonly string $supportCode,
        private readonly string $createdAt,
        private readonly string $location,
        private readonly string $resourceType,
        private readonly ?string $publicKeyBase64 = null,
        private readonly ?string $privateKeyBase64 = null,
        private readonly ?string $fingerprint = null,
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

    public function getPublicKeyBase64(): ?string
    {
        return $this->publicKeyBase64;
    }

    public function getPrivateKeyBase64(): ?string
    {
        return $this->privateKeyBase64;
    }

    public function getFingerprint(): ?string
    {
        return $this->fingerprint;
    }

    public function getTags(): array
    {
        return $this->tags;
    }

    /**
     * 从API响应数据创建密钥对
     */
    public static function fromApiResponse(array $data): self
    {
        $keyPair = $data['keyPair'] ?? $data;

        return new self(
            $keyPair['name'] ?? '',
            $keyPair['arn'] ?? '',
            $keyPair['supportCode'] ?? '',
            $keyPair['createdAt'] ?? '',
            $keyPair['location'] ?? [],
            $keyPair['resourceType'] ?? '',
            $keyPair['publicKeyBase64'] ?? null,
            $keyPair['privateKeyBase64'] ?? null,
            $keyPair['fingerprint'] ?? null,
            $keyPair['tags'] ?? [],
        );
    }

    /**
     * 返回密钥对的字符串表示
     */
    public function __toString(): string
    {
        return $this->name;
    }
}
