<?php

namespace AlipayFundAuthBundle\Tests\Repository;

use AlipayFundAuthBundle\Entity\Account;
use AlipayFundAuthBundle\Entity\FundAuthOrder;
use AlipayFundAuthBundle\Entity\TradeOrder;
use AlipayFundAuthBundle\Entity\TradePromoParam;
use AlipayFundAuthBundle\Enum\FundAuthOrderStatus;
use AlipayFundAuthBundle\Repository\AccountRepository;
use AlipayFundAuthBundle\Repository\FundAuthOrderRepository;
use AlipayFundAuthBundle\Repository\TradeOrderRepository;
use AlipayFundAuthBundle\Repository\TradePromoParamRepository;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\PHPUnitSymfonyKernelTest\AbstractRepositoryTestCase;

/**
 * @internal
 */
#[CoversClass(TradePromoParamRepository::class)]
#[RunTestsInSeparateProcesses]
final class TradePromoParamRepositoryTest extends AbstractRepositoryTestCase
{
    private TradePromoParamRepository $repository;

    private AccountRepository $accountRepository;

    private FundAuthOrderRepository $fundAuthOrderRepository;

    private TradeOrderRepository $tradeOrderRepository;

    protected function onSetUp(): void
    {
        $this->repository = self::getService(TradePromoParamRepository::class);
        $this->accountRepository = self::getService(AccountRepository::class);
        $this->fundAuthOrderRepository = self::getService(FundAuthOrderRepository::class);
        $this->tradeOrderRepository = self::getService(TradeOrderRepository::class);
    }

    public function testFindOneByWithOrderByShouldRespectOrderParameter(): void
    {
        $this->clearDatabase();

        $tradeOrder1 = $this->createTradeOrder('order_1');
        $tradeOrder2 = $this->createTradeOrder('order_2');

        $promoParam1 = new TradePromoParam();
        $promoParam1->setTradeOrder($tradeOrder1);
        $promoParam1->setActualOrderTime(new \DateTimeImmutable('2024-01-15 15:00:00'));
        $this->repository->save($promoParam1);

        $promoParam2 = new TradePromoParam();
        $promoParam2->setTradeOrder($tradeOrder2);
        $promoParam2->setActualOrderTime(new \DateTimeImmutable('2024-01-15 10:00:00'));
        $this->repository->save($promoParam2);

        $firstPromoParam = $this->repository->findOneBy([], ['id' => 'ASC']);
        $this->assertInstanceOf(TradePromoParam::class, $firstPromoParam);

        $lastPromoParam = $this->repository->findOneBy([], ['id' => 'DESC']);
        $this->assertInstanceOf(TradePromoParam::class, $lastPromoParam);

        $this->assertTrue($firstPromoParam->getId() < $lastPromoParam->getId());
    }

    public function testFindByWithNullableFieldShouldSupportIsNullQueries(): void
    {
        $this->clearDatabase();

        $tradeOrder1 = $this->createTradeOrder('order_with_time');
        $tradeOrder2 = $this->createTradeOrder('order_without_time');

        $promoParamWithTime = new TradePromoParam();
        $promoParamWithTime->setTradeOrder($tradeOrder1);
        $promoParamWithTime->setActualOrderTime(new \DateTimeImmutable('2024-01-15 10:00:00'));
        $this->repository->save($promoParamWithTime);

        $promoParamWithoutTime = new TradePromoParam();
        $promoParamWithoutTime->setTradeOrder($tradeOrder2);
        $this->repository->save($promoParamWithoutTime);

        $promoParamsWithoutTime = $this->repository->findBy(['actualOrderTime' => null]);
        $this->assertCount(1, $promoParamsWithoutTime);
        $this->assertInstanceOf(TradeOrder::class, $promoParamsWithoutTime[0]->getTradeOrder());
        $this->assertSame($tradeOrder2->getId(), $promoParamsWithoutTime[0]->getTradeOrder()->getId());
    }

