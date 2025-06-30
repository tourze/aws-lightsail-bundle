<?php

declare(strict_types=1);

namespace AwsLightsailBundle\Tests\Unit\Exception;

use AwsLightsailBundle\Exception\InvalidInstanceDataException;
use PHPUnit\Framework\TestCase;

final class InvalidInstanceDataExceptionTest extends TestCase
{
    public function testException_isInstantiable(): void
    {
        $exception = new InvalidInstanceDataException('Test message');
        $this->assertInstanceOf(InvalidInstanceDataException::class, $exception);
    }

    public function testException_extendsException(): void
    {
        $exception = new InvalidInstanceDataException('Test message');
        $this->assertInstanceOf(\Exception::class, $exception);
    }

    public function testException_hasCorrectMessage(): void
    {
        $message = 'Invalid instance data provided';
        $exception = new InvalidInstanceDataException($message);
        $this->assertSame($message, $exception->getMessage());
    }

    public function testException_hasCorrectNamespace(): void
    {
        $this->assertSame('AwsLightsailBundle\\Exception\\InvalidInstanceDataException', InvalidInstanceDataException::class);
    }
}