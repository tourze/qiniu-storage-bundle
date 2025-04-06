<?php

namespace QiniuStorageBundle\Entity\Traits;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use QiniuStorageBundle\Entity\Bucket;

trait BucketStatisticAware
{
    #[ORM\ManyToOne(targetEntity: Bucket::class)]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private Bucket $bucket;

    #[ORM\Column(type: Types::BIGINT, options: ['comment' => '标准存储量(字节)', 'default' => 0])]
    private int $standardStorage = 0;

    #[ORM\Column(type: Types::BIGINT, options: ['comment' => '低频存储量(字节)', 'default' => 0])]
    private int $lineStorage = 0;

    #[ORM\Column(type: Types::BIGINT, options: ['comment' => '归档存储量(字节)', 'default' => 0])]
    private int $archiveStorage = 0;

    #[ORM\Column(type: Types::BIGINT, options: ['comment' => '归档直读存储量(字节)', 'default' => 0])]
    private int $archiveIrStorage = 0;

    #[ORM\Column(type: Types::BIGINT, options: ['comment' => '深度归档存储量(字节)', 'default' => 0])]
    private int $deepArchiveStorage = 0;

    #[ORM\Column(type: Types::BIGINT, options: ['comment' => '智能分层存储量(字节)', 'default' => 0])]
    private int $intelligentTieringStorage = 0;

    #[ORM\Column(type: Types::BIGINT, options: ['comment' => '智能分层频繁访问层存储量(字节)', 'default' => 0])]
    private int $intelligentTieringFrequentStorage = 0;

    #[ORM\Column(type: Types::BIGINT, options: ['comment' => '智能分层不频繁访问层存储量(字节)', 'default' => 0])]
    private int $intelligentTieringInfrequentStorage = 0;

    #[ORM\Column(type: Types::BIGINT, options: ['comment' => '智能分层归档直读访问层存储量(字节)', 'default' => 0])]
    private int $intelligentTieringArchiveStorage = 0;

    #[ORM\Column(type: Types::INTEGER, options: ['comment' => '标准存储文件数', 'default' => 0])]
    private int $standardCount = 0;

    #[ORM\Column(type: Types::INTEGER, options: ['comment' => '低频存储文件数', 'default' => 0])]
    private int $lineCount = 0;

    #[ORM\Column(type: Types::INTEGER, options: ['comment' => '归档存储文件数', 'default' => 0])]
    private int $archiveCount = 0;

    #[ORM\Column(type: Types::INTEGER, options: ['comment' => '归档直读存储文件数', 'default' => 0])]
    private int $archiveIrCount = 0;

    #[ORM\Column(type: Types::INTEGER, options: ['comment' => '深度归档存储文件数', 'default' => 0])]
    private int $deepArchiveCount = 0;

    #[ORM\Column(type: Types::INTEGER, options: ['comment' => '智能分层存储文件数', 'default' => 0])]
    private int $intelligentTieringCount = 0;

    #[ORM\Column(type: Types::INTEGER, options: ['comment' => '智能分层频繁访问层文件数', 'default' => 0])]
    private int $intelligentTieringFrequentCount = 0;

    #[ORM\Column(type: Types::INTEGER, options: ['comment' => '智能分层不频繁访问层文件数', 'default' => 0])]
    private int $intelligentTieringInfrequentCount = 0;

    #[ORM\Column(type: Types::INTEGER, options: ['comment' => '智能分层归档直读访问层文件数', 'default' => 0])]
    private int $intelligentTieringArchiveCount = 0;

    #[ORM\Column(type: Types::INTEGER, options: ['comment' => '智能分层监控文件数', 'default' => 0])]
    private int $intelligentTieringMonitorCount = 0;

    #[ORM\Column(type: Types::BIGINT, options: ['comment' => '外网流出流量(字节)', 'default' => 0])]
    private int $internetTraffic = 0;

    #[ORM\Column(type: Types::BIGINT, options: ['comment' => 'CDN回源流量(字节)', 'default' => 0])]
    private int $cdnTraffic = 0;

    #[ORM\Column(type: Types::INTEGER, options: ['comment' => 'GET请求次数', 'default' => 0])]
    private int $getRequests = 0;

    #[ORM\Column(type: Types::INTEGER, options: ['comment' => 'PUT请求次数', 'default' => 0])]
    private int $putRequests = 0;

    #[ORM\Column(type: Types::INTEGER, options: ['comment' => '存储类型转换次数', 'default' => 0])]
    private int $storageTypeConversions = 0;

    public function getBucket(): Bucket
    {
        return $this->bucket;
    }

    public function setBucket(Bucket $bucket): self
    {
        $this->bucket = $bucket;
        return $this;
    }

    public function getStandardStorage(): int
    {
        return $this->standardStorage;
    }

    public function setStandardStorage(int $standardStorage): self
    {
        $this->standardStorage = $standardStorage;
        return $this;
    }

    public function getLineStorage(): int
    {
        return $this->lineStorage;
    }

    public function setLineStorage(int $lineStorage): self
    {
        $this->lineStorage = $lineStorage;
        return $this;
    }

    public function getArchiveStorage(): int
    {
        return $this->archiveStorage;
    }

    public function setArchiveStorage(int $archiveStorage): self
    {
        $this->archiveStorage = $archiveStorage;
        return $this;
    }

    public function getArchiveIrStorage(): int
    {
        return $this->archiveIrStorage;
    }

