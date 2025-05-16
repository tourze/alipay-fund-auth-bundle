<?php

namespace AlipayFundAuthBundle\Repository;

use AlipayFundAuthBundle\Entity\TradeVoucherDetail;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;


/**
 * @method TradeVoucherDetail|null find($id, $lockMode = null, $lockVersion = null)
 * @method TradeVoucherDetail|null findOneBy(array $criteria, array $orderBy = null)
 * @method TradeVoucherDetail[]    findAll()
 * @method TradeVoucherDetail[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TradeVoucherDetailRepository extends ServiceEntityRepository
{

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TradeVoucherDetail::class);
    }
}
