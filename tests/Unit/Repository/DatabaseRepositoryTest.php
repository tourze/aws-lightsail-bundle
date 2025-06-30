<?php

declare(strict_types=1);

namespace AwsLightsailBundle\Tests\Repository;

use AwsLightsailBundle\Entity\Database;
use AwsLightsailBundle\Repository\DatabaseRepository;
use Doctrine\Persistence\ManagerRegistry;
use PHPUnit\Framework\TestCase;

final class DatabaseRepositoryTest extends TestCase
{
    public function testRepositoryIsCorrectType(): void
    {
        $registry = $this->createMock(ManagerRegistry::class);
        $repository = new DatabaseRepository($registry);
        
        $this->assertInstanceOf(DatabaseRepository::class, $repository);
    }
}