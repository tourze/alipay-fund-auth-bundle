<?php

namespace AlipayFundAuthBundle\Tests\Repository;

use AlipayFundAuthBundle\Entity\Account;
use AlipayFundAuthBundle\Entity\FundAuthOrder;
use AlipayFundAuthBundle\Entity\TradeOrder;
use AlipayFundAuthBundle\Enum\AliPayType;
use AlipayFundAuthBundle\Enum\FundAuthOrderStatus;
use AlipayFundAuthBundle\Repository\AccountRepository;
use AlipayFundAuthBundle\Repository\FundAuthOrderRepository;
use AlipayFundAuthBundle\Repository\TradeOrderRepository;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\PHPUnitSymfonyKernelTest\AbstractRepositoryTestCase;

/**
 * @internal
 */
#[CoversClass(TradeOrderRepository::class)]
#[RunTestsInSeparateProcesses]
final class TradeOrderRepositoryTest extends AbstractRepositoryTestCase
{
    private TradeOrderRepository $repository;

    private AccountRepository $accountRepository;

    private FundAuthOrderRepository $fundAuthOrderRepository;

    protected function onSetUp(): void
    {
        $this->repository = self::getService(TradeOrderRepository::class);
        $this->accountRepository = self::getService(AccountRepository::class);
        $this->fundAuthOrderRepository = self::getService(FundAuthOrderRepository::class);
    }

    public function testFindOneByWithOrderByShouldRespectOrderParameter(): void
    {
        $this->clearDatabase();

        $account = $this->createAccount();
        $fundAuthOrder = $this->createFundAuthOrder($account);

        $tradeOrder1 = new TradeOrder();
        $tradeOrder1->setAccount($account);
        $tradeOrder1->setFundAuthOrder($fundAuthOrder);
        $tradeOrder1->setOutTradeNo('order_z');
        $tradeOrder1->setSubject('Z订单');
        $tradeOrder1->setTotalAmount('300.00');
        $this->repository->save($tradeOrder1);

        $tradeOrder2 = new TradeOrder();
        $tradeOrder2->setAccount($account);
        $tradeOrder2->setFundAuthOrder($fundAuthOrder);
        $tradeOrder2->setOutTradeNo('order_a');
        $tradeOrder2->setSubject('A订单');
        $tradeOrder2->setTotalAmount('100.00');
        $this->repository->save($tradeOrder2);

        $firstTradeOrder = $this->repository->findOneBy(['account' => $account], ['subject' => 'ASC']);
        $this->assertInstanceOf(TradeOrder::class, $firstTradeOrder);
        $this->assertSame('A订单', $firstTradeOrder->getSubject());

        $lastTradeOrder = $this->repository->findOneBy(['account' => $account], ['subject' => 'DESC']);
        $this->assertInstanceOf(TradeOrder::class, $lastTradeOrder);
        $this->assertSame('Z订单', $lastTradeOrder->getSubject());
    }

    public function testFindByWithNullableFieldShouldSupportIsNullQueries(): void
    {
        $this->clearDatabase();

        $account = $this->createAccount();
        $fundAuthOrder = $this->createFundAuthOrder($account);

        $tradeOrderWithTradeNo = new TradeOrder();
        $tradeOrderWithTradeNo->setAccount($account);
        $tradeOrderWithTradeNo->setFundAuthOrder($fundAuthOrder);
        $tradeOrderWithTradeNo->setOutTradeNo('with_trade_no');
        $tradeOrderWithTradeNo->setSubject('有支付宝交易号订单');
        $tradeOrderWithTradeNo->setTotalAmount('100.00');
        $tradeOrderWithTradeNo->setTradeNo('alipay_trade_123');
        $this->repository->save($tradeOrderWithTradeNo);

        $tradeOrderWithoutTradeNo = new TradeOrder();
        $tradeOrderWithoutTradeNo->setAccount($account);
        $tradeOrderWithoutTradeNo->setFundAuthOrder($fundAuthOrder);
        $tradeOrderWithoutTradeNo->setOutTradeNo('without_trade_no');
        $tradeOrderWithoutTradeNo->setSubject('无支付宝交易号订单');
        $tradeOrderWithoutTradeNo->setTotalAmount('200.00');
        $this->repository->save($tradeOrderWithoutTradeNo);

        $tradeOrdersWithoutTradeNo = $this->repository->findBy(['tradeNo' => null]);
        $this->assertCount(1, $tradeOrdersWithoutTradeNo);
        $this->assertSame('无支付宝交易号订单', $tradeOrdersWithoutTradeNo[0]->getSubject());
    }

