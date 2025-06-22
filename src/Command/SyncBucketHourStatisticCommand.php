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
    description: '同步所有七牛云存储空间的小时统计信息',
)]
#[AsCronTask('*/10 * * * *')]
class SyncBucketHourStatisticCommand extends Command
{
    public const NAME = 'qiniu:sync-bucket-hour-statistics';
    
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly StatisticSyncService $statisticSyncService,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addOption(
            'hours',
            'H',
            InputOption::VALUE_OPTIONAL,
            '需要同步的小时数，默认为24小时',
            24
        );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $hours = (int) $input->getOption('hours');
        if ($hours < 1) {
            $io->error('同步小时数必须大于0');
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
        $now = CarbonImmutable::now()
            ->setMinute(0)
            ->setSecond(0)
            ->startOfSecond();
        for ($i = 1; $i <= $hours; $i++) {
            $times[] = $now->subHours($i);
        }
        $times = array_reverse($times);

        foreach ($buckets as $bucket) {
            $io->section(sprintf('正在同步存储空间 [%s] 的统计信息', $bucket->getName()));
            $progressBar = $io->createProgressBar(count($times));
            $progressBar->setFormat(' %current%/%max% [%bar%] %percent:3s%% %message%');

            foreach ($times as $time) {
                $progressBar->setMessage(sprintf('同步 %s 的统计信息', $time->format('Y-m-d H:i')));
                $this->statisticSyncService->syncBucketStatistic(TimeGranularity::HOUR, $time, $bucket, $io);
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
