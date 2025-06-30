<?php

declare(strict_types=1);

namespace AwsLightsailBundle\Tests\Unit;

use AwsLightsailBundle\AwsLightsailBundle;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpKernel\Bundle\Bundle;

final class AwsLightsailBundleTest extends TestCase
{
    private AwsLightsailBundle $bundle;

    protected function setUp(): void
    {
        $this->bundle = new AwsLightsailBundle();
    }

    public function testIsInstanceOfBundle(): void
    {
        $this->assertInstanceOf(Bundle::class, $this->bundle);
    }

    public function testGetName_returnsCorrectBundleName(): void
    {
        $this->assertSame('AwsLightsailBundle', $this->bundle->getName());
    }

    public function testGetNamespace_returnsCorrectNamespace(): void
    {
        $this->assertSame('AwsLightsailBundle', $this->bundle->getNamespace());
    }

    public function testGetPath_returnsCorrectPath(): void
    {
        $path = $this->bundle->getPath();
        $this->assertStringContainsString('aws-lightsail-bundle', $path);
        $this->assertStringEndsWith('src', $path);
    }
}