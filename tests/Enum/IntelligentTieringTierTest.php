<?php

namespace QiniuStorageBundle\Tests\Enum;

use PHPUnit\Framework\Attributes\CoversClass;
use QiniuStorageBundle\Enum\IntelligentTieringTier;
use Tourze\PHPUnitEnum\AbstractEnumTestCase;

/**
 * @internal
 */
#[CoversClass(IntelligentTieringTier::class)]
final class IntelligentTieringTierTest extends AbstractEnumTestCase
{
    public function testEnumValues(): void
    {
        $this->assertSame(0, IntelligentTieringTier::FREQUENT_ACCESS->value);
        $this->assertSame(1, IntelligentTieringTier::INFREQUENT_ACCESS->value);
        $this->assertSame(4, IntelligentTieringTier::ARCHIVE_DIRECT->value);
    }

    public function testGetLabel(): void
    {
        $this->assertSame('频繁访问层', IntelligentTieringTier::FREQUENT_ACCESS->getLabel());
        $this->assertSame('不频繁访问层', IntelligentTieringTier::INFREQUENT_ACCESS->getLabel());
        $this->assertSame('归档直读访问层', IntelligentTieringTier::ARCHIVE_DIRECT->getLabel());
    }

    public function testItemTraitMethods(): void
    {
        $cases = IntelligentTieringTier::cases();
        $this->assertCount(3, $cases);
        $this->assertContains(IntelligentTieringTier::FREQUENT_ACCESS, $cases);
        $this->assertContains(IntelligentTieringTier::INFREQUENT_ACCESS, $cases);
        $this->assertContains(IntelligentTieringTier::ARCHIVE_DIRECT, $cases);
    }

    public function testFromValue(): void
    {
        $this->assertSame(IntelligentTieringTier::FREQUENT_ACCESS, IntelligentTieringTier::from(0));
        $this->assertSame(IntelligentTieringTier::INFREQUENT_ACCESS, IntelligentTieringTier::from(1));
        $this->assertSame(IntelligentTieringTier::ARCHIVE_DIRECT, IntelligentTieringTier::from(4));
    }

    public function testSelectableTrait(): void
    {
        // 测试 SelectTrait 提供的基本功能
        $tier = IntelligentTieringTier::FREQUENT_ACCESS;
        $this->assertSame('频繁访问层', $tier->getLabel());

        // 验证所有枚举值都有标签
        foreach (IntelligentTieringTier::cases() as $case) {
            $label = $case->getLabel();
            $this->assertIsString($label);
            $this->assertNotEmpty($label);
        }
    }

    public function testToArray(): void
    {
        $array = IntelligentTieringTier::FREQUENT_ACCESS->toArray();

        $this->assertIsArray($array);
        $this->assertCount(2, $array);
        $this->assertArrayHasKey('value', $array);
        $this->assertArrayHasKey('label', $array);
        $this->assertSame(0, $array['value']);
        $this->assertSame('频繁访问层', $array['label']);

        // 测试其他枚举值
        $infrequentArray = IntelligentTieringTier::INFREQUENT_ACCESS->toArray();
        $this->assertSame(1, $infrequentArray['value']);
        $this->assertSame('不频繁访问层', $infrequentArray['label']);

        $archiveArray = IntelligentTieringTier::ARCHIVE_DIRECT->toArray();
        $this->assertSame(4, $archiveArray['value']);
        $this->assertSame('归档直读访问层', $archiveArray['label']);
    }

    public function testGenOptions(): void
    {
        $options = IntelligentTieringTier::genOptions();

        $this->assertIsArray($options);
        $this->assertCount(3, $options);

        // 验证每个选项的结构
        foreach ($options as $index => $option) {
            $this->assertIsArray($option);
            $this->assertArrayHasKey('value', $option);
            $this->assertArrayHasKey('label', $option);
            $this->assertArrayHasKey('text', $option);
            $this->assertArrayHasKey('name', $option);

            // 验证具体值
            match ($index) {
                0 => $this->assertSame(0, $option['value']),
                1 => $this->assertSame(1, $option['value']),
                2 => $this->assertSame(4, $option['value']),
                default => self::fail("Unexpected option index: {$index}"),
            };
        }

        // 验证选项顺序和内容
        $this->assertSame(0, $options[0]['value']);
        $this->assertSame('频繁访问层', $options[0]['label']);

        $this->assertSame(1, $options[1]['value']);
        $this->assertSame('不频繁访问层', $options[1]['label']);

        $this->assertSame(4, $options[2]['value']);
        $this->assertSame('归档直读访问层', $options[2]['label']);
    }
}
