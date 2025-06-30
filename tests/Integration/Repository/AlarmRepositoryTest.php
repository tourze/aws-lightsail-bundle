<?php

declare(strict_types=1);

namespace AwsLightsailBundle\Tests\Integration\Repository;

use AwsLightsailBundle\Entity\Alarm;
use AwsLightsailBundle\Repository\AlarmRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use PHPUnit\Framework\TestCase;

final class AlarmRepositoryTest extends TestCase
{
    private ManagerRegistry $managerRegistry;
    private EntityManagerInterface $entityManager;
    private AlarmRepository $repository;

    protected function setUp(): void
    {
        $this->managerRegistry = $this->createMock(ManagerRegistry::class);
        $this->entityManager = $this->createMock(EntityManagerInterface::class);
        
        $this->managerRegistry->method('getManagerForClass')
            ->with(Alarm::class)
            ->willReturn($this->entityManager);
            
        $this->repository = new AlarmRepository($this->managerRegistry);
    }

    public function testGetEntityClass_returnsCorrectClass(): void
    {
        $this->assertSame(Alarm::class, $this->repository->getClassName());
    }

    public function testInheritsFromServiceEntityRepository(): void
    {
        $this->assertInstanceOf(\Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository::class, $this->repository);
    }

    public function testRepository_isInitializedCorrectly(): void
    {
        $this->assertInstanceOf(AlarmRepository::class, $this->repository);
    }
}
