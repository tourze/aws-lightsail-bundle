<?php

declare(strict_types=1);

namespace AwsLightsailBundle\Tests\Enum;

use AwsLightsailBundle\Enum\ContactMethodTypeEnum;
use PHPUnit\Framework\Attributes\CoversClass;
use Tourze\PHPUnitEnum\AbstractEnumTestCase;

/**
 * @internal
 */
#[CoversClass(ContactMethodTypeEnum::class)]
final class ContactMethodTypeEnumTest extends AbstractEnumTestCase
{
    public function testToArray(): void
    {
        $result = ContactMethodTypeEnum::EMAIL->toArray();
        $this->assertIsArray($result);
        $this->assertArrayHasKey('value', $result);
        $this->assertArrayHasKey('label', $result);
    }
}
