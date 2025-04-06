<?php

namespace QiniuStorageBundle\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use QiniuStorageBundle\Entity\Traits\BucketStatisticAware;
use QiniuStorageBundle\Repository\BucketHourStatisticRepository;
use Symfony\Component\Serializer\Attribute\Groups;
use Tourze\EasyAdmin\Attribute\Column\ExportColumn;
use Tourze\EasyAdmin\Attribute\Column\ListColumn;

#[ORM\Entity(repositoryClass: BucketHourStatisticRepository::class)]
#[ORM\Table(name: 'ims_qiniu_api_storage_bucket_hour_statistic', options: ['comment' => '七牛云存储空间小时统计'])]
#[ORM\UniqueConstraint(columns: ['bucket_id', 'time'])]
class BucketHourStatistic
{
    use BucketStatisticAware;

    #[ListColumn(order: -1)]
    #[ExportColumn]
    #[Groups(['restful_read', 'api_tree', 'admin_curd', 'api_list'])]
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
