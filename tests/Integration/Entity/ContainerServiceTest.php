<?php

namespace AwsLightsailBundle\Tests\Integration\Entity;

use AwsLightsailBundle\Entity\AwsCredential;
use AwsLightsailBundle\Entity\ContainerService;
use AwsLightsailBundle\Enum\ContainerServicePowerEnum;
use AwsLightsailBundle\Enum\ContainerServiceStateEnum;
use PHPUnit\Framework\TestCase;

class ContainerServiceTest extends TestCase
{
    private ContainerService $containerService;

    protected function setUp(): void
    {
        $this->containerService = new ContainerService();
    }

    public function testDefaultValues(): void
    {
        self::assertEquals(ContainerServiceStateEnum::UNKNOWN, $this->containerService->getState());
        self::assertEquals(ContainerServicePowerEnum::NANO, $this->containerService->getPower());
        self::assertEquals(1, $this->containerService->getScale());
        self::assertFalse($this->containerService->isPublicDomainEnabled());
        self::assertFalse($this->containerService->isPrivateDomainEnabled());
        self::assertInstanceOf(\DateTimeInterface::class, $this->containerService->getCreatedAt());
    }

    public function testNameOperations(): void
    {
        $name = 'test-container-service';
        $this->containerService->setName($name);
        self::assertEquals($name, $this->containerService->getName());
    }

    public function testArnOperations(): void
    {
        $arn = 'arn:aws:lightsail:us-east-1:123456789012:ContainerService/test';
        $this->containerService->setArn($arn);
        self::assertEquals($arn, $this->containerService->getArn());
    }

    public function testPowerOperations(): void
    {
        $this->containerService->setPower(ContainerServicePowerEnum::MICRO);
        self::assertEquals(ContainerServicePowerEnum::MICRO, $this->containerService->getPower());
    }

    public function testScaleOperations(): void
    {
        $scale = 5;
        $this->containerService->setScale($scale);
        self::assertEquals($scale, $this->containerService->getScale());
    }

    public function testStateOperations(): void
    {
        $this->containerService->setState(ContainerServiceStateEnum::RUNNING);
        self::assertEquals(ContainerServiceStateEnum::RUNNING, $this->containerService->getState());
    }

    public function testRegionOperations(): void
    {
        $region = 'us-east-1';
        $this->containerService->setRegion($region);
        self::assertEquals($region, $this->containerService->getRegion());
    }

    public function testUrlOperations(): void
    {
        $url = 'https://test.lightsail.amazonaws.com';
        $this->containerService->setUrl($url);
        self::assertEquals($url, $this->containerService->getUrl());

        $this->containerService->setUrl(null);
        self::assertNull($this->containerService->getUrl());
    }

    public function testCurrentDeploymentOperations(): void
    {
        $deployment = ['version' => 1, 'containers' => []];
        $this->containerService->setCurrentDeployment($deployment);
        self::assertEquals($deployment, $this->containerService->getCurrentDeployment());

        $this->containerService->setCurrentDeployment(null);
        self::assertNull($this->containerService->getCurrentDeployment());
    }

    public function testNextDeploymentOperations(): void
    {
        $deployment = ['version' => 2, 'containers' => []];
        $this->containerService->setNextDeployment($deployment);
        self::assertEquals($deployment, $this->containerService->getNextDeployment());

        $this->containerService->setNextDeployment(null);
        self::assertNull($this->containerService->getNextDeployment());
    }

    public function testPublicDomainEnabledOperations(): void
    {
        $this->containerService->setIsPublicDomainEnabled(true);
        self::assertTrue($this->containerService->isPublicDomainEnabled());

        $this->containerService->setIsPublicDomainEnabled(false);
        self::assertFalse($this->containerService->isPublicDomainEnabled());
    }

    public function testPrivateDomainEnabledOperations(): void
    {
        $this->containerService->setIsPrivateDomainEnabled(true);
        self::assertTrue($this->containerService->isPrivateDomainEnabled());

        $this->containerService->setIsPrivateDomainEnabled(false);
        self::assertFalse($this->containerService->isPrivateDomainEnabled());
    }

    public function testPrivateDomainNameOperations(): void
    {
        $domainName = ['domain' => 'private.example.com'];
        $this->containerService->setPrivateDomainName($domainName);
        self::assertEquals($domainName, $this->containerService->getPrivateDomainName());

        $this->containerService->setPrivateDomainName(null);
        self::assertNull($this->containerService->getPrivateDomainName());
    }

    public function testPublicDomainNamesOperations(): void
    {
        $domainNames = 'public.example.com';
        $this->containerService->setPublicDomainNames($domainNames);
        self::assertEquals($domainNames, $this->containerService->getPublicDomainNames());

        $this->containerService->setPublicDomainNames(null);
        self::assertNull($this->containerService->getPublicDomainNames());
    }

    public function testContainerImagesOperations(): void
    {
        $images = ['nginx' => 'nginx:latest'];
        $this->containerService->setContainerImages($images);
        self::assertEquals($images, $this->containerService->getContainerImages());

        $this->containerService->setContainerImages(null);
        self::assertNull($this->containerService->getContainerImages());
    }

    public function testTagsOperations(): void
    {
        $tags = ['Environment' => 'Production', 'Team' => 'Backend'];
        $this->containerService->setTags($tags);
        self::assertEquals($tags, $this->containerService->getTags());

        $this->containerService->setTags(null);
        self::assertNull($this->containerService->getTags());
    }

    public function testSyncedAtOperations(): void
    {
        $syncedAt = new \DateTimeImmutable();
        $this->containerService->setSyncedAt($syncedAt);
        self::assertEquals($syncedAt, $this->containerService->getSyncedAt());

        $this->containerService->setSyncedAt(null);
        self::assertNull($this->containerService->getSyncedAt());
    }

    public function testUpdatedAtOperations(): void
    {
        $updatedAt = new \DateTimeImmutable();
        $this->containerService->setUpdatedAt($updatedAt);
        self::assertEquals($updatedAt, $this->containerService->getUpdatedAt());

        $this->containerService->setUpdatedAt(null);
        self::assertNull($this->containerService->getUpdatedAt());
    }

    public function testToString(): void
    {
        $this->containerService->setName('test-service');
        $this->containerService->setState(ContainerServiceStateEnum::RUNNING);
        $this->containerService->setPower(ContainerServicePowerEnum::MICRO);
        $this->containerService->setScale(3);

        $expected = 'ContainerService test-service (RUNNING) - micro, scale: 3';
        self::assertEquals($expected, (string) $this->containerService);
    }
}