<?php

declare(strict_types=1);

namespace AwsLightsailBundle\Tests\Service;

use AwsLightsailBundle\Service\AdminMenu;
use Knp\Menu\ItemInterface;
use PHPUnit\Framework\TestCase;
use Tourze\EasyAdminMenuBundle\Service\LinkGeneratorInterface;

final class AdminMenuTest extends TestCase
{
    private AdminMenu $adminMenu;

    protected function setUp(): void
    {
        $linkGenerator = $this->createMock(LinkGeneratorInterface::class);
        $linkGenerator->method('getCurdListPage')->willReturn('/admin/test');
        $this->adminMenu = new AdminMenu($linkGenerator);
    }

    public function testAdminMenuIsCallable(): void
    {
        $menuItem = $this->createMock(ItemInterface::class);
        $awsMenuItem = $this->createMock(ItemInterface::class);
        
        // 配置主菜单项的行为
        $menuItem->expects($this->exactly(2))
            ->method('getChild')
            ->with('AWS Lightsail')
            ->willReturnOnConsecutiveCalls(null, $awsMenuItem);
            
        $menuItem->expects($this->once())
            ->method('addChild')
            ->with('AWS Lightsail')
            ->willReturn($awsMenuItem);
        
        // 配置 AWS 菜单项的行为
        $awsMenuItem->expects($this->once())
            ->method('setAttribute')
            ->with('icon', 'fa fa-cloud')
            ->willReturnSelf();
            
        // 创建子菜单项的 mock
        $childMenuItem = $this->createMock(ItemInterface::class);
        $childMenuItem->method('setUri')->willReturnSelf();
        $childMenuItem->method('setAttribute')->willReturnSelf();
            
        $awsMenuItem->expects($this->atLeastOnce())
            ->method('addChild')
            ->willReturn($childMenuItem);
        
        // 测试调用 AdminMenu
        ($this->adminMenu)($menuItem);
        
        // 验证AdminMenu服务被正确实例化
        $this->assertInstanceOf(AdminMenu::class, $this->adminMenu);
    }
}