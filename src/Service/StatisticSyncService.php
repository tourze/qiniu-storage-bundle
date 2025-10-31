<?php

namespace QiniuStorageBundle\Service;

use Doctrine\ORM\EntityManagerInterface;
use Monolog\Attribute\WithMonologChannel;
use Psr\Log\LoggerInterface;
use QiniuStorageBundle\Entity\Bucket;
use QiniuStorageBundle\Entity\BucketDayStatistic;
use QiniuStorageBundle\Entity\BucketHourStatistic;
use QiniuStorageBundle\Entity\BucketMinuteStatistic;
use QiniuStorageBundle\Enum\IntelligentTieringTier;
use QiniuStorageBundle\Enum\TimeGranularity;
use QiniuStorageBundle\Repository\BucketDayStatisticRepository;
use QiniuStorageBundle\Repository\BucketHourStatisticRepository;
use QiniuStorageBundle\Repository\BucketMinuteStatisticRepository;
use QiniuStorageBundle\Repository\BucketRepository;
use Symfony\Component\Console\Style\SymfonyStyle;

#[WithMonologChannel(channel: 'qiniu_storage')]
class StatisticSyncService
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly BucketRepository $bucketRepository,
        private readonly BucketDayStatisticRepository $bucketDayStatisticRepository,
        private readonly BucketHourStatisticRepository $bucketHourStatisticRepository,
        private readonly BucketMinuteStatisticRepository $bucketMinuteStatisticRepository,
        private readonly StorageStatisticsService $storageStatisticsService,
        private readonly LoggerInterface $logger,
    ) {
    }

    /**
     * 同步存储空间统计数据
     *
     * @param TimeGranularity    $granularity 时间粒度
     * @param \DateTimeImmutable $time        统计时间
     * @param Bucket             $bucket      存储空间
     * @param SymfonyStyle|null  $io          输出接口
     */
    public function syncBucketStatistic(
        TimeGranularity $granularity,
        \DateTimeImmutable $time,
        Bucket $bucket,
        ?SymfonyStyle $io = null,
    ): void {
        if (null !== $io) {
            $io->text(sprintf('正在同步存储空间 [%s] 的统计信息 (%s)', $bucket->getName(), $time->format('Y-m-d H:i')));
        }

        $this->logger->info('开始同步存储空间统计信息', [
            'bucket' => $bucket->getName(),
            'time' => $time->format('Y-m-d H:i:s'),
            'granularity' => $granularity->name,
        ]);

        try {
            // 获取统计时间范围
            [$beginTime, $endTime] = $this->getTimeRange($granularity, $time);
            $begin = $beginTime->format('YmdHis');
            $end = $endTime->format('YmdHis');

            // 获取各种存储类型的统计信息
            if (null === $io) {
                return; // If no output interface, skip the sync
            }
            $standardStorage = $this->storageStatisticsService->getStandardStorage($granularity, $bucket, $begin, $end, $io);
            $lineStorage = $this->storageStatisticsService->getLineStorage($granularity, $bucket, $begin, $end, $io);
            $archiveStorage = $this->storageStatisticsService->getArchiveStorage($granularity, $bucket, $begin, $end, $io);
            $archiveIrStorage = $this->storageStatisticsService->getArchiveIrStorage($granularity, $bucket, $begin, $end, $io);
            $deepArchiveStorage = $this->storageStatisticsService->getDeepArchiveStorage($granularity, $bucket, $begin, $end, $io);
            // 获取智能分层存储的不同访问层统计
            $intelligentTieringFrequentStorage = $this->storageStatisticsService->getIntelligentTieringStorage($granularity, $bucket, $begin, $end, IntelligentTieringTier::FREQUENT_ACCESS, $io);
            $intelligentTieringInfrequentStorage = $this->storageStatisticsService->getIntelligentTieringStorage($granularity, $bucket, $begin, $end, IntelligentTieringTier::INFREQUENT_ACCESS, $io);
            $intelligentTieringArchiveStorage = $this->storageStatisticsService->getIntelligentTieringStorage($granularity, $bucket, $begin, $end, IntelligentTieringTier::ARCHIVE_DIRECT, $io);

            // 获取各种存储类型的文件数量统计
            $standardCount = $this->storageStatisticsService->getStandardCount($granularity, $bucket, $begin, $end, $io);
            $lineCount = $this->storageStatisticsService->getLineCount($granularity, $bucket, $begin, $end, $io);
            $archiveCount = $this->storageStatisticsService->getArchiveCount($granularity, $bucket, $begin, $end, $io);
            $archiveIrCount = $this->storageStatisticsService->getArchiveIrCount($granularity, $bucket, $begin, $end, $io);
            $deepArchiveCount = $this->storageStatisticsService->getDeepArchiveCount($granularity, $bucket, $begin, $end, $io);
            $intelligentTieringFrequentCount = $this->storageStatisticsService->getIntelligentTieringCount($granularity, $bucket, $begin, $end, IntelligentTieringTier::FREQUENT_ACCESS, $io);
            $intelligentTieringInfrequentCount = $this->storageStatisticsService->getIntelligentTieringCount($granularity, $bucket, $begin, $end, IntelligentTieringTier::INFREQUENT_ACCESS, $io);
            $intelligentTieringArchiveCount = $this->storageStatisticsService->getIntelligentTieringCount($granularity, $bucket, $begin, $end, IntelligentTieringTier::ARCHIVE_DIRECT, $io);
            $intelligentTieringMonitorCount = $this->storageStatisticsService->getIntelligentTieringMonitorCount($granularity, $bucket, $begin, $end, $io);

            // 获取请求和流量统计
            $putRequests = $this->storageStatisticsService->getPutRequests($granularity, $bucket, $begin, $end, $io);
            $getRequests = $this->storageStatisticsService->getGetRequests($granularity, $bucket, $begin, $end, $io);
            $internetTraffic = $this->storageStatisticsService->getInternetTraffic($granularity, $bucket, $begin, $end, $io);
            $cdnTraffic = $this->storageStatisticsService->getCdnTraffic($granularity, $bucket, $begin, $end, $io);

            // 查找或创建统计记录
            $statistic = $this->findOrCreateStatistic($granularity, $bucket, $time);

            // 更新统计信息
            $statistic->setBucket($bucket);
            $statistic->setTime($time);
            // 存储量统计
            $statistic->setStandardStorage($standardStorage);
            $statistic->setLineStorage($lineStorage);
            $statistic->setArchiveStorage($archiveStorage);
            $statistic->setArchiveIrStorage($archiveIrStorage);
            $statistic->setDeepArchiveStorage($deepArchiveStorage);
            $statistic->setIntelligentTieringFrequentStorage($intelligentTieringFrequentStorage);
            $statistic->setIntelligentTieringInfrequentStorage($intelligentTieringInfrequentStorage);
            $statistic->setIntelligentTieringArchiveStorage($intelligentTieringArchiveStorage);
            $statistic->setIntelligentTieringStorage($intelligentTieringFrequentStorage + $intelligentTieringInfrequentStorage + $intelligentTieringArchiveStorage);
            // 文件数统计
            $statistic->setStandardCount($standardCount);
            $statistic->setLineCount($lineCount);
            $statistic->setArchiveCount($archiveCount);
            $statistic->setArchiveIrCount($archiveIrCount);
            $statistic->setDeepArchiveCount($deepArchiveCount);
            $statistic->setIntelligentTieringFrequentCount($intelligentTieringFrequentCount);
            $statistic->setIntelligentTieringInfrequentCount($intelligentTieringInfrequentCount);
            $statistic->setIntelligentTieringArchiveCount($intelligentTieringArchiveCount);
            $statistic->setIntelligentTieringCount($intelligentTieringFrequentCount + $intelligentTieringInfrequentCount + $intelligentTieringArchiveCount);
            $statistic->setIntelligentTieringMonitorCount($intelligentTieringMonitorCount);
            // 流量统计
            $statistic->setInternetTraffic($internetTraffic);
            $statistic->setCdnTraffic($cdnTraffic);
            // 请求统计
            $statistic->setGetRequests($getRequests);
            $statistic->setPutRequests($putRequests);
            $statistic->setStorageTypeConversions(0);

            $this->entityManager->persist($statistic);
            $this->logger->info('同步存储空间统计信息成功', [
                'bucket' => $bucket->getName(),
                'time' => $time->format('Y-m-d H:i:s'),
                'granularity' => $granularity->name,
            ]);
        } catch (\Throwable $e) {
            $message = sprintf('同步存储空间 [%s] 的统计信息失败：%s',
                $bucket->getName(), $e->getMessage());
            $this->logger->error($message, [
                'bucket' => $bucket->getName(),
                'time' => $time->format('Y-m-d H:i:s'),
                'granularity' => $granularity->name,
                'exception' => $e,
            ]);

            if (null !== $io) {
                $io->error($message);
            }
        }
    }

    /**
     * 获取有效的存储空间列表
     *
     * @return Bucket[]
     */
    public function getValidBuckets(): array
    {
        return $this->bucketRepository->findBy(['valid' => true]);
    }

    /**
     * 根据时间粒度和给定时间获取时间范围
     *
     * @param TimeGranularity    $granularity 时间粒度
     * @param \DateTimeImmutable $time        统计时间
     *
     * @return array{\DateTimeImmutable, \DateTimeImmutable} [$beginTime, $endTime]
     */
    private function getTimeRange(TimeGranularity $granularity, \DateTimeImmutable $time): array
    {
        switch ($granularity) {
            case TimeGranularity::MINUTE:
                // 5分钟粒度，向下取整到5分钟
                $minute = (int) $time->format('i');
                $minuteRounded = (int) floor($minute / 5) * 5;
                $beginTime = $time->setTime((int) $time->format('H'), $minuteRounded, 0);
                $endTime = $time->setTime((int) $time->format('H'), $minuteRounded + 4, 59);
                break;

            case TimeGranularity::HOUR:
                // 小时粒度
                $beginTime = $time->setTime((int) $time->format('H'), 0, 0);
                $endTime = $time->setTime((int) $time->format('H'), 59, 59);
                break;

            case TimeGranularity::DAY:
            default:
                // 天粒度
                $beginTime = $time->setTime(0, 0, 0);
                $endTime = $time->setTime(23, 59, 59);
                break;
        }

        return [$beginTime, $endTime];
    }

    /**
     * 根据时间粒度查找或创建统计记录
     *
     * @param TimeGranularity    $granularity 时间粒度
     * @param Bucket             $bucket      存储空间
     * @param \DateTimeImmutable $time        统计时间
     *
     * @return BucketDayStatistic|BucketHourStatistic|BucketMinuteStatistic
     */
    private function findOrCreateStatistic(TimeGranularity $granularity, Bucket $bucket, \DateTimeImmutable $time): object
    {
        switch ($granularity) {
            case TimeGranularity::MINUTE:
                $entity = $this->bucketMinuteStatisticRepository->findOneBy([
                    'bucket' => $bucket,
                    'time' => $time,
                ]) ?? new BucketMinuteStatistic();
                break;

            case TimeGranularity::HOUR:
                $entity = $this->bucketHourStatisticRepository->findOneBy([
                    'bucket' => $bucket,
                    'time' => $time,
                ]) ?? new BucketHourStatistic();
                break;

            case TimeGranularity::DAY:
            default:
                $entity = $this->bucketDayStatisticRepository->findOneBy([
                    'bucket' => $bucket,
                    'time' => $time,
                ]) ?? new BucketDayStatistic();
                break;
        }

        return $entity;
    }
}
