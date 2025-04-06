<?php

namespace QiniuStorageBundle\Enum;

enum TimeGranularity: string
{
    case MINUTE = '5min';
    case HOUR = 'hour';
    case DAY = 'day';
}