    public function testCountByAccountRelationShouldReturnCorrectCount(): void
    {
        $this->clearDatabase();

        $account1 = $this->createAccount();
        $account2 = $this->createAccount();
        $fundAuthOrder1 = $this->createFundAuthOrder($account1);
        $fundAuthOrder2 = $this->createFundAuthOrder($account2);

        $tradeOrder1 = new TradeOrder();
        $tradeOrder1->setAccount($account1);
        $tradeOrder1->setFundAuthOrder($fundAuthOrder1);
        $tradeOrder1->setOutTradeNo('account1_order1');
        $tradeOrder1->setSubject('账号1订单1');
        $tradeOrder1->setTotalAmount('100.00');
        $this->repository->save($tradeOrder1);

        $tradeOrder2 = new TradeOrder();
        $tradeOrder2->setAccount($account1);
        $tradeOrder2->setFundAuthOrder($fundAuthOrder1);
        $tradeOrder2->setOutTradeNo('account1_order2');
        $tradeOrder2->setSubject('账号1订单2');
        $tradeOrder2->setTotalAmount('200.00');
        $this->repository->save($tradeOrder2);

        $tradeOrder3 = new TradeOrder();
        $tradeOrder3->setAccount($account2);
        $tradeOrder3->setFundAuthOrder($fundAuthOrder2);
        $tradeOrder3->setOutTradeNo('account2_order1');
        $tradeOrder3->setSubject('账号2订单');
        $tradeOrder3->setTotalAmount('300.00');
        $this->repository->save($tradeOrder3);

        $account1Count = $this->repository->count(['account' => $account1]);
        $this->assertSame(2, $account1Count);

        $account2Count = $this->repository->count(['account' => $account2]);
        $this->assertSame(1, $account2Count);
    }

    public function testCountWithNullableFieldShouldSupportIsNullQueries(): void
    {
        $this->clearDatabase();

        $account = $this->createAccount();
        $fundAuthOrder = $this->createFundAuthOrder($account);

        $tradeOrderWithPayType = new TradeOrder();
        $tradeOrderWithPayType->setAccount($account);
        $tradeOrderWithPayType->setFundAuthOrder($fundAuthOrder);
        $tradeOrderWithPayType->setOutTradeNo('with_pay_type');
        $tradeOrderWithPayType->setSubject('有支付类型订单');
        $tradeOrderWithPayType->setTotalAmount('100.00');
        $tradeOrderWithPayType->setPayType(AliPayType::ALIPAY_AOPWAP);
        $this->repository->save($tradeOrderWithPayType);

        $tradeOrderWithoutPayType = new TradeOrder();
        $tradeOrderWithoutPayType->setAccount($account);
        $tradeOrderWithoutPayType->setFundAuthOrder($fundAuthOrder);
        $tradeOrderWithoutPayType->setOutTradeNo('without_pay_type');
        $tradeOrderWithoutPayType->setSubject('无支付类型订单');
        $tradeOrderWithoutPayType->setTotalAmount('200.00');
        $this->repository->save($tradeOrderWithoutPayType);

        $countWithoutPayType = $this->repository->count(['payType' => null]);
        $this->assertSame(1, $countWithoutPayType);

        $countWithoutBuyerUserId = $this->repository->count(['buyerUserId' => null]);
        $this->assertSame(2, $countWithoutBuyerUserId);
    }

