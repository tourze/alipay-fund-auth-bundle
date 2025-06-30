<?php

namespace AlipayFundAuthBundle\Tests\Repository;

use AlipayFundAuthBundle\Repository\FundAuthPostPaymentRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\MockObject\MockObject;

class FundAuthPostPaymentRepositoryTest extends TestCase
{
    private FundAuthPostPaymentRepository $repository;
    private MockObject $registry;

    protected function setUp(): void
    {
        $this->registry = $this->createMock(ManagerRegistry::class);
        $entityManager = $this->createMock(EntityManagerInterface::class);
        
        $this->registry->method('getManagerForClass')->willReturn($entityManager);
        
        $this->repository = new FundAuthPostPaymentRepository($this->registry);
    }

    /**
     * 测试 Repository 创建
     */
    public function testCreateRepository_withValidRegistry_createsRepository(): void
    {
        $this->assertInstanceOf(FundAuthPostPaymentRepository::class, $this->repository);
    }
}