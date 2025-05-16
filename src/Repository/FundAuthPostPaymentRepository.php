<?php

namespace AlipayFundAuthBundle\Repository;

use AlipayFundAuthBundle\Entity\FundAuthPostPayment;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;


/**
 * @method FundAuthPostPayment|null find($id, $lockMode = null, $lockVersion = null)
 * @method FundAuthPostPayment|null findOneBy(array $criteria, array $orderBy = null)
 * @method FundAuthPostPayment[]    findAll()
 * @method FundAuthPostPayment[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class FundAuthPostPaymentRepository extends ServiceEntityRepository
{

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, FundAuthPostPayment::class);
    }
}
