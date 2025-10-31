<?php

declare(strict_types=1);

namespace AwsLightsailBundle\Tests\Entity;

use AwsLightsailBundle\Entity\ContactMethod;
use AwsLightsailBundle\Enum\ContactMethodStatusEnum;
use AwsLightsailBundle\Enum\ContactMethodTypeEnum;
use PHPUnit\Framework\Attributes\CoversClass;
use Tourze\PHPUnitDoctrineEntity\AbstractEntityTestCase;

/**
 * @internal
 */
#[CoversClass(ContactMethod::class)]
final class ContactMethodTest extends AbstractEntityTestCase
{
    private ContactMethod $entity;

    protected function setUp(): void
    {
        parent::setUp();

        $this->entity = new ContactMethod();
    }

    protected function createEntity(): object
    {
        return new ContactMethod();
    }

    public function testConstructorInitializesCorrectly(): void
    {
        $entity = new ContactMethod();
        $this->assertInstanceOf(ContactMethod::class, $entity);
    }

    public function testGetIdReturnsNullWhenNotPersisted(): void
    {
        $this->assertNull($this->entity->getId());
    }

    public function testEntityHasCorrectNamespace(): void
    {
        $this->assertSame('AwsLightsailBundle\Entity\ContactMethod', ContactMethod::class);
    }

    /**
     * @return iterable<array{string, mixed}>
     */
    public static function propertiesProvider(): iterable
    {
        return [
            'name'                 => ['name', 'test-contact-method'],
            'arn'                  => ['arn', 'arn:aws:lightsail:us-east-1:123456789012:ContactMethod/test-contact-method'],
            'type'                 => ['type', ContactMethodTypeEnum::EMAIL],
            'contactEndpoint'      => ['contactEndpoint', 'test@example.com'],
            'status'               => ['status', ContactMethodStatusEnum::VERIFIED],
            'region'               => ['region', 'us-east-1'],
            'protocol'             => ['protocol', 'https'],
            'lastVerificationTime' => ['lastVerificationTime', new \DateTimeImmutable('2023-01-01 12:00:00')],
            'syncTime'             => ['syncTime', new \DateTimeImmutable('2023-01-02 12:00:00')],
        ];
    }
}
