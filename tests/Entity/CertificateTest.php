<?php

declare(strict_types=1);

namespace AwsLightsailBundle\Tests\Entity;

use AwsLightsailBundle\Entity\Certificate;
use AwsLightsailBundle\Enum\CertificateStatusEnum;
use PHPUnit\Framework\Attributes\CoversClass;
use Tourze\PHPUnitDoctrineEntity\AbstractEntityTestCase;

/**
 * @internal
 */
#[CoversClass(Certificate::class)]
final class CertificateTest extends AbstractEntityTestCase
{
    private Certificate $entity;

    protected function setUp(): void
    {
        parent::setUp();

        $this->entity = new Certificate();
    }

    protected function createEntity(): object
    {
        return new Certificate();
    }

    public function testConstructorInitializesCorrectly(): void
    {
        $entity = new Certificate();
        $this->assertInstanceOf(Certificate::class, $entity);
    }

    public function testGetIdReturnsNullWhenNotPersisted(): void
    {
        $this->assertNull($this->entity->getId());
    }

    public function testEntityHasCorrectNamespace(): void
    {
        $this->assertSame('AwsLightsailBundle\Entity\Certificate', Certificate::class);
    }

    /**
     * @return iterable<array{string, mixed}>
     */
    public static function propertiesProvider(): iterable
    {
        return [
            'name'                    => ['name', 'test-certificate'],
            'arn'                     => ['arn', 'arn:aws:lightsail:us-east-1:123456789012:Certificate/test-certificate'],
            'domainName'              => ['domainName', 'example.com'],
            'subjectAlternativeNames' => ['subjectAlternativeNames', ['www.example.com', 'api.example.com']],
            'domainValidationRecords' => ['domainValidationRecords', [
                ['domainName' => 'example.com', 'resourceRecord' => ['name' => '_amazonses.example.com', 'type' => 'CNAME', 'value' => 'amazonses.com']],
            ]],
            'status'        => ['status', CertificateStatusEnum::PENDING_VALIDATION],
            'region'        => ['region', 'us-east-1'],
            'validFromTime' => ['validFromTime', new \DateTimeImmutable('2023-01-01 00:00:00')],
            'validToTime'   => ['validToTime', new \DateTimeImmutable('2024-01-01 00:00:00')],
            'tags'          => ['tags', ['Environment' => 'production', 'Project' => 'test']],
            'serialNumber'  => ['serialNumber', '1234567890ABCDEF1234567890ABCDEF'],
            'keyAlgorithm'  => ['keyAlgorithm', ['name' => 'RSA', 'size' => 2048]],
            // 注意：isManaged 属性暂时排除，因为它的 setter 方法使用了 setIs 前缀，不符合 AbstractEntityTest 的约定
            'inUse'                => ['inUse', true],
            'syncTime'             => ['syncTime', new \DateTimeImmutable('2023-01-01 12:00:00')],
            'supportedOnResources' => ['supportedOnResources', ['instance-1', 'load-balancer-1']],
        ];
    }
}
