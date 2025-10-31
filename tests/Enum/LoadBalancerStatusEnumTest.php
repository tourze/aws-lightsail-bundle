<?php

declare(strict_types=1);

namespace AwsLightsailBundle\Tests\Enum;

use AwsLightsailBundle\Enum\LoadBalancerStatusEnum;
use PHPUnit\Framework\Attributes\CoversClass;
use Tourze\PHPUnitEnum\AbstractEnumTestCase;

/**
 * @internal
 */
#[CoversClass(LoadBalancerStatusEnum::class)]
final class LoadBalancerStatusEnumTest extends AbstractEnumTestCase
{
    public function testToArray(): void
    {
        $result = LoadBalancerStatusEnum::ACTIVE->toArray();
        $this->assertIsArray($result);
        $this->assertArrayHasKey('value', $result);
        $this->assertArrayHasKey('label', $result);
    }
}
