<?php

declare(strict_types=1);

namespace QiniuStorageBundle\Tests\Repository;

use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use QiniuStorageBundle\Entity\Account;
use QiniuStorageBundle\Repository\AccountRepository;
use Tourze\PHPUnitSymfonyKernelTest\AbstractRepositoryTestCase;

/**
 * AccountRepository 测试类
 *
 * @internal
 */
#[RunTestsInSeparateProcesses]
#[CoversClass(AccountRepository::class)]
final class AccountRepositoryTest extends AbstractRepositoryTestCase
{
    protected function onSetUp(): void
    {
        // 如果当前测试是数据库连接测试，跳过数据加载操作
        if ($this->isTestingDatabaseConnection()) {
            return;
        }

        // 清理实体管理器状态，避免影响数据库连接测试
        try {
            self::getEntityManager()->clear();
        } catch (\Exception $e) {
            // 忽略清理错误
        }
    }

    public function testConstructor(): void
    {
        $this->expectNotToPerformAssertions();
        // 基础构造函数测试
    }

    public function testFindByWithValidCriteria(): void
    {
        $repository = self::getService(AccountRepository::class);

        $account = new Account();
        $account->setName('Valid Account');
        $account->setAccessKey('valid_access_key');
        $account->setSecretKey('valid_secret_key');
        $account->setValid(true);

        $repository->save($account);

        $results = $repository->findBy(['valid' => true]);

        $this->assertIsArray($results);
        $this->assertContainsOnlyInstancesOf(Account::class, $results);
    }

    public function testFindByWithInvalidCriteria(): void
    {
        $repository = self::getService(AccountRepository::class);

        $results = $repository->findBy(['valid' => false]);

        $this->assertIsArray($results);
    }

    public function testFindByWithNullableRemark(): void
    {
        $repository = self::getService(AccountRepository::class);

        $account1 = new Account();
        $account1->setName('Account With Remark');
        $account1->setAccessKey('access_key_1');
        $account1->setSecretKey('secret_key_1');
        $account1->setRemark('Test remark');

        $account2 = new Account();
        $account2->setName('Account Without Remark');
        $account2->setAccessKey('access_key_2');
        $account2->setSecretKey('secret_key_2');
        $account2->setRemark(null);

        $repository->save($account1);
        $repository->save($account2);

        $resultsWithRemark = $repository->createQueryBuilder('a')
            ->where('a.remark IS NOT NULL')
            ->getQuery()
            ->getResult()
        ;

        $resultsWithoutRemark = $repository->createQueryBuilder('a')
            ->where('a.remark IS NULL')
            ->getQuery()
            ->getResult()
        ;

        $this->assertIsArray($resultsWithRemark);
        $this->assertIsArray($resultsWithoutRemark);
    }

    public function testFindOneByWithOrderByShouldReturnCorrectOrderedResult(): void
    {
        $repository = self::getService(AccountRepository::class);

        $account1 = new Account();
        $account1->setName('ZZ Last Account');
        $account1->setAccessKey('findoneby_order_z_access_key');
        $account1->setSecretKey('findoneby_order_z_secret_key');
        $account1->setValid(true);

        $account2 = new Account();
        $account2->setName('AA First Account');
        $account2->setAccessKey('findoneby_order_a_access_key');
        $account2->setSecretKey('findoneby_order_a_secret_key');
        $account2->setValid(true);

        $repository->save($account1);
        $repository->save($account2);

        $resultAsc = $repository->findOneBy(['valid' => true], ['name' => 'ASC']);
        $resultDesc = $repository->findOneBy(['valid' => true], ['name' => 'DESC']);

        $this->assertInstanceOf(Account::class, $resultAsc);
        $this->assertInstanceOf(Account::class, $resultDesc);
        $this->assertNotEquals($resultAsc->getId(), $resultDesc->getId());
    }

