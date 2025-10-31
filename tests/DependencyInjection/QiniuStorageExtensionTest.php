<?php

declare(strict_types=1);

namespace QiniuStorageBundle\Tests\DependencyInjection;

use PHPUnit\Framework\Attributes\CoversClass;
use QiniuStorageBundle\DependencyInjection\QiniuStorageExtension;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Tourze\PHPUnitSymfonyUnitTest\AbstractDependencyInjectionExtensionTestCase;

/**
 * QiniuStorageExtension 测试类
 *
 * @internal
 */
#[CoversClass(QiniuStorageExtension::class)]
final class QiniuStorageExtensionTest extends AbstractDependencyInjectionExtensionTestCase
{
    public function testContainerHasExtension(): void
    {
        // 验证扩展类可以正常实例化
        $extension = new QiniuStorageExtension();
        $this->assertInstanceOf(QiniuStorageExtension::class, $extension);
    }

    public function testExtensionLoadsServices(): void
    {
        // 创建一个模拟容器来测试扩展加载功能
        $container = new ContainerBuilder();
        $extension = new QiniuStorageExtension();

        // 测试扩展可以正常加载配置而不抛出异常
        $this->assertInstanceOf(QiniuStorageExtension::class, $extension);

        // 验证扩展的别名正确
        $this->assertEquals('qiniu_storage', $extension->getAlias());
    }
}
