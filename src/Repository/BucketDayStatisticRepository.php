<?php

namespace QiniuStorageBundle\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use QiniuStorageBundle\Entity\Bucket;
use QiniuStorageBundle\Entity\BucketDayStatistic;
use Tourze\PHPUnitSymfonyKernelTest\Attribute\AsRepository;

/**
 * 存储空间日级统计 Repository
 *
 * 提供存储空间日级统计数据的数据访问操作，包括：
 * - 基础的 CRUD 操作
 * - 按时间范围查询统计数据
 * - 按存储空间和时间查找特定统计记录
 *
 * @extends ServiceEntityRepository<BucketDayStatistic>
 */
#[AsRepository(entityClass: BucketDayStatistic::class)]
class BucketDayStatisticRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, BucketDayStatistic::class);
    }

    /**
     * 保存日级统计实体
     *
     * @param BucketDayStatistic $entity 日级统计实体
     * @param bool $flush 是否立即刷新到数据库
     */
    public function save(BucketDayStatistic $entity, bool $flush = true): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * 删除日级统计实体
     *
     * @param BucketDayStatistic $entity 日级统计实体
     * @param bool $flush 是否立即刷新到数据库
     */
    public function remove(BucketDayStatistic $entity, bool $flush = true): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * 查找指定时间范围内的统计数据
     *
     * @param Bucket $bucket 存储空间实体
     * @param \DateTimeInterface $start 开始时间
     * @param \DateTimeInterface $end 结束时间
     * @return array<int, BucketDayStatistic> 按时间升序排列的统计数据数组
     */
    public function findByTimeRange(Bucket $bucket, \DateTimeInterface $start, \DateTimeInterface $end): array
    {
        /** @var array<int, BucketDayStatistic> $result */
        $result = $this->createQueryBuilder('s')
            ->andWhere('s.bucket = :bucket')
            ->andWhere('s.time >= :start')
            ->andWhere('s.time <= :end')
            ->setParameter('bucket', $bucket)
            ->setParameter('start', $start)
            ->setParameter('end', $end)
            ->orderBy('s.time', 'ASC')
            ->getQuery()
            ->getResult()
        ;

        return $result;
    }

    /**
     * 根据存储空间和时间查找特定的统计记录
     *
     * @param Bucket $bucket 存储空间实体
     * @param \DateTimeInterface $time 统计时间
     * @return BucketDayStatistic|null 找到的统计记录，未找到时返回 null
     */
    public function findOneByBucketAndTime(Bucket $bucket, \DateTimeInterface $time): ?BucketDayStatistic
    {
        /** @var BucketDayStatistic|null $result */
        $result = $this->createQueryBuilder('s')
            ->andWhere('s.bucket = :bucket')
            ->andWhere('s.time = :time')
            ->setParameter('bucket', $bucket)
            ->setParameter('time', $time)
            ->getQuery()
            ->getOneOrNullResult()
        ;

        return $result;
    }
}
