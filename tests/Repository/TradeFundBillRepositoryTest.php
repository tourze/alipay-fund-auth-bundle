<?php

namespace AlipayFundAuthBundle\Tests\Repository;

use AlipayFundAuthBundle\Entity\Account;
use AlipayFundAuthBundle\Entity\TradeFundBill;
use AlipayFundAuthBundle\Entity\TradeOrder;
use AlipayFundAuthBundle\Repository\AccountRepository;
use AlipayFundAuthBundle\Repository\TradeFundBillRepository;
use AlipayFundAuthBundle\Repository\TradeOrderRepository;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\PHPUnitSymfonyKernelTest\AbstractRepositoryTestCase;

/**
 * @internal
 */
#[CoversClass(TradeFundBillRepository::class)]
#[RunTestsInSeparateProcesses]
final class TradeFundBillRepositoryTest extends AbstractRepositoryTestCase
{
    private TradeFundBillRepository $repository;

    private TradeOrderRepository $tradeOrderRepository;

    private AccountRepository $accountRepository;

    protected function onSetUp(): void
    {
        $this->repository = self::getService(TradeFundBillRepository::class);
        $this->tradeOrderRepository = self::getService(TradeOrderRepository::class);
        $this->accountRepository = self::getService(AccountRepository::class);
    }

    public function testFindOneByWithOrderByShouldRespectOrderParameter(): void
    {
        $this->clearDatabase();

        $tradeOrder1 = $this->createTradeOrder();
        $tradeOrder2 = $this->createTradeOrder();

        $bill1 = new TradeFundBill();
        $bill1->setTradeOrder($tradeOrder1);
        $bill1->setFundChannel('ZLIPAYACCOUNT');
        $bill1->setAmount('100.00');
        $this->repository->save($bill1);

        $bill2 = new TradeFundBill();
        $bill2->setTradeOrder($tradeOrder2);
        $bill2->setFundChannel('ALIPAYACCOUNT');
        $bill2->setAmount('50.00');
        $this->repository->save($bill2);

        $firstBill = $this->repository->findOneBy([], ['fundChannel' => 'ASC']);
        $this->assertInstanceOf(TradeFundBill::class, $firstBill);
        $this->assertSame('ALIPAYACCOUNT', $firstBill->getFundChannel());

        $lastBill = $this->repository->findOneBy([], ['fundChannel' => 'DESC']);
        $this->assertInstanceOf(TradeFundBill::class, $lastBill);
        $this->assertSame('ZLIPAYACCOUNT', $lastBill->getFundChannel());
    }

    public function testFindByWithNullableFieldShouldSupportIsNullQueries(): void
    {
        $this->clearDatabase();

        $tradeOrder1 = $this->createTradeOrder();
        $tradeOrder2 = $this->createTradeOrder();

        $billWithRealAmount = new TradeFundBill();
        $billWithRealAmount->setTradeOrder($tradeOrder1);
        $billWithRealAmount->setFundChannel('ALIPAYACCOUNT');
        $billWithRealAmount->setAmount('30.00');
        $billWithRealAmount->setRealAmount('29.50');
        $this->repository->save($billWithRealAmount);

        $billWithoutRealAmount = new TradeFundBill();
        $billWithoutRealAmount->setTradeOrder($tradeOrder2);
        $billWithoutRealAmount->setFundChannel('BANKCARD');
        $billWithoutRealAmount->setAmount('40.00');
        $this->repository->save($billWithoutRealAmount);

        $billsWithoutRealAmount = $this->repository->findBy(['realAmount' => null]);
        $this->assertCount(1, $billsWithoutRealAmount);
        $this->assertSame('BANKCARD', $billsWithoutRealAmount[0]->getFundChannel());
    }

