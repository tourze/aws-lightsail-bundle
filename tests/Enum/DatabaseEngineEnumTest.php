<?php

declare(strict_types=1);

namespace AwsLightsailBundle\Tests\Enum;

use AwsLightsailBundle\Enum\DatabaseEngineEnum;
use PHPUnit\Framework\Attributes\CoversClass;
use Tourze\PHPUnitEnum\AbstractEnumTestCase;

/**
 * @internal
 */
#[CoversClass(DatabaseEngineEnum::class)]
final class DatabaseEngineEnumTest extends AbstractEnumTestCase
{
    public function testToArray(): void
    {
        $result = DatabaseEngineEnum::MYSQL->toArray();
        $this->assertIsArray($result);
        $this->assertArrayHasKey('value', $result);
        $this->assertArrayHasKey('label', $result);
    }
}
