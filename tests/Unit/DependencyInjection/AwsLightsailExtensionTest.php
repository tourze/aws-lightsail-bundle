<?php

declare(strict_types=1);

namespace AwsLightsailBundle\Tests\Unit\DependencyInjection;

use AwsLightsailBundle\DependencyInjection\AwsLightsailExtension;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;

final class AwsLightsailExtensionTest extends TestCase
{
    private AwsLightsailExtension $extension;

    protected function setUp(): void
    {
        $this->extension = new AwsLightsailExtension();
    }

    public function testExtension_extendsSymfonyExtension(): void
    {
        $this->assertInstanceOf(Extension::class, $this->extension);
    }

    public function testExtension_isInstantiable(): void
    {
        $this->assertInstanceOf(AwsLightsailExtension::class, $this->extension);
    }public function testLoad_doesNotThrowException(): void
    {
        $container = new ContainerBuilder();
        
        $this->expectNotToPerformAssertions();
        $this->extension->load([], $container);
    }

    public function testGetAlias_returnsCorrectAlias(): void
    {
        $this->assertSame('aws_lightsail', $this->extension->getAlias());
    }
}