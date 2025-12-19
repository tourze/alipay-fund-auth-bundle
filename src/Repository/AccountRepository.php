<?php

namespace AlipayFundAuthBundle\Repository;

use AlipayFundAuthBundle\Entity\Account;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Tourze\PHPUnitSymfonyKernelTest\Attribute\AsRepository;

/**
 * @extends ServiceEntityRepository<Account>
 */
#[AsRepository(entityClass: Account::class)]
final class AccountRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Account::class);
    }

    public function save(Account $entity, bool $flush = true): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Account $entity, bool $flush = true): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * 根据AppID查找账户
     */
    public function findByAppId(string $appId): ?Account
    {
        return $this->findOneBy(['appId' => $appId]);
    }

    /**
     * 查找所有启用的账户
     *
     * @return array<Account>
     */
    public function findEnabled(): array
    {
        return $this->findBy(['valid' => true]);
    }

    /**
     * 查找所有禁用的账户
     *
     * @return array<Account>
     */
    public function findDisabled(): array
    {
        return $this->findBy(['valid' => false]);
    }

    /**
     * 根据账户名称模糊搜索
     *
     * @param string $name 账户名称
     * @return array<Account>
     */
    public function findByNameContaining(string $name): array
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.name LIKE :name')
            ->setParameter('name', '%' . $name . '%')
            ->getQuery()
            ->getResult();
    }

    /**
     * 统计账户总数
     */
    public function countAll(): int
    {
        return (int) $this->createQueryBuilder('a')
            ->select('COUNT(a.id)')
            ->getQuery()
            ->getSingleScalarResult();
    }

    /**
     * 统计启用账户数量
     */
    public function countEnabled(): int
    {
        return (int) $this->createQueryBuilder('a')
            ->select('COUNT(a.id)')
            ->andWhere('a.valid = :valid')
            ->setParameter('valid', true)
            ->getQuery()
            ->getSingleScalarResult();
    }

    /**
     * 查找最近创建的账户
     *
     * @param int $limit 限制数量
     * @return array<Account>
     */
    public function findRecent(int $limit = 10): array
    {
        return $this->createQueryBuilder('a')
            ->orderBy('a.createTime', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }
}
