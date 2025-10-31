<?php

namespace QiniuStorageBundle\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use QiniuStorageBundle\Entity\Traits\BucketStatisticAware;
use QiniuStorageBundle\Repository\BucketMinuteStatisticRepository;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: BucketMinuteStatisticRepository::class)]
#[ORM\Table(name: 'ims_qiniu_api_storage_bucket_minute_statistic', options: ['comment' => '七牛云存储空间5分钟统计'])]
#[ORM\UniqueConstraint(columns: ['bucket_id', 'time'])]
class BucketMinuteStatistic implements \Stringable
{
    use BucketStatisticAware;

    /**
     * @var int|null 主键ID，新建时为null，持久化后为int
     */
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::INTEGER, options: ['comment' => 'ID'])]
    private ?int $id = null;

    #[Assert\NotNull]
    #[Assert\Type(type: \DateTimeImmutable::class)]
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

    public function setTime(\DateTimeImmutable $time): void
    {
        $this->time = $time;
    }

    public function __toString(): string
    {
        return $this->time->format('Y-m-d H:i:s');
    }
}
