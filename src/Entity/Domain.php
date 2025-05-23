<?php

namespace AwsLightsailBundle\Entity;

use AwsLightsailBundle\Repository\DomainRepository;
use Carbon\Carbon;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Tourze\DoctrineTimestampBundle\Attribute\CreateTimeColumn;
use Tourze\DoctrineTimestampBundle\Attribute\UpdateTimeColumn;

#[ORM\Entity(repositoryClass: DomainRepository::class)]
#[ORM\Table(name: 'aws_lightsail_domain', options: ['comment' => 'AWS Lightsail 域名表'])]
class Domain implements \Stringable
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(type: 'string', length: 255, options: ['comment' => '域名'])]
    private string $name;

    #[ORM\Column(type: 'string', length: 255, options: ['comment' => 'AWS ARN'])]
    private string $arn;

    #[ORM\Column(type: 'string', length: 50, options: ['comment' => 'AWS 区域'])]
    private string $region;

    #[ORM\Column(type: 'boolean', options: ['comment' => '是否由 Lightsail 管理'])]
    private bool $isManaged = true;

    #[ORM\OneToMany(targetEntity: DomainEntry::class, mappedBy: 'domain', cascade: ['persist', 'remove'], orphanRemoval: true)]
    private Collection $entries;

    #[ORM\Column(type: 'json', nullable: true, options: ['comment' => '标签'])]
    private ?array $tags = null;

    #[CreateTimeColumn]
    #[ORM\Column(type: Types::DATETIME_MUTABLE, options: ['comment' => '创建时间'])]
    private \DateTimeInterface $createTime;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true, options: ['comment' => '同步时间'])]
    private ?\DateTimeInterface $syncTime = null;

    #[ORM\ManyToOne(targetEntity: AwsCredential::class)]
    #[ORM\JoinColumn(nullable: false)]
    private AwsCredential $credential;

    #[UpdateTimeColumn]
    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true, options: ['comment' => '更新时间'])]
    private ?\DateTimeInterface $updateTime = null;

    public function __construct()
    {
        $this->createTime = Carbon::now();
        $this->entries = new ArrayCollection();
    }

    public function __toString(): string
    {
        return sprintf('Domain %s', $this->name);
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

    public function getRegion(): string
    {
        return $this->region;
    }

    public function setRegion(string $region): self
    {
        $this->region = $region;
        return $this;
    }

    public function isManaged(): bool
    {
        return $this->isManaged;
    }

    public function setIsManaged(bool $isManaged): self
    {
        $this->isManaged = $isManaged;
        return $this;
    }

    /**
     * @return Collection<int, DomainEntry>
     */
    public function getEntries(): Collection
    {
        return $this->entries;
    }

    public function addEntry(DomainEntry $entry): self
    {
        if (!$this->entries->contains($entry)) {
            $this->entries->add($entry);
            $entry->setDomain($this);
        }

        return $this;
    }

    public function removeEntry(DomainEntry $entry): self
    {
        if ($this->entries->removeElement($entry)) {
            // set the owning side to null (unless already changed)
            if ($entry->getDomain() === $this) {
                $entry->setDomain(null);
            }
        }

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

    public function getCreateTime(): \DateTimeInterface
    {
        return $this->createTime;
    }

    public function getSyncTime(): ?\DateTimeInterface
    {
        return $this->syncTime;
    }

    public function setSyncTime(?\DateTimeInterface $syncTime): self
    {
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

    public function getUpdateTime(): ?\DateTimeInterface
    {
        return $this->updateTime;
    }

    public function setUpdateTime(?\DateTimeInterface $updateTime): self
    {
        $this->updateTime = $updateTime;
        return $this;
    }
}
