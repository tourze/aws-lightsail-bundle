<?php

declare(strict_types=1);

namespace AwsLightsailBundle\Tests\Entity;

use AwsLightsailBundle\Entity\ContainerService;
use AwsLightsailBundle\Enum\ContainerServicePowerEnum;
use AwsLightsailBundle\Enum\ContainerServiceStateEnum;
use PHPUnit\Framework\Attributes\CoversClass;
use Tourze\PHPUnitDoctrineEntity\AbstractEntityTestCase;

/**
 * @internal
 */
#[CoversClass(ContainerService::class)]
final class ContainerServiceTest extends AbstractEntityTestCase
{
    private ContainerService $entity;

    protected function setUp(): void
    {
        parent::setUp();

        $this->entity = new ContainerService();
    }

    protected function createEntity(): object
    {
        return new ContainerService();
    }

    public function testConstructorInitializesCorrectly(): void
    {
        $entity = new ContainerService();
        $this->assertInstanceOf(ContainerService::class, $entity);
    }

    public function testGetIdReturnsNullWhenNotPersisted(): void
    {
        $this->assertNull($this->entity->getId());
    }

    public function testEntityHasCorrectNamespace(): void
    {
        $this->assertSame('AwsLightsailBundle\Entity\ContainerService', ContainerService::class);
    }

    /**
     * @return iterable<array{string, mixed}>
     */
    public static function propertiesProvider(): iterable
    {
        return [
            'name'              => ['name', 'test-container-service'],
            'arn'               => ['arn', 'arn:aws:lightsail:us-east-1:123456789012:ContainerService/test-container-service'],
            'power'             => ['power', ContainerServicePowerEnum::MICRO],
            'scale'             => ['scale', 2],
            'state'             => ['state', ContainerServiceStateEnum::RUNNING],
            'region'            => ['region', 'us-east-1'],
            'url'               => ['url', 'https://test-container-service.service.region.amazonaws.com'],
            'currentDeployment' => ['currentDeployment', ['version' => '1.0', 'containers' => ['web' => ['image' => 'nginx:latest']]]],
            'nextDeployment'    => ['nextDeployment', ['version' => '2.0', 'containers' => ['web' => ['image' => 'nginx:2.0']]]],
            // 注意：isPublicDomainEnabled 和 isPrivateDomainEnabled 属性暂时排除，
            // 因为它们的 setter 方法使用了 setIs 前缀，不符合 AbstractEntityTest 的约定
            'privateDomainName' => ['privateDomainName', ['name' => 'container.local', 'type' => 'A']],
            'publicDomainNames' => ['publicDomainNames', 'container.example.com'],
            'containerImages'   => ['containerImages', [['image' => 'nginx:latest', 'containerName' => 'web']]],
            'tags'              => ['tags', ['Environment' => 'production', 'Project' => 'test']],
            'syncTime'          => ['syncTime', new \DateTimeImmutable('2023-01-01 12:00:00')],
        ];
    }
}
