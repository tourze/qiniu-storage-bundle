<?php

declare(strict_types=1);

namespace QiniuStorageBundle\Tests\Entity;

use PHPUnit\Framework\TestCase;
use QiniuStorageBundle\Entity\BucketHourStatistic;

/**
 * BucketHourStatistic 实体测试类
 */
class BucketHourStatisticTest extends TestCase
{
    public function testConstructor(): void
    {
        $entity = new BucketHourStatistic();
        $this->assertInstanceOf(BucketHourStatistic::class, $entity);
    }
} 