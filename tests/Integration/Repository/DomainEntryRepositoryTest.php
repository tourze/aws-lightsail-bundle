<?php

declare(strict_types=1);

namespace AwsLightsailBundle\Tests\Integration\Repository;

use AwsLightsailBundle\Entity\DomainEntry;
use AwsLightsailBundle\Repository\DomainEntryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use PHPUnit\Framework\TestCase;

final class DomainEntryRepositoryTest extends TestCase
{
    private ManagerRegistry $managerRegistry;
    private EntityManagerInterface $entityManager;
    private DomainEntryRepository $repository;

    protected function setUp(): void
    {
        $this->managerRegistry = $this->createMock(ManagerRegistry::class);
        $this->entityManager = $this->createMock(EntityManagerInterface::class);
        
        $this->managerRegistry->method('getManagerForClass')
            ->with(DomainEntry::class)
            ->willReturn($this->entityManager);
            
        $this->repository = new DomainEntryRepository($this->managerRegistry);
    }

    public function testGetEntityClass_returnsCorrectClass(): void
    {
        $this->assertSame(DomainEntry::class, $this->repository->getClassName());
    }

    public function testInheritsFromServiceEntityRepository(): void
    {
        $this->assertInstanceOf(\Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository::class, $this->repository);
    }

    public function testRepository_isInitializedCorrectly(): void
    {
        $this->assertInstanceOf(DomainEntryRepository::class, $this->repository);
    }
}
