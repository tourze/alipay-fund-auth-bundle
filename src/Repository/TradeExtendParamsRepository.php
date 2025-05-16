<?php

namespace AlipayFundAuthBundle\Repository;

use AlipayFundAuthBundle\Entity\TradeExtendParams;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;


/**
 * @method TradeExtendParams|null find($id, $lockMode = null, $lockVersion = null)
 * @method TradeExtendParams|null findOneBy(array $criteria, array $orderBy = null)
 * @method TradeExtendParams[]    findAll()
 * @method TradeExtendParams[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TradeExtendParamsRepository extends ServiceEntityRepository
{

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TradeExtendParams::class);
    }
}
