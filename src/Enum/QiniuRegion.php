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

    case HUADONG_ZHEJIANG = 'z0';
    case HUADONG_ZHEJIANG2 = 'cn-east-2';
    case HUABEI_HEBEI = 'z1';
    case HUANAN_GUANGDONG = 'z2';
    case XIBEI_SHAANXI1 = 'cn-northwest-1';
    case NORTH_AMERICA_LOS_ANGELES = 'na0';
    case ASIA_PACIFIC_SINGAPORE = 'as0';
    case ASIA_PACIFIC_HANOI = 'ap-southeast-2';
    case ASIA_PACIFIC_HO_CHI_MINH = 'ap-southeast-3';

    public function getLabel(): string
    {
        return match ($this) {
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

    public function getUploadDomain(): string
    {
        return match ($this) {
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

    public function getDownloadDomain(): string
    {
        return match ($this) {
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

    public function getRsDomain(): string
    {
        return match ($this) {
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

    public function getRsfDomain(): string
    {
        return match ($this) {
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

    public function getApiFaceDomain(): string
    {
        return match ($this) {
            self::HUADONG_ZHEJIANG => 'api.qiniuapi.com',
            self::HUADONG_ZHEJIANG2 => 'api.qiniuapi.com',
            self::HUABEI_HEBEI => 'api.qiniuapi.com',
            self::HUANAN_GUANGDONG => 'api.qiniuapi.com',
            self::XIBEI_SHAANXI1 => 'api-cn-northwest-1.qiniuapi.com',
            self::NORTH_AMERICA_LOS_ANGELES => 'api.qiniuapi.com',
            self::ASIA_PACIFIC_SINGAPORE => 'api.qiniuapi.com',
            self::ASIA_PACIFIC_HANOI => 'api-ap-southeast-2.qiniuapi.com',
            self::ASIA_PACIFIC_HO_CHI_MINH => 'api-ap-southeast-3.qiniuapi.com',
        };
    }

    /**
     * @return self[]
     */
    public static function getAllRegions(): array
    {
        return self::cases();
    }

    public static function getUcDomain(): string
    {
        return 'uc.qiniuapi.com';
    }
}
