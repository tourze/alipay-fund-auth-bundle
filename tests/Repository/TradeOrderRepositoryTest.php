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

    public function testCountByTradeStatus(): void
    {
        $this->clearDatabase();

        $account = $this->createAccount();
        $fundAuthOrder = $this->createFundAuthOrder($account);

        // 创建3个已支付订单
        for ($i = 1; $i <= 3; ++$i) {
            $tradeOrder = new TradeOrder();
            $tradeOrder->setAccount($account);
            $tradeOrder->setFundAuthOrder($fundAuthOrder);
            $tradeOrder->setOutTradeNo("paid_order_{$i}");
            $tradeOrder->setSubject("已支付订单{$i}");
            $tradeOrder->setTotalAmount('100.00');
            $tradeOrder->setTradeStatus('TRADE_SUCCESS');
            $this->repository->save($tradeOrder);
        }

        // 创建2个未支付订单
        for ($i = 1; $i <= 2; ++$i) {
            $tradeOrder = new TradeOrder();
            $tradeOrder->setAccount($account);
            $tradeOrder->setFundAuthOrder($fundAuthOrder);
            $tradeOrder->setOutTradeNo("unpaid_order_{$i}");
            $tradeOrder->setSubject("未支付订单{$i}");
            $tradeOrder->setTotalAmount('200.00');
            $tradeOrder->setTradeStatus('NO_PAY');
            $this->repository->save($tradeOrder);
        }

        $paidCount = $this->repository->countByTradeStatus('TRADE_SUCCESS');
        $this->assertSame(3, $paidCount);

        $unpaidCount = $this->repository->countByTradeStatus('NO_PAY');
        $this->assertSame(2, $unpaidCount);

        $nonExistentCount = $this->repository->countByTradeStatus('TRADE_CLOSED');
        $this->assertSame(0, $nonExistentCount);
    }

    public function testCreateWithRelationsQueryBuilder(): void
    {
        $this->clearDatabase();

        $account = $this->createAccount();
        $fundAuthOrder = $this->createFundAuthOrder($account);

        $tradeOrder = new TradeOrder();
        $tradeOrder->setAccount($account);
        $tradeOrder->setFundAuthOrder($fundAuthOrder);
        $tradeOrder->setOutTradeNo('test_order_' . uniqid());
        $tradeOrder->setSubject('测试订单');
        $tradeOrder->setTotalAmount('100.00');
        $tradeOrder->setTradeStatus('NO_PAY');
        $this->repository->save($tradeOrder);

        $queryBuilder = $this->repository->createWithRelationsQueryBuilder();
        $this->assertInstanceOf(\Doctrine\ORM\QueryBuilder::class, $queryBuilder);

        $result = $queryBuilder->getQuery()->getResult();
        $this->assertCount(1, $result);
        $this->assertInstanceOf(TradeOrder::class, $result[0]);

        // 验证关联数据已加载，不会触发额外查询
        $loadedOrder = $result[0];
        $this->assertInstanceOf(Account::class, $loadedOrder->getAccount());
        $this->assertInstanceOf(FundAuthOrder::class, $loadedOrder->getFundAuthOrder());
    }

    public function testFindByAccount(): void
    {
        $this->clearDatabase();

        $account1 = $this->createAccount();
        $account2 = $this->createAccount();
        $fundAuthOrder1 = $this->createFundAuthOrder($account1);
        $fundAuthOrder2 = $this->createFundAuthOrder($account2);

        // 为账户1创建3个订单
        for ($i = 1; $i <= 3; ++$i) {
            $tradeOrder = new TradeOrder();
            $tradeOrder->setAccount($account1);
            $tradeOrder->setFundAuthOrder($fundAuthOrder1);
            $tradeOrder->setOutTradeNo("account1_order_{$i}");
            $tradeOrder->setSubject("账户1订单{$i}");
            $tradeOrder->setTotalAmount('100.00');
            $this->repository->save($tradeOrder);
        }

        // 为账户2创建2个订单
        for ($i = 1; $i <= 2; ++$i) {
            $tradeOrder = new TradeOrder();
            $tradeOrder->setAccount($account2);
            $tradeOrder->setFundAuthOrder($fundAuthOrder2);
            $tradeOrder->setOutTradeNo("account2_order_{$i}");
            $tradeOrder->setSubject("账户2订单{$i}");
            $tradeOrder->setTotalAmount('200.00');
            $this->repository->save($tradeOrder);
        }

        $account1Orders = $this->repository->findByAccount($account1);
        $this->assertCount(3, $account1Orders);
        foreach ($account1Orders as $order) {
            $this->assertInstanceOf(TradeOrder::class, $order);
            $this->assertSame($account1->getId(), $order->getAccount()->getId());
        }

        $account2Orders = $this->repository->findByAccount($account2);
        $this->assertCount(2, $account2Orders);
    }

    public function testFindByBuyerUserId(): void
    {
        $this->clearDatabase();

        $account = $this->createAccount();
        $fundAuthOrder = $this->createFundAuthOrder($account);

        // 创建buyer1的订单
        $tradeOrder1 = new TradeOrder();
        $tradeOrder1->setAccount($account);
        $tradeOrder1->setFundAuthOrder($fundAuthOrder);
        $tradeOrder1->setOutTradeNo('buyer1_order1');
        $tradeOrder1->setSubject('买家1订单1');
        $tradeOrder1->setTotalAmount('100.00');
        $tradeOrder1->setBuyerUserId('buyer_user_123');
        $this->repository->save($tradeOrder1);

        $tradeOrder2 = new TradeOrder();
        $tradeOrder2->setAccount($account);
        $tradeOrder2->setFundAuthOrder($fundAuthOrder);
        $tradeOrder2->setOutTradeNo('buyer1_order2');
        $tradeOrder2->setSubject('买家1订单2');
        $tradeOrder2->setTotalAmount('200.00');
        $tradeOrder2->setBuyerUserId('buyer_user_123');
        $this->repository->save($tradeOrder2);

        // 创建buyer2的订单
        $tradeOrder3 = new TradeOrder();
        $tradeOrder3->setAccount($account);
        $tradeOrder3->setFundAuthOrder($fundAuthOrder);
        $tradeOrder3->setOutTradeNo('buyer2_order1');
        $tradeOrder3->setSubject('买家2订单');
        $tradeOrder3->setTotalAmount('300.00');
        $tradeOrder3->setBuyerUserId('buyer_user_456');
        $this->repository->save($tradeOrder3);

        $buyer1Orders = $this->repository->findByBuyerUserId('buyer_user_123');
        $this->assertCount(2, $buyer1Orders);
        foreach ($buyer1Orders as $order) {
            $this->assertSame('buyer_user_123', $order->getBuyerUserId());
        }

        $buyer2Orders = $this->repository->findByBuyerUserId('buyer_user_456');
        $this->assertCount(1, $buyer2Orders);
        $this->assertSame('buyer_user_456', $buyer2Orders[0]->getBuyerUserId());
    }

    public function testFindByDateRange(): void
    {
        $this->clearDatabase();

        $account = $this->createAccount();
        $fundAuthOrder = $this->createFundAuthOrder($account);

        // 创建一个旧订单
        $oldOrder = new TradeOrder();
        $oldOrder->setAccount($account);
        $oldOrder->setFundAuthOrder($fundAuthOrder);
        $oldOrder->setOutTradeNo('old_order');
        $oldOrder->setSubject('旧订单');
        $oldOrder->setTotalAmount('100.00');
        $this->repository->save($oldOrder);

        // 等待以确保时间戳不同
        sleep(1);
        $startTime = new \DateTimeImmutable();

        // 创建范围内的订单
        $recentOrder1 = new TradeOrder();
        $recentOrder1->setAccount($account);
        $recentOrder1->setFundAuthOrder($fundAuthOrder);
        $recentOrder1->setOutTradeNo('recent_order1');
        $recentOrder1->setSubject('最近订单1');
        $recentOrder1->setTotalAmount('200.00');
        $this->repository->save($recentOrder1);

        $recentOrder2 = new TradeOrder();
        $recentOrder2->setAccount($account);
        $recentOrder2->setFundAuthOrder($fundAuthOrder);
        $recentOrder2->setOutTradeNo('recent_order2');
        $recentOrder2->setSubject('最近订单2');
        $recentOrder2->setTotalAmount('300.00');
        $this->repository->save($recentOrder2);

        $endTime = new \DateTimeImmutable('+1 day');

        $ordersInRange = $this->repository->findByDateRange($startTime, $endTime);
        $this->assertCount(2, $ordersInRange);

        $subjects = array_map(fn (TradeOrder $order) => $order->getSubject(), $ordersInRange);
        $this->assertContains('最近订单1', $subjects);
        $this->assertContains('最近订单2', $subjects);
        $this->assertNotContains('旧订单', $subjects);
    }

    public function testFindByOutTradeNo(): void
    {
        $this->clearDatabase();

        $account = $this->createAccount();
        $fundAuthOrder = $this->createFundAuthOrder($account);

        $tradeOrder = new TradeOrder();
        $tradeOrder->setAccount($account);
        $tradeOrder->setFundAuthOrder($fundAuthOrder);
        $tradeOrder->setOutTradeNo('unique_out_trade_no_123');
        $tradeOrder->setSubject('测试订单');
        $tradeOrder->setTotalAmount('100.00');
        $this->repository->save($tradeOrder);

        $foundOrder = $this->repository->findByOutTradeNo('unique_out_trade_no_123');
        $this->assertInstanceOf(TradeOrder::class, $foundOrder);
        $this->assertSame('unique_out_trade_no_123', $foundOrder->getOutTradeNo());
        $this->assertSame('测试订单', $foundOrder->getSubject());

        $notFoundOrder = $this->repository->findByOutTradeNo('non_existent_trade_no');
        $this->assertNull($notFoundOrder);
    }

    public function testFindBySubjectKeyword(): void
    {
        $this->clearDatabase();

        $account = $this->createAccount();
        $fundAuthOrder = $this->createFundAuthOrder($account);

        $tradeOrder1 = new TradeOrder();
        $tradeOrder1->setAccount($account);
        $tradeOrder1->setFundAuthOrder($fundAuthOrder);
        $tradeOrder1->setOutTradeNo('order1');
        $tradeOrder1->setSubject('手机充值订单');
        $tradeOrder1->setTotalAmount('100.00');
        $this->repository->save($tradeOrder1);

        $tradeOrder2 = new TradeOrder();
        $tradeOrder2->setAccount($account);
        $tradeOrder2->setFundAuthOrder($fundAuthOrder);
        $tradeOrder2->setOutTradeNo('order2');
        $tradeOrder2->setSubject('话费充值服务');
        $tradeOrder2->setTotalAmount('200.00');
        $this->repository->save($tradeOrder2);

        $tradeOrder3 = new TradeOrder();
        $tradeOrder3->setAccount($account);
        $tradeOrder3->setFundAuthOrder($fundAuthOrder);
        $tradeOrder3->setOutTradeNo('order3');
        $tradeOrder3->setSubject('商品购买订单');
        $tradeOrder3->setTotalAmount('300.00');
        $this->repository->save($tradeOrder3);

        $chargeOrders = $this->repository->findBySubjectKeyword('充值');
        $this->assertCount(2, $chargeOrders);
        $subjects = array_map(fn (TradeOrder $order) => $order->getSubject(), $chargeOrders);
        $this->assertContains('手机充值订单', $subjects);
        $this->assertContains('话费充值服务', $subjects);

        $phoneOrders = $this->repository->findBySubjectKeyword('手机');
        $this->assertCount(1, $phoneOrders);
        $this->assertSame('手机充值订单', $phoneOrders[0]->getSubject());

        $noMatchOrders = $this->repository->findBySubjectKeyword('不存在的关键词');
        $this->assertCount(0, $noMatchOrders);
    }

    public function testFindByTradeNo(): void
    {
        $this->clearDatabase();

        $account = $this->createAccount();
        $fundAuthOrder = $this->createFundAuthOrder($account);

        $tradeOrder1 = new TradeOrder();
        $tradeOrder1->setAccount($account);
        $tradeOrder1->setFundAuthOrder($fundAuthOrder);
        $tradeOrder1->setOutTradeNo('order_with_trade_no');
        $tradeOrder1->setSubject('有支付宝交易号的订单');
        $tradeOrder1->setTotalAmount('100.00');
        $tradeOrder1->setTradeNo('alipay_trade_number_abc123');
        $this->repository->save($tradeOrder1);

        $tradeOrder2 = new TradeOrder();
        $tradeOrder2->setAccount($account);
        $tradeOrder2->setFundAuthOrder($fundAuthOrder);
        $tradeOrder2->setOutTradeNo('order_without_trade_no');
        $tradeOrder2->setSubject('无支付宝交易号的订单');
        $tradeOrder2->setTotalAmount('200.00');
        $this->repository->save($tradeOrder2);

        $foundOrder = $this->repository->findByTradeNo('alipay_trade_number_abc123');
        $this->assertInstanceOf(TradeOrder::class, $foundOrder);
        $this->assertSame('alipay_trade_number_abc123', $foundOrder->getTradeNo());
        $this->assertSame('有支付宝交易号的订单', $foundOrder->getSubject());

        $notFoundOrder = $this->repository->findByTradeNo('non_existent_trade_no');
        $this->assertNull($notFoundOrder);
    }

    public function testFindByTradeStatus(): void
    {
        $this->clearDatabase();

        $account = $this->createAccount();
        $fundAuthOrder = $this->createFundAuthOrder($account);

        // 创建不同状态的订单
        $successOrder1 = new TradeOrder();
        $successOrder1->setAccount($account);
        $successOrder1->setFundAuthOrder($fundAuthOrder);
        $successOrder1->setOutTradeNo('success_order1');
        $successOrder1->setSubject('成功订单1');
        $successOrder1->setTotalAmount('100.00');
        $successOrder1->setTradeStatus('TRADE_SUCCESS');
        $this->repository->save($successOrder1);

        $successOrder2 = new TradeOrder();
        $successOrder2->setAccount($account);
        $successOrder2->setFundAuthOrder($fundAuthOrder);
        $successOrder2->setOutTradeNo('success_order2');
        $successOrder2->setSubject('成功订单2');
        $successOrder2->setTotalAmount('200.00');
        $successOrder2->setTradeStatus('TRADE_SUCCESS');
        $this->repository->save($successOrder2);

        $noPayOrder = new TradeOrder();
        $noPayOrder->setAccount($account);
        $noPayOrder->setFundAuthOrder($fundAuthOrder);
        $noPayOrder->setOutTradeNo('no_pay_order');
        $noPayOrder->setSubject('未支付订单');
        $noPayOrder->setTotalAmount('300.00');
        $noPayOrder->setTradeStatus('NO_PAY');
        $this->repository->save($noPayOrder);

        $successOrders = $this->repository->findByTradeStatus('TRADE_SUCCESS');
        $this->assertCount(2, $successOrders);
        foreach ($successOrders as $order) {
            $this->assertSame('TRADE_SUCCESS', $order->getTradeStatus());
        }

        $noPayOrders = $this->repository->findByTradeStatus('NO_PAY');
        $this->assertCount(1, $noPayOrders);
        $this->assertSame('NO_PAY', $noPayOrders[0]->getTradeStatus());

        $closedOrders = $this->repository->findByTradeStatus('TRADE_CLOSED');
        $this->assertCount(0, $closedOrders);
    }
}
