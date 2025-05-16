<?php

namespace AlipayFundAuthBundle\Repository;

use AlipayFundAuthBundle\Entity\TradePromoParams;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;


/**
 * @method TradePromoParams|null find($id, $lockMode = null, $lockVersion = null)
 * @method TradePromoParams|null findOneBy(array $criteria, array $orderBy = null)
 * @method TradePromoParams[]    findAll()
 * @method TradePromoParams[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TradePromoParamsRepository extends ServiceEntityRepository
{

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TradePromoParams::class);
    }
}
