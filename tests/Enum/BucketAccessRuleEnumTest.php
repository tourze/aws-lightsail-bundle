<?php

declare(strict_types=1);

namespace AwsLightsailBundle\Tests\Enum;

use AwsLightsailBundle\Enum\BucketAccessRuleEnum;
use PHPUnit\Framework\Attributes\CoversClass;
use Tourze\PHPUnitEnum\AbstractEnumTestCase;

/**
 * @internal
 */
#[CoversClass(BucketAccessRuleEnum::class)]
final class BucketAccessRuleEnumTest extends AbstractEnumTestCase
{
    public function testToArray(): void
    {
        $result = BucketAccessRuleEnum::PUBLIC_READ->toArray();
        $this->assertIsArray($result);
        $this->assertArrayHasKey('value', $result);
        $this->assertArrayHasKey('label', $result);
    }
}
