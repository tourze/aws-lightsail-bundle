<?php

declare(strict_types=1);

namespace AwsLightsailBundle\Tests\Repository;

use AwsLightsailBundle\Entity\Distribution;
use AwsLightsailBundle\Repository\DistributionRepository;
use Doctrine\Persistence\ManagerRegistry;

use PHPUnit\Framework\TestCase;

final class DistributionRepositoryTest extends TestCase
{
    public function testRepositoryIsCorrectType(): void
    {
        $registry = $this->createMock(ManagerRegistry::class);
        $repository = new DistributionRepository($registry);
        
        $this->assertInstanceOf(DistributionRepository::class, $repository);
    }
}