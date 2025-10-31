<?php

namespace QiniuStorageBundle\Enum;

use Tourze\EnumExtra\Itemable;
use Tourze\EnumExtra\ItemTrait;
use Tourze\EnumExtra\Labelable;
use Tourze\EnumExtra\Selectable;
use Tourze\EnumExtra\SelectTrait;

enum IntelligentTieringTier: int implements Labelable, Itemable, Selectable
{
    use ItemTrait;
    use SelectTrait;
    case FREQUENT_ACCESS = 0;
    case INFREQUENT_ACCESS = 1;
    case ARCHIVE_DIRECT = 4;

    public function getLabel(): string
    {
        return match ($this) {
            self::FREQUENT_ACCESS => '频繁访问层',
            self::INFREQUENT_ACCESS => '不频繁访问层',
            self::ARCHIVE_DIRECT => '归档直读访问层',
        };
    }

    /**
     * 获取所有枚举的选项数组（用于下拉列表等）
     *
     * @return array<int, array{value: int, label: string}>
     */
    public static function toSelectItems(): array
    {
        $result = [];
        foreach (self::cases() as $case) {
            $result[] = [
                'value' => $case->value,
                'label' => $case->getLabel(),
            ];
        }

        return $result;
    }
}
