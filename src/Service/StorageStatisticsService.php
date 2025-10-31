<?php

namespace QiniuStorageBundle\Service;

use Monolog\Attribute\WithMonologChannel;
use Psr\Log\LoggerInterface;
use QiniuStorageBundle\Client\QiniuApiClient;
use QiniuStorageBundle\Entity\Bucket;
use QiniuStorageBundle\Enum\IntelligentTieringTier;
use QiniuStorageBundle\Enum\TimeGranularity;
use QiniuStorageBundle\Request\GetStorageStatisticsRequest;
use Symfony\Component\Console\Style\SymfonyStyle;

#[WithMonologChannel(channel: 'qiniu_storage')]
class StorageStatisticsService
{
    public function __construct(
        private readonly QiniuApiClient $apiClient,
        private readonly LoggerInterface $logger,
    ) {
    }

    public function getStandardStorage(TimeGranularity $g, Bucket $bucket, string $begin, string $end, SymfonyStyle $io): int
    {
        $this->logger->info('开始获取标准存储统计信息', [
            'bucket' => $bucket->getName(),
        ]);

        return $this->fetchStorageStatistics($bucket, $begin, $end, $g->value, 'space', '标准存储', $io);
    }

    public function getLineStorage(TimeGranularity $g, Bucket $bucket, string $begin, string $end, SymfonyStyle $io): int
    {
        $this->logger->info('开始获取低频存储统计信息', [
            'bucket' => $bucket->getName(),
        ]);

        return $this->fetchStorageStatistics($bucket, $begin, $end, $g->value, 'space_line', '低频存储', $io);
    }

    public function getArchiveStorage(TimeGranularity $g, Bucket $bucket, string $begin, string $end, SymfonyStyle $io): int
    {
        $this->logger->info('开始获取归档存储统计信息', [
            'bucket' => $bucket->getName(),
        ]);

        return $this->fetchStorageStatistics($bucket, $begin, $end, $g->value, 'space_archive', '归档存储', $io);
    }

    public function getArchiveIrStorage(TimeGranularity $g, Bucket $bucket, string $begin, string $end, SymfonyStyle $io): int
    {
        $this->logger->info('开始获取归档直读存储统计信息', [
            'bucket' => $bucket->getName(),
        ]);

        return $this->fetchStorageStatistics($bucket, $begin, $end, $g->value, 'space_archive_ir', '归档直读存储', $io);
    }

    public function getDeepArchiveStorage(TimeGranularity $g, Bucket $bucket, string $begin, string $end, SymfonyStyle $io): int
    {
        $this->logger->info('开始获取深度归档存储统计信息', [
            'bucket' => $bucket->getName(),
        ]);

        return $this->fetchStorageStatistics($bucket, $begin, $end, $g->value, 'space_deep_archive', '深度归档存储', $io);
    }

    /**
     * 获取智能分层存储统计信息
     */
    public function getIntelligentTieringStorage(TimeGranularity $g, Bucket $bucket, string $begin, string $end, IntelligentTieringTier $tier = IntelligentTieringTier::FREQUENT_ACCESS, ?SymfonyStyle $io = null): int
    {
        $this->logger->info('开始获取智能分层存储统计信息', [
            'bucket' => $bucket->getName(),
            'tier' => $tier->name,
        ]);

        $typeName = sprintf('智能分层存储(%s)', $tier->getLabel());

        return $this->fetchStorageStatistics($bucket, $begin, $end, $g->value, 'space_intelligent_tiering', $typeName, $io);
    }

    /**
     * 获取智能分层存储文件数量统计信息
     */
    public function getIntelligentTieringCount(TimeGranularity $g, Bucket $bucket, string $begin, string $end, IntelligentTieringTier $tier = IntelligentTieringTier::FREQUENT_ACCESS, ?SymfonyStyle $io = null): int
    {
        $this->logger->info('开始获取智能分层存储文件数量统计信息', [
            'bucket' => $bucket->getName(),
            'tier' => $tier->name,
        ]);

        $typeName = sprintf('智能分层文件数(%s)', $tier->getLabel());

        return $this->fetchStorageStatistics($bucket, $begin, $end, $g->value, 'count_intelligent_tiering', $typeName, $io);
    }

