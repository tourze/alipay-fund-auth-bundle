<?php

namespace AlipayFundAuthBundle\Repository;

use AlipayFundAuthBundle\Entity\FundAuthUnfreezeLog;
use AlipayFundAuthBundle\Entity\FundAuthOrder;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\QueryBuilder;
use Tourze\PHPUnitSymfonyKernelTest\Attribute\AsRepository;

/**
 * @extends ServiceEntityRepository<FundAuthUnfreezeLog>
 */
#[AsRepository(entityClass: FundAuthUnfreezeLog::class)]
final class FundAuthUnfreezeLogRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, FundAuthUnfreezeLog::class);
    }

    public function save(FundAuthUnfreezeLog $entity, bool $flush = true): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(FundAuthUnfreezeLog $entity, bool $flush = true): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * 根据预授权订单查找解冻日志
     *
     * @return array<FundAuthUnfreezeLog>
     */
    public function findByFundAuthOrder(FundAuthOrder $fundAuthOrder): array
    {
        return $this->findBy(['fundAuthOrder' => $fundAuthOrder]);
    }

    /**
     * 获取带有关联数据的查询构建器，避免N+1查询问题
     */
    public function createWithRelationsQueryBuilder(): QueryBuilder
    {
        return $this->createQueryBuilder('faul')
            ->leftJoin('faul.fundAuthOrder', 'fundAuthOrder')->addSelect('fundAuthOrder');
    }

    /**
     * 统计指定预授权订单的解冻日志数量
     */
    public function countByFundAuthOrder(FundAuthOrder $fundAuthOrder): int
    {
        return (int) $this->createQueryBuilder('faul')
            ->select('COUNT(faul.id)')
            ->andWhere('faul.fundAuthOrder = :fundAuthOrder')
            ->setParameter('fundAuthOrder', $fundAuthOrder)
            ->getQuery()
            ->getSingleScalarResult();
    }

    /**
     * 查找指定时间范围内的解冻日志
     *
     * @param \DateTimeInterface $startTime 开始时间
     * @param \DateTimeInterface $endTime 结束时间
     * @return array<FundAuthUnfreezeLog>
     */
    public function findByDateRange(\DateTimeInterface $startTime, \DateTimeInterface $endTime): array
    {
        return $this->createQueryBuilder('faul')
            ->andWhere('faul.gmtTrans >= :startTime')
            ->andWhere('faul.gmtTrans <= :endTime')
            ->setParameter('startTime', $startTime)
            ->setParameter('endTime', $endTime)
            ->getQuery()
            ->getResult();
    }
}
