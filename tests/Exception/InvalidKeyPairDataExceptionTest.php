<?php

declare(strict_types=1);

namespace AwsLightsailBundle\Tests\Exception;

use AwsLightsailBundle\Exception\InvalidKeyPairDataException;
use PHPUnit\Framework\Attributes\CoversClass;
use Tourze\PHPUnitBase\AbstractExceptionTestCase;

/**
 * @internal
 */
#[CoversClass(InvalidKeyPairDataException::class)]
final class InvalidKeyPairDataExceptionTest extends AbstractExceptionTestCase
{
    public function testExceptionIsInstantiable(): void
    {
        $exception = new InvalidKeyPairDataException('Test message');
        $this->assertInstanceOf(InvalidKeyPairDataException::class, $exception);
    }

    public function testExceptionExtendsException(): void
    {
        $exception = new InvalidKeyPairDataException('Test message');
        $this->assertInstanceOf(\Exception::class, $exception);
    }

    public function testExceptionHasCorrectMessage(): void
    {
        $message   = 'Invalid key pair data provided';
        $exception = new InvalidKeyPairDataException($message);
        $this->assertSame($message, $exception->getMessage());
    }

    public function testExceptionHasCorrectNamespace(): void
    {
        $this->assertSame('AwsLightsailBundle\Exception\InvalidKeyPairDataException', InvalidKeyPairDataException::class);
    }
}