    public function testCountByTradeOrderRelationShouldReturnCorrectCount(): void
    {
        $this->clearDatabase();

        $tradeOrder1 = $this->createTradeOrder('order_1');
        $tradeOrder2 = $this->createTradeOrder('order_2');

        $promoParam1 = new TradePromoParam();
        $promoParam1->setTradeOrder($tradeOrder1);
        $this->repository->save($promoParam1);

        $promoParam2 = new TradePromoParam();
        $promoParam2->setTradeOrder($tradeOrder2);
        $this->repository->save($promoParam2);

        $tradeOrder1Count = $this->repository->count(['tradeOrder' => $tradeOrder1]);
        $this->assertSame(1, $tradeOrder1Count);

        $tradeOrder2Count = $this->repository->count(['tradeOrder' => $tradeOrder2]);
        $this->assertSame(1, $tradeOrder2Count);

        $totalCount = $this->repository->count([]);
        $this->assertSame(2, $totalCount);
    }

    public function testCountWithNullableFieldShouldSupportIsNullQueries(): void
    {
        $this->clearDatabase();

        $tradeOrder1 = $this->createTradeOrder('order_with_time');
        $tradeOrder2 = $this->createTradeOrder('order_without_time');

        $promoParamWithTime = new TradePromoParam();
        $promoParamWithTime->setTradeOrder($tradeOrder1);
        $promoParamWithTime->setActualOrderTime(new \DateTimeImmutable('2024-01-15 10:00:00'));
        $this->repository->save($promoParamWithTime);

        $promoParamWithoutTime = new TradePromoParam();
        $promoParamWithoutTime->setTradeOrder($tradeOrder2);
        $this->repository->save($promoParamWithoutTime);

        $countWithoutTime = $this->repository->count(['actualOrderTime' => null]);
        $this->assertSame(1, $countWithoutTime);

        $countWithTime = $this->repository->count();
        $this->assertSame(2, $countWithTime);
    }

    public function testFindByTradeOrderRelationShouldReturnCorrectResults(): void
    {
        $this->clearDatabase();

        $tradeOrder1 = $this->createTradeOrder('order_1');
        $tradeOrder2 = $this->createTradeOrder('order_2');

        $promoParam1 = new TradePromoParam();
        $promoParam1->setTradeOrder($tradeOrder1);
        $promoParam1->setActualOrderTime(new \DateTimeImmutable('2024-01-15 10:00:00'));
        $this->repository->save($promoParam1);

        $promoParam2 = new TradePromoParam();
        $promoParam2->setTradeOrder($tradeOrder2);
        $promoParam2->setActualOrderTime(new \DateTimeImmutable('2024-01-15 11:00:00'));
        $this->repository->save($promoParam2);

        $tradeOrder1PromoParams = $this->repository->findBy(['tradeOrder' => $tradeOrder1]);
        $this->assertCount(1, $tradeOrder1PromoParams);
        $this->assertEquals(new \DateTimeImmutable('2024-01-15 10:00:00'), $tradeOrder1PromoParams[0]->getActualOrderTime());
    }

    private function createTradeOrder(string $outTradeNo = 'test_trade_no'): TradeOrder
    {
        $account = $this->createAccount();
        $fundAuthOrder = $this->createFundAuthOrder($account);

        $tradeOrder = new TradeOrder();
        $tradeOrder->setAccount($account);
        $tradeOrder->setFundAuthOrder($fundAuthOrder);
        $tradeOrder->setOutTradeNo($outTradeNo . '_' . uniqid());
        $tradeOrder->setSubject('测试交易订单');
        $tradeOrder->setTotalAmount('100.00');

        $this->tradeOrderRepository->save($tradeOrder);

        return $tradeOrder;
    }

    private function createAccount(): Account
    {
        $account = new Account();
        $account->setName('测试账号_' . uniqid());
        $account->setAppId('test_app_id_' . uniqid());
        $account->setValid(true);

        $this->accountRepository->save($account);

        return $account;
    }

    private function createFundAuthOrder(Account $account): FundAuthOrder
    {
        $fundAuthOrder = new FundAuthOrder();
        $fundAuthOrder->setAccount($account);
        $fundAuthOrder->setOutOrderNo('test_fund_order_' . uniqid());
        $fundAuthOrder->setOutRequestNo('test_fund_request_' . uniqid());
        $fundAuthOrder->setOrderTitle('测试预授权订单');
        $fundAuthOrder->setAmount('100.00');
        $fundAuthOrder->setStatus(FundAuthOrderStatus::INIT);

        $this->fundAuthOrderRepository->save($fundAuthOrder);

        return $fundAuthOrder;
    }

