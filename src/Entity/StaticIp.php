<?php

namespace AwsLightsailBundle\Entity;

use AwsLightsailBundle\Repository\StaticIpRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Tourze\DoctrineTimestampBundle\Traits\TimestampableAware;

#[ORM\Entity(repositoryClass: StaticIpRepository::class)]
#[ORM\Table(name: 'aws_lightsail_static_ip', options: ['comment' => 'AWS Lightsail 静态 IP 表'])]
class StaticIp implements \Stringable
{
    use TimestampableAware;
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::INTEGER, options: ['comment' => '主键ID'])]
    private ?int $id = null;

    #[ORM\Column(type: Types::STRING, length: 255, options: ['comment' => '静态 IP 名称'])]
    private string $name;

    #[ORM\Column(type: Types::STRING, length: 255, options: ['comment' => 'AWS ARN'])]
    private string $arn;

    #[ORM\Column(type: Types::STRING, length: 20, options: ['comment' => 'IP 地址'])]
    private string $ipAddress;

    #[ORM\Column(type: Types::STRING, length: 255, nullable: true, options: ['comment' => '挂载到的实例'])]
    private ?string $attachedTo = null;

    #[ORM\Column(type: Types::BOOLEAN, options: ['comment' => '是否已挂载'])]
    private bool $isAttached = false;

    #[ORM\Column(type: Types::STRING, length: 50, options: ['comment' => 'AWS 区域'])]
    private string $region;


    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, nullable: true, options: ['comment' => '同步时间'])]
    private ?\DateTimeImmutable $syncTime = null;

    #[ORM\ManyToOne(targetEntity: AwsCredential::class)]
    #[ORM\JoinColumn(nullable: false)]
    private AwsCredential $credential;


    public function __toString(): string
    {
        return sprintf('StaticIp %s (%s)', $this->name, $this->ipAddress);
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

    public function getIpAddress(): string
    {
        return $this->ipAddress;
    }

    public function setIpAddress(string $ipAddress): self
    {
        $this->ipAddress = $ipAddress;
        return $this;
    }

    public function getAttachedTo(): ?string
    {
        return $this->attachedTo;
    }

    public function setAttachedTo(?string $attachedTo): self
    {
        $this->attachedTo = $attachedTo;
        return $this;
    }

    public function isAttached(): bool
    {
        return $this->isAttached;
    }

    public function setIsAttached(bool $isAttached): self
    {
        $this->isAttached = $isAttached;
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
    }}
