<?php

declare(strict_types=1);

namespace ChubbyphpTest\AwsLightsailBundle\Unit\Entity;

use AwsLightsailBundle\Entity\AwsCredential;
use AwsLightsailBundle\Entity\KeyPair;
use PHPUnit\Framework\TestCase;

final class KeyPairTest extends TestCase
{
    private KeyPair $keyPair;
    private AwsCredential $credential;

    protected function setUp(): void
    {
        $this->keyPair = new KeyPair();
        $this->credential = new AwsCredential();
        $this->credential->setName('test-credential');
    }

    public function testConstructor_initializesTimestamp(): void
    {
        $keyPair = new KeyPair();
        
        $this->assertInstanceOf(\DateTimeInterface::class, $keyPair->getCreateTime());
        $this->assertNull($keyPair->getSyncTime());
        $this->assertNull($keyPair->getUpdateTime());
    }

    public function testGetId_returnsNull_whenNotPersisted(): void
    {
        $this->assertNull($this->keyPair->getId());
    }

    public function testSetName_andGetName_worksCorrectly(): void
    {
        $name = 'test-keypair';
        
        $result = $this->keyPair->setName($name);
        
        $this->assertSame($this->keyPair, $result);
        $this->assertSame($name, $this->keyPair->getName());
    }

    public function testSetArn_andGetArn_worksCorrectly(): void
    {
        $arn = 'arn:aws:lightsail:us-east-1:123456789012:KeyPair/test-keypair';
        
        $result = $this->keyPair->setArn($arn);
        
        $this->assertSame($this->keyPair, $result);
        $this->assertSame($arn, $this->keyPair->getArn());
    }

    public function testSetFingerprint_andGetFingerprint_worksCorrectly(): void
    {
        $fingerprint = 'SHA256:1234567890abcdef1234567890abcdef12345678';
        
        $result = $this->keyPair->setFingerprint($fingerprint);
        
        $this->assertSame($this->keyPair, $result);
        $this->assertSame($fingerprint, $this->keyPair->getFingerprint());
    }

    public function testSetFingerprint_withNull_worksCorrectly(): void
    {
        $result = $this->keyPair->setFingerprint(null);
        
        $this->assertSame($this->keyPair, $result);
        $this->assertNull($this->keyPair->getFingerprint());
    }

    public function testSetPublicKey_andGetPublicKey_worksCorrectly(): void
    {
        $publicKey = 'ssh-rsa AAAAB3NzaC1yc2EAAAADAQABAAABAQ...';
        
        $result = $this->keyPair->setPublicKey($publicKey);
        
        $this->assertSame($this->keyPair, $result);
        $this->assertSame($publicKey, $this->keyPair->getPublicKey());
    }

    public function testSetPublicKey_withNull_worksCorrectly(): void
    {
        $result = $this->keyPair->setPublicKey(null);
        
        $this->assertSame($this->keyPair, $result);
        $this->assertNull($this->keyPair->getPublicKey());
    }

    public function testSetPrivateKey_andGetPrivateKey_worksCorrectly(): void
    {
        $privateKey = '-----BEGIN RSA PRIVATE KEY-----\nMIIEpAIBAAKCAQEA...';
        
        $result = $this->keyPair->setPrivateKey($privateKey);
        
        $this->assertSame($this->keyPair, $result);
        $this->assertSame($privateKey, $this->keyPair->getPrivateKey());
    }

    public function testSetPrivateKey_withNull_worksCorrectly(): void
    {
        $result = $this->keyPair->setPrivateKey(null);
        
        $this->assertSame($this->keyPair, $result);
        $this->assertNull($this->keyPair->getPrivateKey());
    }

    public function testSetIsEncrypted_andIsEncrypted_worksCorrectly(): void
    {
        $this->assertFalse($this->keyPair->isEncrypted());
        
        $result = $this->keyPair->setIsEncrypted(true);
        
        $this->assertSame($this->keyPair, $result);
        $this->assertTrue($this->keyPair->isEncrypted());
    }

    public function testSetIsEncrypted_withFalse_worksCorrectly(): void
    {
        $this->keyPair->setIsEncrypted(true);
        
        $result = $this->keyPair->setIsEncrypted(false);
        
        $this->assertSame($this->keyPair, $result);
        $this->assertFalse($this->keyPair->isEncrypted());
    }

