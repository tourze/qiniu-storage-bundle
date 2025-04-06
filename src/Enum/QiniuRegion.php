<?php

namespace QiniuStorageBundle\Enum;

use Tourze\EnumExtra\Itemable;
use Tourze\EnumExtra\ItemTrait;
use Tourze\EnumExtra\Labelable;
use Tourze\EnumExtra\Selectable;
use Tourze\EnumExtra\SelectTrait;

/**
 * 七牛云存储区域枚举
 */
enum QiniuRegion: string implements Labelable, Itemable, Selectable
{
    use ItemTrait;
    use SelectTrait;

    /**
     * 华东-浙江
     */
    case HUADONG_ZHEJIANG = 'z0';

    /**
     * 华东-浙江2
     */
    case HUADONG_ZHEJIANG2 = 'cn-east-2';

    /**
     * 华北-河北
     */
    case HUABEI_HEBEI = 'z1';

    /**
     * 华南-广东
     * 
     */
    case HUANAN_GUANGDONG = 'z2';

    /**
     * 西北-陕西1
     */
    case XIBEI_SHAANXI1 = 'cn-northwest-1';

    /**
     * 北美-洛杉矶
     */
    case NORTH_AMERICA_LOS_ANGELES = 'na0';

    /**
     * 亚太-新加坡（原东南亚）
     */
    case ASIA_PACIFIC_SINGAPORE = 'as0';

    /**
     * 亚太-河内
     */
    case ASIA_PACIFIC_HANOI = 'ap-southeast-2';

    /**
     * 亚太-胡志明
     */
    case ASIA_PACIFIC_HO_CHI_MINH = 'ap-southeast-3';

    /**
     * 获取区域名称
     */
    public function getLabel(): string
    {
        return match($this) {
            self::HUADONG_ZHEJIANG => '华东-浙江',
            self::HUADONG_ZHEJIANG2 => '华东-浙江2',
            self::HUABEI_HEBEI => '华北-河北',
            self::HUANAN_GUANGDONG => '华南-广东',
            self::XIBEI_SHAANXI1 => '西北-陕西1',
            self::NORTH_AMERICA_LOS_ANGELES => '北美-洛杉矶',
            self::ASIA_PACIFIC_SINGAPORE => '亚太-新加坡',
            self::ASIA_PACIFIC_HANOI => '亚太-河内',
            self::ASIA_PACIFIC_HO_CHI_MINH => '亚太-胡志明',
        };
    }

    /**
     * 获取上传域名
     */
    public function getUploadDomain(): string
    {
        return match($this) {
            self::HUADONG_ZHEJIANG => 'up-z0.qiniup.com',
            self::HUADONG_ZHEJIANG2 => 'up-cn-east-2.qiniup.com',
            self::HUABEI_HEBEI => 'up-z1.qiniup.com',
            self::HUANAN_GUANGDONG => 'up-z2.qiniup.com',
            self::XIBEI_SHAANXI1 => 'up-cn-northwest-1.qiniup.com',
            self::NORTH_AMERICA_LOS_ANGELES => 'up-na0.qiniup.com',
            self::ASIA_PACIFIC_SINGAPORE => 'up-as0.qiniup.com',
            self::ASIA_PACIFIC_HANOI => 'up-ap-southeast-2.qiniup.com',
            self::ASIA_PACIFIC_HO_CHI_MINH => 'up-ap-southeast-3.qiniup.com',
        };
    }

    /**
     * 获取下载域名
     */
    public function getDownloadDomain(): string
    {
        return match($this) {
            self::HUADONG_ZHEJIANG => 'iovip-z0.qiniuio.com',
            self::HUADONG_ZHEJIANG2 => 'iovip-cn-east-2.qiniuio.com',
            self::HUABEI_HEBEI => 'iovip-z1.qiniuio.com',
            self::HUANAN_GUANGDONG => 'iovip-z2.qiniuio.com',
            self::XIBEI_SHAANXI1 => 'iovip-cn-northwest-1.qiniuio.com',
            self::NORTH_AMERICA_LOS_ANGELES => 'iovip-na0.qiniuio.com',
            self::ASIA_PACIFIC_SINGAPORE => 'iovip-as0.qiniuio.com',
            self::ASIA_PACIFIC_HANOI => 'iovip-ap-southeast-2.qiniuio.com',
            self::ASIA_PACIFIC_HO_CHI_MINH => 'iovip-ap-southeast-3.qiniuio.com',
        };
    }

