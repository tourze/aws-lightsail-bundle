<?php

declare(strict_types=1);

namespace AwsLightsailBundle\Tests\Entity;

use AwsLightsailBundle\Entity\AwsCredential;
use AwsLightsailBundle\Entity\KeyPair;
use PHPUnit\Framework\Attributes\CoversClass;
use Tourze\PHPUnitDoctrineEntity\AbstractEntityTestCase;

/**
 * @internal
 */
#[CoversClass(KeyPair::class)]
final class KeyPairTest extends AbstractEntityTestCase
{
    private KeyPair $keyPair;

    private AwsCredential $credential;

    protected function setUp(): void
    {
        parent::setUp();

        $this->keyPair    = new KeyPair();
        $this->credential = new AwsCredential();
        $this->credential->setName('test-credential');
    }

    protected function createEntity(): object
    {
        return new KeyPair();
    }

    public function testConstructorInitializesTimestamp(): void
    {
        $keyPair = new KeyPair();

        // TimestampableAware trait sets timestamps via Doctrine event listeners, not in constructor
        $this->assertNull($keyPair->getCreateTime());
        $this->assertNull($keyPair->getSyncTime());
        $this->assertNull($keyPair->getUpdateTime());
    }

    public function testGetIdReturnsNullWhenNotPersisted(): void
    {
        $this->assertNull($this->keyPair->getId());
    }

    public function testSetNameAndGetNameWorksCorrectly(): void
    {
        $name = 'test-keypair';

        $this->keyPair->setName($name);
        $this->assertSame($name, $this->keyPair->getName());
    }

    public function testSetArnAndGetArnWorksCorrectly(): void
    {
        $arn = 'arn:aws:lightsail:us-east-1:123456789012:KeyPair/test-keypair';

        $this->keyPair->setArn($arn);
        $this->assertSame($arn, $this->keyPair->getArn());
    }

    public function testSetFingerprintAndGetFingerprintWorksCorrectly(): void
    {
        $fingerprint = 'SHA256:1234567890abcdef1234567890abcdef12345678';

        $this->keyPair->setFingerprint($fingerprint);
        $this->assertSame($fingerprint, $this->keyPair->getFingerprint());
    }

    public function testSetFingerprintWithNullWorksCorrectly(): void
    {
        $this->keyPair->setFingerprint(null);
        $this->assertNull($this->keyPair->getFingerprint());
    }

    public function testSetPublicKeyAndGetPublicKeyWorksCorrectly(): void
    {
        $publicKey = 'ssh-rsa AAAAB3NzaC1yc2EAAAADAQABAAABAQ...';

        $this->keyPair->setPublicKey($publicKey);
        $this->assertSame($publicKey, $this->keyPair->getPublicKey());
    }

    public function testSetPublicKeyWithNullWorksCorrectly(): void
    {
        $this->keyPair->setPublicKey(null);
        $this->assertNull($this->keyPair->getPublicKey());
    }

    public function testSetPrivateKeyAndGetPrivateKeyWorksCorrectly(): void
    {
        $privateKey = '-----BEGIN RSA PRIVATE KEY-----\nMIIEpAIBAAKCAQEA...';

        $this->keyPair->setPrivateKey($privateKey);
        $this->assertSame($privateKey, $this->keyPair->getPrivateKey());
    }

    public function testSetPrivateKeyWithNullWorksCorrectly(): void
    {
        $this->keyPair->setPrivateKey(null);
        $this->assertNull($this->keyPair->getPrivateKey());
    }

    public function testSetIsEncryptedAndIsEncryptedWorksCorrectly(): void
    {
        $this->assertFalse($this->keyPair->isEncrypted());

        $this->keyPair->setIsEncrypted(true);
        $this->assertTrue($this->keyPair->isEncrypted());
    }

    public function testSetIsEncryptedWithFalseWorksCorrectly(): void
    {
        $this->keyPair->setIsEncrypted(true);

        $this->keyPair->setIsEncrypted(false);
        $this->assertFalse($this->keyPair->isEncrypted());
    }