    public function testFindByFundAuthOrderRelationShouldReturnCorrectResults(): void
    {
        $this->clearDatabase();

        $account1 = $this->createAccount();
        $account2 = $this->createAccount();
        $fundAuthOrder1 = $this->createFundAuthOrder($account1);
        $fundAuthOrder2 = $this->createFundAuthOrder($account2);

        $tradeOrder1 = new TradeOrder();
        $tradeOrder1->setAccount($account1);
        $tradeOrder1->setFundAuthOrder($fundAuthOrder1);
        $tradeOrder1->setOutTradeNo('fund1_order');
        $tradeOrder1->setSubject('预授权1的订单');
        $tradeOrder1->setTotalAmount('100.00');
        $this->repository->save($tradeOrder1);

        $tradeOrder2 = new TradeOrder();
        $tradeOrder2->setAccount($account2);
        $tradeOrder2->setFundAuthOrder($fundAuthOrder2);
        $tradeOrder2->setOutTradeNo('fund2_order');
        $tradeOrder2->setSubject('预授权2的订单');
        $tradeOrder2->setTotalAmount('200.00');
        $this->repository->save($tradeOrder2);

        $fundAuthOrder1TradeOrders = $this->repository->findBy(['fundAuthOrder' => $fundAuthOrder1]);
        $this->assertCount(1, $fundAuthOrder1TradeOrders);
        $this->assertSame('预授权1的订单', $fundAuthOrder1TradeOrders[0]->getSubject());
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

    public function testCountByAssociationAccountShouldReturnCorrectNumber(): void
    {
        $this->clearDatabase();

        $account1 = $this->createAccount();
        $account2 = $this->createAccount();
        $fundAuthOrder1 = $this->createFundAuthOrder($account1);
        $fundAuthOrder2 = $this->createFundAuthOrder($account2);

        for ($i = 1; $i <= 3; ++$i) {
            $tradeOrder = new TradeOrder();
            $tradeOrder->setAccount($account1);
            $tradeOrder->setFundAuthOrder($fundAuthOrder1);
            $tradeOrder->setOutTradeNo("account1_trade_{$i}");
            $tradeOrder->setSubject("账号1交易{$i}");
            $tradeOrder->setTotalAmount('100.00');
            $this->repository->save($tradeOrder);
        }

        for ($i = 1; $i <= 2; ++$i) {
            $tradeOrder = new TradeOrder();
            $tradeOrder->setAccount($account2);
            $tradeOrder->setFundAuthOrder($fundAuthOrder2);
            $tradeOrder->setOutTradeNo("account2_trade_{$i}");
            $tradeOrder->setSubject("账号2交易{$i}");
            $tradeOrder->setTotalAmount('200.00');
            $this->repository->save($tradeOrder);
        }

        $count = $this->repository->count(['account' => $account1]);
        $this->assertSame(3, $count);

        $count2 = $this->repository->count(['account' => $account2]);
        $this->assertSame(2, $count2);
    }

    public function testFindOneByAssociationAccountShouldReturnMatchingEntity(): void
    {
        $this->clearDatabase();

        $account1 = $this->createAccount();
        $account2 = $this->createAccount();
        $fundAuthOrder1 = $this->createFundAuthOrder($account1);
        $fundAuthOrder2 = $this->createFundAuthOrder($account2);

        $tradeOrder1 = new TradeOrder();
        $tradeOrder1->setAccount($account1);
        $tradeOrder1->setFundAuthOrder($fundAuthOrder1);
        $tradeOrder1->setOutTradeNo('account1_trade');
        $tradeOrder1->setSubject('账号1的交易');
        $tradeOrder1->setTotalAmount('100.00');
        $this->repository->save($tradeOrder1);

        $tradeOrder2 = new TradeOrder();
        $tradeOrder2->setAccount($account2);
        $tradeOrder2->setFundAuthOrder($fundAuthOrder2);
        $tradeOrder2->setOutTradeNo('account2_trade');
        $tradeOrder2->setSubject('账号2的交易');
        $tradeOrder2->setTotalAmount('200.00');
        $this->repository->save($tradeOrder2);

        $result = $this->repository->findOneBy(['account' => $account1]);
        $this->assertInstanceOf(TradeOrder::class, $result);
        $this->assertSame('账号1的交易', $result->getSubject());
        $resultAccount = $result->getAccount();
        $this->assertInstanceOf(Account::class, $resultAccount);
        $this->assertSame($account1->getId(), $resultAccount->getId());
    }

    private function clearDatabase(): void
    {
        $entityManager = self::getEntityManager();
        $entityManager->createQuery('DELETE FROM ' . TradeOrder::class)->execute();
        $entityManager->createQuery('DELETE FROM ' . FundAuthOrder::class)->execute();
        $entityManager->createQuery('DELETE FROM ' . Account::class)->execute();
    }

    /**
     * @return ServiceEntityRepository<TradeOrder>
     */
    protected function getRepository(): ServiceEntityRepository
    {
        return $this->repository;
    }

    protected function createNewEntity(): TradeOrder
    {
        $account = $this->createAccount();
        $fundAuthOrder = $this->createFundAuthOrder($account);

        $tradeOrder = new TradeOrder();
        $tradeOrder->setAccount($account);
        $tradeOrder->setFundAuthOrder($fundAuthOrder);
        $tradeOrder->setOutTradeNo('test_trade_order_' . uniqid());
        $tradeOrder->setSubject('测试交易订单');
        $tradeOrder->setTotalAmount('100.00');
        $tradeOrder->setProductCode('PRE_AUTH');
        $tradeOrder->setTradeStatus('NO_PAY');
        $tradeOrder->setPayType(AliPayType::ALIPAY_AOPWAP);

        return $tradeOrder;
    }
}
