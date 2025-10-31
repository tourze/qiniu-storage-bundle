<?php

declare(strict_types=1);

namespace QiniuStorageBundle\Tests\Entity;

use PHPUnit\Framework\Attributes\CoversClass;
use QiniuStorageBundle\Entity\Account;
use Tourze\PHPUnitDoctrineEntity\AbstractEntityTestCase;

/**
 * Account 实体测试类
 *
 * @internal
 */
#[CoversClass(Account::class)]
final class AccountTest extends AbstractEntityTestCase
{
    /**
     * 创建被测实体的一个实例.
     */
    protected function createEntity(): object
    {
        return new Account();
    }

    /**
     * 提供属性及其样本值的 Data Provider.
     *
     * @return iterable<string, array{string, mixed}>
     */
    public static function propertiesProvider(): iterable
    {
        yield 'valid' => ['valid', true];
        yield 'name' => ['name', 'Test Account'];
        yield 'accessKey' => ['accessKey', 'test_access_key_' . uniqid()];
        yield 'secretKey' => ['secretKey', 'test_secret_key_' . uniqid()];
        yield 'remark' => ['remark', 'Test remark'];
    }
}