    public function testFindByWithNullRemarkShouldReturnEntitiesWithNullRemark(): void
    {
        $repository = self::getService(AccountRepository::class);

        $account1 = new Account();
        $account1->setName('FindBy Null Remark Test Account 1');
        $account1->setAccessKey('findby_null_remark_access_key_1');
        $account1->setSecretKey('findby_null_remark_secret_key_1');
        $account1->setValid(true);
        $account1->setRemark(null);

        $account2 = new Account();
        $account2->setName('FindBy With Remark Test Account 2');
        $account2->setAccessKey('findby_with_remark_access_key_2');
        $account2->setSecretKey('findby_with_remark_secret_key_2');
        $account2->setValid(true);
        $account2->setRemark('Test remark');

        $repository->save($account1);
        $repository->save($account2);

        $resultsWithNullRemark = $repository->createQueryBuilder('a')
            ->where('a.remark IS NULL')
            ->andWhere('a.valid = :valid')
            ->setParameter('valid', true)
            ->getQuery()
            ->getResult()
        ;

        $this->assertIsArray($resultsWithNullRemark);
        $this->assertContainsOnlyInstancesOf(Account::class, $resultsWithNullRemark);
        $this->assertGreaterThanOrEqual(1, count($resultsWithNullRemark));

        foreach ($resultsWithNullRemark as $account) {
            $this->assertNull($account->getRemark());
        }
    }

    public function testFindOneByWithOrderByShouldReturnFirstMatch(): void
    {
        $repository = self::getService(AccountRepository::class);

        $account1 = new Account();
        $account1->setName('FindOneBy Order Test B');
        $account1->setAccessKey('findoneby_order_access_key_1');
        $account1->setSecretKey('findoneby_order_secret_key_1');
        $account1->setValid(true);

        $account2 = new Account();
        $account2->setName('FindOneBy Order Test A');
        $account2->setAccessKey('findoneby_order_access_key_2');
        $account2->setSecretKey('findoneby_order_secret_key_2');
        $account2->setValid(true);

        $repository->save($account1);
        $repository->save($account2);

        $result = $repository->findOneBy(['valid' => true], ['name' => 'ASC']);

        $this->assertInstanceOf(Account::class, $result);
    }

    public function testFindByWithNullValidShouldReturnEntitiesWithNullValid(): void
    {
        $repository = self::getService(AccountRepository::class);

        $account1 = new Account();
        $account1->setName('FindBy Null Valid Test Account 1');
        $account1->setAccessKey('findby_null_valid_access_key_1');
        $account1->setSecretKey('findby_null_valid_secret_key_1');
        $account1->setValid(null);

        $account2 = new Account();
        $account2->setName('FindBy True Valid Test Account 2');
        $account2->setAccessKey('findby_true_valid_access_key_2');
        $account2->setSecretKey('findby_true_valid_secret_key_2');
        $account2->setValid(true);

        $repository->save($account1);
        $repository->save($account2);

        $resultsWithNullValid = $repository->createQueryBuilder('a')
            ->where('a.valid IS NULL')
            ->getQuery()
            ->getResult()
        ;

        $this->assertIsArray($resultsWithNullValid);
        $this->assertContainsOnlyInstancesOf(Account::class, $resultsWithNullValid);
        $this->assertGreaterThanOrEqual(1, count($resultsWithNullValid));

        foreach ($resultsWithNullValid as $account) {
            $this->assertNull($account->isValid());
        }
    }

    public function testCountWithNullValidField(): void
    {
        $repository = self::getService(AccountRepository::class);

        $account1 = new Account();
        $account1->setName('Count Valid Null Field Test Account 1');
        $account1->setAccessKey('count_valid_null_field_access_key_1');
        $account1->setSecretKey('count_valid_null_field_secret_key_1');
        $account1->setValid(null);

        $account2 = new Account();
        $account2->setName('Count Valid True Field Test Account 2');
        $account2->setAccessKey('count_valid_true_field_access_key_2');
        $account2->setSecretKey('count_valid_true_field_secret_key_2');
        $account2->setValid(true);

        $repository->save($account1);
        $repository->save($account2);

        $nullCount = $repository->createQueryBuilder('a')
            ->select('COUNT(a.id)')
            ->where('a.valid IS NULL')
            ->getQuery()
            ->getSingleScalarResult()
        ;

        $this->assertIsInt((int) $nullCount);
        $this->assertGreaterThanOrEqual(1, (int) $nullCount);
    }

