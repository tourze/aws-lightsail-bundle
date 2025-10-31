<?php

declare(strict_types=1);

namespace AwsLightsailBundle\Tests\Enum;

use AwsLightsailBundle\Enum\DnsRecordTypeEnum;
use PHPUnit\Framework\Attributes\CoversClass;
use Tourze\PHPUnitEnum\AbstractEnumTestCase;

/**
 * @internal
 */
#[CoversClass(DnsRecordTypeEnum::class)]
final class DnsRecordTypeEnumTest extends AbstractEnumTestCase
{
    public function testToArray(): void
    {
        $result = DnsRecordTypeEnum::A->toArray();
        $this->assertIsArray($result);
        $this->assertArrayHasKey('value', $result);
        $this->assertArrayHasKey('label', $result);
    }
}