    public function setArchiveIrStorage(int $archiveIrStorage): self
    {
        $this->archiveIrStorage = $archiveIrStorage;
        return $this;
    }

    public function getDeepArchiveStorage(): int
    {
        return $this->deepArchiveStorage;
    }

    public function setDeepArchiveStorage(int $deepArchiveStorage): self
    {
        $this->deepArchiveStorage = $deepArchiveStorage;
        return $this;
    }

    public function getIntelligentTieringStorage(): int
    {
        return $this->intelligentTieringStorage;
    }

    public function setIntelligentTieringStorage(int $intelligentTieringStorage): self
    {
        $this->intelligentTieringStorage = $intelligentTieringStorage;
        return $this;
    }

    public function getIntelligentTieringFrequentStorage(): int
    {
        return $this->intelligentTieringFrequentStorage;
    }

    public function setIntelligentTieringFrequentStorage(int $intelligentTieringFrequentStorage): self
    {
        $this->intelligentTieringFrequentStorage = $intelligentTieringFrequentStorage;
        return $this;
    }

    public function getIntelligentTieringInfrequentStorage(): int
    {
        return $this->intelligentTieringInfrequentStorage;
    }

    public function setIntelligentTieringInfrequentStorage(int $intelligentTieringInfrequentStorage): self
    {
        $this->intelligentTieringInfrequentStorage = $intelligentTieringInfrequentStorage;
        return $this;
    }

    public function getIntelligentTieringArchiveStorage(): int
    {
        return $this->intelligentTieringArchiveStorage;
    }

    public function setIntelligentTieringArchiveStorage(int $intelligentTieringArchiveStorage): self
    {
        $this->intelligentTieringArchiveStorage = $intelligentTieringArchiveStorage;
        return $this;
    }

    public function getStandardCount(): int
    {
        return $this->standardCount;
    }

    public function setStandardCount(int $standardCount): self
    {
        $this->standardCount = $standardCount;
        return $this;
    }

    public function getLineCount(): int
    {
        return $this->lineCount;
    }

    public function setLineCount(int $lineCount): self
    {
        $this->lineCount = $lineCount;
        return $this;
    }

    public function getArchiveCount(): int
    {
        return $this->archiveCount;
    }

    public function setArchiveCount(int $archiveCount): self
    {
        $this->archiveCount = $archiveCount;
        return $this;
    }

    public function getArchiveIrCount(): int
    {
        return $this->archiveIrCount;
    }

    public function setArchiveIrCount(int $archiveIrCount): self
    {
        $this->archiveIrCount = $archiveIrCount;
        return $this;
    }

    public function getDeepArchiveCount(): int
    {
        return $this->deepArchiveCount;
    }

    public function setDeepArchiveCount(int $deepArchiveCount): self
    {
        $this->deepArchiveCount = $deepArchiveCount;
        return $this;
    }

    public function getIntelligentTieringCount(): int
    {
        return $this->intelligentTieringCount;
    }

    public function setIntelligentTieringCount(int $intelligentTieringCount): self
    {
        $this->intelligentTieringCount = $intelligentTieringCount;
        return $this;
    }

    public function getIntelligentTieringFrequentCount(): int
    {
        return $this->intelligentTieringFrequentCount;
    }

    public function setIntelligentTieringFrequentCount(int $intelligentTieringFrequentCount): self
    {
        $this->intelligentTieringFrequentCount = $intelligentTieringFrequentCount;
        return $this;
    }

    public function getIntelligentTieringInfrequentCount(): int
    {
        return $this->intelligentTieringInfrequentCount;
    }

    public function setIntelligentTieringInfrequentCount(int $intelligentTieringInfrequentCount): self
    {
        $this->intelligentTieringInfrequentCount = $intelligentTieringInfrequentCount;
        return $this;
    }

    public function getIntelligentTieringArchiveCount(): int
    {
        return $this->intelligentTieringArchiveCount;
    }

    public function setIntelligentTieringArchiveCount(int $intelligentTieringArchiveCount): self
    {
        $this->intelligentTieringArchiveCount = $intelligentTieringArchiveCount;
        return $this;
    }

    public function getIntelligentTieringMonitorCount(): int
    {
        return $this->intelligentTieringMonitorCount;
    }

    public function setIntelligentTieringMonitorCount(int $intelligentTieringMonitorCount): self
    {
        $this->intelligentTieringMonitorCount = $intelligentTieringMonitorCount;
        return $this;
    }

    public function getInternetTraffic(): int
    {
        return $this->internetTraffic;
    }

    public function setInternetTraffic(int $internetTraffic): self
    {
        $this->internetTraffic = $internetTraffic;
        return $this;
    }

    public function getCdnTraffic(): int
    {
        return $this->cdnTraffic;
    }

    public function setCdnTraffic(int $cdnTraffic): self
    {
        $this->cdnTraffic = $cdnTraffic;
        return $this;
    }

    public function getGetRequests(): int
    {
        return $this->getRequests;
    }

    public function setGetRequests(int $getRequests): self
    {
        $this->getRequests = $getRequests;
        return $this;
    }

    public function getPutRequests(): int
    {
        return $this->putRequests;
    }

    public function setPutRequests(int $putRequests): self
    {
        $this->putRequests = $putRequests;
        return $this;
    }

    public function getStorageTypeConversions(): int
    {
        return $this->storageTypeConversions;
    }

    public function setStorageTypeConversions(int $storageTypeConversions): self
    {
        $this->storageTypeConversions = $storageTypeConversions;
        return $this;
    }
}
