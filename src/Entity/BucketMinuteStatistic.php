<?php

namespace QiniuStorageBundle\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use QiniuStorageBundle\Entity\Traits\BucketStatisticAware;
use QiniuStorageBundle\Repository\BucketMinuteStatisticRepository;

#[ORM\Entity(repositoryClass: BucketMinuteStatisticRepository::class)]
#[ORM\Table(name: 'ims_qiniu_api_storage_bucket_minute_statistic', options: ['comment' => '七牛云存储空间5分钟统计'])]
#[ORM\UniqueConstraint(columns: ['bucket_id', 'time'])]
class BucketMinuteStatistic
{
    use BucketStatisticAware;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::INTEGER, options: ['comment' => 'ID'])]
    private ?int $id = 0;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, options: ['comment' => '统计时间'])]
    private \DateTimeImmutable $time;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTime(): \DateTimeImmutable
    {
        return $this->time;
    }

    public function setTime(\DateTimeImmutable $time): self
    {
        $this->time = $time;
        return $this;
    }
}