    public function testCountByTradeOrderRelationShouldReturnCorrectCount(): void
    {
        $this->clearDatabase();

        $tradeOrder1 = $this->createTradeOrder();
        $tradeOrder2 = $this->createTradeOrder();

        $bill1 = new TradeFundBill();
        $bill1->setTradeOrder($tradeOrder1);
        $bill1->setFundChannel('ALIPAYACCOUNT');
        $bill1->setAmount('30.00');
        $this->repository->save($bill1);

        $bill2 = new TradeFundBill();
        $bill2->setTradeOrder($tradeOrder1);
        $bill2->setFundChannel('BANKCARD');
        $bill2->setAmount('40.00');
        $this->repository->save($bill2);

        $bill3 = new TradeFundBill();
        $bill3->setTradeOrder($tradeOrder2);
        $bill3->setFundChannel('ALIPAYACCOUNT');
        $bill3->setAmount('50.00');
        $this->repository->save($bill3);

        $order1Count = $this->repository->count(['tradeOrder' => $tradeOrder1]);
        $this->assertSame(2, $order1Count);

        $order2Count = $this->repository->count(['tradeOrder' => $tradeOrder2]);
        $this->assertSame(1, $order2Count);
    }

    public function testCountWithNullableFieldShouldSupportIsNullQueries(): void
    {
        $this->clearDatabase();

        $tradeOrder1 = $this->createTradeOrder();
        $tradeOrder2 = $this->createTradeOrder();

        $billWithRealAmount = new TradeFundBill();
        $billWithRealAmount->setTradeOrder($tradeOrder1);
        $billWithRealAmount->setFundChannel('ALIPAYACCOUNT');
        $billWithRealAmount->setAmount('30.00');
        $billWithRealAmount->setRealAmount('29.50');
        $this->repository->save($billWithRealAmount);

        $billWithoutRealAmount = new TradeFundBill();
        $billWithoutRealAmount->setTradeOrder($tradeOrder2);
        $billWithoutRealAmount->setFundChannel('BANKCARD');
        $billWithoutRealAmount->setAmount('40.00');
        $this->repository->save($billWithoutRealAmount);

        $countWithoutRealAmount = $this->repository->count(['realAmount' => null]);
        $this->assertSame(1, $countWithoutRealAmount);
    }

    public function testSave(): void
    {
        $tradeOrder = $this->createTradeOrder();

        $bill = new TradeFundBill();
        $bill->setTradeOrder($tradeOrder);
        $bill->setFundChannel('TEST_CHANNEL');
        $bill->setAmount('75.50');
        $bill->setRealAmount('75.00');

        $this->repository->save($bill);

        $this->assertNotNull($bill->getId());

        $savedBill = $this->repository->find($bill->getId());
        $this->assertInstanceOf(TradeFundBill::class, $savedBill);
        $this->assertSame('TEST_CHANNEL', $savedBill->getFundChannel());
        $this->assertSame('75.50', $savedBill->getAmount());
        $this->assertSame('75.00', $savedBill->getRealAmount());
    }

    public function testRemove(): void
    {
        $tradeOrder = $this->createTradeOrder();

        $bill = new TradeFundBill();
        $bill->setTradeOrder($tradeOrder);
        $bill->setFundChannel('REMOVE_CHANNEL');
        $bill->setAmount('25.00');

        $this->repository->save($bill);
        $savedId = $bill->getId();

        $this->assertNotNull($this->repository->find($savedId));

        $this->repository->remove($bill);

        $this->assertNull($this->repository->find($savedId));
    }

    public function testFindByTradeOrderRelationShouldReturnCorrectResults(): void
    {
        $this->clearDatabase();

        $tradeOrder1 = $this->createTradeOrder();
        $tradeOrder2 = $this->createTradeOrder();

        $bill1 = new TradeFundBill();
        $bill1->setTradeOrder($tradeOrder1);
        $bill1->setFundChannel('ALIPAYACCOUNT');
        $bill1->setAmount('30.00');
        $this->repository->save($bill1);

        $bill2 = new TradeFundBill();
        $bill2->setTradeOrder($tradeOrder2);
        $bill2->setFundChannel('BANKCARD');
        $bill2->setAmount('40.00');
        $this->repository->save($bill2);

        $order1Bills = $this->repository->findBy(['tradeOrder' => $tradeOrder1]);
        $this->assertCount(1, $order1Bills);
        $this->assertSame('ALIPAYACCOUNT', $order1Bills[0]->getFundChannel());
    }

