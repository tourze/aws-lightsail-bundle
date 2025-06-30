<?php

declare(strict_types=1);

namespace AwsLightsailBundle\Tests\Repository;

use AwsLightsailBundle\Entity\AwsCredential;
use AwsLightsailBundle\Repository\AwsCredentialRepository;
use Doctrine\Persistence\ManagerRegistry;
use PHPUnit\Framework\TestCase;

final class AwsCredentialRepositoryTest extends TestCase
{
    public function testRepositoryIsCorrectType(): void
    {
        $registry = $this->createMock(ManagerRegistry::class);
        $repository = new AwsCredentialRepository($registry);
        
        $this->assertInstanceOf(AwsCredentialRepository::class, $repository);
    }
}