<?php

namespace AlipayFundAuthBundle\Repository;

use AlipayFundAuthBundle\Entity\TradeOrder;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;


/**
 * @method TradeOrder|null find($id, $lockMode = null, $lockVersion = null)
 * @method TradeOrder|null findOneBy(array $criteria, array $orderBy = null)
 * @method TradeOrder[]    findAll()
 * @method TradeOrder[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TradeOrderRepository extends ServiceEntityRepository
{

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TradeOrder::class);
    }
}
