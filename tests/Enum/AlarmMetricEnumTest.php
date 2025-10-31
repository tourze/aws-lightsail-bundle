<?php

declare(strict_types=1);

namespace AwsLightsailBundle\Tests\Enum;

use AwsLightsailBundle\Enum\AlarmMetricEnum;
use PHPUnit\Framework\Attributes\CoversClass;
use Tourze\PHPUnitEnum\AbstractEnumTestCase;

/**
 * @internal
 */
#[CoversClass(AlarmMetricEnum::class)]
final class AlarmMetricEnumTest extends AbstractEnumTestCase
{
    public function testToArray(): void
    {
        $expected = [
            'value' => 'CPUUtilization',
            'label' => 'CPU 使用率',
        ];

        $this->assertSame($expected, AlarmMetricEnum::CPU_UTILIZATION->toArray());
    }
}