    public function testSetRegion_andGetRegion_worksCorrectly(): void
    {
        $region = 'us-east-1';
        
        $result = $this->keyPair->setRegion($region);
        
        $this->assertSame($this->keyPair, $result);
        $this->assertSame($region, $this->keyPair->getRegion());
    }

    public function testSetResourceType_andGetResourceType_worksCorrectly(): void
    {
        $resourceType = 'KeyPair';
        
        $result = $this->keyPair->setResourceType($resourceType);
        
        $this->assertSame($this->keyPair, $result);
        $this->assertSame($resourceType, $this->keyPair->getResourceType());
    }

    public function testSetResourceType_withNull_worksCorrectly(): void
    {
        $result = $this->keyPair->setResourceType(null);
        
        $this->assertSame($this->keyPair, $result);
        $this->assertNull($this->keyPair->getResourceType());
    }

    public function testSetSupportCode_andGetSupportCode_worksCorrectly(): void
    {
        $supportCode = 'ABC123';
        
        $result = $this->keyPair->setSupportCode($supportCode);
        
        $this->assertSame($this->keyPair, $result);
        $this->assertSame($supportCode, $this->keyPair->getSupportCode());
    }

    public function testSetSupportCode_withNull_worksCorrectly(): void
    {
        $result = $this->keyPair->setSupportCode(null);
        
        $this->assertSame($this->keyPair, $result);
        $this->assertNull($this->keyPair->getSupportCode());
    }

    public function testSetTags_andGetTags_worksCorrectly(): void
    {
        $tags = ['environment' => 'test', 'project' => 'example'];
        
        $result = $this->keyPair->setTags($tags);
        
        $this->assertSame($this->keyPair, $result);
        $this->assertSame($tags, $this->keyPair->getTags());
    }

    public function testSetTags_withNull_worksCorrectly(): void
    {
        $result = $this->keyPair->setTags(null);
        
        $this->assertSame($this->keyPair, $result);
        $this->assertNull($this->keyPair->getTags());
    }

    public function testSetTags_withEmptyArray_worksCorrectly(): void
    {
        $result = $this->keyPair->setTags([]);
        
        $this->assertSame($this->keyPair, $result);
        $this->assertSame([], $this->keyPair->getTags());
    }

    public function testSetAwsCreatedAt_andGetAwsCreatedAt_worksCorrectly(): void
    {
        $awsCreatedAt = new \DateTime();
        
        $result = $this->keyPair->setAwsCreatedAt($awsCreatedAt);
        
        $this->assertSame($this->keyPair, $result);
        $this->assertSame($awsCreatedAt, $this->keyPair->getAwsCreatedAt());
    }

    public function testSetAwsCreatedAt_withNull_worksCorrectly(): void
    {
        $result = $this->keyPair->setAwsCreatedAt(null);
        
        $this->assertSame($this->keyPair, $result);
        $this->assertNull($this->keyPair->getAwsCreatedAt());
    }

    public function testSetSyncTime_andGetSyncTime_worksCorrectly(): void
    {
        $syncTime = new \DateTime();
        
        $result = $this->keyPair->setSyncTime($syncTime);
        
        $this->assertSame($this->keyPair, $result);
        $this->assertSame($syncTime, $this->keyPair->getSyncTime());
    }

    public function testSetSyncTime_withNull_worksCorrectly(): void
    {
        $result = $this->keyPair->setSyncTime(null);
        
        $this->assertSame($this->keyPair, $result);
        $this->assertNull($this->keyPair->getSyncTime());
    }

    public function testSetCredential_andGetCredential_worksCorrectly(): void
    {
        $result = $this->keyPair->setCredential($this->credential);
        
        $this->assertSame($this->keyPair, $result);
        $this->assertSame($this->credential, $this->keyPair->getCredential());
    }

    public function testSetUpdateTime_andGetUpdateTime_worksCorrectly(): void
    {
        $updateTime = new \DateTime();
        
        $result = $this->keyPair->setUpdateTime($updateTime);
        
        $this->assertSame($this->keyPair, $result);
        $this->assertSame($updateTime, $this->keyPair->getUpdateTime());
    }

