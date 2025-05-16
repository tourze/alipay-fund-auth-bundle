<?php

namespace AlipayFundAuthBundle\Repository;

use AlipayFundAuthBundle\Entity\FundAuthUnfreezeLog;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;


/**
 * @method FundAuthUnfreezeLog|null find($id, $lockMode = null, $lockVersion = null)
 * @method FundAuthUnfreezeLog|null findOneBy(array $criteria, array $orderBy = null)
 * @method FundAuthUnfreezeLog[]    findAll()
 * @method FundAuthUnfreezeLog[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class FundAuthUnfreezeLogRepository extends ServiceEntityRepository
{

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, FundAuthUnfreezeLog::class);
    }
}
