<?php

namespace AwsLightsailBundle\Entity;

use AwsLightsailBundle\Enum\ContactMethodStatusEnum;
use AwsLightsailBundle\Enum\ContactMethodTypeEnum;
use Doctrine\ORM\Mapping as ORM;
use Tourze\DoctrineTimestampBundle\Attribute\CreateTimeColumn;
use Tourze\DoctrineTimestampBundle\Attribute\UpdateTimeColumn;

#[ORM\Entity]
#[ORM\Table(name: 'aws_lightsail_contact_method', options: ['comment' => 'AWS Lightsail 联系方式表'])]
class ContactMethod implements \Stringable
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(type: 'string', length: 255, options: ['comment' => '联系方式名称'])]
    private string $name;

    #[ORM\Column(type: 'string', length: 255, options: ['comment' => 'AWS ARN'])]
    private string $arn;

    #[ORM\Column(type: 'string', length: 50, enumType: ContactMethodTypeEnum::class, options: ['comment' => '联系方式类型'])]
    private ContactMethodTypeEnum $type;

    #[ORM\Column(type: 'string', length: 255, options: ['comment' => '联系方式终端点（邮箱或手机）'])]
    private string $contactEndpoint;

    #[ORM\Column(type: 'string', length: 50, enumType: ContactMethodStatusEnum::class, options: ['comment' => '联系方式状态'])]
    private ContactMethodStatusEnum $status;

    #[ORM\Column(type: 'string', length: 50, options: ['comment' => 'AWS 区域'])]
    private string $region;

    #[ORM\Column(type: 'string', length: 255, nullable: true, options: ['comment' => '协议'])]
    private ?string $protocol = null;

    #[ORM\Column(type: 'datetime_immutable', nullable: true, options: ['comment' => '最后验证时间'])]
    private ?\DateTimeImmutable $lastVerifiedTime = null;

    #[CreateTimeColumn]
    #[ORM\Column(type: 'datetime_immutable', options: ['comment' => '创建时间'])]
    private \DateTimeImmutable $createdAt;

    #[ORM\Column(type: 'datetime_immutable', nullable: true, options: ['comment' => '同步时间'])]
    private ?\DateTimeImmutable $syncedAt = null;

    #[ORM\ManyToOne(targetEntity: AwsCredential::class)]
    #[ORM\JoinColumn(nullable: false)]
    private AwsCredential $credential;

    #[UpdateTimeColumn]
    #[ORM\Column(type: 'datetime_immutable', nullable: true, options: ['comment' => '更新时间'])]
    private ?\DateTimeImmutable $updatedAt = null;

    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
        $this->status = ContactMethodStatusEnum::PENDING;
    }

    public function __toString(): string
    {
        return sprintf('ContactMethod %s (%s): %s', $this->name, $this->type->value, $this->contactEndpoint);
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

    public function getType(): ContactMethodTypeEnum
    {
        return $this->type;
    }

    public function setType(ContactMethodTypeEnum $type): self
    {
        $this->type = $type;
        return $this;
    }

    public function getContactEndpoint(): string
    {
        return $this->contactEndpoint;
    }

    public function setContactEndpoint(string $contactEndpoint): self
    {
        $this->contactEndpoint = $contactEndpoint;
        return $this;
    }

    public function getStatus(): ContactMethodStatusEnum
    {
        return $this->status;
    }

    public function setStatus(ContactMethodStatusEnum $status): self
    {
        $this->status = $status;
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

    public function getProtocol(): ?string
    {
        return $this->protocol;
    }

    public function setProtocol(?string $protocol): self
    {
        $this->protocol = $protocol;
        return $this;
    }

    public function getLastVerifiedTime(): ?\DateTimeImmutable
    {
        return $this->lastVerifiedTime;
    }

    public function setLastVerifiedTime(?\DateTimeImmutable $lastVerifiedTime): self
    {
        $this->lastVerifiedTime = $lastVerifiedTime;
        return $this;
    }

    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getSyncedAt(): ?\DateTimeImmutable
    {
        return $this->syncedAt;
    }

    public function setSyncedAt(?\DateTimeImmutable $syncedAt): self
    {
        $this->syncedAt = $syncedAt;
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

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?\DateTimeImmutable $updatedAt): self
    {
        $this->updatedAt = $updatedAt;
        return $this;
    }
} 