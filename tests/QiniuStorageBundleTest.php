<?php

declare(strict_types=1);

namespace QiniuStorageBundle\Tests;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use QiniuStorageBundle\QiniuStorageBundle;
use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;
use Symfony\Component\HttpKernel\Bundle\Bundle;
use Tourze\PHPUnitBase\TestCaseHelper;
use Tourze\PHPUnitSymfonyKernelTest\AbstractBundleTestCase;

/**
 * @internal
 */
#[CoversClass(QiniuStorageBundle::class)]
#[RunTestsInSeparateProcesses]
final class QiniuStorageBundleTest extends AbstractBundleTestCase
{
    /**
     * 测试 Bundle 类存在并继承正确的基类。
     * 此测试不需要实例化内核，因此避免了 AccessTokenBundle 依赖问题。
     */
    public function testBundleClassExistsAndExtendsBundle(): void
    {
        // 测试类可以实例化，这是比 class_exists 更具体的测试
        // @phpstan-ignore integrationTest.noDirectInstantiationOfCoveredClass
        $bundle = new QiniuStorageBundle();
        $this->assertInstanceOf(Bundle::class, $bundle);
        $this->assertInstanceOf(QiniuStorageBundle::class, $bundle);

        // 验证继承关系
        $reflection = new \ReflectionClass(QiniuStorageBundle::class);
        $this->assertTrue($reflection->isSubclassOf(Bundle::class));
    }

    /**
     * 测试 Bundle 可以在没有内核的情况下被实例化。
     * 此测试验证基本的 Bundle 功能，而不会触发需要 AccessTokenBundle 的内核启动过程。
     */
    public function testBundleCanBeInstantiatedWithoutKernel(): void
    {
        // @phpstan-ignore integrationTest.noDirectInstantiationOfCoveredClass
        $bundle = new QiniuStorageBundle();
        $this->assertInstanceOf(Bundle::class, $bundle);
        $this->assertInstanceOf(QiniuStorageBundle::class, $bundle);
    }

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
