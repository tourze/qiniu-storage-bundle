<?php

namespace QiniuStorageBundle\Tests\Command;

use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;
use QiniuStorageBundle\Command\SyncBucketMinuteStatisticCommand;
use QiniuStorageBundle\Repository\AccountRepository;
use QiniuStorageBundle\Repository\BucketRepository;
use QiniuStorageBundle\Service\StatisticSyncService;
use Symfony\Component\Console\Tester\CommandTester;

class SyncBucketMinuteStatisticCommandTest extends TestCase
{
    private EntityManagerInterface $entityManager;
    private AccountRepository $accountRepository;
    private BucketRepository $bucketRepository;
    private StatisticSyncService $statisticSyncService;
    private SyncBucketMinuteStatisticCommand $command;
    private CommandTester $commandTester;

    protected function setUp(): void
    {
        // 创建模拟对象
        $this->entityManager = $this->createMock(EntityManagerInterface::class);
        $this->accountRepository = $this->createMock(AccountRepository::class);
        $this->bucketRepository = $this->createMock(BucketRepository::class);
        $this->statisticSyncService = $this->createMock(StatisticSyncService::class);

        // 创建命令
        $this->command = new SyncBucketMinuteStatisticCommand(
            $this->entityManager,
            $this->statisticSyncService
        );

        // 创建命令测试器
        $this->commandTester = new CommandTester($this->command);
    }

    public function testExecute_withNoBuckets_displaysWarning(): void
    {
        // 设置statisticSyncService返回空数组
        $this->statisticSyncService->method('getValidBuckets')
            ->willReturn([]);

        // 执行命令
        $this->commandTester->execute([]);

        // 断言输出包含警告信息
        $output = $this->commandTester->getDisplay();
        $this->assertStringContainsString('没有找到有效的存储空间配置', $output);
    }

    public function testExecute_withValidBuckets_syncsStatistics(): void
    {
        // 创建模拟存储桶
        $bucket = $this->createMock(\QiniuStorageBundle\Entity\Bucket::class);
        $bucket->method('getName')->willReturn('test-bucket');

        // 设置statisticSyncService返回模拟存储桶
        $this->statisticSyncService->method('getValidBuckets')
            ->willReturn([$bucket]);

        // 设置StatisticSyncService预期行为
        $this->statisticSyncService->expects($this->atLeastOnce())
            ->method('syncBucketStatistic')
            ->with(
                $this->equalTo(\QiniuStorageBundle\Enum\TimeGranularity::MINUTE),
                $this->anything(),
                $bucket,
                $this->anything()
            );

        // 执行命令
        $this->commandTester->execute([]);

        // 断言成功信息
        $output = $this->commandTester->getDisplay();
        $this->assertStringContainsString('所有存储空间的5分钟统计信息同步完成', $output);
    }

    public function testExecute_withCustomMinutes_syncsCorrectCount(): void
    {
        // 创建模拟存储桶
        $bucket = $this->createMock(\QiniuStorageBundle\Entity\Bucket::class);
        $bucket->method('getName')->willReturn('test-bucket');

        $this->statisticSyncService->method('getValidBuckets')
            ->willReturn([$bucket]);

        // 设置自定义分钟数
        $customMinutes = 6;

        // 验证StatisticSyncService被调用正确次数
        $this->statisticSyncService->expects($this->exactly($customMinutes))
            ->method('syncBucketStatistic')
            ->with(
                $this->equalTo(\QiniuStorageBundle\Enum\TimeGranularity::MINUTE),
                $this->anything(),
                $bucket,
                $this->anything()
            );

        // 执行命令带自定义分钟参数
        $this->commandTester->execute([
            '--minutes' => $customMinutes
        ]);

        $this->assertEquals(0, $this->commandTester->getStatusCode());
    }

    public function testExecute_withInvalidMinutes_returnsFailure(): void
    {
        // 执行命令带无效的分钟参数
        $this->commandTester->execute([
            '--minutes' => 0
        ]);

        // 断言返回失败状态码
        $this->assertEquals(\Symfony\Component\Console\Command\Command::FAILURE, $this->commandTester->getStatusCode());
        
        // 断言错误信息被显示
        $output = $this->commandTester->getDisplay();
        $this->assertStringContainsString('同步时间段数量必须大于0', $output);
    }
}
