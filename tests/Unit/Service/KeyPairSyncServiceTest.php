<?php

declare(strict_types=1);

namespace AwsLightsailBundle\Tests\Service;

use AwsLightsailBundle\Entity\AwsCredential;
use AwsLightsailBundle\Repository\KeyPairRepository;
use AwsLightsailBundle\Service\KeyPairSyncService;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

final class KeyPairSyncServiceTest extends TestCase
{
    private MockObject&EntityManagerInterface $entityManager;
    private MockObject&KeyPairRepository $keyPairRepository;
    private MockObject&LoggerInterface $logger;
    private KeyPairSyncService $service;

    protected function setUp(): void
    {
        $this->entityManager = $this->createMock(EntityManagerInterface::class);
        $this->keyPairRepository = $this->createMock(KeyPairRepository::class);
        $this->logger = $this->createMock(LoggerInterface::class);

        $this->service = new KeyPairSyncService(
            $this->entityManager,
            $this->keyPairRepository,
            $this->logger
        );
    }

    public function testBatchSyncKeyPairsWithEmptyData(): void
    {
        $credential = new AwsCredential();
        $credential->setName('test-credential');

        $this->entityManager
            ->expects($this->once())
            ->method('flush');

        $result = $this->service->batchSyncKeyPairs($credential, []);

        $this->assertSame(0, $result['total']);
        $this->assertSame(0, $result['new']);
        $this->assertSame(0, $result['updated']);
        $this->assertSame(0, $result['errors']);
    }
}