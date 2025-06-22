<?php

namespace AwsLightsailBundle\Entity;

use AwsLightsailBundle\Repository\KeyPairRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Tourze\DoctrineTimestampBundle\Traits\TimestampableAware;

#[ORM\Entity(repositoryClass: KeyPairRepository::class)]
#[ORM\Table(name: 'aws_lightsail_key_pair', options: ['comment' => 'AWS Lightsail 密钥对表'])]
class KeyPair implements \Stringable
{
    use TimestampableAware;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::INTEGER, options: ['comment' => '主键ID'])]
    private ?int $id = null;

    #[ORM\Column(type: Types::STRING, length: 255, options: ['comment' => '密钥对名称'])]
    private string $name;

    #[ORM\Column(type: Types::STRING, length: 255, options: ['comment' => 'AWS ARN'])]
    private string $arn;

    #[ORM\Column(type: Types::TEXT, nullable: true, options: ['comment' => '指纹'])]
    private ?string $fingerprint = null;

    #[ORM\Column(type: Types::TEXT, nullable: true, options: ['comment' => '公钥'])]
    private ?string $publicKey = null;

    #[ORM\Column(type: Types::TEXT, nullable: true, options: ['comment' => '私钥'])]
    private ?string $privateKey = null;

    #[ORM\Column(type: Types::BOOLEAN, options: ['comment' => '是否加密'])]
    private bool $isEncrypted = false;

    #[ORM\Column(type: Types::STRING, length: 50, options: ['comment' => 'AWS 区域'])]
    private string $region;

    #[ORM\Column(type: Types::STRING, length: 50, nullable: true, options: ['comment' => '资源类型'])]
    private ?string $resourceType = null;

    #[ORM\Column(type: Types::STRING, length: 255, nullable: true, options: ['comment' => '支持代码'])]
    private ?string $supportCode = null;

    #[ORM\Column(type: Types::JSON, nullable: true, options: ['comment' => '标签'])]
    private ?array $tags = null;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, nullable: true, options: ['comment' => 'AWS 创建时间'])]
    private ?\DateTimeImmutable $awsCreatedAt = null;


    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, nullable: true, options: ['comment' => '同步时间'])]
    private ?\DateTimeImmutable $syncTime = null;

    #[ORM\ManyToOne(targetEntity: AwsCredential::class)]
    #[ORM\JoinColumn(nullable: false)]
    private AwsCredential $credential;


    public function __toString(): string
    {
        return sprintf('KeyPair %s (%s)', $this->name, $this->region);
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;
        return $this;
    }

    public function getArn(): string
    {
        return $this->arn;
    }

    public function setArn(string $arn): self
    {
        $this->arn = $arn;
        return $this;
    }

    public function getFingerprint(): ?string
    {
        return $this->fingerprint;
    }

    public function setFingerprint(?string $fingerprint): self
    {
        $this->fingerprint = $fingerprint;
        return $this;
    }

    public function getPublicKey(): ?string
    {
        return $this->publicKey;
    }

    public function setPublicKey(?string $publicKey): self
    {
        $this->publicKey = $publicKey;
        return $this;
    }

    public function getPrivateKey(): ?string
    {
        return $this->privateKey;
    }

    public function setPrivateKey(?string $privateKey): self
    {
        $this->privateKey = $privateKey;
        return $this;
    }

    public function isEncrypted(): bool
    {
        return $this->isEncrypted;
    }

    public function setIsEncrypted(bool $isEncrypted): self
    {
        $this->isEncrypted = $isEncrypted;
        return $this;
    }

    public function getRegion(): string
    {
        return $this->region;
    }

    public function setRegion(string $region): self
    {
        $this->region = $region;
        return $this;
    }

    public function getResourceType(): ?string
    {
        return $this->resourceType;
    }

    public function setResourceType(?string $resourceType): self
    {
        $this->resourceType = $resourceType;
        return $this;
    }

    public function getSupportCode(): ?string
    {
        return $this->supportCode;
    }

    public function setSupportCode(?string $supportCode): self
    {
        $this->supportCode = $supportCode;
        return $this;
    }

    public function getTags(): ?array
    {
        return $this->tags;
    }

    public function setTags(?array $tags): self
    {
        $this->tags = $tags;
        return $this;
    }

    public function getAwsCreatedAt(): ?\DateTimeImmutable
    {
        return $this->awsCreatedAt;
    }

    public function setAwsCreatedAt(?\DateTimeInterface $awsCreatedAt): self
    {
        if ($awsCreatedAt !== null && !$awsCreatedAt instanceof \DateTimeImmutable) {
            $awsCreatedAt = \DateTimeImmutable::createFromInterface($awsCreatedAt);
        }
        $this->awsCreatedAt = $awsCreatedAt;
        return $this;
    }

    public function getSyncTime(): ?\DateTimeImmutable
    {
        return $this->syncTime;
    }

    public function setSyncTime(?\DateTimeInterface $syncTime): self
    {
        if ($syncTime !== null && !$syncTime instanceof \DateTimeImmutable) {
            $syncTime = \DateTimeImmutable::createFromInterface($syncTime);
        }
        $this->syncTime = $syncTime;
        return $this;
    }

    public function getCredential(): AwsCredential
    {
        return $this->credential;
    }

    public function setCredential(AwsCredential $credential): self
    {
        $this->credential = $credential;
        return $this;
    }
}
