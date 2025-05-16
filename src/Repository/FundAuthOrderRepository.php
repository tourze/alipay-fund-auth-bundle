<?php

namespace AlipayFundAuthBundle\Repository;

use AlipayFundAuthBundle\Entity\FundAuthOrder;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;


/**
 * @method FundAuthOrder|null find($id, $lockMode = null, $lockVersion = null)
 * @method FundAuthOrder|null findOneBy(array $criteria, array $orderBy = null)
 * @method FundAuthOrder[]    findAll()
 * @method FundAuthOrder[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class FundAuthOrderRepository extends ServiceEntityRepository
{

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, FundAuthOrder::class);
    }
}
