<?php

declare(strict_types=1);

namespace QiniuStorageBundle\Tests\Entity;

use PHPUnit\Framework\TestCase;
use QiniuStorageBundle\Entity\BucketDayStatistic;

/**
 * BucketDayStatistic 实体测试类
 */
class BucketDayStatisticTest extends TestCase
{
    public function testConstructor(): void
    {
        $entity = new BucketDayStatistic();
        $this->assertInstanceOf(BucketDayStatistic::class, $entity);
    }
} 