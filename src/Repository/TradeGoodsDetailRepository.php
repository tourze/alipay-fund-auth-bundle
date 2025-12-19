<?php

namespace AlipayFundAuthBundle\Repository;

use AlipayFundAuthBundle\Entity\TradeGoodsDetail;
use AlipayFundAuthBundle\Entity\TradeOrder;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\QueryBuilder;
use Tourze\PHPUnitSymfonyKernelTest\Attribute\AsRepository;

/**
 * @extends ServiceEntityRepository<TradeGoodsDetail>
 */
#[AsRepository(entityClass: TradeGoodsDetail::class)]
final class TradeGoodsDetailRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TradeGoodsDetail::class);
    }

    public function save(TradeGoodsDetail $entity, bool $flush = true): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(TradeGoodsDetail $entity, bool $flush = true): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * 根据交易订单查找商品详情
     *
     * @return array<TradeGoodsDetail>
     */
    public function findByTradeOrder(TradeOrder $tradeOrder): array
    {
        return $this->findBy(['tradeOrder' => $tradeOrder]);
    }

    /**
     * 根据商品ID查找
     *
     * @return array<TradeGoodsDetail>
     */
    public function findByGoodsId(string $goodsId): array
    {
        return $this->findBy(['goodsId' => $goodsId]);
    }

    /**
     * 根据商品分类查找
     *
     * @param string $goodsCategory 商品分类
     * @return array<TradeGoodsDetail>
     */
    public function findByGoodsCategory(string $goodsCategory): array
    {
        return $this->findBy(['goodsCategory' => $goodsCategory]);
    }

    /**
     * 获取带有关联数据的查询构建器，避免N+1查询问题
     */
    public function createWithRelationsQueryBuilder(): QueryBuilder
    {
        return $this->createQueryBuilder('tgd')
            ->leftJoin('tgd.tradeOrder', 'tradeOrder')->addSelect('tradeOrder');
    }

    /**
     * 统计指定交易订单的商品数量
     */
    public function countByTradeOrder(TradeOrder $tradeOrder): int
    {
        return (int) $this->createQueryBuilder('tgd')
            ->select('COUNT(tgd.id)')
            ->andWhere('tgd.tradeOrder = :tradeOrder')
            ->setParameter('tradeOrder', $tradeOrder)
            ->getQuery()
            ->getSingleScalarResult();
    }

    /**
     * 根据商品名称模糊搜索
     *
     * @param string $goodsName 商品名称
     * @return array<TradeGoodsDetail>
     */
    public function findByGoodsNameContaining(string $goodsName): array
    {
        return $this->createQueryBuilder('tgd')
            ->andWhere('tgd.goodsName LIKE :goodsName')
            ->setParameter('goodsName', '%' . $goodsName . '%')
            ->getQuery()
            ->getResult();
    }
}
