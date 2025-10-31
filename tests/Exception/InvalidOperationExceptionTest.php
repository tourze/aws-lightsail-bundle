<?php

declare(strict_types=1);

namespace AwsLightsailBundle\Tests\Exception;

use AwsLightsailBundle\Exception\InvalidOperationException;
use PHPUnit\Framework\Attributes\CoversClass;
use Tourze\PHPUnitBase\AbstractExceptionTestCase;

/**
 * @internal
 */
#[CoversClass(InvalidOperationException::class)]
final class InvalidOperationExceptionTest extends AbstractExceptionTestCase
{
    public function testExtendsRuntimeException(): void
    {
        $exception = new InvalidOperationException('Test message');

        self::assertSame('Test message', $exception->getMessage());
    }
}
