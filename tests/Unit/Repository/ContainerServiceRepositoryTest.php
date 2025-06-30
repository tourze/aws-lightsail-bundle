<?php

declare(strict_types=1);

namespace AwsLightsailBundle\Tests\Repository;

use AwsLightsailBundle\Entity\ContainerService;
use AwsLightsailBundle\Repository\ContainerServiceRepository;
use Doctrine\Persistence\ManagerRegistry;

use PHPUnit\Framework\TestCase;

final class ContainerServiceRepositoryTest extends TestCase
{
    public function testRepositoryIsCorrectType(): void
    {
        $registry = $this->createMock(ManagerRegistry::class);
        $repository = new ContainerServiceRepository($registry);
        
        $this->assertInstanceOf(ContainerServiceRepository::class, $repository);
    }
}