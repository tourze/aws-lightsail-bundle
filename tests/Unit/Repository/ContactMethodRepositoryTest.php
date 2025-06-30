<?php

declare(strict_types=1);

namespace AwsLightsailBundle\Tests\Repository;

use AwsLightsailBundle\Entity\ContactMethod;
use AwsLightsailBundle\Repository\ContactMethodRepository;
use Doctrine\Persistence\ManagerRegistry;
use PHPUnit\Framework\TestCase;

final class ContactMethodRepositoryTest extends TestCase
{
    public function testRepositoryIsCorrectType(): void
    {
        $registry = $this->createMock(ManagerRegistry::class);
        $repository = new ContactMethodRepository($registry);
        
        $this->assertInstanceOf(ContactMethodRepository::class, $repository);
    }
}