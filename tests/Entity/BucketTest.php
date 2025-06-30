<?php

declare(strict_types=1);

namespace QiniuStorageBundle\Tests\Entity;

use PHPUnit\Framework\TestCase;
use QiniuStorageBundle\Entity\Bucket;

/**
 * Bucket 实体测试类
 */
class BucketTest extends TestCase
{
    public function testConstructor(): void
    {
        $bucket = new Bucket();
        $this->assertInstanceOf(Bucket::class, $bucket);
    }
} 