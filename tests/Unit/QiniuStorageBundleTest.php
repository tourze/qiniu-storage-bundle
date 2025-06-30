<?php

namespace QiniuStorageBundle\Tests\Unit;

use PHPUnit\Framework\TestCase;
use QiniuStorageBundle\QiniuStorageBundle;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class QiniuStorageBundleTest extends TestCase
{
    public function testBundleExtendsSymfonyBundle(): void
    {
        $bundle = new QiniuStorageBundle();
        $this->assertInstanceOf(Bundle::class, $bundle);
    }

    public function testBundleGetPath(): void
    {
        $bundle = new QiniuStorageBundle();
        $path = $bundle->getPath();
        $this->assertStringContainsString('qiniu-storage-bundle', $path);
    }

    public function testBundleGetName(): void
    {
        $bundle = new QiniuStorageBundle();
        $this->assertSame('QiniuStorageBundle', $bundle->getName());
    }

    public function testBundleGetNamespace(): void
    {
        $bundle = new QiniuStorageBundle();
        $this->assertSame('QiniuStorageBundle', $bundle->getNamespace());
    }
}