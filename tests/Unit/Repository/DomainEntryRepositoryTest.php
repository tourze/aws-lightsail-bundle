<?php

declare(strict_types=1);

namespace AwsLightsailBundle\Tests\Repository;

use AwsLightsailBundle\Entity\DomainEntry;
use AwsLightsailBundle\Repository\DomainEntryRepository;
use Doctrine\Persistence\ManagerRegistry;
use PHPUnit\Framework\TestCase;

final class DomainEntryRepositoryTest extends TestCase
{
    public function testRepositoryIsCorrectType(): void
    {
        $registry = $this->createMock(ManagerRegistry::class);
        $repository = new DomainEntryRepository($registry);
        
        $this->assertInstanceOf(DomainEntryRepository::class, $repository);
    }
}