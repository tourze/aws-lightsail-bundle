<?php

declare(strict_types=1);

namespace AwsLightsailBundle\Tests\Enum;

use AwsLightsailBundle\Enum\AlarmStateEnum;
use PHPUnit\Framework\Attributes\CoversClass;
use Tourze\PHPUnitEnum\AbstractEnumTestCase;

/**
 * @internal
 */
#[CoversClass(AlarmStateEnum::class)]
final class AlarmStateEnumTest extends AbstractEnumTestCase
{
    public function testToArray(): void
    {
        $expected = [
            'value' => 'OK',
            'label' => '正常',
        ];

        $this->assertSame($expected, AlarmStateEnum::OK->toArray());
    }
}
