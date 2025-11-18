<?php

declare(strict_types=1);

namespace AwsLightsailBundle\Tests;

use AwsLightsailBundle\AwsLightsailBundle;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Tourze\BundleDependency\BundleDependencyInterface;

/**
 * @internal
 */
#[CoversClass(AwsLightsailBundle::class)]
final class AwsLightsailBundleSimpleTest extends TestCase
{
    public function testBundleHasContainerExtension(): void
    {
        $bundle    = new AwsLightsailBundle();
        $extension = $bundle->getContainerExtension();
        $this->assertNotNull($extension);
    }

    public function testBundleBuildWithoutErrors(): void
    {
        $bundle    = new AwsLightsailBundle();
        $container = new ContainerBuilder();

        // 测试build方法不抛出异常
        $bundle->build($container);

        // 验证构建过程完成
        $this->assertNotNull($container);
    }

    public function testBundleImplementsDependencyInterface(): void
    {
        $this->assertInstanceOf(BundleDependencyInterface::class, new AwsLightsailBundle());
    }

    public function testBootShouldNoError(): void
    {
        $bundle = new AwsLightsailBundle();

        // 这个测试不应该抛出异常
        $bundle->boot();

        $this->expectNotToPerformAssertions();
    }

    public function testBundleCanBeInstantiatedMultipleTimes(): void
    {
        $bundle1 = new AwsLightsailBundle();
        $bundle2 = new AwsLightsailBundle();

        $this->assertNotSame($bundle1, $bundle2);
        $this->assertEquals($bundle1->getName(), $bundle2->getName());
    }
}
