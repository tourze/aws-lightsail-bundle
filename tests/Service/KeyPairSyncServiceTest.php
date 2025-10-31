<?php

declare(strict_types=1);

namespace AwsLightsailBundle\Tests\Service;

use AwsLightsailBundle\Entity\AwsCredential;
use AwsLightsailBundle\Service\KeyPairSyncService;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\PHPUnitSymfonyKernelTest\AbstractIntegrationTestCase;

/**
 * @internal
 */
#[CoversClass(KeyPairSyncService::class)]
#[RunTestsInSeparateProcesses]
final class KeyPairSyncServiceTest extends AbstractIntegrationTestCase
{
    private KeyPairSyncService $service;

    protected function onSetUp(): void
    {
        $this->service = self::getService(KeyPairSyncService::class);
    }

    public function testBatchSyncKeyPairsWithEmptyData(): void
    {
        $credential = new AwsCredential();
        $credential->setName('test-credential');
        $credential->setAccessKeyId('test-access-key');
        $credential->setSecretAccessKey('test-secret-key');

        // Note: Since we're using AbstractIntegrationTestCase, no need to mock flush()

        $result = $this->service->batchSyncKeyPairs($credential, []);

        $this->assertSame(0, $result['total']);
        $this->assertSame(0, $result['new']);
        $this->assertSame(0, $result['updated']);
        $this->assertSame(0, $result['errors']);
    }

    public function testCleanupDeletedKeyPairs(): void
    {
        $credential = new AwsCredential();
        $credential->setName('test-credential');
        $credential->setAccessKeyId('test-access-key');
        $credential->setSecretAccessKey('test-secret-key');

        $result = $this->service->cleanupDeletedKeyPairs($credential, [], 'us-east-1');
        $this->assertIsInt($result);
        $this->assertGreaterThanOrEqual(0, $result);
    }

    public function testFindKeyPairByNameAndCredentialAndRegion(): void
    {
        $credential = new AwsCredential();
        $credential->setName('test-credential');
        $credential->setAccessKeyId('test-access-key');
        $credential->setSecretAccessKey('test-secret-key');

        // 保存凭证以获得 ID
        $credentialRepository = self::getService('AwsLightsailBundle\Repository\AwsCredentialRepository');
        $credentialRepository->save($credential, true);

        $result = $this->service->findKeyPairByNameAndCredentialAndRegion('test-keypair', $credential, 'us-east-1');
        $this->assertNull($result);
    }

    public function testUpdateKeyPairFromData(): void
    {
        $credential = new AwsCredential();
        $credential->setName('test-credential');
        $credential->setAccessKeyId('test-access-key');
        $credential->setSecretAccessKey('test-secret-key');

        // 保存凭证以获得 ID
        $credentialRepository = self::getService('AwsLightsailBundle\Repository\AwsCredentialRepository');
        $credentialRepository->save($credential, true);

        $data = [
            'name'         => 'test-keypair',
            'arn'          => 'arn:aws:lightsail:us-east-1:123456789012:KeyPair/test-keypair',
            'fingerprint'  => 'aa:bb:cc:dd:ee:ff',
            'location'     => ['regionName' => 'us-east-1'],
            'resourceType' => 'KeyPair',
        ];

        $keyPair = $this->service->updateKeyPairFromData($credential, $data);

        $this->assertInstanceOf('AwsLightsailBundle\Entity\KeyPair', $keyPair);
        $this->assertSame('test-keypair', $keyPair->getName());
    }
}