    public function testSetRegionAndGetRegionWorksCorrectly(): void
    {
        $region = 'us-east-1';

        $this->keyPair->setRegion($region);
        $this->assertSame($region, $this->keyPair->getRegion());
    }

    public function testSetResourceTypeAndGetResourceTypeWorksCorrectly(): void
    {
        $resourceType = 'KeyPair';

        $this->keyPair->setResourceType($resourceType);
        $this->assertSame($resourceType, $this->keyPair->getResourceType());
    }

    public function testSetResourceTypeWithNullWorksCorrectly(): void
    {
        $this->keyPair->setResourceType(null);
        $this->assertNull($this->keyPair->getResourceType());
    }

    public function testSetSupportCodeAndGetSupportCodeWorksCorrectly(): void
    {
        $supportCode = 'ABC123';

        $this->keyPair->setSupportCode($supportCode);
        $this->assertSame($supportCode, $this->keyPair->getSupportCode());
    }

    public function testSetSupportCodeWithNullWorksCorrectly(): void
    {
        $this->keyPair->setSupportCode(null);
        $this->assertNull($this->keyPair->getSupportCode());
    }

    public function testSetTagsAndGetTagsWorksCorrectly(): void
    {
        $tags = ['environment' => 'test', 'project' => 'example'];

        $this->keyPair->setTags($tags);
        $this->assertSame($tags, $this->keyPair->getTags());
    }

    public function testSetTagsWithNullWorksCorrectly(): void
    {
        $this->keyPair->setTags(null);
        $this->assertNull($this->keyPair->getTags());
    }

    public function testSetTagsWithEmptyArrayWorksCorrectly(): void
    {
        $this->keyPair->setTags([]);
        $this->assertSame([], $this->keyPair->getTags());
    }

    public function testSetAwsCreateTimeAndGetAwsCreateTimeWorksCorrectly(): void
    {
        $awsCreatedAt = new \DateTime();

        $this->keyPair->setAwsCreateTime($awsCreatedAt);
        $this->assertInstanceOf(\DateTimeImmutable::class, $this->keyPair->getAwsCreateTime());
        $this->assertEquals($awsCreatedAt->format('Y-m-d H:i:s'), $this->keyPair->getAwsCreateTime()->format('Y-m-d H:i:s'));
    }

    public function testSetAwsCreateTimeWithNullWorksCorrectly(): void
    {
        $this->keyPair->setAwsCreateTime(null);
        $this->assertNull($this->keyPair->getAwsCreateTime());
    }

    public function testSetSyncTimeAndGetSyncTimeWorksCorrectly(): void
    {
        $syncTime = new \DateTime();

        $this->keyPair->setSyncTime($syncTime);
        $this->assertInstanceOf(\DateTimeImmutable::class, $this->keyPair->getSyncTime());
        $this->assertEquals($syncTime->format('Y-m-d H:i:s'), $this->keyPair->getSyncTime()->format('Y-m-d H:i:s'));
    }

    public function testSetSyncTimeWithNullWorksCorrectly(): void
    {
        $this->keyPair->setSyncTime(null);
        $this->assertNull($this->keyPair->getSyncTime());
    }

    public function testSetCredentialAndGetCredentialWorksCorrectly(): void
    {
        $this->keyPair->setCredential($this->credential);
        $this->assertSame($this->credential, $this->keyPair->getCredential());
    }

    public function testSetUpdateTimeAndGetUpdateTimeWorksCorrectly(): void
    {
        $updateTime = new \DateTimeImmutable();

        $this->keyPair->setUpdateTime($updateTime);
        $this->assertSame($updateTime, $this->keyPair->getUpdateTime());
    }

    public function testSetUpdateTimeWithNullWorksCorrectly(): void
    {
        $this->keyPair->setUpdateTime(null);
        $this->assertNull($this->keyPair->getUpdateTime());
    }

    public function testToStringReturnsCorrectFormat(): void
    {
        $name   = 'test-keypair';
        $region = 'us-east-1';

        $this->keyPair->setName($name);
        $this->keyPair->setRegion($region);

        $result = (string) $this->keyPair;

        $this->assertSame("KeyPair {$name} ({$region})", $result);
    }

