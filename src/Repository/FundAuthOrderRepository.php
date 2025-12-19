<?php

namespace AlipayFundAuthBundle\Repository;

use AlipayFundAuthBundle\Entity\FundAuthOrder;
use AlipayFundAuthBundle\Entity\Account;
use AlipayFundAuthBundle\Enum\FundAuthOrderStatus;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\QueryBuilder;
use Tourze\PHPUnitSymfonyKernelTest\Attribute\AsRepository;

/**
 * @extends ServiceEntityRepository<FundAuthOrder>
 */
#[AsRepository(entityClass: FundAuthOrder::class)]
final class FundAuthOrderRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, FundAuthOrder::class);
    }

    public function save(FundAuthOrder $entity, bool $flush = true): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(FundAuthOrder $entity, bool $flush = true): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * 根据商户授权订单号查找
     */
    public function findByOutOrderNo(string $outOrderNo): ?FundAuthOrder
    {
        return $this->findOneBy(['outOrderNo' => $outOrderNo]);
    }

    /**
     * 根据支付宝授权订单号查找
     */
    public function findByAuthNo(string $authNo): ?FundAuthOrder
    {
        return $this->findOneBy(['authNo' => $authNo]);
    }

    /**
     * 根据操作流水号查找
     */
    public function findByOperationId(string $operationId): ?FundAuthOrder
    {
        return $this->findOneBy(['operationId' => $operationId]);
    }

    /**
     * 根据账户查找预授权订单
     *
     * @return array<FundAuthOrder>
     */
    public function findByAccount(Account $account): array
    {
        return $this->findBy(['account' => $account]);
    }

    /**
     * 根据状态查找订单
     *
     * @return array<FundAuthOrder>
     */
    public function findByStatus(FundAuthOrderStatus $status): array
    {
        return $this->findBy(['status' => $status]);
    }

    /**
     * 根据付款用户ID查找订单
     *
     * @return array<FundAuthOrder>
     */
    public function findByPayerUserId(string $payerUserId): array
    {
        return $this->findBy(['payerUserId' => $payerUserId]);
    }

    /**
     * 获取带有关联数据的查询构建器，避免N+1查询问题
     */
    public function createWithRelationsQueryBuilder(): QueryBuilder
    {
        return $this->createQueryBuilder('fao')
            ->leftJoin('fao.account', 'account')->addSelect('account')
            ->leftJoin('fao.postPayments', 'postPayments')->addSelect('postPayments')
            ->leftJoin('fao.unfreezeLogs', 'unfreezeLogs')->addSelect('unfreezeLogs')
            ->leftJoin('fao.trades', 'trades')->addSelect('trades');
    }

    /**
     * 查找指定交易时间范围内的订单
     *
     * @param \DateTimeInterface $startTime 开始时间
     * @param \DateTimeInterface $endTime 结束时间
     * @return array<FundAuthOrder>
     */
    public function findByDateRange(\DateTimeInterface $startTime, \DateTimeInterface $endTime): array
    {
        return $this->createQueryBuilder('fao')
            ->andWhere('fao.gmtTrans >= :startTime')
            ->andWhere('fao.gmtTrans <= :endTime')
            ->setParameter('startTime', $startTime)
            ->setParameter('endTime', $endTime)
            ->getQuery()
            ->getResult();
    }

    /**
     * 根据订单标题模糊搜索
     *
     * @param string $keyword 搜索关键词
     * @return array<FundAuthOrder>
     */
    public function findByOrderTitleKeyword(string $keyword): array
    {
        return $this->createQueryBuilder('fao')
            ->andWhere('fao.orderTitle LIKE :keyword')
            ->setParameter('keyword', '%' . $keyword . '%')
            ->getQuery()
            ->getResult();
    }

    /**
     * 统计指定账户的订单数量
     */
    public function countByAccount(Account $account): int
    {
        return (int) $this->createQueryBuilder('fao')
            ->select('COUNT(fao.id)')
            ->andWhere('fao.account = :account')
            ->setParameter('account', $account)
            ->getQuery()
            ->getSingleScalarResult();
    }

    /**
     * 统计指定状态的订单数量
     */
    public function countByStatus(FundAuthOrderStatus $status): int
    {
        return (int) $this->createQueryBuilder('fao')
            ->select('COUNT(fao.id)')
            ->andWhere('fao.status = :status')
            ->setParameter('status', $status)
            ->getQuery()
            ->getSingleScalarResult();
    }

    /**
     * 查找需要处理的订单（排除已完成状态）
     *
     * @return array<FundAuthOrder>
     */
    public function findPendingOrders(): array
    {
        return $this->createQueryBuilder('fao')
            ->andWhere('fao.status != :completedStatus')
            ->setParameter('completedStatus', FundAuthOrderStatus::SUCCESS)
            ->getQuery()
            ->getResult();
    }

    /**
     * 查找过期未支付的订单
     *
     * @param \DateTimeInterface $expireTime 过期时间
     * @return array<FundAuthOrder>
     */
    public function findExpiredOrders(\DateTimeInterface $expireTime): array
    {
        return $this->createQueryBuilder('fao')
            ->andWhere('fao.status = :initStatus')
            ->andWhere('fao.gmtTrans IS NULL')
            ->setParameter('initStatus', FundAuthOrderStatus::INIT)
            ->getQuery()
            ->getResult();
    }
}
