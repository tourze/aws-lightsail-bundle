<?php

declare(strict_types=1);

namespace AwsLightsailBundle\Tests\Enum;

use AwsLightsailBundle\Enum\CertificateStatusEnum;
use PHPUnit\Framework\Attributes\CoversClass;
use Tourze\PHPUnitEnum\AbstractEnumTestCase;

/**
 * @internal
 */
#[CoversClass(CertificateStatusEnum::class)]
final class CertificateStatusEnumTest extends AbstractEnumTestCase
{
    public function testToArray(): void
    {
        $result = CertificateStatusEnum::PENDING_VALIDATION->toArray();
        $this->assertIsArray($result);
        $this->assertArrayHasKey('value', $result);
        $this->assertArrayHasKey('label', $result);
    }
}
