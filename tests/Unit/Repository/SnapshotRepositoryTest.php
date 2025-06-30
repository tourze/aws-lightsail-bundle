<?php

declare(strict_types=1);

namespace AwsLightsailBundle\Tests\Repository;

use AwsLightsailBundle\Entity\Snapshot;
use AwsLightsailBundle\Repository\SnapshotRepository;
use Doctrine\Persistence\ManagerRegistry;
use PHPUnit\Framework\TestCase;

final class SnapshotRepositoryTest extends TestCase
{
    public function testRepositoryIsCorrectType(): void
    {
        $registry = $this->createMock(ManagerRegistry::class);
        $repository = new SnapshotRepository($registry);
        
        $this->assertInstanceOf(SnapshotRepository::class, $repository);
    }
}