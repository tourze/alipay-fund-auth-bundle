<?php

namespace AlipayFundAuthBundle\Repository;

use AlipayFundAuthBundle\Entity\TradeGoodsDetail;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;


/**
 * @method TradeGoodsDetail|null find($id, $lockMode = null, $lockVersion = null)
 * @method TradeGoodsDetail|null findOneBy(array $criteria, array $orderBy = null)
 * @method TradeGoodsDetail[]    findAll()
 * @method TradeGoodsDetail[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TradeGoodsDetailRepository extends ServiceEntityRepository
{

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TradeGoodsDetail::class);
    }
}
