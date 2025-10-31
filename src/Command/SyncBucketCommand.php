<?php

namespace QiniuStorageBundle\Command;

use Doctrine\ORM\EntityManagerInterface;
use QiniuStorageBundle\Client\QiniuApiClient;
use QiniuStorageBundle\Entity\Account;
use QiniuStorageBundle\Entity\Bucket;
use QiniuStorageBundle\Repository\AccountRepository;
use QiniuStorageBundle\Repository\BucketRepository;
use QiniuStorageBundle\Request\GetBucketDomainsRequest;
use QiniuStorageBundle\Request\GetBucketInfoRequest;
use QiniuStorageBundle\Request\GetBucketsRequest;
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
        private readonly QiniuApiClient $apiClient,
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $accounts = $this->accountRepository->findBy(['valid' => true]);

        if ([] === $accounts) {
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

        try {
            // 设置 API 客户端的账号
            $this->apiClient->setAccount($account);

            // 获取存储空间列表
            $buckets = $this->getBucketList();
            if (false === $buckets) {
                return;
            }

            foreach ($buckets as $bucketName) {
                $this->syncBucketInfo($bucketName, $io);
            }

            $this->entityManager->flush();
            $io->success(sprintf('账号 [%s] 的存储空间同步完成', $account->getName()));
        } catch (\Throwable $e) {
            $io->error(sprintf('同步过程中发生错误：%s', $e->getMessage()));
        }
    }

    private function syncBucketInfo(string $bucketName, SymfonyStyle $io): void
    {
        try {
            // 获取存储空间信息
            $bucketInfo = $this->getBucketInfo($bucketName);
            if (false === $bucketInfo) {
                return;
            }

            // 获取域名信息
            $domainList = $this->getBucketDomains($bucketName);
            if (false === $domainList) {
                return;
            }

            // 解析存储空间信息
            $region = is_string($bucketInfo['zone'] ?? null) ? $bucketInfo['zone'] : 'z0';
            $private = is_bool($bucketInfo['private'] ?? null) ? $bucketInfo['private'] : false;

            // 获取当前账号
            $account = $this->apiClient->getAccount();

            // 查找或创建 Bucket 实体
            $bucket = $this->bucketRepository->findOneBy([
                'account' => $account,
                'name' => $bucketName,
            ]) ?? new Bucket();

            // 更新 Bucket 信息
            $bucket->setAccount($account);
            $bucket->setName($bucketName);
            $bucket->setRegion($region);
            $bucket->setDomain($domainList[0] ?? ''); // 使用第一个域名
            $bucket->setPrivate($private);
            $bucket->setLastSyncTime(new \DateTimeImmutable());
            $bucket->setValid(true);

            $this->entityManager->persist($bucket);
            $io->text(sprintf('已同步存储空间 [%s]', $bucketName));
        } catch (\Throwable $e) {
            $io->error(sprintf('同步存储空间 [%s] 时发生错误：%s', $bucketName, $e->getMessage()));
        }
    }

    /**
     * 获取存储空间列表
     *
     * @return array<string>|false
     */
    private function getBucketList(): array|false
    {
        try {
            $request = new GetBucketsRequest();
            $result = $this->apiClient->request($request);

            if (!is_array($result)) {
                return false;
            }

            // 过滤确保只返回字符串类型的bucket名称
            return array_filter($result, fn($item): bool => is_string($item));
        } catch (\Throwable $e) {
            return false;
        }
    }

    /**
     * 获取存储空间信息
     *
     * @return array<string, mixed>|false
     */
    private function getBucketInfo(string $bucketName): array|false
    {
        try {
            $request = new GetBucketInfoRequest($bucketName);
            $result = $this->apiClient->request($request);

            if (!is_array($result)) {
                return false;
            }

            // 确保返回的是关联数组，且键名为字符串
            /** @var array<string, mixed> $typedResult */
            $typedResult = [];
            foreach ($result as $key => $value) {
                if (is_string($key)) {
                    $typedResult[$key] = $value;
                }
            }
            return $typedResult;
        } catch (\Throwable $e) {
            return false;
        }
    }

    /**
     * 获取存储空间域名列表
     *
     * @return array<string>|false
     */
    private function getBucketDomains(string $bucketName): array|false
    {
        try {
            $request = new GetBucketDomainsRequest($bucketName);
            $response = $this->apiClient->request($request);

            if (!is_array($response)) {
                return false;
            }

            $domains = $response['domains'] ?? null;
            if (!is_array($domains)) {
                return false;
            }

            // 过滤确保只返回字符串类型的域名
            return array_filter($domains, fn($item): bool => is_string($item));
        } catch (\Throwable $e) {
            return false;
        }
    }
}
