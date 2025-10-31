<?php

declare(strict_types=1);

namespace QiniuStorageBundle\Tests\Command;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use QiniuStorageBundle\Command\SyncBucketDayStatisticCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Tester\CommandTester;
use Tourze\PHPUnitSymfonyKernelTest\AbstractCommandTestCase;

/**
 * SyncBucketDayStatisticCommand 测试类
 *
 * @internal
 */
#[CoversClass(SyncBucketDayStatisticCommand::class)]
#[RunTestsInSeparateProcesses]
final class SyncBucketDayStatisticCommandTest extends AbstractCommandTestCase
{
    protected function onSetUp(): void
    {
        // 此测试类无需特殊设置
    }

    protected function getCommandTester(): CommandTester
    {
        $command = self::getService(SyncBucketDayStatisticCommand::class);

        return new CommandTester($command);
    }

    public function testCommandExecutionWithBuckets(): void
    {
        $command = self::getService(SyncBucketDayStatisticCommand::class);
        $commandTester = new CommandTester($command);

        $commandTester->execute([]);

        $this->assertSame(Command::SUCCESS, $commandTester->getStatusCode());
        $this->assertStringContainsString('所有存储空间的统计信息同步完成', $commandTester->getDisplay());
    }

    public function testCommandExecutionWithInvalidDays(): void
    {
        $command = self::getService(SyncBucketDayStatisticCommand::class);
        $commandTester = new CommandTester($command);

        $commandTester->execute(['--days' => '0']);

        $this->assertSame(Command::FAILURE, $commandTester->getStatusCode());
        $this->assertStringContainsString('同步天数必须大于0', $commandTester->getDisplay());
    }

    public function testCommandName(): void
    {
        $command = self::getService(SyncBucketDayStatisticCommand::class);

        $this->assertSame('qiniu:sync-bucket-day-statistics', $command->getName());
    }

    public function testOptionDays(): void
    {
        $command = self::getService(SyncBucketDayStatisticCommand::class);
        $commandTester = new CommandTester($command);

        $commandTester->execute(['--days' => '3']);

        $this->assertSame(Command::SUCCESS, $commandTester->getStatusCode());
        $this->assertStringContainsString('所有存储空间的统计信息同步完成', $commandTester->getDisplay());
    }
}
