<?php

namespace AlipayFundAuthBundle\Repository;

use AlipayFundAuthBundle\Entity\TradeFundBill;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;


/**
 * @method TradeFundBill|null find($id, $lockMode = null, $lockVersion = null)
 * @method TradeFundBill|null findOneBy(array $criteria, array $orderBy = null)
 * @method TradeFundBill[]    findAll()
 * @method TradeFundBill[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TradeFundBillRepository extends ServiceEntityRepository
{

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TradeFundBill::class);
    }
}
