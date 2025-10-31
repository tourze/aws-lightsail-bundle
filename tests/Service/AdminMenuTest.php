<?php

declare(strict_types=1);

namespace AwsLightsailBundle\Tests\Service;

use AwsLightsailBundle\Service\AdminMenu;
use Knp\Menu\ItemInterface;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use PHPUnit\Framework\MockObject\MockObject;
use Tourze\EasyAdminMenuBundle\Service\LinkGeneratorInterface;
use Tourze\PHPUnitSymfonyWebTest\AbstractEasyAdminMenuTestCase;

/**
 * @internal
 */
#[CoversClass(AdminMenu::class)]
#[RunTestsInSeparateProcesses]
final class AdminMenuTest extends AbstractEasyAdminMenuTestCase
{
    protected function onSetUp(): void
    {
        // 设置 LinkGenerator 的匿名类实现
        $linkGenerator = new class () implements LinkGeneratorInterface {
            public function getCurdListPage(string $entityClass): string
            {
                return '/admin/test';
            }

            public function extractEntityFqcn(string $entityClass): string
            {
                return $entityClass;
            }

            public function setDashboard(string $dashboardControllerFqcn): void
            {
                // 实现接口要求，但测试中不需要存储
            }
        };

        self::getContainer()->set(LinkGeneratorInterface::class, $linkGenerator);
    }

    public function testAdminMenuIsCallable(): void
    {
        // 创建AWS菜单项Mock
        /** @var MockObject&ItemInterface $awsMenuItem */
        $awsMenuItem = $this->createMock(ItemInterface::class);

        // AWS菜单项会被设置icon属性
        $awsMenuItem->expects($this->once())
            ->method('setAttribute')
            ->with('icon', 'fa fa-cloud')
            ->willReturnSelf()
        ;

        // 为所有子菜单创建Mock，这些会被addChild调用
        /** @var MockObject&ItemInterface $childMenuItem */
        $childMenuItem = $this->createMock(ItemInterface::class);
        $childMenuItem->method('setUri')->willReturnSelf();
        $childMenuItem->method('setAttribute')->willReturnSelf();

        // AWS菜单项会添加多个子菜单
        $awsMenuItem->expects($this->exactly(19))
            ->method('addChild')
            ->willReturn($childMenuItem)
        ;

        // 创建主菜单项Mock
        /** @var MockObject&ItemInterface $menuItem */
        $menuItem = $this->createMock(ItemInterface::class);

        // 第一次调用getChild返回null，第二次返回awsMenuItem
        $menuItem->expects($this->exactly(2))
            ->method('getChild')
            ->with('AWS Lightsail')
            ->willReturnOnConsecutiveCalls(null, $awsMenuItem)
        ;

        // 添加主菜单项会被调用一次，返回awsMenuItem
        $menuItem->expects($this->once())
            ->method('addChild')
            ->with('AWS Lightsail')
            ->willReturn($awsMenuItem)
        ;

        // 从容器获取 AdminMenu 服务并测试
        $adminMenu = self::getService(AdminMenu::class);
        $adminMenu($menuItem);

        // 验证AdminMenu服务被正确实例化
        $this->assertInstanceOf(AdminMenu::class, $adminMenu);
    }
}
