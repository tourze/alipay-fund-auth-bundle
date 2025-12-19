<?php

namespace AlipayFundAuthBundle\Repository;

use AlipayFundAuthBundle\Entity\TradeFundBill;
use AlipayFundAuthBundle\Entity\TradeOrder;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\QueryBuilder;
use Tourze\PHPUnitSymfonyKernelTest\Attribute\AsRepository;

/**
 * @extends ServiceEntityRepository<TradeFundBill>
 */
#[AsRepository(entityClass: TradeFundBill::class)]
final class TradeFundBillRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TradeFundBill::class);
    }

    public function save(TradeFundBill $entity, bool $flush = true): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(TradeFundBill $entity, bool $flush = true): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * 根据交易订单查找资金账单
     *
     * @return array<TradeFundBill>
     */
    public function findByTradeOrder(TradeOrder $tradeOrder): array
    {
        return $this->findBy(['tradeOrder' => $tradeOrder]);
    }

    /**
     * 根据账单编码查找
     */
    public function findByFundBillNo(string $fundBillNo): ?TradeFundBill
    {
        return $this->findOneBy(['fundBillNo' => $fundBillNo]);
    }

    /**
     * 获取带有关联数据的查询构建器，避免N+1查询问题
     */
    public function createWithRelationsQueryBuilder(): QueryBuilder
    {
        return $this->createQueryBuilder('tfb')
            ->leftJoin('tfb.tradeOrder', 'tradeOrder')->addSelect('tradeOrder');
    }

    /**
     * 统计指定交易订单的账单数量
     */
    public function countByTradeOrder(TradeOrder $tradeOrder): int
    {
        return (int) $this->createQueryBuilder('tfb')
            ->select('COUNT(tfb.id)')
            ->andWhere('tfb.tradeOrder = :tradeOrder')
            ->setParameter('tradeOrder', $tradeOrder)
            ->getQuery()
            ->getSingleScalarResult();
    }
}
