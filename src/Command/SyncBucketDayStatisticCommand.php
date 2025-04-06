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
    name: 'qiniu:sync-bucket-day-statistics',
    description: '同步所有七牛云存储空间的统计信息',
)]
#[AsCronTask('5 0 * * *')]
#[AsCronTask('15 13 * * *')]
class SyncBucketDayStatisticCommand extends Command
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly StatisticSyncService $statisticSyncService,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addOption(
            'days',
            'd',
            InputOption::VALUE_OPTIONAL,
            '需要同步的天数，默认为7天',
            7
        );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $days = (int) $input->getOption('days');
        if ($days < 1) {
            $io->error('同步天数必须大于0');
            return Command::FAILURE;
        }

        $buckets = $this->statisticSyncService->getValidBuckets();
        if (empty($buckets)) {
            $io->warning('没有找到有效的存储空间配置');
            return Command::SUCCESS;
        }

        // 获取需要同步的日期列表
        /** @var CarbonImmutable[] $dates */
        $dates = [];
        $now = CarbonImmutable::today()->startOfDay();
        for ($i = 1; $i <= $days; $i++) {
            $dates[] = $now->subDays($i)->startOfDay();
        }
        $dates = array_reverse($dates);

        foreach ($buckets as $bucket) {
            $io->section(sprintf('正在同步存储空间 [%s] 的统计信息', $bucket->getName()));
            $progressBar = $io->createProgressBar(count($dates));
            $progressBar->setFormat(' %current%/%max% [%bar%] %percent:3s%% %message%');

            foreach ($dates as $date) {
                $progressBar->setMessage(sprintf('同步 %s 的统计信息', $date->format('Y-m-d')));
                $this->statisticSyncService->syncBucketStatistic(TimeGranularity::DAY, $date, $bucket, $io);
                $progressBar->advance();
                $this->entityManager->flush();
            }

            $progressBar->finish();
            $io->newLine(2);
            $io->text(sprintf('已同步存储空间 [%s] 的统计信息', $bucket->getName()));
            $this->entityManager->flush();
        }

        $io->success('所有存储空间的统计信息同步完成');
        return Command::SUCCESS;
    }
}
