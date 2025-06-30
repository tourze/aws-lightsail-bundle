<?php

declare(strict_types=1);

namespace AwsLightsailBundle\Tests\Repository;

use AwsLightsailBundle\Entity\Certificate;
use AwsLightsailBundle\Repository\CertificateRepository;
use Doctrine\Persistence\ManagerRegistry;
use PHPUnit\Framework\TestCase;

final class CertificateRepositoryTest extends TestCase
{
    public function testRepositoryIsCorrectType(): void
    {
        $registry = $this->createMock(ManagerRegistry::class);
        $repository = new CertificateRepository($registry);
        
        $this->assertInstanceOf(CertificateRepository::class, $repository);
    }
}