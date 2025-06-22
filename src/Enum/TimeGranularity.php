<?php

namespace QiniuStorageBundle\Enum;

use Tourze\EnumExtra\Itemable;
use Tourze\EnumExtra\ItemTrait;
use Tourze\EnumExtra\Labelable;
use Tourze\EnumExtra\Selectable;
use Tourze\EnumExtra\SelectTrait;

enum TimeGranularity: string implements Itemable, Labelable, Selectable
{
    use ItemTrait;
    use SelectTrait;
    
    case MINUTE = '5min';
    case HOUR = 'hour';
    case DAY = 'day';

    public function getLabel(): string
    {
        return match($this) {
            self::MINUTE => '5分钟',
            self::HOUR => '小时',
            self::DAY => '天',
        };
    }
}