    public function testSaveShouldPersistEntity(): void
    {
        $repository = self::getService(AccountRepository::class);

        $account = new Account();
        $account->setName('Save Test Account');
        $account->setAccessKey('save_access_key');
        $account->setSecretKey('save_secret_key');
        $account->setValid(true);

        $repository->save($account);

        $this->assertNotNull($account->getId());

        $foundAccount = $repository->find($account->getId());
        $this->assertInstanceOf(Account::class, $foundAccount);
        $this->assertEquals('Save Test Account', $foundAccount->getName());
    }

    public function testSaveWithFlushFalseShouldNotFlushImmediately(): void
    {
        $repository = self::getService(AccountRepository::class);

        $account = new Account();
        $account->setName('Save No Flush Test Account');
        $account->setAccessKey('save_noflush_access_key');
        $account->setSecretKey('save_noflush_secret_key');
        $account->setValid(true);

        $repository->save($account, false);
        $this->assertNull($account->getId());

        self::getService(EntityManagerInterface::class)->flush();
        $this->assertNotNull($account->getId());
    }

    public function testRemoveShouldDeleteEntity(): void
    {
        $repository = self::getService(AccountRepository::class);

        $account = new Account();
        $account->setName('Remove Test Account');
        $account->setAccessKey('remove_access_key');
        $account->setSecretKey('remove_secret_key');
        $account->setValid(true);

        $repository->save($account);
        $this->assertNotNull($account->getId());

        $accountId = $account->getId();
        $repository->remove($account);

        $foundAccount = $repository->find($accountId);
        $this->assertNull($foundAccount);
    }

    public function testRemoveWithFlushFalseShouldNotFlushImmediately(): void
    {
        $repository = self::getService(AccountRepository::class);

        $account = new Account();
        $account->setName('Remove No Flush Test Account');
        $account->setAccessKey('remove_noflush_access_key');
        $account->setSecretKey('remove_noflush_secret_key');
        $account->setValid(true);

        $repository->save($account);
        $this->assertNotNull($account->getId());

        $accountId = $account->getId();
        $repository->remove($account, false);

        $foundAccount = $repository->find($accountId);
        $this->assertInstanceOf(Account::class, $foundAccount);

        self::getService(EntityManagerInterface::class)->flush();
        $foundAccount = $repository->find($accountId);
        $this->assertNull($foundAccount);
    }

    public function testCountWithNullableValidField(): void
    {
        $repository = self::getService(AccountRepository::class);

        $account1 = new Account();
        $account1->setName('Count Valid Null Test Account 1');
        $account1->setAccessKey('count_valid_null_access_key_1');
        $account1->setSecretKey('count_valid_null_secret_key_1');
        $account1->setValid(null);

        $account2 = new Account();
        $account2->setName('Count Valid True Test Account 2');
        $account2->setAccessKey('count_valid_true_access_key_2');
        $account2->setSecretKey('count_valid_true_secret_key_2');
        $account2->setValid(true);

        $repository->save($account1);
        $repository->save($account2);

        $nullCount = $repository->createQueryBuilder('a')
            ->select('COUNT(a.id)')
            ->where('a.valid IS NULL')
            ->getQuery()
            ->getSingleScalarResult()
        ;

        $notNullCount = $repository->createQueryBuilder('a')
            ->select('COUNT(a.id)')
            ->where('a.valid IS NOT NULL')
            ->getQuery()
            ->getSingleScalarResult()
        ;

        $this->assertIsInt((int) $nullCount);
        $this->assertIsInt((int) $notNullCount);
        $this->assertGreaterThanOrEqual(1, (int) $nullCount);
        $this->assertGreaterThanOrEqual(1, (int) $notNullCount);
    }

