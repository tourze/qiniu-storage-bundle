<?php

namespace QiniuStorageBundle\Tests\Command;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use QiniuStorageBundle\Command\SyncBucketCommand;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;
use Tourze\PHPUnitSymfonyKernelTest\AbstractCommandTestCase;

/**
 * @internal
 */
#[CoversClass(SyncBucketCommand::class)]
#[RunTestsInSeparateProcesses]
final class SyncBucketCommandTest extends AbstractCommandTestCase
{
    protected function onSetUp(): void
    {
        // 此测试类无需特殊设置
    }

    protected function getCommandTester(): CommandTester
    {
        $command = self::getService(SyncBucketCommand::class);

        return new CommandTester($command);
    }

    public function testConstructor(): void
    {
        $command = self::getService(SyncBucketCommand::class);

        $this->assertNotNull($command);
    }

    public function testGetName(): void
    {
        $command = self::getService(SyncBucketCommand::class);

        $this->assertEquals('qiniu:sync-buckets', $command->getName());
    }

    public function testCommandCanBeAddedToApplication(): void
    {
        $command = self::getService(SyncBucketCommand::class);

        $application = new Application();
        $application->add($command);

        $commandTester = new CommandTester($command);

        $this->assertNotNull($commandTester);
    }
}
