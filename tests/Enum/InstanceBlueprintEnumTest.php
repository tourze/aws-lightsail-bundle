<?php

declare(strict_types=1);

namespace AwsLightsailBundle\Tests\Enum;

use AwsLightsailBundle\Enum\InstanceBlueprintEnum;
use PHPUnit\Framework\Attributes\CoversClass;
use Tourze\PHPUnitEnum\AbstractEnumTestCase;

/**
 * @internal
 */
#[CoversClass(InstanceBlueprintEnum::class)]
final class InstanceBlueprintEnumTest extends AbstractEnumTestCase
{
    public function testToArray(): void
    {
        $result = InstanceBlueprintEnum::AMAZON_LINUX_2->toArray();
        $this->assertIsArray($result);
        $this->assertArrayHasKey('value', $result);
        $this->assertArrayHasKey('label', $result);
    }
}
