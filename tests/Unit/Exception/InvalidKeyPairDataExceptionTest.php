<?php

declare(strict_types=1);

namespace AwsLightsailBundle\Tests\Unit\Exception;

use AwsLightsailBundle\Exception\InvalidKeyPairDataException;
use PHPUnit\Framework\TestCase;

final class InvalidKeyPairDataExceptionTest extends TestCase
{
    public function testException_isInstantiable(): void
    {
        $exception = new InvalidKeyPairDataException('Test message');
        $this->assertInstanceOf(InvalidKeyPairDataException::class, $exception);
    }

    public function testException_extendsException(): void
    {
        $exception = new InvalidKeyPairDataException('Test message');
        $this->assertInstanceOf(\Exception::class, $exception);
    }

    public function testException_hasCorrectMessage(): void
    {
        $message = 'Invalid key pair data provided';
        $exception = new InvalidKeyPairDataException($message);
        $this->assertSame($message, $exception->getMessage());
    }

    public function testException_hasCorrectNamespace(): void
    {
        $this->assertSame('AwsLightsailBundle\\Exception\\InvalidKeyPairDataException', InvalidKeyPairDataException::class);
    }
}