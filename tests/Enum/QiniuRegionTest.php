<?php

namespace QiniuStorageBundle\Tests\Enum;

use PHPUnit\Framework\Attributes\CoversClass;
use QiniuStorageBundle\Enum\QiniuRegion;
use Tourze\PHPUnitEnum\AbstractEnumTestCase;

/**
 * @internal
 */
#[CoversClass(QiniuRegion::class)]
final class QiniuRegionTest extends AbstractEnumTestCase
{
    public function testEnumValues(): void
    {
        $this->assertSame('z0', QiniuRegion::HUADONG_ZHEJIANG->value);
        $this->assertSame('cn-east-2', QiniuRegion::HUADONG_ZHEJIANG2->value);
        $this->assertSame('z1', QiniuRegion::HUABEI_HEBEI->value);
        $this->assertSame('z2', QiniuRegion::HUANAN_GUANGDONG->value);
        $this->assertSame('cn-northwest-1', QiniuRegion::XIBEI_SHAANXI1->value);
        $this->assertSame('na0', QiniuRegion::NORTH_AMERICA_LOS_ANGELES->value);
        $this->assertSame('as0', QiniuRegion::ASIA_PACIFIC_SINGAPORE->value);
        $this->assertSame('ap-southeast-2', QiniuRegion::ASIA_PACIFIC_HANOI->value);
        $this->assertSame('ap-southeast-3', QiniuRegion::ASIA_PACIFIC_HO_CHI_MINH->value);
    }

    public function testGetLabel(): void
    {
        $this->assertSame('华东-浙江', QiniuRegion::HUADONG_ZHEJIANG->getLabel());
        $this->assertSame('华东-浙江2', QiniuRegion::HUADONG_ZHEJIANG2->getLabel());
        $this->assertSame('华北-河北', QiniuRegion::HUABEI_HEBEI->getLabel());
        $this->assertSame('华南-广东', QiniuRegion::HUANAN_GUANGDONG->getLabel());
        $this->assertSame('西北-陕西1', QiniuRegion::XIBEI_SHAANXI1->getLabel());
        $this->assertSame('北美-洛杉矶', QiniuRegion::NORTH_AMERICA_LOS_ANGELES->getLabel());
        $this->assertSame('亚太-新加坡', QiniuRegion::ASIA_PACIFIC_SINGAPORE->getLabel());
        $this->assertSame('亚太-河内', QiniuRegion::ASIA_PACIFIC_HANOI->getLabel());
        $this->assertSame('亚太-胡志明', QiniuRegion::ASIA_PACIFIC_HO_CHI_MINH->getLabel());
    }

    public function testGetUploadDomain(): void
    {
        $this->assertSame('up-z0.qiniup.com', QiniuRegion::HUADONG_ZHEJIANG->getUploadDomain());
        $this->assertSame('up-cn-east-2.qiniup.com', QiniuRegion::HUADONG_ZHEJIANG2->getUploadDomain());
        $this->assertSame('up-z1.qiniup.com', QiniuRegion::HUABEI_HEBEI->getUploadDomain());
        $this->assertSame('up-z2.qiniup.com', QiniuRegion::HUANAN_GUANGDONG->getUploadDomain());
        $this->assertSame('up-cn-northwest-1.qiniup.com', QiniuRegion::XIBEI_SHAANXI1->getUploadDomain());
        $this->assertSame('up-na0.qiniup.com', QiniuRegion::NORTH_AMERICA_LOS_ANGELES->getUploadDomain());
        $this->assertSame('up-as0.qiniup.com', QiniuRegion::ASIA_PACIFIC_SINGAPORE->getUploadDomain());
        $this->assertSame('up-ap-southeast-2.qiniup.com', QiniuRegion::ASIA_PACIFIC_HANOI->getUploadDomain());
        $this->assertSame('up-ap-southeast-3.qiniup.com', QiniuRegion::ASIA_PACIFIC_HO_CHI_MINH->getUploadDomain());
    }

