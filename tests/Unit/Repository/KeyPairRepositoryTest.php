<?php

declare(strict_types=1);

namespace AwsLightsailBundle\Tests\Repository;

use AwsLightsailBundle\Repository\KeyPairRepository;
use Doctrine\Persistence\ManagerRegistry;
use PHPUnit\Framework\TestCase;

final class KeyPairRepositoryTest extends TestCase
{
    public function testRepositoryIsCorrectType(): void
    {
        $registry = $this->createMock(ManagerRegistry::class);
        $repository = new KeyPairRepository($registry);
        
        $this->assertInstanceOf(KeyPairRepository::class, $repository);
    }
}