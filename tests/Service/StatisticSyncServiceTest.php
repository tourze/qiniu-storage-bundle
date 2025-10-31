<?php

declare(strict_types=1);

namespace QiniuStorageBundle\Tests\Service;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use QiniuStorageBundle\Entity\Account;
use QiniuStorageBundle\Entity\Bucket;
use QiniuStorageBundle\Enum\IntelligentTieringTier;
use QiniuStorageBundle\Enum\TimeGranularity;
use QiniuStorageBundle\Repository\AccountRepository;
use QiniuStorageBundle\Repository\BucketRepository;
use QiniuStorageBundle\Service\StatisticSyncService;
use QiniuStorageBundle\Service\StorageStatisticsService;
use Symfony\Component\Console\Style\SymfonyStyle;
use Tourze\PHPUnitSymfonyKernelTest\AbstractIntegrationTestCase;

/**
 * StatisticSyncService 测试类
 *
 * @internal
 */
#[CoversClass(StatisticSyncService::class)]
#[RunTestsInSeparateProcesses]
final class StatisticSyncServiceTest extends AbstractIntegrationTestCase
{
    public function testConstructor(): void
    {
        $this->expectNotToPerformAssertions();
        // 基础构造函数测试
    }

    protected function onSetUp(): void
    {
        // 简化设置：创建一个 StorageStatisticsService 的匿名类实现
        /** @phpstan-ignore-next-line */
        $mockStorageStatisticsService = new class extends StorageStatisticsService {
            /** @phpstan-ignore-next-line constructor.missingParentCall */
            public function __construct()
            {
                // 简化构造函数，避免复杂的依赖链
            }

            public function getStandardStorage(TimeGranularity $g, Bucket $bucket, string $begin, string $end, SymfonyStyle $io): int
            {
                return 1024;
            }

            public function getLineStorage(TimeGranularity $g, Bucket $bucket, string $begin, string $end, SymfonyStyle $io): int
            {
                return 512;
            }

            public function getArchiveStorage(TimeGranularity $g, Bucket $bucket, string $begin, string $end, SymfonyStyle $io): int
            {
                return 256;
            }

            public function getArchiveIrStorage(TimeGranularity $g, Bucket $bucket, string $begin, string $end, SymfonyStyle $io): int
            {
                return 128;
            }

            public function getDeepArchiveStorage(TimeGranularity $g, Bucket $bucket, string $begin, string $end, SymfonyStyle $io): int
            {
                return 64;
            }

            public function getIntelligentTieringStorage(TimeGranularity $g, Bucket $bucket, string $begin, string $end, IntelligentTieringTier $tier = IntelligentTieringTier::FREQUENT_ACCESS, ?SymfonyStyle $io = null): int
            {
                return 32;
            }

            public function getStandardCount(TimeGranularity $g, Bucket $bucket, string $begin, string $end, ?SymfonyStyle $io = null): int
            {
                return 100;
            }

            public function getLineCount(TimeGranularity $g, Bucket $bucket, string $begin, string $end, ?SymfonyStyle $io = null): int
            {
                return 50;
            }

            public function getArchiveCount(TimeGranularity $g, Bucket $bucket, string $begin, string $end, ?SymfonyStyle $io = null): int
            {
                return 25;
            }

            public function getArchiveIrCount(TimeGranularity $g, Bucket $bucket, string $begin, string $end, ?SymfonyStyle $io = null): int
            {
                return 12;
            }

            public function getDeepArchiveCount(TimeGranularity $g, Bucket $bucket, string $begin, string $end, ?SymfonyStyle $io = null): int
            {
                return 6;
            }

            public function getIntelligentTieringCount(TimeGranularity $g, Bucket $bucket, string $begin, string $end, IntelligentTieringTier $tier = IntelligentTieringTier::FREQUENT_ACCESS, ?SymfonyStyle $io = null): int
            {
                return 3;
            }

            public function getIntelligentTieringMonitorCount(TimeGranularity $g, Bucket $bucket, string $begin, string $end, ?SymfonyStyle $io = null): int
            {
                return 10;
            }

            public function getPutRequests(TimeGranularity $g, Bucket $bucket, string $begin, string $end, ?SymfonyStyle $io = null): int
            {
                return 200;
            }

            public function getGetRequests(TimeGranularity $g, Bucket $bucket, string $begin, string $end, ?SymfonyStyle $io = null): int
            {
                return 300;
            }

            public function getInternetTraffic(TimeGranularity $g, Bucket $bucket, string $begin, string $end, ?SymfonyStyle $io = null): int
            {
                return 1000;
            }

            public function getCdnTraffic(TimeGranularity $g, Bucket $bucket, string $begin, string $end, ?SymfonyStyle $io = null): int
            {
                return 500;
            }
        };

        self::getContainer()->set(StorageStatisticsService::class, $mockStorageStatisticsService);
    }

