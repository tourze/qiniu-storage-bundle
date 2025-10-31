<?php

declare(strict_types=1);

namespace QiniuStorageBundle\Tests;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use QiniuStorageBundle\QiniuStorageBundle;
use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;
use Tourze\PHPUnitSymfonyKernelTest\AbstractBundleTestCase;

/**
 * @internal
 */
#[CoversClass(QiniuStorageBundle::class)]
#[RunTestsInSeparateProcesses]
final class QiniuStorageBundleTest extends AbstractBundleTestCase
{
    /**
     * 测试 Bundle 具有有效的容器扩展。
     * 这是一个不需要内核启动的基本功能测试。
     */
    public function testBundleHasValidContainerExtension(): void
    {
        // @phpstan-ignore integrationTest.noDirectInstantiationOfCoveredClass
        $bundle = new QiniuStorageBundle();
        $extension = $bundle->getContainerExtension();

        // 扩展可以为 null，但如果存在，应该是正确的类型
        if (null !== $extension) {
            $this->assertInstanceOf(ExtensionInterface::class, $extension);
        }
    }

    /**
     * 测试 Bundle 路径设置正确。
     * 这验证了基本的 Bundle 配置，没有内核依赖。
     */
    public function testBundlePathIsCorrect(): void
    {
        // @phpstan-ignore integrationTest.noDirectInstantiationOfCoveredClass
        $bundle = new QiniuStorageBundle();
        $path = $bundle->getPath();

        $this->assertStringContainsString('qiniu-storage-bundle', $path);
        $this->assertStringContainsString('src', $path);
        $this->assertDirectoryExists($path);
    }
}
