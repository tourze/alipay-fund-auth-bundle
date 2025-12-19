<?php

namespace AlipayFundAuthBundle\Repository;

use AlipayFundAuthBundle\Entity\TradeOrder;
use AlipayFundAuthBundle\Entity\FundAuthOrder;
use AlipayFundAuthBundle\Entity\Account;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\QueryBuilder;
use Tourze\PHPUnitSymfonyKernelTest\Attribute\AsRepository;

/**
 * @extends ServiceEntityRepository<TradeOrder>
 */
#[AsRepository(entityClass: TradeOrder::class)]
final class TradeOrderRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TradeOrder::class);
    }

    public function save(TradeOrder $entity, bool $flush = true): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(TradeOrder $entity, bool $flush = true): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * 根据商户订单号查找交易订单
     */
    public function findByOutTradeNo(string $outTradeNo): ?TradeOrder
    {
        return $this->findOneBy(['outTradeNo' => $outTradeNo]);
    }

    /**
     * 根据支付宝交易号查找交易订单
     */
    public function findByTradeNo(string $tradeNo): ?TradeOrder
    {
        return $this->findOneBy(['tradeNo' => $tradeNo]);
    }

    /**
     * 根据账户查找交易订单
     *
     * @return array<TradeOrder>
     */
    public function findByAccount(Account $account): array
    {
        return $this->findBy(['account' => $account]);
    }

    /**
     * 根据预授权订单查找交易订单
     *
     * @return array<TradeOrder>
     */
    public function findByFundAuthOrder(FundAuthOrder $fundAuthOrder): array
    {
        return $this->findBy(['fundAuthOrder' => $fundAuthOrder]);
    }

    /**
     * 根据交易状态查找订单
     *
     * @return array<TradeOrder>
     */
    public function findByTradeStatus(string $tradeStatus): array
    {
        return $this->findBy(['tradeStatus' => $tradeStatus]);
    }

    /**
     * 根据买家用户ID查找订单
     *
     * @return array<TradeOrder>
     */
    public function findByBuyerUserId(string $buyerUserId): array
    {
        return $this->findBy(['buyerUserId' => $buyerUserId]);
    }

    /**
     * 获取带有关联数据的查询构建器，避免N+1查询问题
     */
    public function createWithRelationsQueryBuilder(): QueryBuilder
    {
        return $this->createQueryBuilder('to')
            ->leftJoin('to.account', 'account')->addSelect('account')
            ->leftJoin('to.fundAuthOrder', 'fundAuthOrder')->addSelect('fundAuthOrder')
            ->leftJoin('to.goodsDetails', 'goodsDetails')->addSelect('goodsDetails')
            ->leftJoin('to.fundBills', 'fundBills')->addSelect('fundBills')
            ->leftJoin('to.voucherDetails', 'voucherDetails')->addSelect('voucherDetails');
    }

    /**
     * 查找指定时间范围内的订单
     *
     * @param \DateTimeInterface $startTime 开始时间
     * @param \DateTimeInterface $endTime 结束时间
     * @return array<TradeOrder>
     */
    public function findByDateRange(\DateTimeInterface $startTime, \DateTimeInterface $endTime): array
    {
        return $this->createQueryBuilder('to')
            ->andWhere('to.createTime >= :startTime')
            ->andWhere('to.createTime <= :endTime')
            ->setParameter('startTime', $startTime)
            ->setParameter('endTime', $endTime)
            ->getQuery()
            ->getResult();
    }

    /**
     * 根据订单标题模糊搜索
     *
     * @param string $keyword 搜索关键词
     * @return array<TradeOrder>
     */
    public function findBySubjectKeyword(string $keyword): array
    {
        return $this->createQueryBuilder('to')
            ->andWhere('to.subject LIKE :keyword')
            ->setParameter('keyword', '%' . $keyword . '%')
            ->getQuery()
            ->getResult();
    }

    /**
     * 统计指定账户的订单数量
     */
    public function countByAccount(Account $account): int
    {
        return (int) $this->createQueryBuilder('to')
            ->select('COUNT(to.id)')
            ->andWhere('to.account = :account')
            ->setParameter('account', $account)
            ->getQuery()
            ->getSingleScalarResult();
    }

    /**
     * 统计指定状态的订单数量
     */
    public function countByTradeStatus(string $tradeStatus): int
    {
        return (int) $this->createQueryBuilder('to')
            ->select('COUNT(to.id)')
            ->andWhere('to.tradeStatus = :tradeStatus')
            ->setParameter('tradeStatus', $tradeStatus)
            ->getQuery()
            ->getSingleScalarResult();
    }
}
