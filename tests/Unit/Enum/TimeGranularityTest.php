<?php

namespace QiniuStorageBundle\Tests\Unit\Enum;

use PHPUnit\Framework\TestCase;
use QiniuStorageBundle\Enum\TimeGranularity;

class TimeGranularityTest extends TestCase
{
    public function testEnumValues(): void
    {
        $this->assertSame('5min', TimeGranularity::MINUTE->value);
        $this->assertSame('hour', TimeGranularity::HOUR->value);
        $this->assertSame('day', TimeGranularity::DAY->value);
    }

    public function testGetLabel(): void
    {
        $this->assertSame('5分钟', TimeGranularity::MINUTE->getLabel());
        $this->assertSame('小时', TimeGranularity::HOUR->getLabel());
        $this->assertSame('天', TimeGranularity::DAY->getLabel());
    }

    public function testItemTraitMethods(): void
    {
        $cases = TimeGranularity::cases();
        $this->assertCount(3, $cases);
        $this->assertContains(TimeGranularity::MINUTE, $cases);
        $this->assertContains(TimeGranularity::HOUR, $cases);
        $this->assertContains(TimeGranularity::DAY, $cases);
    }

    public function testFromValue(): void
    {
        $this->assertSame(TimeGranularity::MINUTE, TimeGranularity::from('5min'));
        $this->assertSame(TimeGranularity::HOUR, TimeGranularity::from('hour'));
        $this->assertSame(TimeGranularity::DAY, TimeGranularity::from('day'));
    }

    public function testSelectableTrait(): void
    {
        // 测试 SelectTrait 提供的基本功能
        $granularity = TimeGranularity::MINUTE;
        $this->assertSame('5分钟', $granularity->getLabel());
        
        // 验证所有枚举值都实现了 Selectable 接口
        foreach (TimeGranularity::cases() as $case) {
            $this->assertInstanceOf(\Tourze\EnumExtra\Selectable::class, $case);
        }
    }
}