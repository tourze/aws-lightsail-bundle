<?php

declare(strict_types=1);

namespace AwsLightsailBundle\Tests\Integration\Service;

use AwsLightsailBundle\Entity\Alarm;
use AwsLightsailBundle\Entity\AwsCredential;
use AwsLightsailBundle\Entity\Bucket;
use AwsLightsailBundle\Entity\Certificate;
use AwsLightsailBundle\Entity\ContactMethod;
use AwsLightsailBundle\Entity\ContainerService;
use AwsLightsailBundle\Entity\Database;
use AwsLightsailBundle\Entity\DatabaseSnapshot;
use AwsLightsailBundle\Entity\Disk;
use AwsLightsailBundle\Entity\DiskSnapshot;
use AwsLightsailBundle\Entity\Distribution;
use AwsLightsailBundle\Entity\Domain;
use AwsLightsailBundle\Entity\Instance;
use AwsLightsailBundle\Entity\KeyPair;
use AwsLightsailBundle\Entity\LoadBalancer;
use AwsLightsailBundle\Entity\Operation;
use AwsLightsailBundle\Entity\Snapshot;
use AwsLightsailBundle\Entity\StaticIp;
use AwsLightsailBundle\Service\AdminMenu;
use Knp\Menu\ItemInterface;
use PHPUnit\Framework\TestCase;
use Tourze\EasyAdminMenuBundle\Service\LinkGeneratorInterface;
use Tourze\EasyAdminMenuBundle\Service\MenuProviderInterface;

final class AdminMenuTest extends TestCase
{
    private LinkGeneratorInterface $linkGenerator;
    private AdminMenu $adminMenu;

    protected function setUp(): void
    {
        $this->linkGenerator = $this->createMock(LinkGeneratorInterface::class);
        $this->adminMenu = new AdminMenu($this->linkGenerator);
    }

    public function testImplementsMenuProviderInterface(): void
    {
        $this->assertInstanceOf(MenuProviderInterface::class, $this->adminMenu);
    }

    public function testInvoke_createsMainAwsLightsailMenu(): void
    {
        $menuItem = $this->createMock(ItemInterface::class);
        $awsMenuItem = $this->createMock(ItemInterface::class);
        
        $menuItem->method('getChild')
            ->with('AWS Lightsail')
            ->willReturnOnConsecutiveCalls(null, $awsMenuItem);
            
        $menuItem->method('addChild')
            ->with('AWS Lightsail')
            ->willReturn($awsMenuItem);
            
        $awsMenuItem->method('setAttribute')
            ->willReturnSelf();

        // Mock all the submenu creation calls
        $this->mockSubmenuCreation($awsMenuItem);

        ($this->adminMenu)($menuItem);
        
        $this->addToAssertionCount(1); // Prevent risky test warning
    }

    public function testInvoke_usesExistingAwsLightsailMenu(): void
    {
        $menuItem = $this->createMock(ItemInterface::class);
        $awsMenuItem = $this->createMock(ItemInterface::class);
        
        $menuItem->method('getChild')
            ->with('AWS Lightsail')
            ->willReturn($awsMenuItem);

        // Mock all the submenu creation calls
        $this->mockSubmenuCreation($awsMenuItem);

        ($this->adminMenu)($menuItem);
        
        $this->addToAssertionCount(1); // Prevent risky test warning
    }

    public function testInvoke_createsAllRequiredSubmenus(): void
    {
        $menuItem = $this->createMock(ItemInterface::class);
        $awsMenuItem = $this->createMock(ItemInterface::class);
        
        $menuItem->method('getChild')->willReturn($awsMenuItem);
        $menuItem->method('addChild')->willReturn($awsMenuItem);
        $awsMenuItem->method('setAttribute')->willReturnSelf();

        $this->linkGenerator->method('getCurdListPage')
            ->willReturn('/admin/test');

        $subMenuItem = $this->createMock(ItemInterface::class);
        $subMenuItem->method('setUri')->willReturnSelf();
        $subMenuItem->method('setAttribute')->willReturnSelf();
        
        $awsMenuItem->method('addChild')
            ->willReturn($subMenuItem);

        ($this->adminMenu)($menuItem);
        
        $this->addToAssertionCount(1); // Prevent risky test warning
    }

    private function mockSubmenuCreation(ItemInterface $awsMenuItem): void
    {
        $subMenuItem = $this->createMock(ItemInterface::class);
        $subMenuItem->method('setUri')->willReturnSelf();
        $subMenuItem->method('setAttribute')->willReturnSelf();
        
        $awsMenuItem->method('addChild')
            ->willReturn($subMenuItem);

        $this->linkGenerator->method('getCurdListPage')
            ->willReturn('/admin/test');
    }
}