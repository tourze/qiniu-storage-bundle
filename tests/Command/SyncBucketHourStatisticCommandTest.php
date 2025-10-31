<?php

declare(strict_types=1);

namespace QiniuStorageBundle\Tests\Command;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use QiniuStorageBundle\Command\SyncBucketHourStatisticCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Tester\CommandTester;
use Tourze\PHPUnitSymfonyKernelTest\AbstractCommandTestCase;

/**
 * SyncBucketHourStatisticCommand 测试类
 *
 * @internal
 */
#[CoversClass(SyncBucketHourStatisticCommand::class)]
#[RunTestsInSeparateProcesses]
final class SyncBucketHourStatisticCommandTest extends AbstractCommandTestCase
{
    protected function onSetUp(): void
    {
        // 此测试类无需特殊设置
    }

    protected function getCommandTester(): CommandTester
    {
        $command = self::getService(SyncBucketHourStatisticCommand::class);

        return new CommandTester($command);
    }

    public function testCommandExecutionWithBuckets(): void
    {
        $command = self::getService(SyncBucketHourStatisticCommand::class);
        $commandTester = new CommandTester($command);

        $commandTester->execute([]);

        $this->assertSame(Command::SUCCESS, $commandTester->getStatusCode());
        $this->assertStringContainsString('所有存储空间的统计信息同步完成', $commandTester->getDisplay());
    }

    public function testCommandExecutionWithInvalidHours(): void
    {
        $command = self::getService(SyncBucketHourStatisticCommand::class);
        $commandTester = new CommandTester($command);

        $commandTester->execute(['--hours' => '0']);

        $this->assertSame(Command::FAILURE, $commandTester->getStatusCode());
        $this->assertStringContainsString('同步小时数必须大于0', $commandTester->getDisplay());
    }

    public function testCommandName(): void
    {
        $command = self::getService(SyncBucketHourStatisticCommand::class);

        $this->assertSame('qiniu:sync-bucket-hour-statistics', $command->getName());
    }

    public function testOptionHours(): void
    {
        $command = self::getService(SyncBucketHourStatisticCommand::class);
        $commandTester = new CommandTester($command);

        $commandTester->execute(['--hours' => '12']);

        $this->assertSame(Command::SUCCESS, $commandTester->getStatusCode());
        $this->assertStringContainsString('所有存储空间的统计信息同步完成', $commandTester->getDisplay());
    }
}