    /**
     * 获取标准存储文件数量统计信息
     */
    public function getStandardCount(TimeGranularity $g, Bucket $bucket, string $begin, string $end, ?SymfonyStyle $io = null): int
    {
        $this->logger->info('开始获取标准存储文件数量统计信息', [
            'bucket' => $bucket->getName(),
        ]);

        return $this->fetchStorageStatistics($bucket, $begin, $end, $g->value, 'count', '标准存储文件数', $io);
    }

    /**
     * 获取低频存储文件数量统计信息
     */
    public function getLineCount(TimeGranularity $g, Bucket $bucket, string $begin, string $end, ?SymfonyStyle $io = null): int
    {
        $this->logger->info('开始获取低频存储文件数量统计信息', [
            'bucket' => $bucket->getName(),
        ]);

        return $this->fetchStorageStatistics($bucket, $begin, $end, $g->value, 'count_line', '低频存储文件数', $io);
    }

    /**
     * 获取归档存储文件数量统计信息
     */
    public function getArchiveCount(TimeGranularity $g, Bucket $bucket, string $begin, string $end, ?SymfonyStyle $io = null): int
    {
        $this->logger->info('开始获取归档存储文件数量统计信息', [
            'bucket' => $bucket->getName(),
        ]);

        return $this->fetchStorageStatistics($bucket, $begin, $end, $g->value, 'count_archive', '归档存储文件数', $io);
    }

    /**
     * 获取归档直读存储文件数量统计信息
     */
    public function getArchiveIrCount(TimeGranularity $g, Bucket $bucket, string $begin, string $end, ?SymfonyStyle $io = null): int
    {
        $this->logger->info('开始获取归档直读存储文件数量统计信息', [
            'bucket' => $bucket->getName(),
        ]);

        return $this->fetchStorageStatistics($bucket, $begin, $end, $g->value, 'count_archive_ir', '归档直读存储文件数', $io);
    }

    /**
     * 获取深度归档存储文件数量统计信息
     */
    public function getDeepArchiveCount(TimeGranularity $g, Bucket $bucket, string $begin, string $end, ?SymfonyStyle $io = null): int
    {
        $this->logger->info('开始获取深度归档存储文件数量统计信息', [
            'bucket' => $bucket->getName(),
        ]);

        return $this->fetchStorageStatistics($bucket, $begin, $end, $g->value, 'count_deep_archive', '深度归档存储文件数', $io);
    }

    /**
     * 获取智能分层监控文件数量统计信息
     */
    public function getIntelligentTieringMonitorCount(TimeGranularity $g, Bucket $bucket, string $begin, string $end, ?SymfonyStyle $io = null): int
    {
        $this->logger->info('开始获取智能分层监控文件数量统计信息', [
            'bucket' => $bucket->getName(),
        ]);

        return $this->fetchStorageStatistics($bucket, $begin, $end, $g->value, 'count_intelligent_tiering_monitor', '智能分层监控文件数', $io);
    }

    /**
     * 获取PUT请求次数统计信息
     */
    public function getPutRequests(TimeGranularity $g, Bucket $bucket, string $begin, string $end, ?SymfonyStyle $io = null): int
    {
        $this->logger->info('开始获取PUT请求次数统计信息', [
            'bucket' => $bucket->getName(),
        ]);

        return $this->fetchStorageStatistics($bucket, $begin, $end, $g->value, 'rs_put', 'PUT请求次数', $io);
    }

