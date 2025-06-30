<?php

declare(strict_types=1);

namespace AwsLightsailBundle\Tests\Repository;

use AwsLightsailBundle\Entity\StaticIp;
use AwsLightsailBundle\Repository\StaticIpRepository;
use Doctrine\Persistence\ManagerRegistry;
use PHPUnit\Framework\TestCase;

final class StaticIpRepositoryTest extends TestCase
{
    public function testRepositoryIsCorrectType(): void
    {
        $registry = $this->createMock(ManagerRegistry::class);
        $repository = new StaticIpRepository($registry);
        
        $this->assertInstanceOf(StaticIpRepository::class, $repository);
    }
}