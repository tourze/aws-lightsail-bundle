<?php

declare(strict_types=1);

namespace AwsLightsailBundle\Tests\Repository;

use AwsLightsailBundle\Repository\DomainRepository;
use Doctrine\Persistence\ManagerRegistry;
use PHPUnit\Framework\TestCase;

final class DomainRepositoryTest extends TestCase
{
    public function testRepositoryIsCorrectType(): void
    {
        $registry = $this->createMock(ManagerRegistry::class);
        $repository = new DomainRepository($registry);
        
        $this->assertInstanceOf(DomainRepository::class, $repository);
    }
}