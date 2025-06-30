<?php

declare(strict_types=1);

namespace AwsLightsailBundle\Tests\Service;

use AwsLightsailBundle\Entity\AwsCredential;
use AwsLightsailBundle\Repository\InstanceRepository;
use AwsLightsailBundle\Service\InstanceSyncService;
use AwsLightsailBundle\Service\KeyPairSyncService;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

final class InstanceSyncServiceTest extends TestCase
{
    private MockObject&EntityManagerInterface $entityManager;
    private MockObject&InstanceRepository $instanceRepository;
    private MockObject&KeyPairSyncService $keyPairSyncService;
    private MockObject&LoggerInterface $logger;
    private InstanceSyncService $service;

    protected function setUp(): void
    {
        $this->entityManager = $this->createMock(EntityManagerInterface::class);
        $this->instanceRepository = $this->createMock(InstanceRepository::class);
        $this->keyPairSyncService = $this->createMock(KeyPairSyncService::class);
        $this->logger = $this->createMock(LoggerInterface::class);

        $this->service = new InstanceSyncService(
            $this->entityManager,
            $this->instanceRepository,
            $this->keyPairSyncService,
            $this->logger
        );
    }

    public function testServiceIsInstantiated(): void
    {
        $this->assertInstanceOf(InstanceSyncService::class, $this->service);
    }

    public function testBatchSyncInstancesWithEmptyData(): void
    {
        $credential = new AwsCredential();
        $credential->setName('test-credential');

        $this->entityManager
            ->expects($this->once())
            ->method('flush');

        $result = $this->service->batchSyncInstances($credential, []);

        $this->assertArrayHasKey('total', $result);
        $this->assertArrayHasKey('new', $result);
        $this->assertArrayHasKey('updated', $result);
        $this->assertArrayHasKey('errors', $result);
    }
}