    private function clearDatabase(): void
    {
        $entityManager = self::getEntityManager();
        $entityManager->createQuery('DELETE FROM ' . TradePromoParam::class)->execute();
        $entityManager->createQuery('DELETE FROM ' . TradeOrder::class)->execute();
        $entityManager->createQuery('DELETE FROM ' . FundAuthOrder::class)->execute();
        $entityManager->createQuery('DELETE FROM ' . Account::class)->execute();
    }

    public function testCountByAssociationTradeOrderShouldReturnCorrectNumber(): void
    {
        $this->clearDatabase();

        $tradeOrder1 = $this->createTradeOrder('order_1');
        $tradeOrder2 = $this->createTradeOrder('order_2');

        $param1 = new TradePromoParam();
        $param1->setTradeOrder($tradeOrder1);
        $param1->setActualOrderTime(new \DateTimeImmutable('2024-01-15 10:00:00'));
        $this->repository->save($param1);

        $param2 = new TradePromoParam();
        $param2->setTradeOrder($tradeOrder2);
        $param2->setActualOrderTime(new \DateTimeImmutable('2024-01-15 11:00:00'));
        $this->repository->save($param2);

        $order1Count = $this->repository->count(['tradeOrder' => $tradeOrder1]);
        $this->assertSame(1, $order1Count);

        $order2Count = $this->repository->count(['tradeOrder' => $tradeOrder2]);
        $this->assertSame(1, $order2Count);
    }

    public function testFindByNullableFieldsShouldSupportIsNullQueries(): void
    {
        $this->clearDatabase();

        $tradeOrder1 = $this->createTradeOrder('order_with_time');
        $tradeOrder2 = $this->createTradeOrder('order_without_time');

        $paramWithTime = new TradePromoParam();
        $paramWithTime->setTradeOrder($tradeOrder1);
        $paramWithTime->setActualOrderTime(new \DateTimeImmutable('2024-01-15 10:00:00'));
        $this->repository->save($paramWithTime);

        $paramWithoutTime = new TradePromoParam();
        $paramWithoutTime->setTradeOrder($tradeOrder2);
        $this->repository->save($paramWithoutTime);

        $paramsWithoutTime = $this->repository->findBy(['actualOrderTime' => null]);
        $this->assertCount(1, $paramsWithoutTime);
        $this->assertInstanceOf(TradeOrder::class, $paramsWithoutTime[0]->getTradeOrder());
        $this->assertSame($tradeOrder2->getId(), $paramsWithoutTime[0]->getTradeOrder()->getId());
    }

    public function testFindOneByAssociationTradeOrderShouldReturnMatchingEntity(): void
    {
        $tradeOrder1 = $this->createTradeOrder('order_1');
        $tradeOrder2 = $this->createTradeOrder('order_2');

        $param1 = new TradePromoParam();
        $param1->setTradeOrder($tradeOrder1);
        $param1->setActualOrderTime(new \DateTimeImmutable('2024-01-15 10:00:00'));
        $this->repository->save($param1);

        $param2 = new TradePromoParam();
        $param2->setTradeOrder($tradeOrder2);
        $param2->setActualOrderTime(new \DateTimeImmutable('2024-01-15 11:00:00'));
        $this->repository->save($param2);

        $foundParam = $this->repository->findOneBy(['tradeOrder' => $tradeOrder1]);
        $this->assertInstanceOf(TradePromoParam::class, $foundParam);
        $this->assertEquals(new \DateTimeImmutable('2024-01-15 10:00:00'), $foundParam->getActualOrderTime());
        $this->assertInstanceOf(TradeOrder::class, $foundParam->getTradeOrder());
        $this->assertSame($tradeOrder1->getId(), $foundParam->getTradeOrder()->getId());
    }

    /**
     * @return ServiceEntityRepository<TradePromoParam>
     */
    protected function getRepository(): ServiceEntityRepository
    {
        return $this->repository;
    }

    protected function createNewEntity(): TradePromoParam
    {
        $tradeOrder = $this->createTradeOrder();

        $promoParam = new TradePromoParam();
        $promoParam->setTradeOrder($tradeOrder);
        $promoParam->setActualOrderTime(new \DateTimeImmutable('2024-01-15 10:30:00'));

        return $promoParam;
    }
}
