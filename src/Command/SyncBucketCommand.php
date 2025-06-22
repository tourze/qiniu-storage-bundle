<?php

namespace QiniuStorageBundle\Command;

use Doctrine\ORM\EntityManagerInterface;
use Qiniu\Auth;
use Qiniu\Config;
use Qiniu\Storage\BucketManager;
use QiniuStorageBundle\Entity\Account;
use QiniuStorageBundle\Entity\Bucket;
use QiniuStorageBundle\Repository\AccountRepository;
use QiniuStorageBundle\Repository\BucketRepository;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: self::NAME,
    description: '同步所有七牛云账号的存储空间信息',
)]
class SyncBucketCommand extends Command
{
    public const NAME = 'qiniu:sync-buckets';
    
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly AccountRepository $accountRepository,
        private readonly BucketRepository $bucketRepository,
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $accounts = $this->accountRepository->findBy(['valid' => true]);

        if (empty($accounts)) {
            $io->warning('没有找到有效的七牛云账号配置');
            return Command::SUCCESS;
        }

        foreach ($accounts as $account) {
            $this->syncBucketsForAccount($account, $io);
        }

        $io->success('所有账号的存储空间同步完成');
        return Command::SUCCESS;
    }

    private function syncBucketsForAccount(Account $account, SymfonyStyle $io): void
    {
        $io->section(sprintf('正在同步账号 [%s] 的存储空间', $account->getName()));

        // 初始化七牛云客户端
        $auth = new Auth($account->getAccessKey(), $account->getSecretKey());
        $config = new Config();
        $bucketManager = new BucketManager($auth, $config);

        try {
            // 获取存储空间列表
            list($buckets, $err) = $bucketManager->buckets();
            if ($err !== null) {
                $io->error(sprintf('获取存储空间列表失败：%s', $err->message()));
                return;
            }

            foreach ($buckets as $bucketName) {
                $this->syncBucketInfo($bucketManager, $account, $bucketName, $io);
            }

            $this->entityManager->flush();
            $io->success(sprintf('账号 [%s] 的存储空间同步完成', $account->getName()));
        } catch (\Throwable $e) {
            $io->error(sprintf('同步过程中发生错误：%s', $e->getMessage()));
        }
    }

    private function syncBucketInfo(BucketManager $bucketManager, Account $account, string $bucketName, SymfonyStyle $io): void
    {
        try {
            // 获取存储空间信息
            list($domainList, $err) = $bucketManager->domains($bucketName);
            if ($err !== null) {
                $io->error(sprintf('获取存储空间 [%s] 的域名信息失败：%s', $bucketName, $err->message()));
                return;
            }

            // 获取存储空间区域
            list($region, $err) = $bucketManager->bucketInfo($bucketName);
            if ($err !== null) {
                $io->error(sprintf('获取存储空间 [%s] 的区域信息失败：%s', $bucketName, $err->message()));
                return;
            }

            // 获取存储空间信息（包含访问权限）
            list($bucketInfo, $err) = $bucketManager->bucketInfo($bucketName);
            if ($err !== null) {
                $io->error(sprintf('获取存储空间 [%s] 的信息失败：%s', $bucketName, $err->message()));
                return;
            }

            // 解析存储空间信息
            $region = $bucketInfo['zone'] ?? 'z0';
            $private = $bucketInfo['private'] ?? false;

            // 查找或创建 Bucket 实体
            $bucket = $this->bucketRepository->findOneBy([
                'account' => $account,
                'name' => $bucketName,
            ]) ?? new Bucket();

            // 更新 Bucket 信息
            $bucket->setAccount($account)
                ->setName($bucketName)
                ->setRegion($region)
                ->setDomain($domainList[0] ?? '') // 使用第一个域名
                ->setPrivate($private)
                ->setLastSyncTime(new \DateTimeImmutable())
                ->setValid(true);

            $this->entityManager->persist($bucket);
            $io->text(sprintf('已同步存储空间 [%s]', $bucketName));
        } catch (\Throwable $e) {
            $io->error(sprintf('同步存储空间 [%s] 时发生错误：%s', $bucketName, $e->getMessage()));
        }
    }
}
