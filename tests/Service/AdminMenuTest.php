<?php

declare(strict_types=1);

namespace QiniuStorageBundle\Tests\Service;

use Knp\Menu\ItemInterface;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use QiniuStorageBundle\Service\AdminMenu;
use Tourze\PHPUnitSymfonyWebTest\AbstractEasyAdminMenuTestCase;

/**
 * AdminMenu 单元测试
 *
 * @internal
 */
#[CoversClass(AdminMenu::class)]
#[RunTestsInSeparateProcesses]
final class AdminMenuTest extends AbstractEasyAdminMenuTestCase
{
    private ItemInterface $item;

    public function testInvokeMethod(): void
    {
        // 测试 AdminMenu 的 __invoke 方法正常工作
        $this->expectNotToPerformAssertions();

        try {
            $adminMenu = self::getService(AdminMenu::class);
            ($adminMenu)($this->item);
        } catch (\Throwable $e) {
            self::fail('AdminMenu __invoke method should not throw exception: ' . $e->getMessage());
        }
    }

    protected function onSetUp(): void
    {
        // 创建 ItemInterface 的 mock - 遵循项目的静态分析规则
        $this->item = $this->createMock(ItemInterface::class);
    }
}
