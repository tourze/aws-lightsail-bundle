<?php

declare(strict_types=1);

namespace AwsLightsailBundle\Tests\Enum;

use AwsLightsailBundle\Enum\ContainerServiceStateEnum;
use PHPUnit\Framework\Attributes\CoversClass;
use Tourze\PHPUnitEnum\AbstractEnumTestCase;

/**
 * @internal
 */
#[CoversClass(ContainerServiceStateEnum::class)]
final class ContainerServiceStateEnumTest extends AbstractEnumTestCase
{
    public function testToArray(): void
    {
        $result = ContainerServiceStateEnum::PENDING->toArray();
        $this->assertIsArray($result);
        $this->assertArrayHasKey('value', $result);
        $this->assertArrayHasKey('label', $result);
    }
}
