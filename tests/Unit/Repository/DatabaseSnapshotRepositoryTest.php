<?php

declare(strict_types=1);

namespace AwsLightsailBundle\Tests\Repository;

use AwsLightsailBundle\Entity\DatabaseSnapshot;
use AwsLightsailBundle\Repository\DatabaseSnapshotRepository;
use Doctrine\Persistence\ManagerRegistry;

use PHPUnit\Framework\TestCase;

final class DatabaseSnapshotRepositoryTest extends TestCase
{
    public function testRepositoryIsCorrectType(): void
    {
        $registry = $this->createMock(ManagerRegistry::class);
        $repository = new DatabaseSnapshotRepository($registry);
        
        $this->assertInstanceOf(DatabaseSnapshotRepository::class, $repository);
    }
}