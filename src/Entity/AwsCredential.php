<?php

namespace AwsLightsailBundle\Entity;

use AwsLightsailBundle\Repository\AwsCredentialRepository;
use Carbon\Carbon;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Tourze\DoctrineTimestampBundle\Attribute\CreateTimeColumn;
use Tourze\DoctrineTimestampBundle\Attribute\UpdateTimeColumn;

#[ORM\Entity(repositoryClass: AwsCredentialRepository::class)]
#[ORM\Table(name: 'aws_lightsail_credential', options: ['comment' => 'AWS 凭证表'])]
class AwsCredential implements \Stringable
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(type: 'string', length: 255, options: ['comment' => '凭证名称'])]
    private string $name;

    #[ORM\Column(type: 'string', length: 255, options: ['comment' => 'AWS Access Key ID'])]
    private string $accessKeyId;

    #[ORM\Column(type: 'string', length: 255, options: ['comment' => 'AWS Secret Access Key'])]
    private string $secretAccessKey;

    #[ORM\Column(type: 'boolean', options: ['comment' => '是否为默认凭证'])]
    private bool $isDefault = false;

    #[CreateTimeColumn]
    #[ORM\Column(type: Types::DATETIME_MUTABLE, options: ['comment' => '创建时间'])]
    private \DateTimeInterface $createdAt;

    #[UpdateTimeColumn]
    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true, options: ['comment' => '更新时间'])]
    private ?\DateTimeInterface $updatedAt = null;

    public function __construct()
    {
        $this->createdAt = Carbon::now();
    }

    public function __toString(): string
    {
        return sprintf('AwsCredential %s', $this->name);
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

    public function getAccessKeyId(): string
    {
        return $this->accessKeyId;
    }

    public function setAccessKeyId(string $accessKeyId): self
    {
        $this->accessKeyId = $accessKeyId;
        return $this;
    }

    public function getSecretAccessKey(): string
    {
        return $this->secretAccessKey;
    }

    public function setSecretAccessKey(string $secretAccessKey): self
    {
        $this->secretAccessKey = $secretAccessKey;
        return $this;
    }

    public function isDefault(): bool
    {
        return $this->isDefault;
    }

    public function setIsDefault(bool $isDefault): self
    {
        $this->isDefault = $isDefault;
        return $this;
    }

    public function getCreatedAt(): \DateTimeInterface
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?\DateTimeInterface $updatedAt): self
    {
        $this->updatedAt = $updatedAt;
        return $this;
    }
}