    private function createTradeOrder(): TradeOrder
    {
        $account = $this->createAccount();

        $order = new TradeOrder();
        $order->setAccount($account);
        $order->setOutTradeNo('test_trade_' . uniqid());
        $order->setTotalAmount('100.00');
        $order->setSubject('测试交易订单');

        $this->tradeOrderRepository->save($order);

        return $order;
    }

    private function createAccount(): Account
    {
        $account = new Account();
        $account->setName('测试账号' . uniqid());
        $account->setAppId('test_app_id_' . uniqid());
        $account->setValid(true);

        $this->accountRepository->save($account);

        return $account;
    }

    private function clearDatabase(): void
    {
        $entityManager = self::getEntityManager();
        $entityManager->createQuery('DELETE FROM ' . TradeFundBill::class)->execute();
        $entityManager->createQuery('DELETE FROM ' . TradeOrder::class)->execute();
        $entityManager->createQuery('DELETE FROM ' . Account::class)->execute();
    }

    public function testCountByAssociationTradeOrderShouldReturnCorrectNumber(): void
    {
        $this->clearDatabase();

        $tradeOrder1 = $this->createTradeOrder();
        $tradeOrder2 = $this->createTradeOrder();

        $bill1 = new TradeFundBill();
        $bill1->setTradeOrder($tradeOrder1);
        $bill1->setFundChannel('ALIPAYACCOUNT');
        $bill1->setAmount('30.00');
        $this->repository->save($bill1);

        $bill2 = new TradeFundBill();
        $bill2->setTradeOrder($tradeOrder1);
        $bill2->setFundChannel('BANKCARD');
        $bill2->setAmount('40.00');
        $this->repository->save($bill2);

        $bill3 = new TradeFundBill();
        $bill3->setTradeOrder($tradeOrder2);
        $bill3->setFundChannel('ALIPAYACCOUNT');
        $bill3->setAmount('50.00');
        $this->repository->save($bill3);

        $order1Count = $this->repository->count(['tradeOrder' => $tradeOrder1]);
        $this->assertSame(2, $order1Count);

        $order2Count = $this->repository->count(['tradeOrder' => $tradeOrder2]);
        $this->assertSame(1, $order2Count);
    }

    public function testFindByNullableFieldsShouldSupportIsNullQueries(): void
    {
        $this->clearDatabase();

        $tradeOrder1 = $this->createTradeOrder();
        $tradeOrder2 = $this->createTradeOrder();

        $billWithRealAmount = new TradeFundBill();
        $billWithRealAmount->setTradeOrder($tradeOrder1);
        $billWithRealAmount->setFundChannel('ALIPAYACCOUNT');
        $billWithRealAmount->setAmount('30.00');
        $billWithRealAmount->setRealAmount('29.50');
        $this->repository->save($billWithRealAmount);

        $billWithoutRealAmount = new TradeFundBill();
        $billWithoutRealAmount->setTradeOrder($tradeOrder2);
        $billWithoutRealAmount->setFundChannel('BANKCARD');
        $billWithoutRealAmount->setAmount('40.00');
        $this->repository->save($billWithoutRealAmount);

        $billsWithoutRealAmount = $this->repository->findBy(['realAmount' => null]);
        $this->assertCount(1, $billsWithoutRealAmount);
        $this->assertSame('BANKCARD', $billsWithoutRealAmount[0]->getFundChannel());
    }

