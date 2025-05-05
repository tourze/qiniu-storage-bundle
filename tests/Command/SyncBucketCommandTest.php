<?php

namespace QiniuStorageBundle\Tests\Command;

use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;
use QiniuStorageBundle\Command\SyncBucketCommand;
use QiniuStorageBundle\Entity\Account;
use QiniuStorageBundle\Entity\Bucket;
use QiniuStorageBundle\Repository\AccountRepository;
use QiniuStorageBundle\Repository\BucketRepository;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;

class SyncBucketCommandTest extends TestCase
{
    private EntityManagerInterface $entityManager;
    private AccountRepository $accountRepository;
    private BucketRepository $bucketRepository;
    private SyncBucketCommand $command;
    private CommandTester $commandTester;

    protected function setUp(): void
    {
        // 创建模拟对象
        $this->entityManager = $this->createMock(EntityManagerInterface::class);
        $this->accountRepository = $this->createMock(AccountRepository::class);
        $this->bucketRepository = $this->createMock(BucketRepository::class);

        // 创建命令
        $this->command = new SyncBucketCommand(
            $this->entityManager,
            $this->accountRepository,
            $this->bucketRepository
        );

        // 创建应用并添加命令
        $application = new Application();
        $application->add($this->command);

        // 创建命令测试器
        $this->commandTester = new CommandTester($this->command);
    }

    public function testExecute_withNoValidAccounts_displaysWarning(): void
    {
        // 设置accountRepository返回空数组
        $this->accountRepository->method('findBy')
            ->with(['valid' => true])
            ->willReturn([]);

        // 执行命令
        $this->commandTester->execute([]);

        // 断言输出包含警告信息
        $output = $this->commandTester->getDisplay();
        $this->assertStringContainsString('没有找到有效的七牛云账号配置', $output);
    }

    public function testExecute_withValidAccounts_syncsBuckets(): void
    {
        // 创建模拟账号
        $account = $this->createMock(Account::class);
        $account->method('getName')->willReturn('测试账号');
        $account->method('getAccessKey')->willReturn('test_access_key');
        $account->method('getSecretKey')->willReturn('test_secret_key');

        // 设置accountRepository返回模拟账号
        $this->accountRepository->method('findBy')
            ->with(['valid' => true])
            ->willReturn([$account]);

        // 创建模拟存储桶
        $bucket = $this->createMock(Bucket::class);

        // 设置bucketRepository
        $this->bucketRepository->method('findOneBy')
            ->willReturn(null); // 假设存储桶不存在，需要创建新的

        // 设置entityManager预期行为
        $this->entityManager->expects($this->atLeastOnce())
            ->method('persist')
            ->with($this->isInstanceOf(Bucket::class));

        $this->entityManager->expects($this->once())
            ->method('flush');

        // 由于无法简单地mock七牛云SDK的静态类，我们这里跳过该测试
        $this->markTestSkipped('由于七牛云SDK依赖问题，暂时跳过此测试');
    }

    public function testSyncBucketInfo_handlesErrors(): void
    {
        // 跳过七牛云依赖测试
        $this->markTestSkipped('由于七牛云SDK依赖问题，暂时跳过此测试');
    }
}
