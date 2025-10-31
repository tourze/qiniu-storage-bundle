<?php

declare(strict_types=1);

namespace QiniuStorageBundle\Tests\Entity;

use PHPUnit\Framework\Attributes\CoversClass;
use QiniuStorageBundle\Entity\BucketMinuteStatistic;
use Tourze\PHPUnitDoctrineEntity\AbstractEntityTestCase;

/**
 * BucketMinuteStatistic 实体测试类
 *
 * 注意：此测试直接实例化实体，因为实体测试需要测试属性和关联关系。
 * NoDirectInstantiationOfCoveredClassRule 规则不适用于实体测试。
 *
 * @internal
 */
#[CoversClass(BucketMinuteStatistic::class)]
final class BucketMinuteStatisticTest extends AbstractEntityTestCase
{
    /**
     * 创建被测实体的一个实例.
     */
    protected function createEntity(): object
    {
        return new BucketMinuteStatistic();
    }

    /**
     * 提供属性及其样本值的 Data Provider.
     *
     * @return iterable<string, array{string, mixed}>
     */
    public static function propertiesProvider(): iterable
    {
        yield 'time' => ['time', new \DateTimeImmutable('2023-01-01 10:15:00')];
        yield 'standardStorage' => ['standardStorage', 1024];
        yield 'lineStorage' => ['lineStorage', 512];
        yield 'archiveStorage' => ['archiveStorage', 256];
        yield 'standardCount' => ['standardCount', 10];
        yield 'lineCount' => ['lineCount', 5];
        yield 'archiveCount' => ['archiveCount', 3];
        yield 'internetTraffic' => ['internetTraffic', 2048];
        yield 'cdnTraffic' => ['cdnTraffic', 1024];
        yield 'getRequests' => ['getRequests', 15];
        yield 'putRequests' => ['putRequests', 8];
        yield 'storageTypeConversions' => ['storageTypeConversions', 2];
    }
}
