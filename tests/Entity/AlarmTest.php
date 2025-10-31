<?php

declare(strict_types=1);

namespace AwsLightsailBundle\Tests\Entity;

use AwsLightsailBundle\Entity\Alarm;
use AwsLightsailBundle\Enum\AlarmMetricEnum;
use AwsLightsailBundle\Enum\AlarmStateEnum;
use PHPUnit\Framework\Attributes\CoversClass;
use Tourze\PHPUnitDoctrineEntity\AbstractEntityTestCase;

/**
 * @internal
 */
#[CoversClass(Alarm::class)]
final class AlarmTest extends AbstractEntityTestCase
{
    private Alarm $entity;

    protected function setUp(): void
    {
        parent::setUp();

        $this->entity = new Alarm();
    }

    protected function createEntity(): object
    {
        return new Alarm();
    }

    public function testConstructorInitializesCorrectly(): void
    {
        $entity = new Alarm();
        $this->assertInstanceOf(Alarm::class, $entity);
    }

    public function testGetIdReturnsNullWhenNotPersisted(): void
    {
        $this->assertNull($this->entity->getId());
    }

    public function testEntityHasCorrectNamespace(): void
    {
        $this->assertSame('AwsLightsailBundle\Entity\Alarm', Alarm::class);
    }

    /**
     * @return iterable<array{string, mixed}>
     */
    public static function propertiesProvider(): iterable
    {
        return [
            'name'                      => ['name', 'test-alarm'],
            'arn'                       => ['arn', 'arn:aws:lightsail:us-east-1:123456789012:Alarm/test-alarm'],
            'resourceName'              => ['resourceName', 'test-instance'],
            'resourceType'              => ['resourceType', 'Instance'],
            'metricName'                => ['metricName', AlarmMetricEnum::CPU_UTILIZATION],
            'state'                     => ['state', AlarmStateEnum::ALARM],
            'region'                    => ['region', 'us-east-1'],
            'comparisonOperator'        => ['comparisonOperator', 'GreaterThanOrEqualToThreshold'],
            'evaluationPeriods'         => ['evaluationPeriods', '1'],
            'threshold'                 => ['threshold', 80.5],
            'treatMissingData'          => ['treatMissingData', 'notBreaching'],
            'contactProtocols'          => ['contactProtocols', ['email']],
            'monitoredResourceInfo'     => ['monitoredResourceInfo', ['resourceName' => 'test-instance']],
            'datapointsToAlarm'         => ['datapointsToAlarm', [1, 2, 3]],
            'notificationEnabled'       => ['notificationEnabled', true],
            'notificationTriggeredTime' => ['notificationTriggeredTime', new \DateTimeImmutable('2023-01-01 12:00:00')],
            'syncTime'                  => ['syncTime', new \DateTimeImmutable('2023-01-02 12:00:00')],
        ];
    }
}
