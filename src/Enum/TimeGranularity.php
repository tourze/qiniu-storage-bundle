<?php

namespace QiniuStorageBundle\Enum;

use Tourze\EnumExtra\Itemable;
use Tourze\EnumExtra\ItemTrait;
use Tourze\EnumExtra\Labelable;
use Tourze\EnumExtra\Selectable;
use Tourze\EnumExtra\SelectTrait;

enum TimeGranularity: string implements Labelable, Itemable, Selectable
{
    use ItemTrait;
    use SelectTrait;

    case MINUTE = '5min';
    case HOUR = 'hour';
    case DAY = 'day';

    public function getLabel(): string
    {
        return match ($this) {
            self::MINUTE => '5分钟',
            self::HOUR => '小时',
            self::DAY => '天',
        };
    }

    /**
     * 获取所有枚举的选项数组（用于下拉列表等）
     *
     * @return array<int, array{value: string, label: string}>
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
