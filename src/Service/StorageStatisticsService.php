<?php

namespace QiniuStorageBundle\Service;

use Psr\Log\LoggerInterface;
use Qiniu\Auth;
use QiniuStorageBundle\Entity\Bucket;
use QiniuStorageBundle\Enum\IntelligentTieringTier;
use QiniuStorageBundle\Enum\TimeGranularity;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class StorageStatisticsService
{
    public function __construct(
        private readonly HttpClientInterface $httpClient,
        private readonly LoggerInterface $logger,
    ) {
    }

    public function getStandardStorage(TimeGranularity $g, Bucket $bucket, string $begin, string $end, SymfonyStyle $io): int
    {
        $url = sprintf('http://api.qiniuapi.com/v6/space?bucket=%s&begin=%s&end=%s&g=%s',
            $bucket->getName(), $begin, $end, $g->value);

        $this->logger->info('开始获取标准存储统计信息', [
            'bucket' => $bucket->getName(),
            'url' => $url
        ]);

        return $this->fetchStorageStatistics($bucket, $url, '标准存储', $io);
    }

    public function getLineStorage(TimeGranularity $g, Bucket $bucket, string $begin, string $end, SymfonyStyle $io): int
    {
        $url = sprintf('http://api.qiniuapi.com/v6/space_line?bucket=%s&begin=%s&end=%s&g=%s',
            $bucket->getName(), $begin, $end, $g->value);

        $this->logger->info('开始获取低频存储统计信息', [
            'bucket' => $bucket->getName(),
            'url' => $url
        ]);

        return $this->fetchStorageStatistics($bucket, $url, '低频存储', $io);
    }

    public function getArchiveStorage(TimeGranularity $g, Bucket $bucket, string $begin, string $end, SymfonyStyle $io): int
    {
        $url = sprintf('http://api.qiniuapi.com/v6/space_archive?bucket=%s&begin=%s&end=%s&g=%s',
            $bucket->getName(), $begin, $end, $g->value);

        $this->logger->info('开始获取归档存储统计信息', [
            'bucket' => $bucket->getName(),
            'url' => $url
        ]);

        return $this->fetchStorageStatistics($bucket, $url, '归档存储', $io);
    }

    public function getArchiveIrStorage(TimeGranularity $g, Bucket $bucket, string $begin, string $end, SymfonyStyle $io): int
    {
        $url = sprintf('http://api.qiniuapi.com/v6/space_archive_ir?bucket=%s&begin=%s&end=%s&g=%s',
            $bucket->getName(), $begin, $end, $g->value);

        $this->logger->info('开始获取归档直读存储统计信息', [
            'bucket' => $bucket->getName(),
            'url' => $url
        ]);

        return $this->fetchStorageStatistics($bucket, $url, '归档直读存储', $io);
    }

    public function getDeepArchiveStorage(TimeGranularity $g, Bucket $bucket, string $begin, string $end, SymfonyStyle $io): int
    {
        $url = sprintf('http://api.qiniuapi.com/v6/space_deep_archive?bucket=%s&begin=%s&end=%s&g=%s',
            $bucket->getName(), $begin, $end, $g->value);

        $this->logger->info('开始获取深度归档存储统计信息', [
            'bucket' => $bucket->getName(),
            'url' => $url
        ]);

        return $this->fetchStorageStatistics($bucket, $url, '深度归档存储', $io);
    }

    /**
     * 获取智能分层存储统计信息
     */
    public function getIntelligentTieringStorage(TimeGranularity $g, Bucket $bucket, string $begin, string $end, IntelligentTieringTier $tier = IntelligentTieringTier::FREQUENT_ACCESS, ?SymfonyStyle $io = null): int
    {
        $url = sprintf('http://api.qiniuapi.com/v6/space_intelligent_tiering?bucket=%s&begin=%s&end=%s&g=%s&tier=%s',
            $bucket->getName(), $begin, $end, $g->value, $tier->name);

        $this->logger->info('开始获取智能分层存储统计信息', [
            'bucket' => $bucket->getName(),
            'tier' => $tier->name,
            'url' => $url
        ]);

        $typeName = sprintf('智能分层存储(%s)', $tier->getLabel());
        return $this->fetchStorageStatistics($bucket, $url, $typeName, $io);
    }

    /**
     * 获取智能分层存储文件数量统计信息
     */
    public function getIntelligentTieringCount(TimeGranularity $g, Bucket $bucket, string $begin, string $end, IntelligentTieringTier $tier = IntelligentTieringTier::FREQUENT_ACCESS, ?SymfonyStyle $io = null): int
    {
        $url = sprintf('http://api.qiniuapi.com/v6/count_intelligent_tiering?bucket=%s&begin=%s&end=%s&g=%s&tier=%s',
            $bucket->getName(), $begin, $end, $g->value, $tier->name);

        $this->logger->info('开始获取智能分层存储文件数量统计信息', [
            'bucket' => $bucket->getName(),
            'tier' => $tier->name,
            'url' => $url
        ]);

        $typeName = sprintf('智能分层文件数(%s)', $tier->getLabel());
        return $this->fetchStorageStatistics($bucket, $url, $typeName, $io);
    }

    /**
     * 获取标准存储文件数量统计信息
     */
    public function getStandardCount(TimeGranularity $g, Bucket $bucket, string $begin, string $end, ?SymfonyStyle $io = null): int
    {
        $url = sprintf('http://api.qiniuapi.com/v6/count?bucket=%s&begin=%s&end=%s&g=%s',
            $bucket->getName(), $begin, $end, $g->value);

        $this->logger->info('开始获取标准存储文件数量统计信息', [
            'bucket' => $bucket->getName(),
            'url' => $url
        ]);

        return $this->fetchStorageStatistics($bucket, $url, '标准存储文件数', $io);
    }

    /**
     * 获取低频存储文件数量统计信息
     */
    public function getLineCount(TimeGranularity $g, Bucket $bucket, string $begin, string $end, ?SymfonyStyle $io = null): int
    {
        $url = sprintf('http://api.qiniuapi.com/v6/count_line?bucket=%s&begin=%s&end=%s&g=%s',
            $bucket->getName(), $begin, $end, $g->value);

        $this->logger->info('开始获取低频存储文件数量统计信息', [
            'bucket' => $bucket->getName(),
            'url' => $url
        ]);

        return $this->fetchStorageStatistics($bucket, $url, '低频存储文件数', $io);
    }

    /**
     * 获取归档存储文件数量统计信息
     */
    public function getArchiveCount(TimeGranularity $g, Bucket $bucket, string $begin, string $end, ?SymfonyStyle $io = null): int
    {
        $url = sprintf('http://api.qiniuapi.com/v6/count_archive?bucket=%s&begin=%s&end=%s&g=%s',
            $bucket->getName(), $begin, $end, $g->value);

        $this->logger->info('开始获取归档存储文件数量统计信息', [
            'bucket' => $bucket->getName(),
            'url' => $url
        ]);

        return $this->fetchStorageStatistics($bucket, $url, '归档存储文件数', $io);
    }

    /**
     * 获取归档直读存储文件数量统计信息
     */
    public function getArchiveIrCount(TimeGranularity $g, Bucket $bucket, string $begin, string $end, ?SymfonyStyle $io = null): int
    {
        $url = sprintf('http://api.qiniuapi.com/v6/count_archive_ir?bucket=%s&begin=%s&end=%s&g=%s',
            $bucket->getName(), $begin, $end, $g->value);

        $this->logger->info('开始获取归档直读存储文件数量统计信息', [
            'bucket' => $bucket->getName(),
            'url' => $url
        ]);

        return $this->fetchStorageStatistics($bucket, $url, '归档直读存储文件数', $io);
    }

    /**
     * 获取深度归档存储文件数量统计信息
     */
    public function getDeepArchiveCount(TimeGranularity $g, Bucket $bucket, string $begin, string $end, ?SymfonyStyle $io = null): int
    {
        $url = sprintf('http://api.qiniuapi.com/v6/count_deep_archive?bucket=%s&begin=%s&end=%s&g=%s',
            $bucket->getName(), $begin, $end, $g->value);

        $this->logger->info('开始获取深度归档存储文件数量统计信息', [
            'bucket' => $bucket->getName(),
            'url' => $url
        ]);

        return $this->fetchStorageStatistics($bucket, $url, '深度归档存储文件数', $io);
    }

    /**
     * 获取智能分层监控文件数量统计信息
     */
    public function getIntelligentTieringMonitorCount(TimeGranularity $g, Bucket $bucket, string $begin, string $end, ?SymfonyStyle $io = null): int
    {
        $url = sprintf('http://api.qiniuapi.com/v6/count_intelligent_tiering_monitor?bucket=%s&begin=%s&end=%s&g=%s',
            $bucket->getName(), $begin, $end, $g->value);

        $this->logger->info('开始获取智能分层监控文件数量统计信息', [
            'bucket' => $bucket->getName(),
            'url' => $url
        ]);

        return $this->fetchStorageStatistics($bucket, $url, '智能分层监控文件数', $io);
    }

    /**
     * 获取PUT请求次数统计信息
     */
    public function getPutRequests(TimeGranularity $g, Bucket $bucket, string $begin, string $end, ?SymfonyStyle $io = null): int
    {
        $url = sprintf('http://api.qiniuapi.com/v6/rs_put?bucket=%s&begin=%s&end=%s&g=%s',
            $bucket->getName(), $begin, $end, $g->value);

        $this->logger->info('开始获取PUT请求次数统计信息', [
            'bucket' => $bucket->getName(),
            'url' => $url
        ]);

        return $this->fetchStorageStatistics($bucket, $url, 'PUT请求次数', $io);
    }

    /**
     * 获取GET请求次数统计
     */
    public function getGetRequests(TimeGranularity $g, Bucket $bucket, string $begin, string $end, ?SymfonyStyle $io = null): int
    {
        try {
            $url = sprintf('http://api.qiniuapi.com/v6/blob_io?begin=%s&end=%s&g=%s&select=hits&$metric=hits&$bucket=%s',
                $begin, $end, $g->value, $bucket->getName());
            $mac = new Auth($bucket->getAccount()->getAccessKey(), $bucket->getAccount()->getSecretKey());
            $authorization = 'QBox ' . $mac->signRequest($url, '', '');
            $response = $this->httpClient->request('GET', $url, [
                'headers' => [
                    'Authorization' => $authorization
                ]
            ]);

            $content = $response->getContent();
            $stats = json_decode($content, true);
            $hits = 0;
            if (!empty($stats[0]['values']['hits'])) {
                $hits = $stats[0]['values']['hits'];
            }

            $this->logger->info('获取GET请求次数统计成功', [
                'bucket' => $bucket->getName(),
                'hits' => $hits
            ]);

            return $hits;
        } catch  (\Throwable $e) {
            $message = sprintf('获取存储空间 [%s] 的GET请求次数统计失败：%s',
                $bucket->getName(), $e->getMessage());
            $this->logger->error($message, [
                'bucket' => $bucket->getName(),
                'exception' => $e
            ]);
            return 0;
        }
    }

    /**
     * 获取外网流出流量统计
     */
    public function getInternetTraffic(TimeGranularity $g, Bucket $bucket, string $begin, string $end, ?SymfonyStyle $io = null): int
    {
        try {
            $url = sprintf('http://api.qiniuapi.com/v6/blob_io?begin=%s&end=%s&g=%s&select=flow&$metric=flow_out&$bucket=%s',
                $begin, $end, $g->value, $bucket->getName());
            $mac = new Auth($bucket->getAccount()->getAccessKey(), $bucket->getAccount()->getSecretKey());
            $authorization = 'QBox ' . $mac->signRequest($url, '', '');
            $response = $this->httpClient->request('GET', $url, [
                'headers' => [
                    'Authorization' => $authorization
                ]
            ]);

            $content = $response->getContent();
            $stats = json_decode($content, true);
            $flow = 0;
            if (!empty($stats[0]['values']['flow'])) {
                $flow = $stats[0]['values']['flow'];
            }

            $this->logger->info('获取外网流出流量统计成功', [
                'bucket' => $bucket->getName(),
                'flow' => $flow
            ]);

            return $flow;
        } catch  (\Throwable $e) {
            $message = sprintf('获取存储空间 [%s] 的外网流出流量统计失败：%s',
                $bucket->getName(), $e->getMessage());
            $this->logger->error($message, [
                'bucket' => $bucket->getName(),
                'exception' => $e
            ]);
            return 0;
        }
    }

    /**
     * 获取CDN回源流量统计
     */
    public function getCdnTraffic(TimeGranularity $g, Bucket $bucket, string $begin, string $end, ?SymfonyStyle $io = null): int
    {
        try {
            $url = sprintf('http://api.qiniuapi.com/v6/blob_io?begin=%s&end=%s&g=%s&select=flow&$metric=cdn_flow_out&$bucket=%s',
                $begin, $end, $g->value, $bucket->getName());
            $mac = new Auth($bucket->getAccount()->getAccessKey(), $bucket->getAccount()->getSecretKey());
            $authorization = 'QBox ' . $mac->signRequest($url, '', '');
            $response = $this->httpClient->request('GET', $url, [
                'headers' => [
                    'Authorization' => $authorization
                ]
            ]);

            $content = $response->getContent();
            $stats = json_decode($content, true);
            $flow = 0;
            if (!empty($stats[0]['values']['flow'])) {
                $flow = $stats[0]['values']['flow'];
            }

            $this->logger->info('获取CDN回源流量统计成功', [
                'bucket' => $bucket->getName(),
                'flow' => $flow
            ]);

            return $flow;
        } catch  (\Throwable $e) {
            $message = sprintf('获取存储空间 [%s] 的CDN回源流量统计失败：%s',
                $bucket->getName(), $e->getMessage());
            $this->logger->error($message, [
                'bucket' => $bucket->getName(),
                'exception' => $e
            ]);
            return 0;
        }
    }

    private function fetchStorageStatistics(Bucket $bucket, string $url, string $typeName, ?SymfonyStyle $io = null): int
    {
        try {
            $mac = new Auth($bucket->getAccount()->getAccessKey(), $bucket->getAccount()->getSecretKey());
            $authorization = 'QBox ' . $mac->signRequest($url, '', '');
            $response = $this->httpClient->request('GET', $url, [
                'headers' => [
                    'Authorization' => $authorization
                ]
            ]);

            $content = $response->getContent();
            $stats = json_decode($content, true);
            $datas = $stats['datas'] ?? [];
            $storage = !empty($datas) ? (int)(end($datas) ?? 0) : 0;
            //dump($typeName, $content);

            $this->logger->info('获取存储统计信息成功', [
                'bucket' => $bucket->getName(),
                'type' => $typeName,
                'storage' => $storage
            ]);

            return $storage;
        } catch  (\Throwable $e) {
//            $message = sprintf('获取存储空间 [%s] 的%s统计信息失败：%s',
//                $bucket->getName(), $typeName, $e->getMessage());
//             if ($io) {
//                 $io->error($message);
//             }
//            $this->logger->error($message, [
//                'bucket' => $bucket->getName(),
//                'type' => $typeName,
//                'exception' => $e
//            ]);
            return 0;
        }
    }
}
