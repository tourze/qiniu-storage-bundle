<?php

namespace QiniuStorageBundle\Tests\Command;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use QiniuStorageBundle\Command\SyncBucketMinuteStatisticCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Tester\CommandTester;
use Tourze\PHPUnitSymfonyKernelTest\AbstractCommandTestCase;

/**
 * @internal
 */
#[CoversClass(SyncBucketMinuteStatisticCommand::class)]
#[RunTestsInSeparateProcesses]
final class SyncBucketMinuteStatisticCommandTest extends AbstractCommandTestCase
{
    protected function onSetUp(): void
    {
        // 此测试类无需特殊设置
    }

    protected function getCommandTester(): CommandTester
    {
        $command = self::getService(SyncBucketMinuteStatisticCommand::class);

        return new CommandTester($command);
    }

    public function testConstructor(): void
    {
        $command = self::getService(SyncBucketMinuteStatisticCommand::class);

        $this->assertNotNull($command);
    }

    public function testGetName(): void
    {
        $command = self::getService(SyncBucketMinuteStatisticCommand::class);

        $this->assertEquals('qiniu:sync-bucket-minute-statistics', $command->getName());
    }

    public function testExecuteWithInvalidMinutesReturnsFailure(): void
    {
        $command = self::getService(SyncBucketMinuteStatisticCommand::class);

        $commandTester = new CommandTester($command);

        $commandTester->execute([
            '--minutes' => 0,
        ]);

        $this->assertEquals(Command::FAILURE, $commandTester->getStatusCode());

        $output = $commandTester->getDisplay();
        $this->assertStringContainsString('同步时间段数量必须大于0', $output);
    }

    public function testOptionMinutes(): void
    {
        $command = self::getService(SyncBucketMinuteStatisticCommand::class);
        $commandTester = new CommandTester($command);

        $commandTester->execute(['--minutes' => '6']);

        $this->assertSame(Command::SUCCESS, $commandTester->getStatusCode());
        $this->assertStringContainsString('所有存储空间的5分钟统计信息同步完成', $commandTester->getDisplay());
    }
}
