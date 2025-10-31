<?php

declare(strict_types=1);

namespace AwsLightsailBundle\Tests;

use AwsLightsailBundle\AwsLightsailBundle;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\PHPUnitSymfonyKernelTest\AbstractBundleTestCase;

/**
 * @internal
 */
#[CoversClass(AwsLightsailBundle::class)]
#[RunTestsInSeparateProcesses]
final class AwsLightsailBundleTest extends AbstractBundleTestCase
{
}
