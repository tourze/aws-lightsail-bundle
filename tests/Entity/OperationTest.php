<?php

declare(strict_types=1);

namespace AwsLightsailBundle\Tests\Entity;

use AwsLightsailBundle\Entity\Operation;
use AwsLightsailBundle\Enum\OperationStatusEnum;
use AwsLightsailBundle\Enum\OperationTypeEnum;
use PHPUnit\Framework\Attributes\CoversClass;
use Tourze\PHPUnitDoctrineEntity\AbstractEntityTestCase;

/**
 * @internal
 */
#[CoversClass(Operation::class)]
final class OperationTest extends AbstractEntityTestCase
{
    private Operation $entity;

    protected function setUp(): void
    {
        parent::setUp();

        $this->entity = new Operation();
    }

    protected function createEntity(): object
    {
        return new Operation();
    }

    public function testConstructorInitializesCorrectly(): void
    {
        $entity = new Operation();
        $this->assertInstanceOf(Operation::class, $entity);
    }

    public function testGetIdReturnsNullWhenNotPersisted(): void
    {
        $this->assertNull($this->entity->getId());
    }

    public function testEntityHasCorrectNamespace(): void
    {
        $this->assertSame('AwsLightsailBundle\Entity\Operation', Operation::class);
    }

    /**
     * @return iterable<array{string, mixed}>
     */
    public static function propertiesProvider(): iterable
    {
        return [
            'operationId'    => ['operationId', 'op-1234567890abcdef0'],
            'resourceName'   => ['resourceName', 'test-instance'],
            'resourceType'   => ['resourceType', 'Instance'],
            'type'           => ['type', OperationTypeEnum::CREATE_INSTANCE],
            'status'         => ['status', OperationStatusEnum::SUCCEEDED],
            'region'         => ['region', 'us-east-1'],
            'errorCode'      => ['errorCode', 'InvalidParameterValue'],
            'errorDetails'   => ['errorDetails', 'The specified parameter value is invalid'],
            'completionTime' => ['completionTime', new \DateTimeImmutable('2023-01-01 12:00:00')],
            'metadata'       => ['metadata', ['createdBy' => 'system', 'priority' => 'high']],
        ];
    }
}