    /**
     * 获取GET请求次数统计
     */
    public function getGetRequests(TimeGranularity $g, Bucket $bucket, string $begin, string $end, ?SymfonyStyle $io = null): int
    {
        $startTime = microtime(true);

        try {
            $this->logger->info('开始请求GET请求次数统计', [
                'bucket' => $bucket->getName(),
            ]);

            $this->apiClient->setAccount($bucket->getAccount());

            // 对于 blob_io 接口，我们需要特殊处理
            $url = sprintf('http://api.qiniuapi.com/v6/blob_io?begin=%s&end=%s&g=%s&select=hits&$metric=hits&$bucket=%s',
                $begin, $end, $g->value, $bucket->getName());

            $authorization = $this->apiClient->createQBoxAuthorization($url, '');
            $response = $this->apiClient->getHttpClientInstance()->request('GET', $url, [
                'headers' => [
                    'Authorization' => $authorization,
                ],
            ]);

            $content = $response->getContent();
            $stats = json_decode($content, true);
            $hits = 0;
            if (is_array($stats) && isset($stats[0]) && is_array($stats[0]) && isset($stats[0]['values']) && is_array($stats[0]['values']) && isset($stats[0]['values']['hits']) && '' !== $stats[0]['values']['hits']) {
                $hits = is_numeric($stats[0]['values']['hits']) ? (int) $stats[0]['values']['hits'] : 0;
            }

            $duration = round((microtime(true) - $startTime) * 1000, 2);

            $this->logger->info('获取GET请求次数统计成功', [
                'bucket' => $bucket->getName(),
                'hits' => $hits,
                'duration_ms' => $duration,
            ]);

            return $hits;
        } catch (\Throwable $e) {
            $duration = round((microtime(true) - $startTime) * 1000, 2);
            $message = sprintf('获取存储空间 [%s] 的GET请求次数统计失败：%s',
                $bucket->getName(), $e->getMessage());
            $this->logger->error($message, [
                'bucket' => $bucket->getName(),
                'duration_ms' => $duration,
                'exception' => $e,
            ]);

            return 0;
        }
    }

    /**
     * 获取外网流出流量统计
     */
    public function getInternetTraffic(TimeGranularity $g, Bucket $bucket, string $begin, string $end, ?SymfonyStyle $io = null): int
    {
        $startTime = microtime(true);

        try {
            $this->logger->info('开始请求外网流出流量统计', [
                'bucket' => $bucket->getName(),
            ]);

            $this->apiClient->setAccount($bucket->getAccount());

            // 对于 blob_io 接口，我们需要特殊处理
            $url = sprintf('http://api.qiniuapi.com/v6/blob_io?begin=%s&end=%s&g=%s&select=flow&$metric=flow_out&$bucket=%s',
                $begin, $end, $g->value, $bucket->getName());

            $authorization = $this->apiClient->createQBoxAuthorization($url, '');
            $response = $this->apiClient->getHttpClientInstance()->request('GET', $url, [
                'headers' => [
                    'Authorization' => $authorization,
                ],
            ]);

            $content = $response->getContent();
            $stats = json_decode($content, true);
            $flow = 0;
            if (is_array($stats) && isset($stats[0]) && is_array($stats[0]) && isset($stats[0]['values']) && is_array($stats[0]['values']) && isset($stats[0]['values']['flow']) && '' !== $stats[0]['values']['flow']) {
                $flow = is_numeric($stats[0]['values']['flow']) ? (int) $stats[0]['values']['flow'] : 0;
            }

            $duration = round((microtime(true) - $startTime) * 1000, 2);

            $this->logger->info('获取外网流出流量统计成功', [
                'bucket' => $bucket->getName(),
                'flow' => $flow,
                'duration_ms' => $duration,
            ]);

            return $flow;
        } catch (\Throwable $e) {
            $duration = round((microtime(true) - $startTime) * 1000, 2);
            $message = sprintf('获取存储空间 [%s] 的外网流出流量统计失败：%s',
                $bucket->getName(), $e->getMessage());
            $this->logger->error($message, [
                'bucket' => $bucket->getName(),
                'duration_ms' => $duration,
                'exception' => $e,
            ]);

            return 0;
        }
    }

