<?php

namespace QiniuStorageBundle\Enum;

use Tourze\EnumExtra\Itemable;
use Tourze\EnumExtra\ItemTrait;
use Tourze\EnumExtra\Labelable;
use Tourze\EnumExtra\Selectable;
use Tourze\EnumExtra\SelectTrait;

enum IntelligentTieringTier: int implements Itemable, Labelable, Selectable
{
    use ItemTrait;
    use SelectTrait;
    case FREQUENT_ACCESS = 0;      // 频繁访问层
    case INFREQUENT_ACCESS = 1;    // 不频繁访问层
    case ARCHIVE_DIRECT = 4;       // 归档直读访问层

    public function getLabel(): string
    {
        return match($this) {
            self::FREQUENT_ACCESS => '频繁访问层',
            self::INFREQUENT_ACCESS => '不频繁访问层',
            self::ARCHIVE_DIRECT => '归档直读访问层',
        };
    }
}
