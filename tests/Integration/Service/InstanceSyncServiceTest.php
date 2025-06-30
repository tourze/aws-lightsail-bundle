<?php

declare(strict_types=1);

namespace AwsLightsailBundle\Tests\Integration\Service;

use AwsLightsailBundle\Entity\AwsCredential;
use AwsLightsailBundle\Entity\Instance;
use AwsLightsailBundle\Repository\InstanceRepository;
use AwsLightsailBundle\Service\InstanceSyncService;
use AwsLightsailBundle\Service\KeyPairSyncService;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

final class InstanceSyncServiceTest extends TestCase
{
    private EntityManagerInterface $entityManager;
    private InstanceRepository $instanceRepository;
    private KeyPairSyncService $keyPairSyncService;
    private LoggerInterface $logger;
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

    public function testService_isInitializedCorrectly(): void
    {
        $this->assertInstanceOf(InstanceSyncService::class, $this->service);
    }

    public function testUpdateInstanceFromData_withValidData_createsNewInstance(): void
    {
        $credential = new AwsCredential();
        $credential->setName('test-credential');

        $data = [
            'name' => 'test-instance',
            'location' => [
                'regionName' => 'us-east-1',
            ],
            'arn' => 'arn:aws:lightsail:us-east-1:123456789012:Instance/test-instance',
        ];

        $this->instanceRepository->expects($this->once())
            ->method('findOneByNameAndCredential')
            ->with('test-instance', $credential)
            ->willReturn(null);

        $this->entityManager->expects($this->once())
            ->method('persist')
            ->with($this->isInstanceOf(Instance::class));

        $this->entityManager->expects($this->once())
            ->method('flush');

        $result = $this->service->updateInstanceFromData($credential, $data);

        $this->assertInstanceOf(Instance::class, $result);
        $this->assertSame('test-instance', $result->getName());
        $this->assertSame($credential, $result->getCredential());
    }

    public function testUpdateInstanceFromData_withExistingInstance_updatesInstance(): void
    {
        $credential = new AwsCredential();
        $credential->setName('test-credential');

        $existingInstance = new Instance();
        $existingInstance->setName('test-instance');
        $existingInstance->setCredential($credential);

        $data = [
            'name' => 'test-instance',
            'location' => [
                'regionName' => 'us-east-1',
            ],
            'arn' => 'arn:aws:lightsail:us-east-1:123456789012:Instance/test-instance',
        ];

        $this->instanceRepository->expects($this->once())
            ->method('findOneByNameAndCredential')
            ->with('test-instance', $credential)
            ->willReturn($existingInstance);

        $this->entityManager->expects($this->once())
            ->method('persist')
            ->with($existingInstance);

        $this->entityManager->expects($this->once())
            ->method('flush');

        $result = $this->service->updateInstanceFromData($credential, $data);

        $this->assertSame($existingInstance, $result);
    }

    public function testUpdateInstanceFromData_withEmptyName_throwsException(): void
    {
        $credential = new AwsCredential();
        $data = [
            'name' => '',
            'location' => [
                'regionName' => 'us-east-1',
            ],
        ];

        $this->expectException(\AwsLightsailBundle\Exception\InvalidInstanceDataException::class);
        $this->expectExceptionMessage('实例名称不能为空');

        $this->service->updateInstanceFromData($credential, $data);
    }

    public function testUpdateInstanceFromData_withEmptyRegion_throwsException(): void
    {
        $credential = new AwsCredential();
        $data = [
            'name' => 'test-instance',
            'location' => [
                'regionName' => '',
            ],
        ];

        $this->expectException(\AwsLightsailBundle\Exception\InvalidInstanceDataException::class);
        $this->expectExceptionMessage('实例区域不能为空');

        $this->service->updateInstanceFromData($credential, $data);
    }

    public function testUpdateInstanceFromData_withFlushFalse_doesNotFlush(): void
    {
        $credential = new AwsCredential();
        $credential->setName('test-credential');
        $data = [
            'name' => 'test-instance',
            'location' => [
                'regionName' => 'us-east-1',
            ],
        ];

        $this->instanceRepository->method('findOneByNameAndCredential')
            ->willReturn(null);

        $this->entityManager->expects($this->once())
            ->method('persist');

        $this->entityManager->expects($this->never())
            ->method('flush');

        $this->service->updateInstanceFromData($credential, $data, false);
    }

    public function testBatchSyncInstances_withValidData_returnsStats(): void
    {
        $credential = new AwsCredential();
        $credential->setName('test-credential');
        $instancesData = [
            [
                'name' => 'instance-1',
                'location' => ['regionName' => 'us-east-1'],
            ],
            [
                'name' => 'instance-2',
                'location' => ['regionName' => 'us-east-1'],
            ],
        ];

        $this->instanceRepository->method('findOneByNameAndCredential')
            ->willReturn(null);

        $this->entityManager->expects($this->once())
            ->method('flush');

        $result = $this->service->batchSyncInstances($credential, $instancesData);

        $this->assertArrayHasKey('total', $result);
        $this->assertArrayHasKey('new', $result);
        $this->assertArrayHasKey('updated', $result);
        $this->assertArrayHasKey('errors', $result);
        $this->assertSame(2, $result['total']);
        $this->assertSame(2, $result['new']);
        $this->assertSame(0, $result['updated']);
        $this->assertSame(0, $result['errors']);
    }

    public function testCleanupDeletedInstances_removesNonExistentInstances(): void
    {
        $credential = new AwsCredential();
        $credential->setName('test-credential');
        $remoteInstanceNames = ['instance-1', 'instance-2'];

        $localInstance1 = new Instance();
        $localInstance1->setName('instance-1');

        $localInstance2 = new Instance();
        $localInstance2->setName('instance-3'); // This one should be deleted

        $this->instanceRepository->expects($this->once())
            ->method('findBy')
            ->with(['credential' => $credential])
            ->willReturn([$localInstance1, $localInstance2]);

        $this->entityManager->expects($this->once())
            ->method('remove')
            ->with($localInstance2);

        $this->entityManager->expects($this->once())
            ->method('flush');

        $deletedCount = $this->service->cleanupDeletedInstances($credential, $remoteInstanceNames);

        $this->assertSame(1, $deletedCount);
    }

    public function testCleanupDeletedInstances_withNoDeletedInstances_returnsZero(): void
    {
        $credential = new AwsCredential();
        $credential->setName('test-credential');
        $remoteInstanceNames = ['instance-1', 'instance-2'];

        $localInstance1 = new Instance();
        $localInstance1->setName('instance-1');

        $localInstance2 = new Instance();
        $localInstance2->setName('instance-2');

        $this->instanceRepository->expects($this->once())
            ->method('findBy')
            ->with(['credential' => $credential])
            ->willReturn([$localInstance1, $localInstance2]);

        $this->entityManager->expects($this->never())
            ->method('remove');

        $this->entityManager->expects($this->never())
            ->method('flush');

        $deletedCount = $this->service->cleanupDeletedInstances($credential, $remoteInstanceNames);

        $this->assertSame(0, $deletedCount);
    }
}