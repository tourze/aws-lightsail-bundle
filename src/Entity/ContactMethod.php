<?php

declare(strict_types=1);

namespace AwsLightsailBundle\Entity;

use AwsLightsailBundle\Enum\ContactMethodStatusEnum;
use AwsLightsailBundle\Enum\ContactMethodTypeEnum;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Tourze\DoctrineTimestampBundle\Traits\TimestampableAware;

#[ORM\Entity]
#[ORM\Table(name: 'aws_lightsail_contact_method', options: ['comment' => 'AWS Lightsail 联系方式表'])]
class ContactMethod implements \Stringable
{
    use TimestampableAware;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::INTEGER, options: ['comment' => '主键ID'])]
    private ?int $id = null;

    #[ORM\Column(type: Types::STRING, length: 255, options: ['comment' => '联系方式名称'])]
    #[Assert\NotBlank]
    #[Assert\Length(max: 255)]
    private string $name;

    #[ORM\Column(type: Types::STRING, length: 255, options: ['comment' => 'AWS ARN'])]
    #[Assert\NotBlank]
    #[Assert\Length(max: 255)]
    private string $arn;

    #[ORM\Column(type: Types::STRING, length: 50, enumType: ContactMethodTypeEnum::class, options: ['comment' => '联系方式类型'])]
    #[Assert\Choice(callback: [ContactMethodTypeEnum::class, 'cases'])]
    private ContactMethodTypeEnum $type;

    #[ORM\Column(type: Types::STRING, length: 255, options: ['comment' => '联系方式终端点（邮箱或手机）'])]
    #[Assert\NotBlank]
    #[Assert\Length(max: 255)]
    private string $contactEndpoint;

    #[ORM\Column(type: Types::STRING, length: 50, enumType: ContactMethodStatusEnum::class, options: ['comment' => '联系方式状态'])]
    #[Assert\Choice(callback: [ContactMethodStatusEnum::class, 'cases'])]
    private ContactMethodStatusEnum $status = ContactMethodStatusEnum::PENDING;

    #[ORM\Column(type: Types::STRING, length: 50, options: ['comment' => 'AWS 区域'])]
    #[Assert\NotBlank]
    #[Assert\Length(max: 50)]
    private string $region;

    #[ORM\Column(type: Types::STRING, length: 255, nullable: true, options: ['comment' => '协议'])]
    #[Assert\Length(max: 255)]
    private ?string $protocol = null;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, nullable: true, options: ['comment' => '最后验证时间'])]
    #[Assert\DateTime]
    private ?\DateTimeImmutable $lastVerificationTime = null;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, nullable: true, options: ['comment' => '同步时间'])]
    #[Assert\DateTime]
    private ?\DateTimeImmutable $syncTime = null;

    #[ORM\ManyToOne(targetEntity: AwsCredential::class, cascade: ['persist'])]
    #[ORM\JoinColumn(nullable: false)]
    private AwsCredential $credential;

    public function __construct()
    {
        // createTime 会通过 TimestampableAware trait 自动初始化
    }

    public function __toString(): string
    {
        return \sprintf('ContactMethod %s (%s): %s', $this->name, $this->type->value, $this->contactEndpoint);
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getArn(): string
    {
        return $this->arn;
    }

    public function setArn(string $arn): void
    {
        $this->arn = $arn;
    }

    public function getType(): ContactMethodTypeEnum
    {
        return $this->type;
    }

    public function setType(ContactMethodTypeEnum $type): void
    {
        $this->type = $type;
    }

    public function getContactEndpoint(): string
    {
        return $this->contactEndpoint;
    }

    public function setContactEndpoint(string $contactEndpoint): void
    {
        $this->contactEndpoint = $contactEndpoint;
    }

    public function getStatus(): ContactMethodStatusEnum
    {
        return $this->status;
    }

    public function setStatus(ContactMethodStatusEnum $status): void
    {
        $this->status = $status;
    }

    public function getRegion(): string
    {
        return $this->region;
    }

    public function setRegion(string $region): void
    {
        $this->region = $region;
    }

    public function getProtocol(): ?string
    {
        return $this->protocol;
    }

    public function setProtocol(?string $protocol): void
    {
        $this->protocol = $protocol;
    }

    public function getLastVerificationTime(): ?\DateTimeImmutable
    {
        return $this->lastVerificationTime;
    }

    public function setLastVerificationTime(?\DateTimeInterface $lastVerificationTime): void
    {
        if (null !== $lastVerificationTime && !$lastVerificationTime instanceof \DateTimeImmutable) {
            $lastVerificationTime = \DateTimeImmutable::createFromInterface($lastVerificationTime);
        }
        $this->lastVerificationTime = $lastVerificationTime;
    }

    public function getSyncTime(): ?\DateTimeImmutable
    {
        return $this->syncTime;
    }

    public function setSyncTime(?\DateTimeInterface $syncTime): void
    {
        if (null !== $syncTime && !$syncTime instanceof \DateTimeImmutable) {
            $syncTime = \DateTimeImmutable::createFromInterface($syncTime);
        }
        $this->syncTime = $syncTime;
    }

    public function getCredential(): AwsCredential
    {
        return $this->credential;
    }

    public function setCredential(AwsCredential $credential): void
    {
        $this->credential = $credential;
    }
}
