<?php

declare(strict_types=1);

namespace AwsLightsailBundle\Tests\Exception;

use AwsLightsailBundle\Exception\InvalidInstanceDataException;
use PHPUnit\Framework\Attributes\CoversClass;
use Tourze\PHPUnitBase\AbstractExceptionTestCase;

/**
 * @internal
 */
#[CoversClass(InvalidInstanceDataException::class)]
final class InvalidInstanceDataExceptionTest extends AbstractExceptionTestCase
{
    public function testExceptionIsInstantiable(): void
    {
        $exception = new InvalidInstanceDataException('Test message');
        $this->assertInstanceOf(InvalidInstanceDataException::class, $exception);
    }

    public function testExceptionExtendsException(): void
    {
        $exception = new InvalidInstanceDataException('Test message');
        $this->assertInstanceOf(\Exception::class, $exception);
    }

    public function testExceptionHasCorrectMessage(): void
    {
        $message   = 'Invalid instance data provided';
        $exception = new InvalidInstanceDataException($message);
        $this->assertSame($message, $exception->getMessage());
    }

    public function testExceptionHasCorrectNamespace(): void
    {
        $this->assertSame('AwsLightsailBundle\Exception\InvalidInstanceDataException', InvalidInstanceDataException::class);
    }
}
