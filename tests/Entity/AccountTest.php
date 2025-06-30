<?php

declare(strict_types=1);

namespace QiniuStorageBundle\Tests\Entity;

use PHPUnit\Framework\TestCase;
use QiniuStorageBundle\Entity\Account;

/**
 * Account 实体测试类
 */
class AccountTest extends TestCase
{
    public function testConstructor(): void
    {
        $account = new Account();
        $this->assertInstanceOf(Account::class, $account);
    }
} 