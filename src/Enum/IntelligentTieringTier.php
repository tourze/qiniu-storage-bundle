<?php

namespace QiniuStorageBundle\Enum;

enum IntelligentTieringTier: int
{
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