    /**
     * 获取对象管理域名
     */
    public function getRsDomain(): string
    {
        return match($this) {
            self::HUADONG_ZHEJIANG => 'rs-z0.qiniuapi.com',
            self::HUADONG_ZHEJIANG2 => 'rs-cn-east-2.qiniuapi.com',
            self::HUABEI_HEBEI => 'rs-z1.qiniuapi.com',
            self::HUANAN_GUANGDONG => 'rs-z2.qiniuapi.com',
            self::XIBEI_SHAANXI1 => 'rs-cn-northwest-1.qiniuapi.com',
            self::NORTH_AMERICA_LOS_ANGELES => 'rs-na0.qiniuapi.com',
            self::ASIA_PACIFIC_SINGAPORE => 'rs-as0.qiniuapi.com',
            self::ASIA_PACIFIC_HANOI => 'rs-ap-southeast-2.qiniuapi.com',
            self::ASIA_PACIFIC_HO_CHI_MINH => 'rs-ap-southeast-3.qiniuapi.com',
        };
    }

    /**
     * 获取对象列举域名
     */
    public function getRsfDomain(): string
    {
        return match($this) {
            self::HUADONG_ZHEJIANG => 'rsf-z0.qiniuapi.com',
            self::HUADONG_ZHEJIANG2 => 'rsf-cn-east-2.qiniuapi.com',
            self::HUABEI_HEBEI => 'rsf-z1.qiniuapi.com',
            self::HUANAN_GUANGDONG => 'rsf-z2.qiniuapi.com',
            self::XIBEI_SHAANXI1 => 'rsf-cn-northwest-1.qiniuapi.com',
            self::NORTH_AMERICA_LOS_ANGELES => 'rsf-na0.qiniuapi.com',
            self::ASIA_PACIFIC_SINGAPORE => 'rsf-as0.qiniuapi.com',
            self::ASIA_PACIFIC_HANOI => 'rsf-ap-southeast-2.qiniuapi.com',
            self::ASIA_PACIFIC_HO_CHI_MINH => 'rsf-ap-southeast-3.qiniuapi.com',
        };
    }

    /**
     * 获取计量查询域名
     */
    public function getApiFaceDomain(): string
    {
        return match($this) {
            self::HUADONG_ZHEJIANG, 
            self::HUADONG_ZHEJIANG2, 
            self::HUABEI_HEBEI, 
            self::HUANAN_GUANGDONG, 
            self::NORTH_AMERICA_LOS_ANGELES, 
            self::ASIA_PACIFIC_SINGAPORE => 'api.qiniuapi.com',
            self::XIBEI_SHAANXI1 => 'api-cn-northwest-1.qiniuapi.com',
            self::ASIA_PACIFIC_HANOI => 'api-ap-southeast-2.qiniuapi.com',
            self::ASIA_PACIFIC_HO_CHI_MINH => 'api-ap-southeast-3.qiniuapi.com',
        };
    }

    /**
     * 获取所有区域
     */
    public static function getAllRegions(): array
    {
        return [
            self::HUADONG_ZHEJIANG,
            self::HUADONG_ZHEJIANG2,
            self::HUABEI_HEBEI,
            self::HUANAN_GUANGDONG,
            self::XIBEI_SHAANXI1,
            self::NORTH_AMERICA_LOS_ANGELES,
            self::ASIA_PACIFIC_SINGAPORE,
            self::ASIA_PACIFIC_HANOI,
            self::ASIA_PACIFIC_HO_CHI_MINH,
        ];
    }

    /**
     * 获取空间管理域名（所有区域通用）
     */
    public static function getUcDomain(): string
    {
        return 'uc.qiniuapi.com';
    }
}
