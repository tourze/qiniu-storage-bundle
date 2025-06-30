<?php

declare(strict_types=1);

namespace QiniuStorageBundle\Tests\Entity;

use PHPUnit\Framework\TestCase;
use QiniuStorageBundle\Entity\BucketMinuteStatistic;

/**
 * BucketMinuteStatistic 实体测试类
 */
class BucketMinuteStatisticTest extends TestCase
{
    public function testConstructor(): void
    {
        $entity = new BucketMinuteStatistic();
        $this->assertInstanceOf(BucketMinuteStatistic::class, $entity);
    }
} 