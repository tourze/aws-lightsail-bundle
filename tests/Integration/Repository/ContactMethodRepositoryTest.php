<?php

declare(strict_types=1);

namespace AwsLightsailBundle\Tests\Integration\Repository;

use AwsLightsailBundle\Entity\ContactMethod;
use AwsLightsailBundle\Repository\ContactMethodRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use PHPUnit\Framework\TestCase;

final class ContactMethodRepositoryTest extends TestCase
{
    private ManagerRegistry $managerRegistry;
    private EntityManagerInterface $entityManager;
    private ContactMethodRepository $repository;

    protected function setUp(): void
    {
        $this->managerRegistry = $this->createMock(ManagerRegistry::class);
        $this->entityManager = $this->createMock(EntityManagerInterface::class);
        
        $this->managerRegistry->method('getManagerForClass')
            ->with(ContactMethod::class)
            ->willReturn($this->entityManager);
            
        $this->repository = new ContactMethodRepository($this->managerRegistry);
    }

    public function testGetEntityClass_returnsCorrectClass(): void
    {
        $this->assertSame(ContactMethod::class, $this->repository->getClassName());
    }

    public function testInheritsFromServiceEntityRepository(): void
    {
        $this->assertInstanceOf(\Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository::class, $this->repository);
    }

    public function testRepository_isInitializedCorrectly(): void
    {
        $this->assertInstanceOf(ContactMethodRepository::class, $this->repository);
    }
}
