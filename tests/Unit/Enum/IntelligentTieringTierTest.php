<?php

namespace QiniuStorageBundle\Tests\Unit\Enum;

use PHPUnit\Framework\TestCase;
use QiniuStorageBundle\Enum\IntelligentTieringTier;

class IntelligentTieringTierTest extends TestCase
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
        
        // 验证所有枚举值都实现了 Selectable 接口
        foreach (IntelligentTieringTier::cases() as $case) {
            $this->assertInstanceOf(\Tourze\EnumExtra\Selectable::class, $case);
        }
    }
}