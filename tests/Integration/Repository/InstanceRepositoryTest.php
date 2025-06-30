<?php

declare(strict_types=1);

namespace AwsLightsailBundle\Tests\Integration\Repository;

use AwsLightsailBundle\Entity\Instance;
use AwsLightsailBundle\Repository\InstanceRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use PHPUnit\Framework\TestCase;

final class InstanceRepositoryTest extends TestCase
{
    public function testRepository_classExists(): void
    {
        $this->assertTrue(class_exists(InstanceRepository::class));
    }

    public function testRepository_extendsServiceEntityRepository(): void
    {
        $reflection = new \ReflectionClass(InstanceRepository::class);
        $this->assertTrue($reflection->isSubclassOf(\Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository::class));
    }

    public function testRepository_isInstantiable(): void
    {
        $managerRegistry = $this->createMock(ManagerRegistry::class);
        $entityManager = $this->createMock(EntityManagerInterface::class);
        
        $managerRegistry->method('getManagerForClass')
            ->willReturn($entityManager);
            
        $repository = new InstanceRepository($managerRegistry);
        $this->assertInstanceOf(InstanceRepository::class, $repository);
    }
}