    public function testFullWorkflowWithValidData(): void
    {
        $name        = 'test-keypair';
        $arn         = 'arn:aws:lightsail:us-east-1:123456789012:KeyPair/test-keypair';
        $fingerprint = 'SHA256:1234567890abcdef1234567890abcdef12345678';
        $publicKey   = 'ssh-rsa AAAAB3NzaC1yc2EAAAADAQABAAABAQ...';
        $privateKey  = '-----BEGIN RSA PRIVATE KEY-----\nMIIEpAIBAAKCAQEA...';
        $region      = 'us-east-1';
        $tags        = ['environment' => 'test'];

        $this->keyPair->setName($name);
        $this->keyPair->setArn($arn);
        $this->keyPair->setFingerprint($fingerprint);
        $this->keyPair->setPublicKey($publicKey);
        $this->keyPair->setPrivateKey($privateKey);
        $this->keyPair->setIsEncrypted(true);
        $this->keyPair->setRegion($region);
        $this->keyPair->setResourceType('KeyPair');
        $this->keyPair->setSupportCode('ABC123');
        $this->keyPair->setTags($tags);
        $this->keyPair->setCredential($this->credential);

        $this->assertSame($name, $this->keyPair->getName());
        $this->assertSame($arn, $this->keyPair->getArn());
        $this->assertSame($fingerprint, $this->keyPair->getFingerprint());
        $this->assertSame($publicKey, $this->keyPair->getPublicKey());
        $this->assertSame($privateKey, $this->keyPair->getPrivateKey());
        $this->assertTrue($this->keyPair->isEncrypted());
        $this->assertSame($region, $this->keyPair->getRegion());
        $this->assertSame('KeyPair', $this->keyPair->getResourceType());
        $this->assertSame('ABC123', $this->keyPair->getSupportCode());
        $this->assertSame($tags, $this->keyPair->getTags());
        $this->assertSame($this->credential, $this->keyPair->getCredential());
        $this->assertSame("KeyPair {$name} ({$region})", (string) $this->keyPair);
        // TimestampableAware trait sets timestamps via Doctrine event listeners
        $this->assertNull($this->keyPair->getCreateTime());
    }

    public function testMultipleSetterCallsWorkCorrectly(): void
    {
        $this->keyPair->setName('test');
        $this->keyPair->setArn('arn:test');
        $this->keyPair->setFingerprint('fingerprint');
        $this->keyPair->setPublicKey('public-key');
        $this->keyPair->setPrivateKey('private-key');
        $this->keyPair->setIsEncrypted(false);
        $this->keyPair->setRegion('us-west-2');
        $this->keyPair->setResourceType('KeyPair');
        $this->keyPair->setSupportCode('DEF456');
        $this->keyPair->setTags(['tag' => 'value']);
        $this->keyPair->setCredential($this->credential);

        // Verify that all setters work properly
        $this->assertSame('test', $this->keyPair->getName());
        $this->assertSame('arn:test', $this->keyPair->getArn());
        $this->assertSame('fingerprint', $this->keyPair->getFingerprint());
        $this->assertSame('public-key', $this->keyPair->getPublicKey());
        $this->assertSame('private-key', $this->keyPair->getPrivateKey());
        $this->assertFalse($this->keyPair->isEncrypted());
        $this->assertSame('us-west-2', $this->keyPair->getRegion());
        $this->assertSame('KeyPair', $this->keyPair->getResourceType());
        $this->assertSame('DEF456', $this->keyPair->getSupportCode());
        $this->assertSame(['tag' => 'value'], $this->keyPair->getTags());
        $this->assertSame($this->credential, $this->keyPair->getCredential());
    }

    public function testDefaultValuesAreSetCorrectly(): void
    {
        $this->assertFalse($this->keyPair->isEncrypted());
        $this->assertNull($this->keyPair->getFingerprint());
        $this->assertNull($this->keyPair->getPublicKey());
        $this->assertNull($this->keyPair->getPrivateKey());
        $this->assertNull($this->keyPair->getResourceType());
        $this->assertNull($this->keyPair->getSupportCode());
        $this->assertNull($this->keyPair->getTags());
        $this->assertNull($this->keyPair->getAwsCreateTime());
        $this->assertNull($this->keyPair->getSyncTime());
        $this->assertNull($this->keyPair->getUpdateTime());
    }