    public function testCountWithNullableRemarkField(): void
    {
        $repository = self::getService(AccountRepository::class);

        $account1 = new Account();
        $account1->setName('Count Remark Null Test Account 1');
        $account1->setAccessKey('count_remark_null_access_key_1');
        $account1->setSecretKey('count_remark_null_secret_key_1');
        $account1->setRemark(null);

        $account2 = new Account();
        $account2->setName('Count Remark Test Account 2');
        $account2->setAccessKey('count_remark_access_key_2');
        $account2->setSecretKey('count_remark_secret_key_2');
        $account2->setRemark('Test remark');

        $repository->save($account1);
        $repository->save($account2);

        $nullCount = $repository->createQueryBuilder('a')
            ->select('COUNT(a.id)')
            ->where('a.remark IS NULL')
            ->getQuery()
            ->getSingleScalarResult()
        ;

        $notNullCount = $repository->createQueryBuilder('a')
            ->select('COUNT(a.id)')
            ->where('a.remark IS NOT NULL')
            ->getQuery()
            ->getSingleScalarResult()
        ;

        $this->assertIsInt((int) $nullCount);
        $this->assertIsInt((int) $notNullCount);
        $this->assertGreaterThanOrEqual(1, (int) $nullCount);
        $this->assertGreaterThanOrEqual(1, (int) $notNullCount);
    }

    public function testFindOneByWithNullRemarkShouldFindEntityWithNullRemark(): void
    {
        $repository = self::getService(AccountRepository::class);

        $account1 = new Account();
        $account1->setName('FindOneBy Null Remark Test Account 1');
        $account1->setAccessKey('findoneby_null_remark_access_key_1');
        $account1->setSecretKey('findoneby_null_remark_secret_key_1');
        $account1->setValid(true);
        $account1->setRemark(null);

        $account2 = new Account();
        $account2->setName('FindOneBy With Remark Test Account 2');
        $account2->setAccessKey('findoneby_with_remark_access_key_2');
        $account2->setSecretKey('findoneby_with_remark_secret_key_2');
        $account2->setValid(true);
        $account2->setRemark('Test remark');

        $repository->save($account1);
        $repository->save($account2);

        $resultWithNullRemark = $repository->createQueryBuilder('a')
            ->where('a.remark IS NULL')
            ->andWhere('a.valid = :valid')
            ->setParameter('valid', true)
            ->getQuery()
            ->getOneOrNullResult()
        ;

        $this->assertInstanceOf(Account::class, $resultWithNullRemark);
        $this->assertNull($resultWithNullRemark->getRemark());
    }

    public function testFindOneByWithNullValidShouldFindEntityWithNullValid(): void
    {
        $repository = self::getService(AccountRepository::class);

        $account1 = new Account();
        $account1->setName('FindOneBy Null Valid Test Account 1');
        $account1->setAccessKey('findoneby_null_valid_access_key_1');
        $account1->setSecretKey('findoneby_null_valid_secret_key_1');
        $account1->setValid(null);

        $account2 = new Account();
        $account2->setName('FindOneBy True Valid Test Account 2');
        $account2->setAccessKey('findoneby_true_valid_access_key_2');
        $account2->setSecretKey('findoneby_true_valid_secret_key_2');
        $account2->setValid(true);

        $repository->save($account1);
        $repository->save($account2);

        $resultWithNullValid = $repository->createQueryBuilder('a')
            ->where('a.valid IS NULL')
            ->getQuery()
            ->getOneOrNullResult()
        ;

        $this->assertInstanceOf(Account::class, $resultWithNullValid);
        $this->assertNull($resultWithNullValid->isValid());
    }

