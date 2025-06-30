<?php

declare(strict_types=1);

namespace AwsLightsailBundle\Tests\Integration\Repository;

use AwsLightsailBundle\Entity\Disk;
use AwsLightsailBundle\Repository\DiskRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use PHPUnit\Framework\TestCase;

final class DiskRepositoryTest extends TestCase
{
    private ManagerRegistry $managerRegistry;
    private EntityManagerInterface $entityManager;
    private DiskRepository $repository;

    protected function setUp(): void
    {
        $this->managerRegistry = $this->createMock(ManagerRegistry::class);
        $this->entityManager = $this->createMock(EntityManagerInterface::class);
        
        $this->managerRegistry->method('getManagerForClass')
            ->with(Disk::class)
            ->willReturn($this->entityManager);
            
        $this->repository = new DiskRepository($this->managerRegistry);
    }

    public function testGetEntityClass_returnsCorrectClass(): void
    {
        $this->assertSame(Disk::class, $this->repository->getClassName());
    }

    public function testInheritsFromServiceEntityRepository(): void
    {
        $this->assertInstanceOf(\Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository::class, $this->repository);
    }

    public function testRepository_isInitializedCorrectly(): void
    {
        $this->assertInstanceOf(DiskRepository::class, $this->repository);
    }
}
