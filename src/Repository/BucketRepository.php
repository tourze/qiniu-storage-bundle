<?php

namespace QiniuStorageBundle\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use QiniuStorageBundle\Entity\Bucket;
use Tourze\PHPUnitSymfonyKernelTest\Attribute\AsRepository;

/**
 * 七牛云存储空间 Repository
 *
 * 提供七牛云存储空间的数据访问操作，包括：
 * - 基础的 CRUD 操作
 * - 查找需要同步的存储空间
 *
 * @extends ServiceEntityRepository<Bucket>
 */
#[AsRepository(entityClass: Bucket::class)]
class BucketRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Bucket::class);
    }

    /**
     * 保存存储空间实体
     *
     * @param Bucket $entity 存储空间实体
     * @param bool $flush 是否立即刷新到数据库
     */
    public function save(Bucket $entity, bool $flush = true): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * 删除存储空间实体
     *
     * @param Bucket $entity 存储空间实体
     * @param bool $flush 是否立即刷新到数据库
     */
    public function remove(Bucket $entity, bool $flush = true): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * 查找需要同步的存储空间
     *
     * @param \DateTimeInterface $before 同步截止时间
     * @return array<int, Bucket> 需要同步的存储空间列表
     */
    public function findNeedSync(\DateTimeInterface $before): array
    {
        /** @var array<int, Bucket> $result */
        $result = $this->createQueryBuilder('b')
            ->andWhere('b.lastSyncTime IS NULL OR b.lastSyncTime < :before')
            ->setParameter('before', $before)
            ->getQuery()
            ->getResult()
        ;

        return $result;
    }
}