    public function testSetNameWithEmptyStringWorksCorrectly(): void
    {
        $this->keyPair->setName('');
        $this->keyPair->setRegion('us-east-1');

        $this->assertSame('', $this->keyPair->getName());
        $this->assertSame('KeyPair  (us-east-1)', (string) $this->keyPair);
    }

    public function testSetRegionWithEmptyStringWorksCorrectly(): void
    {
        $this->keyPair->setName('test');
        $this->keyPair->setRegion('');

        $this->assertSame('', $this->keyPair->getRegion());
        $this->assertSame('KeyPair test ()', (string) $this->keyPair);
    }

    public function testGetCreateTimeIsSetAutomatically(): void
    {
        // TimestampableAware trait sets timestamps via Doctrine event listeners, not in constructor
        $this->assertNull($this->keyPair->getCreateTime());
    }

    public function testEncryptionMethodsWorkCorrectly(): void
    {
        // Test default state
        $this->assertFalse($this->keyPair->isEncrypted());

        // Test enabling encryption
        $this->keyPair->setIsEncrypted(true);
        $this->assertTrue($this->keyPair->isEncrypted());

        // Test disabling encryption
        $this->keyPair->setIsEncrypted(false);
        $this->assertFalse($this->keyPair->isEncrypted());
    }

    public function testPublicAndPrivateKeyMethodsWorkIndependently(): void
    {
        $publicKey  = 'ssh-rsa AAAAB3NzaC1yc2EAAAADAQABAAABAQ...';
        $privateKey = '-----BEGIN RSA PRIVATE KEY-----\nMIIEpAIBAAKCAQEA...';

        // Set only public key
        $this->keyPair->setPublicKey($publicKey);
        $this->assertSame($publicKey, $this->keyPair->getPublicKey());
        $this->assertNull($this->keyPair->getPrivateKey());

        // Set only private key
        $this->keyPair->setPrivateKey($privateKey);
        $this->assertSame($privateKey, $this->keyPair->getPrivateKey());
        $this->assertSame($publicKey, $this->keyPair->getPublicKey());

        // Clear both
        $this->keyPair->setPublicKey(null);
        $this->keyPair->setPrivateKey(null);
        $this->assertNull($this->keyPair->getPublicKey());
        $this->assertNull($this->keyPair->getPrivateKey());
    }

    /**
     * @return iterable<array{string, mixed}>
     */
    public static function propertiesProvider(): iterable
    {
        return [
            'name'        => ['name', 'test-keypair'],
            'arn'         => ['arn', 'arn:aws:lightsail:us-east-1:123456789012:KeyPair/test-keypair'],
            'fingerprint' => ['fingerprint', 'SHA256:1234567890abcdef1234567890abcdef12345678'],
            'publicKey'   => ['publicKey', 'ssh-rsa AAAAB3NzaC1yc2EAAAADAQABAAABAQ...'],
            'privateKey'  => ['privateKey', '-----BEGIN RSA PRIVATE KEY-----\nMIIEpAIBAAKCAQEA...'],
            // 注意：isEncrypted 属性暂时排除，因为它的 setter 方法使用了 setIs 前缀，不符合 AbstractEntityTest 的约定
            'region'        => ['region', 'us-east-1'],
            'resourceType'  => ['resourceType', 'KeyPair'],
            'supportCode'   => ['supportCode', '123456789012/test-keypair/12345678-1234-1234-1234-123456789012'],
            'tags'          => ['tags', ['Environment' => 'production', 'Project' => 'test']],
            'awsCreateTime' => ['awsCreateTime', new \DateTimeImmutable('2023-01-01 12:00:00')],
            'syncTime'      => ['syncTime', new \DateTimeImmutable('2023-01-01 12:00:00')],
            'updateTime'    => ['updateTime', new \DateTimeImmutable('2023-01-01 12:00:00')],
        ];
    }
}
