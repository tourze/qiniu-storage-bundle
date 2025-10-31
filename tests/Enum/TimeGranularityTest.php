<?php

namespace QiniuStorageBundle\Tests\Enum;

use PHPUnit\Framework\Attributes\CoversClass;
use QiniuStorageBundle\Enum\TimeGranularity;
use Tourze\PHPUnitEnum\AbstractEnumTestCase;

/**
 * @internal
 */
#[CoversClass(TimeGranularity::class)]
final class TimeGranularityTest extends AbstractEnumTestCase
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

        // 验证所有枚举值都有标签
        foreach (TimeGranularity::cases() as $case) {
            $label = $case->getLabel();
            $this->assertIsString($label);
            $this->assertNotEmpty($label);
        }
    }

    public function testToArray(): void
    {
        $array = TimeGranularity::MINUTE->toArray();

        $this->assertIsArray($array);
        $this->assertCount(2, $array);
        $this->assertArrayHasKey('value', $array);
        $this->assertArrayHasKey('label', $array);
        $this->assertSame('5min', $array['value']);
        $this->assertSame('5分钟', $array['label']);

        // 测试其他枚举值
        $hourArray = TimeGranularity::HOUR->toArray();
        $this->assertSame('hour', $hourArray['value']);
        $this->assertSame('小时', $hourArray['label']);

        $dayArray = TimeGranularity::DAY->toArray();
        $this->assertSame('day', $dayArray['value']);
        $this->assertSame('天', $dayArray['label']);
    }
}
