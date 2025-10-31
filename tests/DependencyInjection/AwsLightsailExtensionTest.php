<?php

declare(strict_types=1);

namespace AwsLightsailBundle\Tests\DependencyInjection;

use AwsLightsailBundle\DependencyInjection\AwsLightsailExtension;
use PHPUnit\Framework\Attributes\CoversClass;
use Tourze\PHPUnitSymfonyUnitTest\AbstractDependencyInjectionExtensionTestCase;
use Tourze\SymfonyDependencyServiceLoader\AutoExtension;

/**
 * @internal
 */
#[CoversClass(AwsLightsailExtension::class)]
final class AwsLightsailExtensionTest extends AbstractDependencyInjectionExtensionTestCase
{
    private AwsLightsailExtension $extension;

    protected function setUp(): void
    {
        parent::setUp();

        $this->extension = new AwsLightsailExtension();
    }

    public function testExtensionExtendsAutoExtension(): void
    {
        $this->assertInstanceOf(AutoExtension::class, $this->extension);
    }

    public function testExtensionIsInstantiable(): void
    {
        $this->assertInstanceOf(AwsLightsailExtension::class, $this->extension);
    }

    public function testGetAliasReturnsCorrectAlias(): void
    {
        $this->assertSame('aws_lightsail', $this->extension->getAlias());
    }
}
