<?php

namespace QiniuStorageBundle\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use QiniuStorageBundle\Entity\Account;
use Tourze\PHPUnitSymfonyKernelTest\Attribute\AsRepository;

/**
 * 七牛云账户 Repository
 *
 * 提供七牛云账户的数据访问操作，包括基础的 CRUD 操作
 *
 * @extends ServiceEntityRepository<Account>
 */
#[AsRepository(entityClass: Account::class)]
class AccountRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Account::class);
    }

    /**
     * 保存账户实体
     *
     * @param Account $entity 账户实体
     * @param bool $flush 是否立即刷新到数据库
     */
    public function save(Account $entity, bool $flush = true): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * 删除账户实体
     *
     * @param Account $entity 账户实体
     * @param bool $flush 是否立即刷新到数据库
     */
    public function remove(Account $entity, bool $flush = true): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
}
