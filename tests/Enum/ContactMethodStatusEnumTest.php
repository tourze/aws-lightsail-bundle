<?php

declare(strict_types=1);

namespace AwsLightsailBundle\Tests\Enum;

use AwsLightsailBundle\Enum\ContactMethodStatusEnum;
use PHPUnit\Framework\Attributes\CoversClass;
use Tourze\PHPUnitEnum\AbstractEnumTestCase;

/**
 * @internal
 */
#[CoversClass(ContactMethodStatusEnum::class)]
final class ContactMethodStatusEnumTest extends AbstractEnumTestCase
{
    public function testToArray(): void
    {
        $result = ContactMethodStatusEnum::VERIFIED->toArray();
        $this->assertIsArray($result);
        $this->assertArrayHasKey('value', $result);
        $this->assertArrayHasKey('label', $result);
    }
}