    public function testFindOneByWithValidNullQueryShouldWork(): void
    {
        $repository = self::getService(AccountRepository::class);

        $account1 = new Account();
        $account1->setName('Valid Null Query Test Account 1');
        $account1->setAccessKey('valid_null_query_access_key_1');
        $account1->setSecretKey('valid_null_query_secret_key_1');
        $account1->setValid(null);

        $account2 = new Account();
        $account2->setName('Valid Null Query Test Account 2');
        $account2->setAccessKey('valid_null_query_access_key_2');
        $account2->setSecretKey('valid_null_query_secret_key_2');
        $account2->setValid(true);

        $repository->save($account1);
        $repository->save($account2);

        $result = $repository->createQueryBuilder('a')
            ->where('a.valid IS NULL')
            ->getQuery()
            ->getOneOrNullResult()
        ;

        $this->assertInstanceOf(Account::class, $result);
        $this->assertNull($result->isValid());
    }

    public function testFindOneByWithRemarkNullQueryShouldWork(): void
    {
        $repository = self::getService(AccountRepository::class);

        $account1 = new Account();
        $account1->setName('Remark Null Query Test Account 1');
        $account1->setAccessKey('remark_null_query_access_key_1');
        $account1->setSecretKey('remark_null_query_secret_key_1');
        $account1->setRemark(null);

        $account2 = new Account();
        $account2->setName('Remark Null Query Test Account 2');
        $account2->setAccessKey('remark_null_query_access_key_2');
        $account2->setSecretKey('remark_null_query_secret_key_2');
        $account2->setRemark('Test remark');

        $repository->save($account1);
        $repository->save($account2);

        $result = $repository->createQueryBuilder('a')
            ->where('a.remark IS NULL')
            ->getQuery()
            ->getOneOrNullResult()
        ;

        $this->assertInstanceOf(Account::class, $result);
        $this->assertNull($result->getRemark());
    }

    public function testCountWithNullRemarkField(): void
    {
        $repository = self::getService(AccountRepository::class);

        $account1 = new Account();
        $account1->setName('Count Null Remark Field Test Account 1');
        $account1->setAccessKey('count_null_remark_field_access_key_1');
        $account1->setSecretKey('count_null_remark_field_secret_key_1');
        $account1->setRemark(null);

        $account2 = new Account();
        $account2->setName('Count Not Null Remark Field Test Account 2');
        $account2->setAccessKey('count_not_null_remark_field_access_key_2');
        $account2->setSecretKey('count_not_null_remark_field_secret_key_2');
        $account2->setRemark('Test remark');

        $repository->save($account1);
        $repository->save($account2);

        $nullCount = $repository->createQueryBuilder('a')
            ->select('COUNT(a.id)')
            ->where('a.remark IS NULL')
            ->getQuery()
            ->getSingleScalarResult()
        ;

        $this->assertIsInt((int) $nullCount);
        $this->assertGreaterThanOrEqual(1, (int) $nullCount);
    }

    protected function createNewEntity(): object
    {
        $account = new Account();
        $account->setName('Test Account ' . uniqid());
        $account->setAccessKey('test_access_key_' . uniqid());
        $account->setSecretKey('test_secret_key_' . uniqid());
        $account->setValid(true);

        return $account;
    }

    private function isTestingDatabaseConnection(): bool
    {
        $backtrace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS);
        foreach ($backtrace as $trace) {
            if (str_contains($trace['function'], 'testFindWhenDatabaseIsUnavailable')) {
                return true;
            }
            if (str_contains($trace['function'], 'testFindByWhenDatabaseIsUnavailable')) {
                return true;
            }
            if (str_contains($trace['function'], 'testFindAllWhenDatabaseIsUnavailable')) {
                return true;
            }
            if (str_contains($trace['function'], 'testCountWhenDatabaseIsUnavailable')) {
                return true;
            }
        }

        return false;
    }

    protected function getRepository(): AccountRepository
    {
        return self::getService(AccountRepository::class);
    }
}
