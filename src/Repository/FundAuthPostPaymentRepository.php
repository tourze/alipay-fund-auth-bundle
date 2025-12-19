<?php

namespace AlipayFundAuthBundle\Repository;

use AlipayFundAuthBundle\Entity\FundAuthPostPayment;
use AlipayFundAuthBundle\Entity\FundAuthOrder;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\QueryBuilder;
use Tourze\PHPUnitSymfonyKernelTest\Attribute\AsRepository;

/**
 * @extends ServiceEntityRepository<FundAuthPostPayment>
 */
#[AsRepository(entityClass: FundAuthPostPayment::class)]
final class FundAuthPostPaymentRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, FundAuthPostPayment::class);
    }

    public function save(FundAuthPostPayment $entity, bool $flush = true): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(FundAuthPostPayment $entity, bool $flush = true): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * 根据预授权订单查找后续支付记录
     *
     * @return array<FundAuthPostPayment>
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
        return $this->createQueryBuilder('fapp')
            ->leftJoin('fapp.fundAuthOrder', 'fundAuthOrder')->addSelect('fundAuthOrder');
    }

    /**
     * 统计指定预授权订单的后续支付记录数量
     */
    public function countByFundAuthOrder(FundAuthOrder $fundAuthOrder): int
    {
        return (int) $this->createQueryBuilder('fapp')
            ->select('COUNT(fapp.id)')
            ->andWhere('fapp.fundAuthOrder = :fundAuthOrder')
            ->setParameter('fundAuthOrder', $fundAuthOrder)
            ->getQuery()
            ->getSingleScalarResult();
    }
}
