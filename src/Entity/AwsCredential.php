<?php

namespace AwsLightsailBundle\Entity;

use AwsLightsailBundle\Repository\AwsCredentialRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Tourze\DoctrineTimestampBundle\Traits\TimestampableAware;

#[ORM\Entity(repositoryClass: AwsCredentialRepository::class)]
#[ORM\Table(name: 'aws_lightsail_credential', options: ['comment' => 'AWS 凭证表'])]
class AwsCredential implements \Stringable
{
    use TimestampableAware;
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::INTEGER, options: ['comment' => '主键ID'])]
    private ?int $id = null;

    #[ORM\Column(type: Types::STRING, length: 255, options: ['comment' => '凭证名称'])]
    private string $name;

    #[ORM\Column(type: Types::STRING, length: 255, options: ['comment' => 'AWS Access Key ID'])]
    private string $accessKeyId;

    #[ORM\Column(type: Types::STRING, length: 255, options: ['comment' => 'AWS Secret Access Key'])]
    private string $secretAccessKey;

    #[ORM\Column(type: Types::BOOLEAN, options: ['comment' => '是否为默认凭证'])]
    private bool $isDefault = false;



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

}