    public function testGetDownloadDomain(): void
    {
        $this->assertSame('iovip-z0.qiniuio.com', QiniuRegion::HUADONG_ZHEJIANG->getDownloadDomain());
        $this->assertSame('iovip-cn-east-2.qiniuio.com', QiniuRegion::HUADONG_ZHEJIANG2->getDownloadDomain());
        $this->assertSame('iovip-z1.qiniuio.com', QiniuRegion::HUABEI_HEBEI->getDownloadDomain());
        $this->assertSame('iovip-z2.qiniuio.com', QiniuRegion::HUANAN_GUANGDONG->getDownloadDomain());
        $this->assertSame('iovip-cn-northwest-1.qiniuio.com', QiniuRegion::XIBEI_SHAANXI1->getDownloadDomain());
        $this->assertSame('iovip-na0.qiniuio.com', QiniuRegion::NORTH_AMERICA_LOS_ANGELES->getDownloadDomain());
        $this->assertSame('iovip-as0.qiniuio.com', QiniuRegion::ASIA_PACIFIC_SINGAPORE->getDownloadDomain());
        $this->assertSame('iovip-ap-southeast-2.qiniuio.com', QiniuRegion::ASIA_PACIFIC_HANOI->getDownloadDomain());
        $this->assertSame('iovip-ap-southeast-3.qiniuio.com', QiniuRegion::ASIA_PACIFIC_HO_CHI_MINH->getDownloadDomain());
    }

    public function testGetRsDomain(): void
    {
        $this->assertSame('rs-z0.qiniuapi.com', QiniuRegion::HUADONG_ZHEJIANG->getRsDomain());
        $this->assertSame('rs-cn-east-2.qiniuapi.com', QiniuRegion::HUADONG_ZHEJIANG2->getRsDomain());
        $this->assertSame('rs-z1.qiniuapi.com', QiniuRegion::HUABEI_HEBEI->getRsDomain());
        $this->assertSame('rs-z2.qiniuapi.com', QiniuRegion::HUANAN_GUANGDONG->getRsDomain());
        $this->assertSame('rs-cn-northwest-1.qiniuapi.com', QiniuRegion::XIBEI_SHAANXI1->getRsDomain());
        $this->assertSame('rs-na0.qiniuapi.com', QiniuRegion::NORTH_AMERICA_LOS_ANGELES->getRsDomain());
        $this->assertSame('rs-as0.qiniuapi.com', QiniuRegion::ASIA_PACIFIC_SINGAPORE->getRsDomain());
        $this->assertSame('rs-ap-southeast-2.qiniuapi.com', QiniuRegion::ASIA_PACIFIC_HANOI->getRsDomain());
        $this->assertSame('rs-ap-southeast-3.qiniuapi.com', QiniuRegion::ASIA_PACIFIC_HO_CHI_MINH->getRsDomain());
    }

    public function testGetRsfDomain(): void
    {
        $this->assertSame('rsf-z0.qiniuapi.com', QiniuRegion::HUADONG_ZHEJIANG->getRsfDomain());
        $this->assertSame('rsf-cn-east-2.qiniuapi.com', QiniuRegion::HUADONG_ZHEJIANG2->getRsfDomain());
        $this->assertSame('rsf-z1.qiniuapi.com', QiniuRegion::HUABEI_HEBEI->getRsfDomain());
        $this->assertSame('rsf-z2.qiniuapi.com', QiniuRegion::HUANAN_GUANGDONG->getRsfDomain());
        $this->assertSame('rsf-cn-northwest-1.qiniuapi.com', QiniuRegion::XIBEI_SHAANXI1->getRsfDomain());
        $this->assertSame('rsf-na0.qiniuapi.com', QiniuRegion::NORTH_AMERICA_LOS_ANGELES->getRsfDomain());
        $this->assertSame('rsf-as0.qiniuapi.com', QiniuRegion::ASIA_PACIFIC_SINGAPORE->getRsfDomain());
        $this->assertSame('rsf-ap-southeast-2.qiniuapi.com', QiniuRegion::ASIA_PACIFIC_HANOI->getRsfDomain());
        $this->assertSame('rsf-ap-southeast-3.qiniuapi.com', QiniuRegion::ASIA_PACIFIC_HO_CHI_MINH->getRsfDomain());
    }