    public function testFindOneByAssociationTradeOrderShouldReturnMatchingEntity(): void
    {
        $this->clearDatabase();

        $tradeOrder1 = $this->createTradeOrder();
        $tradeOrder2 = $this->createTradeOrder();

        $bill1 = new TradeFundBill();
        $bill1->setTradeOrder($tradeOrder1);
        $bill1->setFundChannel('ALIPAYACCOUNT');
        $bill1->setAmount('30.00');
        $this->repository->save($bill1);

        $bill2 = new TradeFundBill();
        $bill2->setTradeOrder($tradeOrder2);
        $bill2->setFundChannel('BANKCARD');
        $bill2->setAmount('40.00');
        $this->repository->save($bill2);

        $foundBill = $this->repository->findOneBy(['tradeOrder' => $tradeOrder1]);
        $this->assertInstanceOf(TradeFundBill::class, $foundBill);
        $this->assertSame('ALIPAYACCOUNT', $foundBill->getFundChannel());
        $foundTradeOrder = $foundBill->getTradeOrder();
        $this->assertInstanceOf(TradeOrder::class, $foundTradeOrder);
        $this->assertSame($tradeOrder1->getId(), $foundTradeOrder->getId());
    }

    public function testCreateWithRelationsQueryBuilder(): void
    {
        $this->clearDatabase();

        $tradeOrder1 = $this->createTradeOrder();
        $tradeOrder2 = $this->createTradeOrder();

        $bill1 = new TradeFundBill();
        $bill1->setTradeOrder($tradeOrder1);
        $bill1->setFundChannel('ALIPAYACCOUNT');
        $bill1->setAmount('100.00');
        $this->repository->save($bill1);

        $bill2 = new TradeFundBill();
        $bill2->setTradeOrder($tradeOrder2);
        $bill2->setFundChannel('BANKCARD');
        $bill2->setAmount('200.00');
        $this->repository->save($bill2);

        $queryBuilder = $this->repository->createWithRelationsQueryBuilder();
        $results = $queryBuilder->getQuery()->getResult();

        $this->assertCount(2, $results);
        $this->assertContainsOnlyInstancesOf(TradeFundBill::class, $results);

        foreach ($results as $bill) {
            $this->assertInstanceOf(TradeOrder::class, $bill->getTradeOrder());
            $this->assertNotNull($bill->getTradeOrder()->getId());
        }

        $bill1Result = array_values(array_filter($results, fn($b) => $b->getFundChannel() === 'ALIPAYACCOUNT'))[0];
        $this->assertInstanceOf(TradeFundBill::class, $bill1Result);
        $this->assertSame('100.00', $bill1Result->getAmount());
        $this->assertSame($tradeOrder1->getId(), $bill1Result->getTradeOrder()->getId());
    }

    public function testFindByFundBillNo(): void
    {
        $this->clearDatabase();

        $tradeOrder = $this->createTradeOrder();

        $bill = new TradeFundBill();
        $bill->setTradeOrder($tradeOrder);
        $bill->setFundChannel('ALIPAYACCOUNT');
        $bill->setAmount('150.00');
        $this->repository->save($bill);

        $this->expectException(\Doctrine\ORM\Persisters\Exception\UnrecognizedField::class);
        $this->repository->findByFundBillNo('nonexistent_bill_no');
    }

    /**
     * @return ServiceEntityRepository<TradeFundBill>
     */
    protected function getRepository(): ServiceEntityRepository
    {
        return $this->repository;
    }

    protected function createNewEntity(): object
    {
        $account = new Account();
        $account->setName('测试账号_' . uniqid());
        $account->setAppId('test_app_id_' . uniqid());
        $account->setValid(true);

        $tradeOrder = new TradeOrder();
        $tradeOrder->setAccount($account);
        $tradeOrder->setOutTradeNo('test_trade_' . uniqid());
        $tradeOrder->setTotalAmount('100.00');
        $tradeOrder->setSubject('测试交易订单');

        $entity = new TradeFundBill();
        $entity->setTradeOrder($tradeOrder);
        $entity->setFundChannel('ALIPAYACCOUNT');
        $entity->setAmount('50.00');
        $entity->setRealAmount('49.50');

        return $entity;
    }
}
