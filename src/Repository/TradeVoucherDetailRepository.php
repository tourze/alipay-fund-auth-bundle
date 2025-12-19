<?php

namespace AlipayFundAuthBundle\Repository;

use AlipayFundAuthBundle\Entity\TradeVoucherDetail;
use AlipayFundAuthBundle\Entity\TradeOrder;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\QueryBuilder;
use Tourze\PHPUnitSymfonyKernelTest\Attribute\AsRepository;

/**
 * @extends ServiceEntityRepository<TradeVoucherDetail>
 */
#[AsRepository(entityClass: TradeVoucherDetail::class)]
final class TradeVoucherDetailRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TradeVoucherDetail::class);
    }

    public function save(TradeVoucherDetail $entity, bool $flush = true): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(TradeVoucherDetail $entity, bool $flush = true): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * 根据交易订单查找代金券详情
     *
     * @return array<TradeVoucherDetail>
     */
    public function findByTradeOrder(TradeOrder $tradeOrder): array
    {
        return $this->findBy(['tradeOrder' => $tradeOrder]);
    }

    /**
     * 根据代金券ID查找
     *
     * @return array<TradeVoucherDetail>
     */
    public function findByVoucherId(string $voucherId): array
    {
        return $this->findBy(['voucherId' => $voucherId]);
    }

    /**
     * 获取带有关联数据的查询构建器，避免N+1查询问题
     */
    public function createWithRelationsQueryBuilder(): QueryBuilder
    {
        return $this->createQueryBuilder('tvd')
            ->leftJoin('tvd.tradeOrder', 'tradeOrder')->addSelect('tradeOrder');
    }

    /**
     * 统计指定交易订单的代金券数量
     */
    public function countByTradeOrder(TradeOrder $tradeOrder): int
    {
        return (int) $this->createQueryBuilder('tvd')
            ->select('COUNT(tvd.id)')
            ->andWhere('tvd.tradeOrder = :tradeOrder')
            ->setParameter('tradeOrder', $tradeOrder)
            ->getQuery()
            ->getSingleScalarResult();
    }
}
