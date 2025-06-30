<?php

namespace QiniuStorageBundle\Command;

use Carbon\CarbonImmutable;
use Doctrine\ORM\EntityManagerInterface;
use QiniuStorageBundle\Enum\TimeGranularity;
use QiniuStorageBundle\Service\StatisticSyncService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Tourze\Symfony\CronJob\Attribute\AsCronTask;

#[AsCommand(
    name: self::NAME,
    description: '同步所有七牛云存储空间的5分钟统计信息',
)]
#[AsCronTask(expression: '*/5 * * * *')]
class SyncBucketMinuteStatisticCommand extends Command
{
    public const NAME = 'qiniu:sync-bucket-minute-statistics';
    
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly StatisticSyncService $statisticSyncService,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addOption(
            'minutes',
            'm',
            InputOption::VALUE_OPTIONAL,
            '需要同步的5分钟时间段数量，默认为12个时间段（1小时）',
            12
        );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $minutes = (int) $input->getOption('minutes');
        if ($minutes < 1) {
            $io->error('同步时间段数量必须大于0');
            return Command::FAILURE;
        }

        $buckets = $this->statisticSyncService->getValidBuckets();
        if (empty($buckets)) {
            $io->warning('没有找到有效的存储空间配置');
            return Command::SUCCESS;
        }

        // 获取需要同步的时间列表
        /** @var CarbonImmutable[] $times */
        $times = [];
        $now = CarbonImmutable::now();
        // 向下取整到5分钟
        $minute = (int) $now->format('i');
        $minuteRounded = (int) floor($minute / 5) * 5;
        $now = $now->setMinute($minuteRounded)->setSecond(0)->startOfSecond();

        for ($i = 1; $i <= $minutes; $i++) {
            $times[] = $now->subMinutes($i * 5);
        }
        $times = array_reverse($times);

        foreach ($buckets as $bucket) {
            $io->section(sprintf('正在同步存储空间 [%s] 的5分钟统计信息', $bucket->getName()));
            $progressBar = $io->createProgressBar(count($times));
            $progressBar->setFormat(' %current%/%max% [%bar%] %percent:3s%% %message%');

            foreach ($times as $time) {
                $progressBar->setMessage(sprintf('同步 %s 的统计信息', $time->format('Y-m-d H:i')));
                $this->statisticSyncService->syncBucketStatistic(TimeGranularity::MINUTE, $time, $bucket, $io);
                $progressBar->advance();
                $this->entityManager->flush();
            }

            $progressBar->finish();
            $io->newLine(2);
            $io->text(sprintf('已同步存储空间 [%s] 的5分钟统计信息', $bucket->getName()));
            $this->entityManager->flush();
        }

        $io->success('所有存储空间的5分钟统计信息同步完成');
        return Command::SUCCESS;
    }
}
