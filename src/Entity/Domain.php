<?php

declare(strict_types=1);

namespace AwsLightsailBundle\Entity;

use AwsLightsailBundle\Repository\DomainRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Tourze\DoctrineTimestampBundle\Traits\TimestampableAware;

#[ORM\Entity(repositoryClass: DomainRepository::class)]
#[ORM\Table(name: 'aws_lightsail_domain', options: ['comment' => 'AWS Lightsail 域名表'])]
class Domain implements \Stringable
{
    use TimestampableAware;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::INTEGER, options: ['comment' => '主键ID'])]
    private ?int $id = null;

    #[ORM\Column(type: Types::STRING, length: 255, options: ['comment' => '域名'])]
    #[Assert\NotBlank]
    #[Assert\Length(max: 255)]
    private string $name;

    #[ORM\Column(type: Types::STRING, length: 255, options: ['comment' => 'AWS ARN'])]
    #[Assert\NotBlank]
    #[Assert\Length(max: 255)]
    private string $arn;

    #[ORM\Column(type: Types::STRING, length: 50, options: ['comment' => 'AWS 区域'])]
    #[Assert\NotBlank]
    #[Assert\Length(max: 50)]
    private string $region;

    #[ORM\Column(type: Types::BOOLEAN, options: ['comment' => '是否由 Lightsail 管理'])]
    #[Assert\NotNull]
    private bool $isManaged = true;

    /**
     * @var Collection<int, DomainEntry>
     */
    #[ORM\OneToMany(targetEntity: DomainEntry::class, mappedBy: 'domain', cascade: ['persist', 'remove'], orphanRemoval: true)]
    private Collection $entries;

    /**
     * @var array<string, mixed>|null
     */
    #[ORM\Column(type: Types::JSON, nullable: true, options: ['comment' => '标签'])]
    #[Assert\Valid]
    private ?array $tags = null;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, nullable: true, options: ['comment' => '同步时间'])]
    #[Assert\Type(type: '\DateTimeImmutable')]
    private ?\DateTimeImmutable $syncTime = null;

    #[ORM\ManyToOne(targetEntity: AwsCredential::class, cascade: ['persist'])]
    #[ORM\JoinColumn(nullable: false)]
    private AwsCredential $credential;

    public function __construct()
    {
        $this->entries = new ArrayCollection();
    }

    public function __toString(): string
    {
        return \sprintf('Domain %s', $this->name);
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

    public function getRegion(): string
    {
        return $this->region;
    }

    public function setRegion(string $region): void
    {
        $this->region = $region;
    }

    public function isManaged(): bool
    {
        return $this->isManaged;
    }

    public function setIsManaged(bool $isManaged): void
    {
        $this->isManaged = $isManaged;
    }

    /**
     * @return Collection<int, DomainEntry>
     */
    public function getEntries(): Collection
    {
        return $this->entries;
    }

    public function addEntry(DomainEntry $entry): void
    {
        if (!$this->entries->contains($entry)) {
            $this->entries->add($entry);
            $entry->setDomain($this);
        }
    }

    public function removeEntry(DomainEntry $entry): void
    {
        // orphanRemoval=true will automatically delete the entry when removed
        $this->entries->removeElement($entry);
    }

    /**
     * @return array<string, mixed>|null
     */
    public function getTags(): ?array
    {
        return $this->tags;
    }

    /**
     * @param array<string, mixed>|null $tags
     */
    public function setTags(?array $tags): void
    {
        $this->tags = $tags;
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
