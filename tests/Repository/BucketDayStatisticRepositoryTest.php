<?php

declare(strict_types=1);

namespace QiniuStorageBundle\Tests\Repository;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use QiniuStorageBundle\Entity\Account;
use QiniuStorageBundle\Entity\Bucket;
use QiniuStorageBundle\Entity\BucketDayStatistic;
use QiniuStorageBundle\Repository\AccountRepository;
use QiniuStorageBundle\Repository\BucketDayStatisticRepository;
use QiniuStorageBundle\Repository\BucketRepository;
use Tourze\PHPUnitSymfonyKernelTest\AbstractRepositoryTestCase;

/**
 * BucketDayStatisticRepository 测试类
 *
 * @internal
 */
#[RunTestsInSeparateProcesses]
#[CoversClass(BucketDayStatisticRepository::class)]
final class BucketDayStatisticRepositoryTest extends AbstractRepositoryTestCase
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

    public function testFindByTimeRange(): void
    {
        $repository = self::getService(BucketDayStatisticRepository::class);
        $accountRepository = self::getService(AccountRepository::class);
        $bucketRepository = self::getService(BucketRepository::class);

        // 创建并保存真实的实体用于测试
        $account = new Account();
        $account->setName('Test Account for TimeRange');
        $account->setAccessKey('test_access_key_timerange');
        $account->setSecretKey('test_secret_key_timerange');
        $account->setValid(true);
        $accountRepository->save($account);

        $bucket = new Bucket();
        $bucket->setAccount($account);
        $bucket->setName('test-bucket-timerange');
        $bucket->setRegion('z0');
        $bucket->setDomain('timerange.example.com');
        $bucket->setPrivate(false);
        $bucket->setValid(true);
        $bucketRepository->save($bucket);

        // 创建测试数据
        $statistic1 = new BucketDayStatistic();
        $statistic1->setBucket($bucket);
        $statistic1->setTime(new \DateTimeImmutable('2024-01-15'));
        $statistic1->setStandardStorage(1000);
        $repository->save($statistic1);

        $statistic2 = new BucketDayStatistic();
        $statistic2->setBucket($bucket);
        $statistic2->setTime(new \DateTimeImmutable('2024-02-15')); // 超出范围
        $statistic2->setStandardStorage(2000);
        $repository->save($statistic2);

        $start = new \DateTimeImmutable('2024-01-01');
        $end = new \DateTimeImmutable('2024-01-31');

        $result = $repository->findByTimeRange($bucket, $start, $end);

        $this->assertIsArray($result);
        $this->assertCount(1, $result); // 只有一个在范围内
        $this->assertEquals('2024-01-15', $result[0]->getTime()->format('Y-m-d'));
    }

    public function testFindOneByBucketAndTime(): void
    {
        $repository = self::getService(BucketDayStatisticRepository::class);
        $accountRepository = self::getService(AccountRepository::class);
        $bucketRepository = self::getService(BucketRepository::class);

        // 创建并保存真实的实体用于测试
        $account = new Account();
        $account->setName('Test Account for BucketAndTime');
        $account->setAccessKey('test_access_key_buckettime');
        $account->setSecretKey('test_secret_key_buckettime');
        $account->setValid(true);
        $accountRepository->save($account);

        $bucket = new Bucket();
        $bucket->setAccount($account);
        $bucket->setName('test-bucket-buckettime');
        $bucket->setRegion('z0');
        $bucket->setDomain('buckettime.example.com');
        $bucket->setPrivate(false);
        $bucket->setValid(true);
        $bucketRepository->save($bucket);

        $time = new \DateTimeImmutable('2024-01-01');

        // 测试没有数据时返回null
        $result = $repository->findOneByBucketAndTime($bucket, $time);
        $this->assertNull($result);

        // 创建测试数据
        $statistic = new BucketDayStatistic();
        $statistic->setBucket($bucket);
        $statistic->setTime($time);
        $statistic->setStandardStorage(1000);
        $repository->save($statistic);

        // 测试有数据时返回正确对象
        $result = $repository->findOneByBucketAndTime($bucket, $time);
        $this->assertInstanceOf(BucketDayStatistic::class, $result);
        $this->assertEquals($time->format('Y-m-d'), $result->getTime()->format('Y-m-d'));
        $this->assertEquals(1000, $result->getStandardStorage());
    }

    public function testFindByWithBucketRelation(): void
    {
        $repository = self::getService(BucketDayStatisticRepository::class);
        $accountRepository = self::getService(AccountRepository::class);
        $bucketRepository = self::getService(BucketRepository::class);

        $account = new Account();
        $account->setName('Statistic Test Account');
        $account->setAccessKey('stat_access_key');
        $account->setSecretKey('stat_secret_key');
        $account->setValid(true);
        $accountRepository->save($account);

        $bucket = new Bucket();
        $bucket->setAccount($account);
        $bucket->setName('statistic-bucket');
        $bucket->setRegion('z0');
        $bucket->setDomain('stat.example.com');
        $bucket->setPrivate(false);
        $bucket->setValid(true);
        $bucketRepository->save($bucket);

        $statistic = new BucketDayStatistic();
        $statistic->setBucket($bucket);
        $statistic->setTime(new \DateTimeImmutable('2024-01-01'));
        $statistic->setStandardStorage(2000);
        $statistic->setStandardCount(100);

        $repository->save($statistic);

        $results = $repository->findBy(['bucket' => $bucket]);

        $this->assertIsArray($results);
        $this->assertContainsOnlyInstancesOf(BucketDayStatistic::class, $results);
    }

    public function testSaveShouldPersistEntity(): void
    {
        $repository = self::getService(BucketDayStatisticRepository::class);
        $accountRepository = self::getService(AccountRepository::class);
        $bucketRepository = self::getService(BucketRepository::class);

        $account = new Account();
        $account->setName('Save Test Account');
        $account->setAccessKey('save_test_access_key');
        $account->setSecretKey('save_test_secret_key');
        $account->setValid(true);
        $accountRepository->save($account);

        $bucket = new Bucket();
        $bucket->setAccount($account);
        $bucket->setName('save-test-bucket');
        $bucket->setRegion('z0');
        $bucket->setDomain('savetest.example.com');
        $bucket->setPrivate(false);
        $bucket->setValid(true);
        $bucketRepository->save($bucket);

        $statistic = new BucketDayStatistic();
        $statistic->setBucket($bucket);
        $statistic->setTime(new \DateTimeImmutable('2024-01-01'));
        $statistic->setStandardStorage(4000);

        $repository->save($statistic);

        $this->assertNotNull($statistic->getId());
        $this->assertEquals(4000, $statistic->getStandardStorage());
    }

    public function testRemoveShouldDeleteEntity(): void
    {
        $repository = self::getService(BucketDayStatisticRepository::class);
        $accountRepository = self::getService(AccountRepository::class);
        $bucketRepository = self::getService(BucketRepository::class);

        $account = new Account();
        $account->setName('Remove Test Account');
        $account->setAccessKey('remove_test_access_key');
        $account->setSecretKey('remove_test_secret_key');
        $account->setValid(true);
        $accountRepository->save($account);

        $bucket = new Bucket();
        $bucket->setAccount($account);
        $bucket->setName('remove-test-bucket');
        $bucket->setRegion('z0');
        $bucket->setDomain('removetest.example.com');
        $bucket->setPrivate(false);
        $bucket->setValid(true);
        $bucketRepository->save($bucket);

        $statistic = new BucketDayStatistic();
        $statistic->setBucket($bucket);
        $statistic->setTime(new \DateTimeImmutable('2024-01-01'));
        $statistic->setStandardStorage(5000);
        $repository->save($statistic);

        $id = $statistic->getId();
        $this->assertNotNull($id);

        $repository->remove($statistic);

        $result = $repository->find($id);
        $this->assertNull($result);
    }

    public function testCountWithAssociationQuery(): void
    {
        $repository = self::getService(BucketDayStatisticRepository::class);
        $accountRepository = self::getService(AccountRepository::class);
        $bucketRepository = self::getService(BucketRepository::class);

        $account = new Account();
        $account->setName('Count Association Test Account');
        $account->setAccessKey('count_assoc_access_key');
        $account->setSecretKey('count_assoc_secret_key');
        $account->setValid(true);
        $accountRepository->save($account);

        $bucket = new Bucket();
        $bucket->setAccount($account);
        $bucket->setName('count-assoc-bucket');
        $bucket->setRegion('z0');
        $bucket->setDomain('countassoc.example.com');
        $bucket->setPrivate(false);
        $bucket->setValid(true);
        $bucketRepository->save($bucket);

        $statistic = new BucketDayStatistic();
        $statistic->setBucket($bucket);
        $statistic->setTime(new \DateTimeImmutable('2024-01-01'));
        $statistic->setStandardStorage(6000);
        $repository->save($statistic);

        $count = $repository->count(['bucket' => $bucket]);

        $this->assertIsInt($count);
        $this->assertGreaterThanOrEqual(1, $count);
    }

    public function testFindByWithAssociationQuery(): void
    {
        $repository = self::getService(BucketDayStatisticRepository::class);
        $accountRepository = self::getService(AccountRepository::class);
        $bucketRepository = self::getService(BucketRepository::class);

        $account = new Account();
        $account->setName('FindBy Association Test Account');
        $account->setAccessKey('findby_assoc_access_key');
        $account->setSecretKey('findby_assoc_secret_key');
        $account->setValid(true);
        $accountRepository->save($account);

        $bucket = new Bucket();
        $bucket->setAccount($account);
        $bucket->setName('findby-assoc-bucket');
        $bucket->setRegion('z0');
        $bucket->setDomain('findbyassoc.example.com');
        $bucket->setPrivate(false);
        $bucket->setValid(true);
        $bucketRepository->save($bucket);

        $statistic = new BucketDayStatistic();
        $statistic->setBucket($bucket);
        $statistic->setTime(new \DateTimeImmutable('2024-01-01'));
        $statistic->setStandardStorage(7000);
        $repository->save($statistic);

        $results = $repository->findBy(['bucket' => $bucket]);

        $this->assertIsArray($results);
        $this->assertContainsOnlyInstancesOf(BucketDayStatistic::class, $results);
        $this->assertGreaterThanOrEqual(1, count($results));
    }

    public function testFindOneByWithAssociationQuery(): void
    {
        $repository = self::getService(BucketDayStatisticRepository::class);
        $accountRepository = self::getService(AccountRepository::class);
        $bucketRepository = self::getService(BucketRepository::class);

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
        $bucketRepository->save($bucket);

        $statistic = new BucketDayStatistic();
        $statistic->setBucket($bucket);
        $statistic->setTime(new \DateTimeImmutable('2024-01-01'));
        $statistic->setStandardStorage(8000);
        $repository->save($statistic);

        $result = $repository->findOneBy(['bucket' => $bucket]);

        $this->assertInstanceOf(BucketDayStatistic::class, $result);
        $this->assertEquals($bucket->getId(), $result->getBucket()->getId());
        $this->assertEquals(8000, $result->getStandardStorage());
    }

    public function testCountByAssociationBucketShouldReturnCorrectNumber(): void
    {
        $repository = self::getService(BucketDayStatisticRepository::class);
        $accountRepository = self::getService(AccountRepository::class);
        $bucketRepository = self::getService(BucketRepository::class);

        $account = new Account();
        $account->setName('Count Bucket Association Test Account');
        $account->setAccessKey('count_bucket_assoc_access_key');
        $account->setSecretKey('count_bucket_assoc_secret_key');
        $account->setValid(true);
        $accountRepository->save($account);

        $bucket = new Bucket();
        $bucket->setAccount($account);
        $bucket->setName('count-bucket-assoc-bucket');
        $bucket->setRegion('z0');
        $bucket->setDomain('countbucketassoc.example.com');
        $bucket->setPrivate(false);
        $bucket->setValid(true);
        $bucketRepository->save($bucket);

        $statistic1 = new BucketDayStatistic();
        $statistic1->setBucket($bucket);
        $statistic1->setTime(new \DateTimeImmutable('2024-01-01'));
        $statistic1->setStandardStorage(1000);

        $statistic2 = new BucketDayStatistic();
        $statistic2->setBucket($bucket);
        $statistic2->setTime(new \DateTimeImmutable('2024-01-02'));
        $statistic2->setStandardStorage(2000);

        $repository->save($statistic1);
        $repository->save($statistic2);

        $count = $repository->count(['bucket' => $bucket]);

        $this->assertIsInt($count);
        $this->assertGreaterThanOrEqual(2, $count);
    }

    public function testFindOneByAssociationBucketShouldReturnMatchingEntity(): void
    {
        $repository = self::getService(BucketDayStatisticRepository::class);
        $accountRepository = self::getService(AccountRepository::class);
        $bucketRepository = self::getService(BucketRepository::class);

        $account = new Account();
        $account->setName('FindOneBy Association Bucket Test Account');
        $account->setAccessKey('findoneby_assoc_bucket_access_key');
        $account->setSecretKey('findoneby_assoc_bucket_secret_key');
        $account->setValid(true);
        $accountRepository->save($account);

        $bucket = new Bucket();
        $bucket->setAccount($account);
        $bucket->setName('findoneby-assoc-bucket');
        $bucket->setRegion('z0');
        $bucket->setDomain('findonebyassocbucket.example.com');
        $bucket->setPrivate(false);
        $bucket->setValid(true);
        $bucketRepository->save($bucket);

        $statistic = new BucketDayStatistic();
        $statistic->setBucket($bucket);
        $statistic->setTime(new \DateTimeImmutable('2024-01-01'));
        $statistic->setStandardStorage(8000);
        $repository->save($statistic);

        $result = $repository->findOneBy(['bucket' => $bucket]);

        $this->assertInstanceOf(BucketDayStatistic::class, $result);
        $this->assertEquals($bucket->getId(), $result->getBucket()->getId());
        $this->assertEquals(8000, $result->getStandardStorage());
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

            $statistic = new BucketDayStatistic();
            $statistic->setBucket($bucket);
            $statistic->setTime(new \DateTimeImmutable());
            $statistic->setStandardStorage(1000);
            $statistic->setStandardCount(50);

            return $statistic;
        }

        $accountRepository = self::getService(AccountRepository::class);
        $bucketRepository = self::getService(BucketRepository::class);

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
        $bucketRepository->save($bucket);

        $statistic = new BucketDayStatistic();
        $statistic->setBucket($bucket);
        $statistic->setTime(new \DateTimeImmutable());
        $statistic->setStandardStorage(1000);
        $statistic->setStandardCount(50);

        return $statistic;
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

    protected function getRepository(): BucketDayStatisticRepository
    {
        return self::getService(BucketDayStatisticRepository::class);
    }
}
