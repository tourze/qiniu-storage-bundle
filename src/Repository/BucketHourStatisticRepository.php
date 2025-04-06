<?php

namespace QiniuStorageBundle\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use QiniuStorageBundle\Entity\Bucket;
use QiniuStorageBundle\Entity\BucketHourStatistic;

/**
 * @method BucketHourStatistic|null find($id, $lockMode = null, $lockVersion = null)
 * @method BucketHourStatistic|null findOneBy(array $criteria, array $orderBy = null)
 * @method BucketHourStatistic[] findAll()
 * @method BucketHourStatistic[] findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BucketHourStatisticRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, BucketHourStatistic::class);
    }

    /**
     * 查找指定时间范围内的统计数据
     *
     * @return BucketHourStatistic[]
     */
    public function findByTimeRange(Bucket $bucket, \DateTimeInterface $start, \DateTimeInterface $end): array
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.bucket = :bucket')
            ->andWhere('s.time >= :start')
            ->andWhere('s.time <= :end')
            ->setParameter('bucket', $bucket)
            ->setParameter('start', $start)
            ->setParameter('end', $end)
            ->orderBy('s.time', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function findOneByBucketAndTime(Bucket $bucket, \DateTimeInterface $time): ?BucketHourStatistic
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.bucket = :bucket')
            ->andWhere('s.time = :time')
            ->setParameter('bucket', $bucket)
            ->setParameter('time', $time)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
