<?php

declare(strict_types=1);

namespace AwsLightsailBundle\Tests\Enum;

use AwsLightsailBundle\Enum\ContainerServicePowerEnum;
use PHPUnit\Framework\Attributes\CoversClass;
use Tourze\PHPUnitEnum\AbstractEnumTestCase;

/**
 * @internal
 */
#[CoversClass(ContainerServicePowerEnum::class)]
final class ContainerServicePowerEnumTest extends AbstractEnumTestCase
{
    public function testToArray(): void
    {
        $result = ContainerServicePowerEnum::NANO->toArray();
        $this->assertIsArray($result);
        $this->assertArrayHasKey('value', $result);
        $this->assertArrayHasKey('label', $result);
    }
}
