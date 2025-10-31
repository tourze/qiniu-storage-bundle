<?php

declare(strict_types=1);

namespace QiniuStorageBundle\Tests\Entity;

use PHPUnit\Framework\Attributes\CoversClass;
use QiniuStorageBundle\Entity\Bucket;
use Tourze\PHPUnitDoctrineEntity\AbstractEntityTestCase;

/**
 * Bucket 实体测试类
 *
 * 注意：此测试直接实例化实体，因为实体测试需要测试属性和关联关系。
 * NoDirectInstantiationOfCoveredClassRule 规则不适用于实体测试。
 *
 * @internal
 */
#[CoversClass(Bucket::class)]
final class BucketTest extends AbstractEntityTestCase
{
    /**
     * 创建被测实体的一个实例.
     */
    protected function createEntity(): object
    {
        return new Bucket();
    }

    /**
     * 提供属性及其样本值的 Data Provider.
     *
     * @return iterable<string, array{string, mixed}>
     */
    public static function propertiesProvider(): iterable
    {
        yield 'name' => ['name', 'test-bucket-' . uniqid()];
        yield 'region' => ['region', 'test-region'];
        yield 'domain' => ['domain', 'test.example.com'];
        yield 'private' => ['private', true];
        yield 'valid' => ['valid', true];
    }
}
