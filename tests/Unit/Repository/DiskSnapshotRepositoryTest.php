<?php

declare(strict_types=1);

namespace AwsLightsailBundle\Tests\Repository;

use AwsLightsailBundle\Entity\DiskSnapshot;
use AwsLightsailBundle\Repository\DiskSnapshotRepository;
use Doctrine\Persistence\ManagerRegistry;

use PHPUnit\Framework\TestCase;

final class DiskSnapshotRepositoryTest extends TestCase
{
    public function testRepositoryIsCorrectType(): void
    {
        $registry = $this->createMock(ManagerRegistry::class);
        $repository = new DiskSnapshotRepository($registry);
        
        $this->assertInstanceOf(DiskSnapshotRepository::class, $repository);
    }
}