    /**
     * 获取CDN回源流量统计
     */
    public function getCdnTraffic(TimeGranularity $g, Bucket $bucket, string $begin, string $end, ?SymfonyStyle $io = null): int
    {
        $startTime = microtime(true);

        try {
            $this->logger->info('开始请求CDN回源流量统计', [
                'bucket' => $bucket->getName(),
            ]);

            $this->apiClient->setAccount($bucket->getAccount());

            // 对于 blob_io 接口，我们需要特殊处理
            $url = sprintf('http://api.qiniuapi.com/v6/blob_io?begin=%s&end=%s&g=%s&select=flow&$metric=cdn_flow_out&$bucket=%s',
                $begin, $end, $g->value, $bucket->getName());

            $authorization = $this->apiClient->createQBoxAuthorization($url, '');
            $response = $this->apiClient->getHttpClientInstance()->request('GET', $url, [
                'headers' => [
                    'Authorization' => $authorization,
                ],
            ]);

            $content = $response->getContent();
            $stats = json_decode($content, true);
            $flow = 0;
            if (is_array($stats) && isset($stats[0]) && is_array($stats[0]) && isset($stats[0]['values']) && is_array($stats[0]['values']) && isset($stats[0]['values']['flow']) && '' !== $stats[0]['values']['flow']) {
                $flow = is_numeric($stats[0]['values']['flow']) ? (int) $stats[0]['values']['flow'] : 0;
            }

            $duration = round((microtime(true) - $startTime) * 1000, 2);

            $this->logger->info('获取CDN回源流量统计成功', [
                'bucket' => $bucket->getName(),
                'flow' => $flow,
                'duration_ms' => $duration,
            ]);

            return $flow;
        } catch (\Throwable $e) {
            $duration = round((microtime(true) - $startTime) * 1000, 2);
            $message = sprintf('获取存储空间 [%s] 的CDN回源流量统计失败：%s',
                $bucket->getName(), $e->getMessage());
            $this->logger->error($message, [
                'bucket' => $bucket->getName(),
                'duration_ms' => $duration,
                'exception' => $e,
            ]);

            return 0;
        }
    }

    private function fetchStorageStatistics(Bucket $bucket, string $begin, string $end, string $granularity, string $type, string $typeName, ?SymfonyStyle $io = null): int
    {
        $startTime = microtime(true);

        $this->logger->info('开始请求存储统计信息', [
            'bucket' => $bucket->getName(),
            'type' => $typeName,
        ]);

        try {
            $this->apiClient->setAccount($bucket->getAccount());
            $request = new GetStorageStatisticsRequest($bucket->getName(), $begin, $end, $granularity, $type);
            $response = $this->apiClient->request($request);

            $datas = is_array($response) ? ($response['datas'] ?? []) : [];
            $storage = [] !== $datas ? (is_array($datas) && is_numeric(end($datas)) ? (int) end($datas) : 0) : 0;
            $duration = round((microtime(true) - $startTime) * 1000, 2);

            $this->logger->info('获取存储统计信息成功', [
                'bucket' => $bucket->getName(),
                'type' => $typeName,
                'storage' => $storage,
                'duration_ms' => $duration,
            ]);

            return $storage;
        } catch (\Throwable $e) {
            $duration = round((microtime(true) - $startTime) * 1000, 2);
            $message = sprintf('获取存储空间 [%s] 的%s统计信息失败：%s',
                $bucket->getName(), $typeName, $e->getMessage());

            $this->logger->error($message, [
                'bucket' => $bucket->getName(),
                'type' => $typeName,
                'duration_ms' => $duration,
                'exception' => $e,
            ]);

            if (null !== $io) {
                $io->error($message);
            }

            return 0;
        }
    }
}
