<?php

namespace AlipayFundAuthBundle\Repository;

use AlipayFundAuthBundle\Entity\FundAuthPostPayment;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Tourze\PHPUnitSymfonyKernelTest\Attribute\AsRepository;

/**
 * @extends ServiceEntityRepository<FundAuthPostPayment>
 */
#[AsRepository(entityClass: FundAuthPostPayment::class)]
class FundAuthPostPaymentRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, FundAuthPostPayment::class);
    }

    public function save(FundAuthPostPayment $entity, bool $flush = true): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(FundAuthPostPayment $entity, bool $flush = true): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
}
