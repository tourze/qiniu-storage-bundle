<?php

namespace QiniuStorageBundle\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use QiniuStorageBundle\Entity\Bucket;

/**
 * @method Bucket|null find($id, $lockMode = null, $lockVersion = null)
 * @method Bucket|null findOneBy(array $criteria, array $orderBy = null)
 * @method Bucket[] findAll()
 * @method Bucket[] findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BucketRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Bucket::class);
    }

    /**
     * 查找需要同步的存储空间
     *
     * @return Bucket[]
     */
    public function findNeedSync(\DateTimeInterface $before): array
    {
        return $this->createQueryBuilder('b')
            ->andWhere('b.lastSyncAt IS NULL OR b.lastSyncAt < :before')
            ->setParameter('before', $before)
            ->getQuery()
            ->getResult();
    }
}
