<?php

namespace QiniuStorageBundle\Tests\Service;

use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use QiniuStorageBundle\Entity\Account;
use QiniuStorageBundle\Entity\Bucket;
use QiniuStorageBundle\Repository\BucketDayStatisticRepository;
use QiniuStorageBundle\Repository\BucketHourStatisticRepository;
use QiniuStorageBundle\Repository\BucketMinuteStatisticRepository;
use QiniuStorageBundle\Repository\BucketRepository;
use QiniuStorageBundle\Service\AuthService;
use QiniuStorageBundle\Service\StorageStatisticsService;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class StorageStatisticsServiceTest extends TestCase
{
    private EntityManagerInterface $entityManager;
    private BucketRepository $bucketRepository;
    private BucketHourStatisticRepository $hourStatisticRepository;
    private BucketDayStatisticRepository $dayStatisticRepository;
    private BucketMinuteStatisticRepository $minuteStatisticRepository;
    private AuthService $authService;
    private HttpClientInterface $httpClient;
    private LoggerInterface $logger;
    private StorageStatisticsService $statisticsService;

    protected function setUp(): void
    {
        $this->entityManager = $this->createMock(EntityManagerInterface::class);
        $this->bucketRepository = $this->createMock(BucketRepository::class);
        $this->hourStatisticRepository = $this->createMock(BucketHourStatisticRepository::class);
        $this->dayStatisticRepository = $this->createMock(BucketDayStatisticRepository::class);
        $this->minuteStatisticRepository = $this->createMock(BucketMinuteStatisticRepository::class);
        $this->authService = $this->createMock(AuthService::class);
        $this->httpClient = $this->createMock(HttpClientInterface::class);
        $this->logger = $this->createMock(LoggerInterface::class);

        $this->statisticsService = new StorageStatisticsService(
            $this->httpClient,
            $this->logger
        );
    }

    public function testGetStandardStorage_withValidParams_returnsSizeValue(): void
    {
        $bucket = $this->createBucketMock();
        $begin = '20230101';
        $end = '20230102';
        $granularity = \QiniuStorageBundle\Enum\TimeGranularity::DAY;
        
        // 模拟HTTP响应
        $mockResponse = $this->createMock(\Symfony\Contracts\HttpClient\ResponseInterface::class);
        $mockResponse->method('getContent')
            ->willReturn(json_encode(['datas' => [1000, 2000, 3000]]));
        
        $this->httpClient->expects($this->once())
            ->method('request')
            ->with('GET', $this->stringContains('v6/space'))
            ->willReturn($mockResponse);
        
        // 创建模拟SymfonyStyle
        $io = $this->createMock(\Symfony\Component\Console\Style\SymfonyStyle::class);
        
        $result = $this->statisticsService->getStandardStorage($granularity, $bucket, $begin, $end, $io);
        
        $this->assertEquals(3000, $result); // 返回最后一个值
    }

    public function testGetLineStorage_withValidParams_returnsSizeValue(): void
    {
        $bucket = $this->createBucketMock();
        $begin = '20230101';
        $end = '20230102';
        $granularity = \QiniuStorageBundle\Enum\TimeGranularity::DAY;
        
        // 模拟HTTP响应
        $mockResponse = $this->createMock(\Symfony\Contracts\HttpClient\ResponseInterface::class);
        $mockResponse->method('getContent')
            ->willReturn(json_encode(['datas' => [500, 1500]]));
        
        $this->httpClient->expects($this->once())
            ->method('request')
            ->with('GET', $this->stringContains('v6/space_line'))
            ->willReturn($mockResponse);
        
        // 创建模拟SymfonyStyle
        $io = $this->createMock(\Symfony\Component\Console\Style\SymfonyStyle::class);
        
        $result = $this->statisticsService->getLineStorage($granularity, $bucket, $begin, $end, $io);
        
        $this->assertEquals(1500, $result);
    }

    public function testGetArchiveStorage_withValidParams_returnsSizeValue(): void
    {
        $bucket = $this->createBucketMock();
        $begin = '20230101';
        $end = '20230102';
        $granularity = \QiniuStorageBundle\Enum\TimeGranularity::HOUR;
        
        // 模拟HTTP响应
        $mockResponse = $this->createMock(\Symfony\Contracts\HttpClient\ResponseInterface::class);
        $mockResponse->method('getContent')
            ->willReturn(json_encode(['datas' => [100, 200, 300, 400]]));
        
        $this->httpClient->expects($this->once())
            ->method('request')
            ->with('GET', $this->stringContains('v6/space_archive'))
            ->willReturn($mockResponse);
        
        // 创建模拟SymfonyStyle
        $io = $this->createMock(\Symfony\Component\Console\Style\SymfonyStyle::class);
        
        $result = $this->statisticsService->getArchiveStorage($granularity, $bucket, $begin, $end, $io);
        
        $this->assertEquals(400, $result);
    }

    public function testGetStandardStorage_withApiError_returnsZero(): void
    {
        $bucket = $this->createBucketMock();
        $begin = '20230101';
        $end = '20230102';
        $granularity = \QiniuStorageBundle\Enum\TimeGranularity::DAY;
        
        // 模拟HTTP请求异常
        $this->httpClient->expects($this->once())
            ->method('request')
            ->willThrowException(new \Exception('API调用失败'));
        
        // 创建模拟SymfonyStyle
        $io = $this->createMock(\Symfony\Component\Console\Style\SymfonyStyle::class);
        
        $result = $this->statisticsService->getStandardStorage($granularity, $bucket, $begin, $end, $io);
        
        $this->assertEquals(0, $result); // 异常情况下返回0
    }

    /**
     * 创建一个模拟的Bucket对象
     */
    private function createBucketMock(): Bucket
    {
        $account = $this->createMock(Account::class);
        $account->method('getAccessKey')->willReturn('test_access_key');
        $account->method('getSecretKey')->willReturn('test_secret_key');

        $bucket = $this->createMock(Bucket::class);
        $bucket->method('getId')->willReturn(1);
        $bucket->method('getName')->willReturn('test-bucket');
        $bucket->method('getAccount')->willReturn($account);

        return $bucket;
    }
}
