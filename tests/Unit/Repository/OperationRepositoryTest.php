<?php

declare(strict_types=1);

namespace AwsLightsailBundle\Tests\Repository;

use AwsLightsailBundle\Entity\Operation;
use AwsLightsailBundle\Repository\OperationRepository;
use Doctrine\Persistence\ManagerRegistry;
use PHPUnit\Framework\TestCase;

final class OperationRepositoryTest extends TestCase
{
    public function testRepositoryIsCorrectType(): void
    {
        $registry = $this->createMock(ManagerRegistry::class);
        $repository = new OperationRepository($registry);
        
        $this->assertInstanceOf(OperationRepository::class, $repository);
    }
}