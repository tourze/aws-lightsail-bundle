<?php

declare(strict_types=1);

namespace AwsLightsailBundle\Tests\Enum;

use AwsLightsailBundle\Enum\InstanceBundleEnum;
use PHPUnit\Framework\Attributes\CoversClass;
use Tourze\PHPUnitEnum\AbstractEnumTestCase;

/**
 * @internal
 */
#[CoversClass(InstanceBundleEnum::class)]
final class InstanceBundleEnumTest extends AbstractEnumTestCase
{
    public function testToArray(): void
    {
        $result = InstanceBundleEnum::NANO_2_0->toArray();
        $this->assertIsArray($result);
        $this->assertArrayHasKey('value', $result);
        $this->assertArrayHasKey('label', $result);
    }
}