    public function testServiceInstantiation(): void
    {
        $service = self::getService(StatisticSyncService::class);

        $this->assertNotNull($service);
        $this->assertInstanceOf(StatisticSyncService::class, $service);
    }

    public function testSyncBucketStatisticShouldProcessSuccessfully(): void
    {
        $service = self::getService(StatisticSyncService::class);
        $accountRepository = self::getService(AccountRepository::class);
        $bucketRepository = self::getService(BucketRepository::class);

        $account = new Account();
        $account->setName('Sync Test Account');
        $account->setAccessKey('sync_test_access_key');
        $account->setSecretKey('sync_test_secret_key');
        $account->setValid(true);
        $accountRepository->save($account);

        $bucket = new Bucket();
        $bucket->setAccount($account);
        $bucket->setName('sync-test-bucket');
        $bucket->setRegion('z0');
        $bucket->setDomain('synctest.example.com');
        $bucket->setPrivate(false);
        $bucket->setValid(true);
        $bucketRepository->save($bucket);

        $time = new \DateTimeImmutable('2024-01-01 12:00:00');
        $granularity = TimeGranularity::DAY;

        $this->expectNotToPerformAssertions();
        $service->syncBucketStatistic($granularity, $time, $bucket);
    }

    public function testSyncBucketStatisticWithHourGranularityShouldWork(): void
    {
        $service = self::getService(StatisticSyncService::class);
        $accountRepository = self::getService(AccountRepository::class);
        $bucketRepository = self::getService(BucketRepository::class);

        $account = new Account();
        $account->setName('Sync Hour Test Account');
        $account->setAccessKey('sync_hour_test_access_key');
        $account->setSecretKey('sync_hour_test_secret_key');
        $account->setValid(true);
        $accountRepository->save($account);

        $bucket = new Bucket();
        $bucket->setAccount($account);
        $bucket->setName('sync-hour-test-bucket');
        $bucket->setRegion('z0');
        $bucket->setDomain('synchourtest.example.com');
        $bucket->setPrivate(false);
        $bucket->setValid(true);
        $bucketRepository->save($bucket);

        $time = new \DateTimeImmutable('2024-01-01 15:00:00');
        $granularity = TimeGranularity::HOUR;

        $this->expectNotToPerformAssertions();
        $service->syncBucketStatistic($granularity, $time, $bucket);
    }

    public function testSyncBucketStatisticWithMinuteGranularityShouldWork(): void
    {
        $service = self::getService(StatisticSyncService::class);
        $accountRepository = self::getService(AccountRepository::class);
        $bucketRepository = self::getService(BucketRepository::class);

        $account = new Account();
        $account->setName('Sync Minute Test Account');
        $account->setAccessKey('sync_minute_test_access_key');
        $account->setSecretKey('sync_minute_test_secret_key');
        $account->setValid(true);
        $accountRepository->save($account);

        $bucket = new Bucket();
        $bucket->setAccount($account);
        $bucket->setName('sync-minute-test-bucket');
        $bucket->setRegion('z0');
        $bucket->setDomain('syncminutetest.example.com');
        $bucket->setPrivate(false);
        $bucket->setValid(true);
        $bucketRepository->save($bucket);

        $time = new \DateTimeImmutable('2024-01-01 15:30:00');
        $granularity = TimeGranularity::MINUTE;

        $this->expectNotToPerformAssertions();
        $service->syncBucketStatistic($granularity, $time, $bucket);
    }

    public function testGetValidBucketsShouldReturnArray(): void
    {
        $service = self::getService(StatisticSyncService::class);

        $validBuckets = $service->getValidBuckets();

        $this->assertIsArray($validBuckets);
        $this->assertContainsOnlyInstancesOf(Bucket::class, $validBuckets);
    }
}
