<?php

declare(strict_types=1);

namespace QiniuStorageBundle\Tests\Repository;

use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use QiniuStorageBundle\Entity\Account;
use QiniuStorageBundle\Entity\Bucket;
use QiniuStorageBundle\Repository\AccountRepository;
use QiniuStorageBundle\Repository\BucketRepository;
use Tourze\PHPUnitSymfonyKernelTest\AbstractRepositoryTestCase;

/**
 * BucketRepository 测试类
 *
 * @internal
 */
#[RunTestsInSeparateProcesses]
#[CoversClass(BucketRepository::class)]
final class BucketRepositoryTest extends AbstractRepositoryTestCase
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

    public function testFindNeedSync(): void
    {
        $repository = self::getService(BucketRepository::class);

        $before = new \DateTimeImmutable('2024-01-01 12:00:00');

        $result = $repository->findNeedSync($before);

        $this->assertIsArray($result);
    }

    public function testFindByWithAccountRelation(): void
    {
        $repository = self::getService(BucketRepository::class);
        $accountRepository = self::getService(AccountRepository::class);

        $account = new Account();
        $account->setName('Test Account for Bucket');
        $account->setAccessKey('bucket_access_key');
        $account->setSecretKey('bucket_secret_key');
        $account->setValid(true);
        $accountRepository->save($account);

        $bucket = new Bucket();
        $bucket->setAccount($account);
        $bucket->setName('relation-test-bucket');
        $bucket->setRegion('z1');
        $bucket->setDomain('relation.example.com');
        $bucket->setPrivate(true);
        $bucket->setValid(true);

        $repository->save($bucket);

        $results = $repository->findBy(['account' => $account]);

        $this->assertIsArray($results);
        $this->assertContainsOnlyInstancesOf(Bucket::class, $results);
    }

    public function testFindByWithValidCriteria(): void
    {
        $repository = self::getService(BucketRepository::class);

        $results = $repository->findBy(['valid' => true]);

        $this->assertIsArray($results);
        $this->assertContainsOnlyInstancesOf(Bucket::class, $results);
    }

    public function testFindByWithLastSyncTimeNull(): void
    {
        $repository = self::getService(BucketRepository::class);
        $accountRepository = self::getService(AccountRepository::class);

        $account = new Account();
        $account->setName('Sync Test Account');
        $account->setAccessKey('sync_access_key');
        $account->setSecretKey('sync_secret_key');
        $account->setValid(true);
        $accountRepository->save($account);

        $bucket1 = new Bucket();
        $bucket1->setAccount($account);
        $bucket1->setName('never-synced-bucket');
        $bucket1->setRegion('z0');
        $bucket1->setDomain('never.example.com');
        $bucket1->setPrivate(false);
        $bucket1->setLastSyncTime(null);

        $bucket2 = new Bucket();
        $bucket2->setAccount($account);
        $bucket2->setName('synced-bucket');
        $bucket2->setRegion('z1');
        $bucket2->setDomain('synced.example.com');
        $bucket2->setPrivate(false);
        $bucket2->setLastSyncTime(new \DateTimeImmutable('2024-01-01'));

        $repository->save($bucket1);
        $repository->save($bucket2);

        $resultsWithNullSync = $repository->createQueryBuilder('b')
            ->where('b.lastSyncTime IS NULL')
            ->getQuery()
            ->getResult()
        ;

        $resultsWithSync = $repository->createQueryBuilder('b')
            ->where('b.lastSyncTime IS NOT NULL')
            ->getQuery()
            ->getResult()
        ;

        $this->assertIsArray($resultsWithNullSync);
        $this->assertIsArray($resultsWithSync);
    }

    public function testFindByWithRemarkNull(): void
    {
        $repository = self::getService(BucketRepository::class);
        $accountRepository = self::getService(AccountRepository::class);

        $account = new Account();
        $account->setName('Remark Test Account');
        $account->setAccessKey('remark_access_key');
        $account->setSecretKey('remark_secret_key');
        $account->setValid(true);
        $accountRepository->save($account);

        $bucket1 = new Bucket();
        $bucket1->setAccount($account);
        $bucket1->setName('bucket-with-remark');
        $bucket1->setRegion('z0');
        $bucket1->setDomain('remark.example.com');
        $bucket1->setPrivate(false);
        $bucket1->setRemark('Test remark');

        $bucket2 = new Bucket();
        $bucket2->setAccount($account);
        $bucket2->setName('bucket-without-remark');
        $bucket2->setRegion('z1');
        $bucket2->setDomain('noremark.example.com');
        $bucket2->setPrivate(false);
        $bucket2->setRemark(null);

        $repository->save($bucket1);
        $repository->save($bucket2);

        $resultsWithRemark = $repository->createQueryBuilder('b')
            ->where('b.remark IS NOT NULL')
            ->getQuery()
            ->getResult()
        ;

        $resultsWithoutRemark = $repository->createQueryBuilder('b')
            ->where('b.remark IS NULL')
            ->getQuery()
            ->getResult()
        ;

        $this->assertIsArray($resultsWithRemark);
        $this->assertIsArray($resultsWithoutRemark);
    }

    public function testFindOneByWithOrderByShouldReturnCorrectOrderedResult(): void
    {
        $repository = self::getService(BucketRepository::class);
        $accountRepository = self::getService(AccountRepository::class);

        $account = new Account();
        $account->setName('FindOneBy Order Test Account');
        $account->setAccessKey('findoneby_order_access_key');
        $account->setSecretKey('findoneby_order_secret_key');
        $account->setValid(true);
        $accountRepository->save($account);

        $bucket1 = new Bucket();
        $bucket1->setAccount($account);
        $bucket1->setName('ZZ Last Bucket');
        $bucket1->setRegion('z0');
        $bucket1->setDomain('zz.example.com');
        $bucket1->setPrivate(false);
        $bucket1->setValid(true);

        $bucket2 = new Bucket();
        $bucket2->setAccount($account);
        $bucket2->setName('AA First Bucket');
        $bucket2->setRegion('z1');
        $bucket2->setDomain('aa.example.com');
        $bucket2->setPrivate(false);
        $bucket2->setValid(true);

        $repository->save($bucket1);
        $repository->save($bucket2);

        $resultAsc = $repository->findOneBy(['valid' => true], ['name' => 'ASC']);
        $resultDesc = $repository->findOneBy(['valid' => true], ['name' => 'DESC']);

        $this->assertInstanceOf(Bucket::class, $resultAsc);
        $this->assertInstanceOf(Bucket::class, $resultDesc);
        $this->assertNotEquals($resultAsc->getId(), $resultDesc->getId());
    }

    public function testFindByWithNullRemarkShouldReturnEntitiesWithNullRemark(): void
    {
        $repository = self::getService(BucketRepository::class);
        $accountRepository = self::getService(AccountRepository::class);

        $account = new Account();
        $account->setName('FindBy Null Remark Test Account');
        $account->setAccessKey('findby_null_remark_access_key');
        $account->setSecretKey('findby_null_remark_secret_key');
        $account->setValid(true);
        $accountRepository->save($account);

        $bucket1 = new Bucket();
        $bucket1->setAccount($account);
        $bucket1->setName('findby-null-remark-bucket-1');
        $bucket1->setRegion('z0');
        $bucket1->setDomain('findbynull1.example.com');
        $bucket1->setPrivate(false);
        $bucket1->setValid(true);
        $bucket1->setRemark(null);

        $bucket2 = new Bucket();
        $bucket2->setAccount($account);
        $bucket2->setName('findby-with-remark-bucket-2');
        $bucket2->setRegion('z1');
        $bucket2->setDomain('findbywith2.example.com');
        $bucket2->setPrivate(false);
        $bucket2->setValid(true);
        $bucket2->setRemark('Test remark');

        $repository->save($bucket1);
        $repository->save($bucket2);

        $resultsWithNullRemark = $repository->createQueryBuilder('b')
            ->where('b.remark IS NULL')
            ->andWhere('b.valid = :valid')
            ->setParameter('valid', true)
            ->getQuery()
            ->getResult()
        ;

        $this->assertIsArray($resultsWithNullRemark);
        $this->assertContainsOnlyInstancesOf(Bucket::class, $resultsWithNullRemark);
        $this->assertGreaterThanOrEqual(1, count($resultsWithNullRemark));

        foreach ($resultsWithNullRemark as $bucket) {
            $this->assertNull($bucket->getRemark());
        }
    }

    public function testFindOneByWithOrderByShouldReturnFirstMatch(): void
    {
        $repository = self::getService(BucketRepository::class);
        $accountRepository = self::getService(AccountRepository::class);

        $account = new Account();
        $account->setName('FindOneBy Order Test Account');
        $account->setAccessKey('findoneby_order_access_key');
        $account->setSecretKey('findoneby_order_secret_key');
        $account->setValid(true);
        $accountRepository->save($account);

        $bucket1 = new Bucket();
        $bucket1->setAccount($account);
        $bucket1->setName('findoneby-order-test-b');
        $bucket1->setRegion('z0');
        $bucket1->setDomain('findonebyorderb.example.com');
        $bucket1->setPrivate(false);
        $bucket1->setValid(true);

        $bucket2 = new Bucket();
        $bucket2->setAccount($account);
        $bucket2->setName('findoneby-order-test-a');
        $bucket2->setRegion('z1');
        $bucket2->setDomain('findonebyordera.example.com');
        $bucket2->setPrivate(false);
        $bucket2->setValid(true);

        $repository->save($bucket1);
        $repository->save($bucket2);

        $result = $repository->findOneBy(['valid' => true], ['name' => 'ASC']);

        $this->assertInstanceOf(Bucket::class, $result);
    }

    public function testFindByWithNullValidShouldReturnEntitiesWithNullValid(): void
    {
        $repository = self::getService(BucketRepository::class);
        $accountRepository = self::getService(AccountRepository::class);

        $account = new Account();
        $account->setName('FindBy Null Valid Test Account');
        $account->setAccessKey('findby_null_valid_access_key');
        $account->setSecretKey('findby_null_valid_secret_key');
        $account->setValid(true);
        $accountRepository->save($account);

        $bucket1 = new Bucket();
        $bucket1->setAccount($account);
        $bucket1->setName('findby-null-valid-bucket-1');
        $bucket1->setRegion('z0');
        $bucket1->setDomain('findbynullvalid1.example.com');
        $bucket1->setPrivate(false);
        $bucket1->setValid(null);

        $bucket2 = new Bucket();
        $bucket2->setAccount($account);
        $bucket2->setName('findby-true-valid-bucket-2');
        $bucket2->setRegion('z1');
        $bucket2->setDomain('findbytruevalid2.example.com');
        $bucket2->setPrivate(false);
        $bucket2->setValid(true);

        $repository->save($bucket1);
        $repository->save($bucket2);

        $resultsWithNullValid = $repository->createQueryBuilder('b')
            ->where('b.valid IS NULL')
            ->getQuery()
            ->getResult()
        ;

        $this->assertIsArray($resultsWithNullValid);
        $this->assertContainsOnlyInstancesOf(Bucket::class, $resultsWithNullValid);
        $this->assertGreaterThanOrEqual(1, count($resultsWithNullValid));

        foreach ($resultsWithNullValid as $bucket) {
            $this->assertNull($bucket->isValid());
        }
    }

    public function testCountWithNullValidField(): void
    {
        $repository = self::getService(BucketRepository::class);
        $accountRepository = self::getService(AccountRepository::class);

        $account = new Account();
        $account->setName('Count Valid Null Field Test Account');
        $account->setAccessKey('count_valid_null_field_access_key');
        $account->setSecretKey('count_valid_null_field_secret_key');
        $account->setValid(true);
        $accountRepository->save($account);

        $bucket1 = new Bucket();
        $bucket1->setAccount($account);
        $bucket1->setName('count-valid-null-field-bucket-1');
        $bucket1->setRegion('z0');
        $bucket1->setDomain('countvalidnullfield1.example.com');
        $bucket1->setPrivate(false);
        $bucket1->setValid(null);

        $bucket2 = new Bucket();
        $bucket2->setAccount($account);
        $bucket2->setName('count-valid-true-field-bucket-2');
        $bucket2->setRegion('z1');
        $bucket2->setDomain('countvalidtruefield2.example.com');
        $bucket2->setPrivate(false);
        $bucket2->setValid(true);

        $repository->save($bucket1);
        $repository->save($bucket2);

        $nullCount = $repository->createQueryBuilder('b')
            ->select('COUNT(b.id)')
            ->where('b.valid IS NULL')
            ->getQuery()
            ->getSingleScalarResult()
        ;

        $this->assertIsInt((int) $nullCount);
        $this->assertGreaterThanOrEqual(1, (int) $nullCount);
    }

    public function testSaveShouldPersistEntity(): void
    {
        $repository = self::getService(BucketRepository::class);
        $accountRepository = self::getService(AccountRepository::class);

        $account = new Account();
        $account->setName('Save Test Account');
        $account->setAccessKey('save_access_key');
        $account->setSecretKey('save_secret_key');
        $account->setValid(true);
        $accountRepository->save($account);

        $bucket = new Bucket();
        $bucket->setAccount($account);
        $bucket->setName('save-test-bucket');
        $bucket->setRegion('z0');
        $bucket->setDomain('save.example.com');
        $bucket->setPrivate(false);
        $bucket->setValid(true);

        $repository->save($bucket);

        $this->assertNotNull($bucket->getId());

        $foundBucket = $repository->find($bucket->getId());
        $this->assertInstanceOf(Bucket::class, $foundBucket);
        $this->assertEquals('save-test-bucket', $foundBucket->getName());
    }

    public function testSaveWithFlushFalseShouldNotFlushImmediately(): void
    {
        $repository = self::getService(BucketRepository::class);
        $accountRepository = self::getService(AccountRepository::class);

        $account = new Account();
        $account->setName('Save No Flush Test Account');
        $account->setAccessKey('save_noflush_access_key');
        $account->setSecretKey('save_noflush_secret_key');
        $account->setValid(true);
        $accountRepository->save($account);

        $bucket = new Bucket();
        $bucket->setAccount($account);
        $bucket->setName('save-noflush-test-bucket');
        $bucket->setRegion('z0');
        $bucket->setDomain('savenoflush.example.com');
        $bucket->setPrivate(false);
        $bucket->setValid(true);

        $repository->save($bucket, false);
        $this->assertNull($bucket->getId());

        self::getService(EntityManagerInterface::class)->flush();
        $this->assertNotNull($bucket->getId());
    }

    public function testRemoveShouldDeleteEntity(): void
    {
        $repository = self::getService(BucketRepository::class);
        $accountRepository = self::getService(AccountRepository::class);

        $account = new Account();
        $account->setName('Remove Test Account');
        $account->setAccessKey('remove_access_key');
        $account->setSecretKey('remove_secret_key');
        $account->setValid(true);
        $accountRepository->save($account);

        $bucket = new Bucket();
        $bucket->setAccount($account);
        $bucket->setName('remove-test-bucket');
        $bucket->setRegion('z0');
        $bucket->setDomain('remove.example.com');
        $bucket->setPrivate(false);
        $bucket->setValid(true);

        $repository->save($bucket);
        $this->assertNotNull($bucket->getId());

        $bucketId = $bucket->getId();
        $repository->remove($bucket);

        $foundBucket = $repository->find($bucketId);
        $this->assertNull($foundBucket);
    }

    public function testRemoveWithFlushFalseShouldNotFlushImmediately(): void
    {
        $repository = self::getService(BucketRepository::class);
        $accountRepository = self::getService(AccountRepository::class);

        $account = new Account();
        $account->setName('Remove No Flush Test Account');
        $account->setAccessKey('remove_noflush_access_key');
        $account->setSecretKey('remove_noflush_secret_key');
        $account->setValid(true);
        $accountRepository->save($account);

        $bucket = new Bucket();
        $bucket->setAccount($account);
        $bucket->setName('remove-noflush-test-bucket');
        $bucket->setRegion('z0');
        $bucket->setDomain('removenoflush.example.com');
        $bucket->setPrivate(false);
        $bucket->setValid(true);

        $repository->save($bucket);
        $this->assertNotNull($bucket->getId());

        $bucketId = $bucket->getId();
        $repository->remove($bucket, false);

        $foundBucket = $repository->find($bucketId);
        $this->assertInstanceOf(Bucket::class, $foundBucket);

        self::getService(EntityManagerInterface::class)->flush();
        $foundBucket = $repository->find($bucketId);
        $this->assertNull($foundBucket);
    }

    public function testCountWithAccountAssociation(): void
    {
        $repository = self::getService(BucketRepository::class);
        $accountRepository = self::getService(AccountRepository::class);

        $account1 = new Account();
        $account1->setName('Count Association Test Account 1');
        $account1->setAccessKey('count_assoc_access_key_1');
        $account1->setSecretKey('count_assoc_secret_key_1');
        $account1->setValid(true);
        $accountRepository->save($account1);

        $account2 = new Account();
        $account2->setName('Count Association Test Account 2');
        $account2->setAccessKey('count_assoc_access_key_2');
        $account2->setSecretKey('count_assoc_secret_key_2');
        $account2->setValid(true);
        $accountRepository->save($account2);

        $bucket1 = new Bucket();
        $bucket1->setAccount($account1);
        $bucket1->setName('count-assoc-bucket-1');
        $bucket1->setRegion('z0');
        $bucket1->setDomain('countassoc1.example.com');
        $bucket1->setPrivate(false);
        $bucket1->setValid(true);

        $bucket2 = new Bucket();
        $bucket2->setAccount($account1);
        $bucket2->setName('count-assoc-bucket-2');
        $bucket2->setRegion('z1');
        $bucket2->setDomain('countassoc2.example.com');
        $bucket2->setPrivate(false);
        $bucket2->setValid(true);

        $bucket3 = new Bucket();
        $bucket3->setAccount($account2);
        $bucket3->setName('count-assoc-bucket-3');
        $bucket3->setRegion('z0');
        $bucket3->setDomain('countassoc3.example.com');
        $bucket3->setPrivate(false);
        $bucket3->setValid(true);

        $repository->save($bucket1);
        $repository->save($bucket2);
        $repository->save($bucket3);

        $countForAccount1 = $repository->count(['account' => $account1]);
        $countForAccount2 = $repository->count(['account' => $account2]);

        $this->assertIsInt($countForAccount1);
        $this->assertIsInt($countForAccount2);
        $this->assertGreaterThanOrEqual(2, $countForAccount1);
        $this->assertGreaterThanOrEqual(1, $countForAccount2);
    }

    public function testFindOneByWithAccountAssociation(): void
    {
        $repository = self::getService(BucketRepository::class);
        $accountRepository = self::getService(AccountRepository::class);

        $account = new Account();
        $account->setName('FindOneBy Association Test Account');
        $account->setAccessKey('findoneby_assoc_access_key');
        $account->setSecretKey('findoneby_assoc_secret_key');
        $account->setValid(true);
        $accountRepository->save($account);

        $bucket = new Bucket();
        $bucket->setAccount($account);
        $bucket->setName('findoneby-assoc-bucket');
        $bucket->setRegion('z0');
        $bucket->setDomain('findonebyassoc.example.com');
        $bucket->setPrivate(false);
        $bucket->setValid(true);

        $repository->save($bucket);

        $result = $repository->findOneBy(['account' => $account]);

        $this->assertInstanceOf(Bucket::class, $result);
        $this->assertEquals($account->getId(), $result->getAccount()->getId());
    }

    public function testFindAllWithAccountAssociation(): void
    {
        $repository = self::getService(BucketRepository::class);
        $accountRepository = self::getService(AccountRepository::class);

        $account = new Account();
        $account->setName('FindAll Association Test Account');
        $account->setAccessKey('findall_assoc_access_key');
        $account->setSecretKey('findall_assoc_secret_key');
        $account->setValid(true);
        $accountRepository->save($account);

        $bucket1 = new Bucket();
        $bucket1->setAccount($account);
        $bucket1->setName('findall-assoc-bucket-1');
        $bucket1->setRegion('z0');
        $bucket1->setDomain('findallassoc1.example.com');
        $bucket1->setPrivate(false);
        $bucket1->setValid(true);

        $bucket2 = new Bucket();
        $bucket2->setAccount($account);
        $bucket2->setName('findall-assoc-bucket-2');
        $bucket2->setRegion('z1');
        $bucket2->setDomain('findallassoc2.example.com');
        $bucket2->setPrivate(false);
        $bucket2->setValid(true);

        $repository->save($bucket1);
        $repository->save($bucket2);

        $results = $repository->findBy(['account' => $account]);

        $this->assertIsArray($results);
        $this->assertContainsOnlyInstancesOf(Bucket::class, $results);
        $this->assertGreaterThanOrEqual(2, count($results));

        foreach ($results as $bucket) {
            $this->assertEquals($account->getId(), $bucket->getAccount()->getId());
        }
    }

    public function testCountWithNullableValidField(): void
    {
        $repository = self::getService(BucketRepository::class);
        $accountRepository = self::getService(AccountRepository::class);

        $account = new Account();
        $account->setName('Count Valid Null Test Account');
        $account->setAccessKey('count_valid_null_access_key');
        $account->setSecretKey('count_valid_null_secret_key');
        $account->setValid(true);
        $accountRepository->save($account);

        $bucket1 = new Bucket();
        $bucket1->setAccount($account);
        $bucket1->setName('count-valid-null-bucket-1');
        $bucket1->setRegion('z0');
        $bucket1->setDomain('countvalidnull1.example.com');
        $bucket1->setPrivate(false);
        $bucket1->setValid(null);

        $bucket2 = new Bucket();
        $bucket2->setAccount($account);
        $bucket2->setName('count-valid-true-bucket-2');
        $bucket2->setRegion('z1');
        $bucket2->setDomain('countvalidtrue2.example.com');
        $bucket2->setPrivate(false);
        $bucket2->setValid(true);

        $repository->save($bucket1);
        $repository->save($bucket2);

        $nullCount = $repository->createQueryBuilder('b')
            ->select('COUNT(b.id)')
            ->where('b.valid IS NULL')
            ->getQuery()
            ->getSingleScalarResult()
        ;

        $notNullCount = $repository->createQueryBuilder('b')
            ->select('COUNT(b.id)')
            ->where('b.valid IS NOT NULL')
            ->getQuery()
            ->getSingleScalarResult()
        ;

        $this->assertIsInt((int) $nullCount);
        $this->assertIsInt((int) $notNullCount);
        $this->assertGreaterThanOrEqual(1, (int) $nullCount);
        $this->assertGreaterThanOrEqual(1, (int) $notNullCount);
    }

    public function testCountWithNullableLastSyncTimeField(): void
    {
        $repository = self::getService(BucketRepository::class);
        $accountRepository = self::getService(AccountRepository::class);

        $account = new Account();
        $account->setName('Count Sync Time Null Test Account');
        $account->setAccessKey('count_sync_null_access_key');
        $account->setSecretKey('count_sync_null_secret_key');
        $account->setValid(true);
        $accountRepository->save($account);

        $bucket1 = new Bucket();
        $bucket1->setAccount($account);
        $bucket1->setName('count-sync-null-bucket-1');
        $bucket1->setRegion('z0');
        $bucket1->setDomain('countsyncnull1.example.com');
        $bucket1->setPrivate(false);
        $bucket1->setLastSyncTime(null);

        $bucket2 = new Bucket();
        $bucket2->setAccount($account);
        $bucket2->setName('count-sync-time-bucket-2');
        $bucket2->setRegion('z1');
        $bucket2->setDomain('countsynctime2.example.com');
        $bucket2->setPrivate(false);
        $bucket2->setLastSyncTime(new \DateTimeImmutable('2024-01-01'));

        $repository->save($bucket1);
        $repository->save($bucket2);

        $nullCount = $repository->createQueryBuilder('b')
            ->select('COUNT(b.id)')
            ->where('b.lastSyncTime IS NULL')
            ->getQuery()
            ->getSingleScalarResult()
        ;

        $notNullCount = $repository->createQueryBuilder('b')
            ->select('COUNT(b.id)')
            ->where('b.lastSyncTime IS NOT NULL')
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
        $repository = self::getService(BucketRepository::class);
        $accountRepository = self::getService(AccountRepository::class);

        $account = new Account();
        $account->setName('Count Remark Null Test Account');
        $account->setAccessKey('count_remark_null_access_key');
        $account->setSecretKey('count_remark_null_secret_key');
        $account->setValid(true);
        $accountRepository->save($account);

        $bucket1 = new Bucket();
        $bucket1->setAccount($account);
        $bucket1->setName('count-remark-null-bucket-1');
        $bucket1->setRegion('z0');
        $bucket1->setDomain('countremarknull1.example.com');
        $bucket1->setPrivate(false);
        $bucket1->setRemark(null);

        $bucket2 = new Bucket();
        $bucket2->setAccount($account);
        $bucket2->setName('count-remark-bucket-2');
        $bucket2->setRegion('z1');
        $bucket2->setDomain('countremark2.example.com');
        $bucket2->setPrivate(false);
        $bucket2->setRemark('Test remark');

        $repository->save($bucket1);
        $repository->save($bucket2);

        $nullCount = $repository->createQueryBuilder('b')
            ->select('COUNT(b.id)')
            ->where('b.remark IS NULL')
            ->getQuery()
            ->getSingleScalarResult()
        ;

        $notNullCount = $repository->createQueryBuilder('b')
            ->select('COUNT(b.id)')
            ->where('b.remark IS NOT NULL')
            ->getQuery()
            ->getSingleScalarResult()
        ;

        $this->assertIsInt((int) $nullCount);
        $this->assertIsInt((int) $notNullCount);
        $this->assertGreaterThanOrEqual(1, (int) $nullCount);
        $this->assertGreaterThanOrEqual(1, (int) $notNullCount);
    }

    public function testFindOneByWithValidNullQueryShouldWork(): void
    {
        $repository = self::getService(BucketRepository::class);
        $accountRepository = self::getService(AccountRepository::class);

        $account = new Account();
        $account->setName('Valid Null Query Test Account');
        $account->setAccessKey('valid_null_query_access_key');
        $account->setSecretKey('valid_null_query_secret_key');
        $account->setValid(true);
        $accountRepository->save($account);

        $bucket1 = new Bucket();
        $bucket1->setAccount($account);
        $bucket1->setName('valid-null-query-bucket-1');
        $bucket1->setRegion('z0');
        $bucket1->setDomain('validnullquery1.example.com');
        $bucket1->setPrivate(false);
        $bucket1->setValid(null);

        $bucket2 = new Bucket();
        $bucket2->setAccount($account);
        $bucket2->setName('valid-null-query-bucket-2');
        $bucket2->setRegion('z1');
        $bucket2->setDomain('validnullquery2.example.com');
        $bucket2->setPrivate(false);
        $bucket2->setValid(true);

        $repository->save($bucket1);
        $repository->save($bucket2);

        $result = $repository->createQueryBuilder('b')
            ->where('b.valid IS NULL')
            ->getQuery()
            ->getOneOrNullResult()
        ;

        $this->assertInstanceOf(Bucket::class, $result);
        $this->assertNull($result->isValid());
    }

    public function testFindOneByWithRemarkNullQueryShouldWork(): void
    {
        $repository = self::getService(BucketRepository::class);
        $accountRepository = self::getService(AccountRepository::class);

        $account = new Account();
        $account->setName('Remark Null Query Test Account');
        $account->setAccessKey('remark_null_query_access_key');
        $account->setSecretKey('remark_null_query_secret_key');
        $account->setValid(true);
        $accountRepository->save($account);

        $bucket1 = new Bucket();
        $bucket1->setAccount($account);
        $bucket1->setName('remark-null-query-bucket-1');
        $bucket1->setRegion('z0');
        $bucket1->setDomain('remarknullquery1.example.com');
        $bucket1->setPrivate(false);
        $bucket1->setRemark(null);

        $bucket2 = new Bucket();
        $bucket2->setAccount($account);
        $bucket2->setName('remark-null-query-bucket-2');
        $bucket2->setRegion('z1');
        $bucket2->setDomain('remarknullquery2.example.com');
        $bucket2->setPrivate(false);
        $bucket2->setRemark('Test remark');

        $repository->save($bucket1);
        $repository->save($bucket2);

        $result = $repository->createQueryBuilder('b')
            ->where('b.remark IS NULL')
            ->getQuery()
            ->getOneOrNullResult()
        ;

        $this->assertInstanceOf(Bucket::class, $result);
        $this->assertNull($result->getRemark());
    }

    public function testFindOneByWithLastSyncTimeNullQueryShouldWork(): void
    {
        $repository = self::getService(BucketRepository::class);
        $accountRepository = self::getService(AccountRepository::class);

        $account = new Account();
        $account->setName('LastSyncTime Null Query Test Account');
        $account->setAccessKey('last_sync_time_null_query_access_key');
        $account->setSecretKey('last_sync_time_null_query_secret_key');
        $account->setValid(true);
        $accountRepository->save($account);

        $bucket1 = new Bucket();
        $bucket1->setAccount($account);
        $bucket1->setName('last-sync-time-null-query-bucket-1');
        $bucket1->setRegion('z0');
        $bucket1->setDomain('lastsynctimenovery1.example.com');
        $bucket1->setPrivate(false);
        $bucket1->setLastSyncTime(null);

        $bucket2 = new Bucket();
        $bucket2->setAccount($account);
        $bucket2->setName('last-sync-time-null-query-bucket-2');
        $bucket2->setRegion('z1');
        $bucket2->setDomain('lastsynctimenovery2.example.com');
        $bucket2->setPrivate(false);
        $bucket2->setLastSyncTime(new \DateTimeImmutable('2024-01-01'));

        $repository->save($bucket1);
        $repository->save($bucket2);

        $result = $repository->createQueryBuilder('b')
            ->where('b.lastSyncTime IS NULL')
            ->andWhere('b.name = :name')
            ->setParameter('name', 'last-sync-time-null-query-bucket-1')
            ->getQuery()
            ->getOneOrNullResult()
        ;

        $this->assertInstanceOf(Bucket::class, $result);
        $this->assertNull($result->getLastSyncTime());
    }

    public function testCountWithAccountAssociationQuery(): void
    {
        $repository = self::getService(BucketRepository::class);
        $accountRepository = self::getService(AccountRepository::class);

        $account1 = new Account();
        $account1->setName('Count Association Query Test Account 1');
        $account1->setAccessKey('count_assoc_query_access_key_1');
        $account1->setSecretKey('count_assoc_query_secret_key_1');
        $account1->setValid(true);
        $accountRepository->save($account1);

        $account2 = new Account();
        $account2->setName('Count Association Query Test Account 2');
        $account2->setAccessKey('count_assoc_query_access_key_2');
        $account2->setSecretKey('count_assoc_query_secret_key_2');
        $account2->setValid(true);
        $accountRepository->save($account2);

        $bucket1 = new Bucket();
        $bucket1->setAccount($account1);
        $bucket1->setName('count-assoc-query-bucket-1');
        $bucket1->setRegion('z0');
        $bucket1->setDomain('countassocquery1.example.com');
        $bucket1->setPrivate(false);
        $bucket1->setValid(true);

        $bucket2 = new Bucket();
        $bucket2->setAccount($account2);
        $bucket2->setName('count-assoc-query-bucket-2');
        $bucket2->setRegion('z1');
        $bucket2->setDomain('countassocquery2.example.com');
        $bucket2->setPrivate(false);
        $bucket2->setValid(true);

        $repository->save($bucket1);
        $repository->save($bucket2);

        $countForAccount1 = $repository->createQueryBuilder('b')
            ->select('COUNT(b.id)')
            ->where('b.account = :account')
            ->setParameter('account', $account1)
            ->getQuery()
            ->getSingleScalarResult()
        ;

        $this->assertIsInt((int) $countForAccount1);
        $this->assertGreaterThanOrEqual(1, (int) $countForAccount1);
    }

    public function testFindByWithAccountAssociationQuery(): void
    {
        $repository = self::getService(BucketRepository::class);
        $accountRepository = self::getService(AccountRepository::class);

        $account = new Account();
        $account->setName('FindBy Association Query Test Account');
        $account->setAccessKey('findby_assoc_query_access_key');
        $account->setSecretKey('findby_assoc_query_secret_key');
        $account->setValid(true);
        $accountRepository->save($account);

        $bucket1 = new Bucket();
        $bucket1->setAccount($account);
        $bucket1->setName('findby-assoc-query-bucket-1');
        $bucket1->setRegion('z0');
        $bucket1->setDomain('findbyassocquery1.example.com');
        $bucket1->setPrivate(false);
        $bucket1->setValid(true);

        $bucket2 = new Bucket();
        $bucket2->setAccount($account);
        $bucket2->setName('findby-assoc-query-bucket-2');
        $bucket2->setRegion('z1');
        $bucket2->setDomain('findbyassocquery2.example.com');
        $bucket2->setPrivate(false);
        $bucket2->setValid(true);

        $repository->save($bucket1);
        $repository->save($bucket2);

        $results = $repository->createQueryBuilder('b')
            ->where('b.account = :account')
            ->setParameter('account', $account)
            ->getQuery()
            ->getResult()
        ;

        $this->assertIsArray($results);
        $this->assertContainsOnlyInstancesOf(Bucket::class, $results);
        $this->assertGreaterThanOrEqual(2, count($results));

        foreach ($results as $bucket) {
            $this->assertEquals($account->getId(), $bucket->getAccount()->getId());
        }
    }

    public function testFindOneByWithAccountAssociationQuery(): void
    {
        $repository = self::getService(BucketRepository::class);
        $accountRepository = self::getService(AccountRepository::class);

        $account = new Account();
        $account->setName('FindOneBy Association Query Test Account');
        $account->setAccessKey('findoneby_assoc_query_access_key');
        $account->setSecretKey('findoneby_assoc_query_secret_key');
        $account->setValid(true);
        $accountRepository->save($account);

        $bucket = new Bucket();
        $bucket->setAccount($account);
        $bucket->setName('findoneby-assoc-query-bucket');
        $bucket->setRegion('z0');
        $bucket->setDomain('findonebyassocquery.example.com');
        $bucket->setPrivate(false);
        $bucket->setValid(true);

        $repository->save($bucket);

        $result = $repository->createQueryBuilder('b')
            ->where('b.account = :account')
            ->setParameter('account', $account)
            ->getQuery()
            ->getOneOrNullResult()
        ;

        $this->assertInstanceOf(Bucket::class, $result);
        $this->assertEquals($account->getId(), $result->getAccount()->getId());
    }

    public function testCountWithNullLastSyncTimeField(): void
    {
        $repository = self::getService(BucketRepository::class);
        $accountRepository = self::getService(AccountRepository::class);

        $account = new Account();
        $account->setName('Count Null LastSyncTime Field Test Account');
        $account->setAccessKey('count_null_last_sync_time_field_access_key');
        $account->setSecretKey('count_null_last_sync_time_field_secret_key');
        $account->setValid(true);
        $accountRepository->save($account);

        $bucket1 = new Bucket();
        $bucket1->setAccount($account);
        $bucket1->setName('count-null-last-sync-time-field-bucket-1');
        $bucket1->setRegion('z0');
        $bucket1->setDomain('countnulllastsynctimefield1.example.com');
        $bucket1->setPrivate(false);
        $bucket1->setLastSyncTime(null);

        $bucket2 = new Bucket();
        $bucket2->setAccount($account);
        $bucket2->setName('count-not-null-last-sync-time-field-bucket-2');
        $bucket2->setRegion('z1');
        $bucket2->setDomain('countnotnulllastsynctimefield2.example.com');
        $bucket2->setPrivate(false);
        $bucket2->setLastSyncTime(new \DateTimeImmutable('2024-01-01'));

        $repository->save($bucket1);
        $repository->save($bucket2);

        $nullCount = $repository->createQueryBuilder('b')
            ->select('COUNT(b.id)')
            ->where('b.lastSyncTime IS NULL')
            ->getQuery()
            ->getSingleScalarResult()
        ;

        $this->assertIsInt((int) $nullCount);
        $this->assertGreaterThanOrEqual(1, (int) $nullCount);
    }

    public function testCountWithNullRemarkFieldQuery(): void
    {
        $repository = self::getService(BucketRepository::class);
        $accountRepository = self::getService(AccountRepository::class);

        $account = new Account();
        $account->setName('Count Null Remark Field Query Test Account');
        $account->setAccessKey('count_null_remark_field_query_access_key');
        $account->setSecretKey('count_null_remark_field_query_secret_key');
        $account->setValid(true);
        $accountRepository->save($account);

        $bucket1 = new Bucket();
        $bucket1->setAccount($account);
        $bucket1->setName('count-null-remark-field-query-bucket-1');
        $bucket1->setRegion('z0');
        $bucket1->setDomain('countnullremarkfieldquery1.example.com');
        $bucket1->setPrivate(false);
        $bucket1->setRemark(null);

        $bucket2 = new Bucket();
        $bucket2->setAccount($account);
        $bucket2->setName('count-not-null-remark-field-query-bucket-2');
        $bucket2->setRegion('z1');
        $bucket2->setDomain('countnotnullremarkfieldquery2.example.com');
        $bucket2->setPrivate(false);
        $bucket2->setRemark('Test remark');

        $repository->save($bucket1);
        $repository->save($bucket2);

        $nullCount = $repository->createQueryBuilder('b')
            ->select('COUNT(b.id)')
            ->where('b.remark IS NULL')
            ->getQuery()
            ->getSingleScalarResult()
        ;

        $this->assertIsInt((int) $nullCount);
        $this->assertGreaterThanOrEqual(1, (int) $nullCount);
    }

    public function testFindOneByAssociationAccountShouldReturnMatchingEntity(): void
    {
        $repository = self::getService(BucketRepository::class);
        $accountRepository = self::getService(AccountRepository::class);

        $account = new Account();
        $account->setName('FindOneBy Association Account Test Account');
        $account->setAccessKey('findoneby_assoc_account_access_key');
        $account->setSecretKey('findoneby_assoc_account_secret_key');
        $account->setValid(true);
        $accountRepository->save($account);

        $bucket1 = new Bucket();
        $bucket1->setAccount($account);
        $bucket1->setName('findoneby-assoc-account-bucket-1');
        $bucket1->setRegion('z0');
        $bucket1->setDomain('findonebyassocaccount1.example.com');
        $bucket1->setPrivate(false);
        $bucket1->setValid(true);

        $repository->save($bucket1);

        $result = $repository->findOneBy(['account' => $account]);

        $this->assertInstanceOf(Bucket::class, $result);
        $this->assertEquals($account->getId(), $result->getAccount()->getId());
    }

    public function testCountByAssociationAccountShouldReturnCorrectNumber(): void
    {
        $repository = self::getService(BucketRepository::class);
        $accountRepository = self::getService(AccountRepository::class);

        $account = new Account();
        $account->setName('Count Association Account Test Account');
        $account->setAccessKey('count_assoc_account_access_key');
        $account->setSecretKey('count_assoc_account_secret_key');
        $account->setValid(true);
        $accountRepository->save($account);

        $bucket1 = new Bucket();
        $bucket1->setAccount($account);
        $bucket1->setName('count-assoc-account-bucket-1');
        $bucket1->setRegion('z0');
        $bucket1->setDomain('countassocaccount1.example.com');
        $bucket1->setPrivate(false);
        $bucket1->setValid(true);

        $bucket2 = new Bucket();
        $bucket2->setAccount($account);
        $bucket2->setName('count-assoc-account-bucket-2');
        $bucket2->setRegion('z1');
        $bucket2->setDomain('countassocaccount2.example.com');
        $bucket2->setPrivate(false);
        $bucket2->setValid(true);

        $repository->save($bucket1);
        $repository->save($bucket2);

        $count = $repository->count(['account' => $account]);

        $this->assertIsInt($count);
        $this->assertGreaterThanOrEqual(2, $count);
    }

    protected function createNewEntity(): object
    {
        // 在数据库连接测试中，创建简单的实体对象但不依赖数据库操作
        if ($this->isTestingDatabaseConnection()) {
            $account = new Account();
            $account->setName('Test Account');
            $account->setAccessKey('test_access_key');
            $account->setSecretKey('test_secret_key');
            $account->setValid(true);

            $bucket = new Bucket();
            $bucket->setAccount($account);
            $bucket->setName('test-bucket');
            $bucket->setRegion('z0');
            $bucket->setDomain('test.example.com');
            $bucket->setPrivate(false);
            $bucket->setValid(true);

            return $bucket;
        }

        $accountRepository = self::getService(AccountRepository::class);

        $account = new Account();
        $account->setName('Test Account ' . uniqid());
        $account->setAccessKey('test_access_key_' . uniqid());
        $account->setSecretKey('test_secret_key_' . uniqid());
        $account->setValid(true);
        $accountRepository->save($account);

        $bucket = new Bucket();
        $bucket->setAccount($account);
        $bucket->setName('test-bucket-' . uniqid());
        $bucket->setRegion('z0');
        $bucket->setDomain('test' . uniqid() . '.example.com');
        $bucket->setPrivate(false);
        $bucket->setValid(true);

        return $bucket;
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

    protected function getRepository(): BucketRepository
    {
        return self::getService(BucketRepository::class);
    }
}
