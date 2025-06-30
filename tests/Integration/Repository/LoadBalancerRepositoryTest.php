<?php

declare(strict_types=1);

namespace AwsLightsailBundle\Tests\Integration\Repository;

use AwsLightsailBundle\Entity\LoadBalancer;
use AwsLightsailBundle\Repository\LoadBalancerRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use PHPUnit\Framework\TestCase;

final class LoadBalancerRepositoryTest extends TestCase
{
    private ManagerRegistry $managerRegistry;
    private EntityManagerInterface $entityManager;
    private LoadBalancerRepository $repository;

    protected function setUp(): void
    {
        $this->managerRegistry = $this->createMock(ManagerRegistry::class);
        $this->entityManager = $this->createMock(EntityManagerInterface::class);
        
        $this->managerRegistry->method('getManagerForClass')
            ->with(LoadBalancer::class)
            ->willReturn($this->entityManager);
            
        $this->repository = new LoadBalancerRepository($this->managerRegistry);
    }

    public function testGetEntityClass_returnsCorrectClass(): void
    {
        $this->assertSame(LoadBalancer::class, $this->repository->getClassName());
    }

    public function testInheritsFromServiceEntityRepository(): void
    {
        $this->assertInstanceOf(\Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository::class, $this->repository);
    }

    public function testRepository_isInitializedCorrectly(): void
    {
        $this->assertInstanceOf(LoadBalancerRepository::class, $this->repository);
    }
}