    public function testSetUpdateTime_withNull_worksCorrectly(): void
    {
        $result = $this->keyPair->setUpdateTime(null);
        
        $this->assertSame($this->keyPair, $result);
        $this->assertNull($this->keyPair->getUpdateTime());
    }

    public function testToString_returnsCorrectFormat(): void
    {
        $name = 'test-keypair';
        $region = 'us-east-1';
        
        $this->keyPair->setName($name)->setRegion($region);
        
        $result = (string) $this->keyPair;
        
        $this->assertSame("KeyPair {$name} ({$region})", $result);
    }

    public function testFullWorkflow_withValidData(): void
    {
        $name = 'test-keypair';
        $arn = 'arn:aws:lightsail:us-east-1:123456789012:KeyPair/test-keypair';
        $fingerprint = 'SHA256:1234567890abcdef1234567890abcdef12345678';
        $publicKey = 'ssh-rsa AAAAB3NzaC1yc2EAAAADAQABAAABAQ...';
        $privateKey = '-----BEGIN RSA PRIVATE KEY-----\nMIIEpAIBAAKCAQEA...';
        $region = 'us-east-1';
        $tags = ['environment' => 'test'];

        $this->keyPair
            ->setName($name)
            ->setArn($arn)
            ->setFingerprint($fingerprint)
            ->setPublicKey($publicKey)
            ->setPrivateKey($privateKey)
            ->setIsEncrypted(true)
            ->setRegion($region)
            ->setResourceType('KeyPair')
            ->setSupportCode('ABC123')
            ->setTags($tags)
            ->setCredential($this->credential);

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
        $this->assertInstanceOf(\DateTimeInterface::class, $this->keyPair->getCreateTime());
    }

    public function testChainedMethodCalls_returnsSameInstance(): void
    {
        $result = $this->keyPair
            ->setName('test')
            ->setArn('arn:test')
            ->setFingerprint('fingerprint')
            ->setPublicKey('public-key')
            ->setPrivateKey('private-key')
            ->setIsEncrypted(false)
            ->setRegion('us-west-2')
            ->setResourceType('KeyPair')
            ->setSupportCode('DEF456')
            ->setTags(['tag' => 'value'])
            ->setCredential($this->credential);

        $this->assertSame($this->keyPair, $result);
    }

    public function testDefaultValues_areSetCorrectly(): void
    {
        $this->assertFalse($this->keyPair->isEncrypted());
        $this->assertNull($this->keyPair->getFingerprint());
        $this->assertNull($this->keyPair->getPublicKey());
        $this->assertNull($this->keyPair->getPrivateKey());
        $this->assertNull($this->keyPair->getResourceType());
        $this->assertNull($this->keyPair->getSupportCode());
        $this->assertNull($this->keyPair->getTags());
        $this->assertNull($this->keyPair->getAwsCreatedAt());
        $this->assertNull($this->keyPair->getSyncTime());
        $this->assertNull($this->keyPair->getUpdateTime());
    }

    public function testSetName_withEmptyString_worksCorrectly(): void
    {
        $this->keyPair->setName('')->setRegion('us-east-1');
        
        $this->assertSame('', $this->keyPair->getName());
        $this->assertSame('KeyPair  (us-east-1)', (string) $this->keyPair);
    }

    public function testSetRegion_withEmptyString_worksCorrectly(): void
    {
        $this->keyPair->setName('test')->setRegion('');
        
        $this->assertSame('', $this->keyPair->getRegion());
        $this->assertSame('KeyPair test ()', (string) $this->keyPair);
    }

    public function testGetCreateTime_isSetAutomatically(): void
    {
        $this->assertInstanceOf(\DateTimeInterface::class, $this->keyPair->getCreateTime());
        $this->assertLessThanOrEqual(time(), $this->keyPair->getCreateTime()->getTimestamp());
    }

    public function testEncryptionMethods_workCorrectly(): void
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

    public function testPublicAndPrivateKeyMethods_workIndependently(): void
    {
        $publicKey = 'ssh-rsa AAAAB3NzaC1yc2EAAAADAQABAAABAQ...';
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
        $this->keyPair->setPublicKey(null)->setPrivateKey(null);
        $this->assertNull($this->keyPair->getPublicKey());
        $this->assertNull($this->keyPair->getPrivateKey());
    }
} 