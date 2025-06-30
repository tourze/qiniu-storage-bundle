<?php

declare(strict_types=1);

namespace QiniuStorageBundle\Tests\DependencyInjection;

use PHPUnit\Framework\TestCase;
use QiniuStorageBundle\DependencyInjection\QiniuStorageExtension;

/**
 * QiniuStorageExtension 测试类
 */
class QiniuStorageExtensionTest extends TestCase
{
    public function testConstructor(): void
    {
        $extension = new QiniuStorageExtension();
        $this->assertInstanceOf(QiniuStorageExtension::class, $extension);
    }
} 