    public function testGetApiFaceDomain(): void
    {
        $this->assertSame('api.qiniuapi.com', QiniuRegion::HUADONG_ZHEJIANG->getApiFaceDomain());
        $this->assertSame('api.qiniuapi.com', QiniuRegion::HUADONG_ZHEJIANG2->getApiFaceDomain());
        $this->assertSame('api.qiniuapi.com', QiniuRegion::HUABEI_HEBEI->getApiFaceDomain());
        $this->assertSame('api.qiniuapi.com', QiniuRegion::HUANAN_GUANGDONG->getApiFaceDomain());
        $this->assertSame('api-cn-northwest-1.qiniuapi.com', QiniuRegion::XIBEI_SHAANXI1->getApiFaceDomain());
        $this->assertSame('api.qiniuapi.com', QiniuRegion::NORTH_AMERICA_LOS_ANGELES->getApiFaceDomain());
        $this->assertSame('api.qiniuapi.com', QiniuRegion::ASIA_PACIFIC_SINGAPORE->getApiFaceDomain());
        $this->assertSame('api-ap-southeast-2.qiniuapi.com', QiniuRegion::ASIA_PACIFIC_HANOI->getApiFaceDomain());
        $this->assertSame('api-ap-southeast-3.qiniuapi.com', QiniuRegion::ASIA_PACIFIC_HO_CHI_MINH->getApiFaceDomain());
    }

    public function testGetAllRegions(): void
    {
        $regions = QiniuRegion::getAllRegions();
        $this->assertCount(9, $regions);
        $this->assertContains(QiniuRegion::HUADONG_ZHEJIANG, $regions);
        $this->assertContains(QiniuRegion::HUADONG_ZHEJIANG2, $regions);
        $this->assertContains(QiniuRegion::HUABEI_HEBEI, $regions);
        $this->assertContains(QiniuRegion::HUANAN_GUANGDONG, $regions);
        $this->assertContains(QiniuRegion::XIBEI_SHAANXI1, $regions);
        $this->assertContains(QiniuRegion::NORTH_AMERICA_LOS_ANGELES, $regions);
        $this->assertContains(QiniuRegion::ASIA_PACIFIC_SINGAPORE, $regions);
        $this->assertContains(QiniuRegion::ASIA_PACIFIC_HANOI, $regions);
        $this->assertContains(QiniuRegion::ASIA_PACIFIC_HO_CHI_MINH, $regions);
    }

    public function testGetUcDomain(): void
    {
        $this->assertSame('uc.qiniuapi.com', QiniuRegion::getUcDomain());
    }

    public function testSelectableTrait(): void
    {
        // 测试 SelectTrait 提供的基本功能
        $region = QiniuRegion::HUADONG_ZHEJIANG;
        $this->assertSame('华东-浙江', $region->getLabel());

        // 验证所有枚举值都有标签
        foreach (QiniuRegion::cases() as $case) {
            $label = $case->getLabel();
            $this->assertIsString($label);
            $this->assertNotEmpty($label);
        }
    }

    public function testToArray(): void
    {
        $array = QiniuRegion::HUADONG_ZHEJIANG->toArray();

        $this->assertIsArray($array);
        $this->assertCount(2, $array);
        $this->assertArrayHasKey('value', $array);
        $this->assertArrayHasKey('label', $array);
        $this->assertSame('z0', $array['value']);
        $this->assertSame('华东-浙江', $array['label']);

        // 测试其他枚举值
        $zhejiang2Array = QiniuRegion::HUADONG_ZHEJIANG2->toArray();
        $this->assertSame('cn-east-2', $zhejiang2Array['value']);
        $this->assertSame('华东-浙江2', $zhejiang2Array['label']);

        $hebeiArray = QiniuRegion::HUABEI_HEBEI->toArray();
        $this->assertSame('z1', $hebeiArray['value']);
        $this->assertSame('华北-河北', $hebeiArray['label']);
    }
}
