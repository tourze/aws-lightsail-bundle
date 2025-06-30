<?php

declare(strict_types=1);

namespace AwsLightsailBundle\Tests\Repository;

use AwsLightsailBundle\Entity\Disk;
use AwsLightsailBundle\Repository\DiskRepository;
use Doctrine\Persistence\ManagerRegistry;

use PHPUnit\Framework\TestCase;

final class DiskRepositoryTest extends TestCase
{
    public function testRepositoryIsCorrectType(): void
    {
        $registry = $this->createMock(ManagerRegistry::class);
        $repository = new DiskRepository($registry);
        
        $this->assertInstanceOf(DiskRepository::class, $repository);
    }
}