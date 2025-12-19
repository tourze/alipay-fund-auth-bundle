<?php

namespace AlipayFundAuthBundle\Tests\Repository;

use AlipayFundAuthBundle\Entity\Account;
use AlipayFundAuthBundle\Entity\FundAuthOrder;
use AlipayFundAuthBundle\Enum\FundAuthOrderStatus;
use AlipayFundAuthBundle\Repository\AccountRepository;
use AlipayFundAuthBundle\Repository\FundAuthOrderRepository;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\PHPUnitSymfonyKernelTest\AbstractRepositoryTestCase;

/**
 * @internal
 */
#[CoversClass(FundAuthOrderRepository::class)]
#[RunTestsInSeparateProcesses]
final class FundAuthOrderRepositoryTest extends AbstractRepositoryTestCase
{
    private FundAuthOrderRepository $repository;

    private AccountRepository $accountRepository;

    protected function onSetUp(): void
    {
        $this->repository = self::getService(FundAuthOrderRepository::class);
        $this->accountRepository = self::getService(AccountRepository::class);
    }

    public function testSave(): void
    {
        $account = $this->createAccount();

        $order = new FundAuthOrder();
        $order->setAccount($account);
        $order->setOutOrderNo('save_test_order');
        $order->setOutRequestNo('save_test_request');
        $order->setOrderTitle('保存测试订单');
        $order->setAmount('150.50');
        $order->setStatus(FundAuthOrderStatus::SUCCESS);
        $order->setPayeeUserId('payee_123');
        $order->setPayeeLogonId('payee@example.com');
        $order->setAuthNo('auth_123456');
        $order->setOperationId('op_123456');

        $this->repository->save($order);

        $this->assertNotNull($order->getId());

        $savedOrder = $this->repository->find($order->getId());
        $this->assertInstanceOf(FundAuthOrder::class, $savedOrder);
        $this->assertSame('save_test_order', $savedOrder->getOutOrderNo());
        $this->assertSame('保存测试订单', $savedOrder->getOrderTitle());
        $this->assertSame('150.50', $savedOrder->getAmount());
        $this->assertSame(FundAuthOrderStatus::SUCCESS, $savedOrder->getStatus());
        $this->assertSame('payee_123', $savedOrder->getPayeeUserId());
        $this->assertSame('payee@example.com', $savedOrder->getPayeeLogonId());
        $this->assertSame('auth_123456', $savedOrder->getAuthNo());
        $this->assertSame('op_123456', $savedOrder->getOperationId());
    }

    public function testRemove(): void
    {
        $account = $this->createAccount();

        $order = new FundAuthOrder();
        $order->setAccount($account);
        $order->setOutOrderNo('remove_test_order');
        $order->setOutRequestNo('remove_test_request');
        $order->setOrderTitle('删除测试订单');
        $order->setAmount('75.25');

        $this->repository->save($order);
        $savedId = $order->getId();

        $this->assertNotNull($this->repository->find($savedId));

        $this->repository->remove($order);

        $this->assertNull($this->repository->find($savedId));
    }

    public function testFindByAccountShouldReturnRelatedEntities(): void
    {
        $this->clearDatabase();

        $account1 = $this->createAccount('测试账号1', 'app_id_1');
        $account2 = $this->createAccount('测试账号2', 'app_id_2');

        $order1 = new FundAuthOrder();
        $order1->setAccount($account1);
        $order1->setOutOrderNo('account1_order');
        $order1->setOutRequestNo('account1_request');
        $order1->setOrderTitle('账号1订单');
        $order1->setAmount('100.00');
        $this->repository->save($order1);

        $order2 = new FundAuthOrder();
        $order2->setAccount($account2);
        $order2->setOutOrderNo('account2_order');
        $order2->setOutRequestNo('account2_request');
        $order2->setOrderTitle('账号2订单');
        $order2->setAmount('200.00');
        $this->repository->save($order2);

        $account1Orders = $this->repository->findBy(['account' => $account1]);
        $this->assertCount(1, $account1Orders);
        $this->assertSame('账号1订单', $account1Orders[0]->getOrderTitle());

        $account2Orders = $this->repository->findBy(['account' => $account2]);
        $this->assertCount(1, $account2Orders);
        $this->assertSame('账号2订单', $account2Orders[0]->getOrderTitle());
    }

    public function testFindByStatusShouldReturnCorrectResults(): void
    {
        $this->clearDatabase();

        $account = $this->createAccount();

        $initOrder = new FundAuthOrder();
        $initOrder->setAccount($account);
        $initOrder->setOutOrderNo('init_order');
        $initOrder->setOutRequestNo('init_request');
        $initOrder->setOrderTitle('初始状态订单');
        $initOrder->setAmount('100.00');
        $initOrder->setStatus(FundAuthOrderStatus::INIT);
        $this->repository->save($initOrder);

        $successOrder = new FundAuthOrder();
        $successOrder->setAccount($account);
        $successOrder->setOutOrderNo('success_order');
        $successOrder->setOutRequestNo('success_request');
        $successOrder->setOrderTitle('成功状态订单');
        $successOrder->setAmount('200.00');
        $successOrder->setStatus(FundAuthOrderStatus::SUCCESS);
        $this->repository->save($successOrder);

        $initOrders = $this->repository->findBy(['status' => FundAuthOrderStatus::INIT]);
        $this->assertCount(1, $initOrders);
        $this->assertSame('初始状态订单', $initOrders[0]->getOrderTitle());

        $successOrders = $this->repository->findBy(['status' => FundAuthOrderStatus::SUCCESS]);
        $this->assertCount(1, $successOrders);
        $this->assertSame('成功状态订单', $successOrders[0]->getOrderTitle());
    }

    public function testFindByNullableFieldsShouldReturnCorrectResults(): void
    {
        $this->clearDatabase();

        $account = $this->createAccount();

        $orderWithAuthNo = new FundAuthOrder();
        $orderWithAuthNo->setAccount($account);
        $orderWithAuthNo->setOutOrderNo('with_auth_order');
        $orderWithAuthNo->setOutRequestNo('with_auth_request');
        $orderWithAuthNo->setOrderTitle('有授权号订单');
        $orderWithAuthNo->setAmount('100.00');
        $orderWithAuthNo->setAuthNo('auth_123');
        $this->repository->save($orderWithAuthNo);

        $orderWithoutAuthNo = new FundAuthOrder();
        $orderWithoutAuthNo->setAccount($account);
        $orderWithoutAuthNo->setOutOrderNo('without_auth_order');
        $orderWithoutAuthNo->setOutRequestNo('without_auth_request');
        $orderWithoutAuthNo->setOrderTitle('无授权号订单');
        $orderWithoutAuthNo->setAmount('200.00');
        $this->repository->save($orderWithoutAuthNo);

        $ordersWithAuthNo = $this->repository->findBy(['authNo' => 'auth_123']);
        $this->assertCount(1, $ordersWithAuthNo);
        $this->assertSame('有授权号订单', $ordersWithAuthNo[0]->getOrderTitle());

        $ordersWithoutAuthNo = $this->repository->findBy(['authNo' => null]);
        $this->assertCount(1, $ordersWithoutAuthNo);
        $this->assertSame('无授权号订单', $ordersWithoutAuthNo[0]->getOrderTitle());
    }

    public function testCountByStatusShouldReturnCorrectCount(): void
    {
        $this->clearDatabase();

        $account = $this->createAccount();

        $initOrder1 = new FundAuthOrder();
        $initOrder1->setAccount($account);
        $initOrder1->setOutOrderNo('init_order_1');
        $initOrder1->setOutRequestNo('init_request_1');
        $initOrder1->setOrderTitle('初始订单1');
        $initOrder1->setAmount('100.00');
        $initOrder1->setStatus(FundAuthOrderStatus::INIT);
        $this->repository->save($initOrder1);

        $initOrder2 = new FundAuthOrder();
        $initOrder2->setAccount($account);
        $initOrder2->setOutOrderNo('init_order_2');
        $initOrder2->setOutRequestNo('init_request_2');
        $initOrder2->setOrderTitle('初始订单2');
        $initOrder2->setAmount('150.00');
        $initOrder2->setStatus(FundAuthOrderStatus::INIT);
        $this->repository->save($initOrder2);

        $successOrder = new FundAuthOrder();
        $successOrder->setAccount($account);
        $successOrder->setOutOrderNo('success_order');
        $successOrder->setOutRequestNo('success_request');
        $successOrder->setOrderTitle('成功订单');
        $successOrder->setAmount('200.00');
        $successOrder->setStatus(FundAuthOrderStatus::SUCCESS);
        $this->repository->save($successOrder);

        $initCount = $this->repository->count(['status' => FundAuthOrderStatus::INIT]);
        $this->assertSame(2, $initCount);

        $successCount = $this->repository->count(['status' => FundAuthOrderStatus::SUCCESS]);
        $this->assertSame(1, $successCount);

        $closedCount = $this->repository->count(['status' => FundAuthOrderStatus::CLOSED]);
        $this->assertSame(0, $closedCount);
    }

    public function testFindWithOrderingShouldReturnOrderedResults(): void
    {
        $this->clearDatabase();

        $account = $this->createAccount();

        $order1 = new FundAuthOrder();
        $order1->setAccount($account);
        $order1->setOutOrderNo('z_order');
        $order1->setOutRequestNo('z_request');
        $order1->setOrderTitle('Z订单');
        $order1->setAmount('300.00');
        $this->repository->save($order1);

        $order2 = new FundAuthOrder();
        $order2->setAccount($account);
        $order2->setOutOrderNo('a_order');
        $order2->setOutRequestNo('a_request');
        $order2->setOrderTitle('A订单');
        $order2->setAmount('100.00');
        $this->repository->save($order2);

        $orderedByTitle = $this->repository->findBy([], ['orderTitle' => 'ASC']);
        $this->assertCount(2, $orderedByTitle);
        $this->assertSame('A订单', $orderedByTitle[0]->getOrderTitle());
        $this->assertSame('Z订单', $orderedByTitle[1]->getOrderTitle());

        $orderedByAmount = $this->repository->findBy([], ['amount' => 'DESC']);
        $this->assertCount(2, $orderedByAmount);
        $this->assertSame('300.00', $orderedByAmount[0]->getAmount());
        $this->assertSame('100.00', $orderedByAmount[1]->getAmount());
    }

    public function testFindOneByWithOrderByShouldRespectOrderParameter(): void
    {
        $this->clearDatabase();

        $account = $this->createAccount();

        $order1 = new FundAuthOrder();
        $order1->setAccount($account);
        $order1->setOutOrderNo('order_z');
        $order1->setOutRequestNo('request_z');
        $order1->setOrderTitle('Z订单');
        $order1->setAmount('300.00');
        $order1->setStatus(FundAuthOrderStatus::INIT);
        $this->repository->save($order1);

        $order2 = new FundAuthOrder();
        $order2->setAccount($account);
        $order2->setOutOrderNo('order_a');
        $order2->setOutRequestNo('request_a');
        $order2->setOrderTitle('A订单');
        $order2->setAmount('100.00');
        $order2->setStatus(FundAuthOrderStatus::INIT);
        $this->repository->save($order2);

        $firstOrder = $this->repository->findOneBy(['status' => FundAuthOrderStatus::INIT], ['orderTitle' => 'ASC']);
        $this->assertInstanceOf(FundAuthOrder::class, $firstOrder);
        $this->assertSame('A订单', $firstOrder->getOrderTitle());

        $lastOrder = $this->repository->findOneBy(['status' => FundAuthOrderStatus::INIT], ['orderTitle' => 'DESC']);
        $this->assertInstanceOf(FundAuthOrder::class, $lastOrder);
        $this->assertSame('Z订单', $lastOrder->getOrderTitle());
    }

    public function testFindByWithNullableFieldShouldSupportIsNullQueries(): void
    {
        $this->clearDatabase();

        $account = $this->createAccount();

        $orderWithPayeeUserId = new FundAuthOrder();
        $orderWithPayeeUserId->setAccount($account);
        $orderWithPayeeUserId->setOutOrderNo('with_payee_order');
        $orderWithPayeeUserId->setOutRequestNo('with_payee_request');
        $orderWithPayeeUserId->setOrderTitle('有收款方用户号订单');
        $orderWithPayeeUserId->setAmount('100.00');
        $orderWithPayeeUserId->setPayeeUserId('payee_123');
        $this->repository->save($orderWithPayeeUserId);

        $orderWithoutPayeeUserId = new FundAuthOrder();
        $orderWithoutPayeeUserId->setAccount($account);
        $orderWithoutPayeeUserId->setOutOrderNo('without_payee_order');
        $orderWithoutPayeeUserId->setOutRequestNo('without_payee_request');
        $orderWithoutPayeeUserId->setOrderTitle('无收款方用户号订单');
        $orderWithoutPayeeUserId->setAmount('200.00');
        $this->repository->save($orderWithoutPayeeUserId);

        $ordersWithoutPayeeUserId = $this->repository->findBy(['payeeUserId' => null]);
        $this->assertCount(1, $ordersWithoutPayeeUserId);
        $this->assertSame('无收款方用户号订单', $ordersWithoutPayeeUserId[0]->getOrderTitle());
    }

    public function testCountWithNullableFieldShouldSupportIsNullQueries(): void
    {
        $this->clearDatabase();

        $account = $this->createAccount();

        $orderWithPayeeLogonId = new FundAuthOrder();
        $orderWithPayeeLogonId->setAccount($account);
        $orderWithPayeeLogonId->setOutOrderNo('with_logon_order');
        $orderWithPayeeLogonId->setOutRequestNo('with_logon_request');
        $orderWithPayeeLogonId->setOrderTitle('有收款账号订单');
        $orderWithPayeeLogonId->setAmount('100.00');
        $orderWithPayeeLogonId->setPayeeLogonId('payee@example.com');
        $this->repository->save($orderWithPayeeLogonId);

        $orderWithoutPayeeLogonId = new FundAuthOrder();
        $orderWithoutPayeeLogonId->setAccount($account);
        $orderWithoutPayeeLogonId->setOutOrderNo('without_logon_order');
        $orderWithoutPayeeLogonId->setOutRequestNo('without_logon_request');
        $orderWithoutPayeeLogonId->setOrderTitle('无收款账号订单');
        $orderWithoutPayeeLogonId->setAmount('200.00');
        $this->repository->save($orderWithoutPayeeLogonId);

        $countWithoutLogonId = $this->repository->count(['payeeLogonId' => null]);
        $this->assertSame(1, $countWithoutLogonId);

        $countWithoutOperationId = $this->repository->count(['operationId' => null]);
        $this->assertSame(2, $countWithoutOperationId);
    }

    public function testFindByAccountRelationShouldReturnCorrectResults(): void
    {
        $this->clearDatabase();

        $account1 = $this->createAccount('账号1', 'app_id_1');
        $account2 = $this->createAccount('账号2', 'app_id_2');

        $order1 = new FundAuthOrder();
        $order1->setAccount($account1);
        $order1->setOutOrderNo('account1_order');
        $order1->setOutRequestNo('account1_request');
        $order1->setOrderTitle('账号1的订单');
        $order1->setAmount('100.00');
        $this->repository->save($order1);

        $order2 = new FundAuthOrder();
        $order2->setAccount($account2);
        $order2->setOutOrderNo('account2_order');
        $order2->setOutRequestNo('account2_request');
        $order2->setOrderTitle('账号2的订单');
        $order2->setAmount('200.00');
        $this->repository->save($order2);

        $account1Orders = $this->repository->findBy(['account' => $account1]);
        $this->assertCount(1, $account1Orders);
        $this->assertSame('账号1的订单', $account1Orders[0]->getOrderTitle());
    }

    public function testCountByAccountRelationShouldReturnCorrectCount(): void
    {
        $this->clearDatabase();

        $account1 = $this->createAccount('账号1', 'app_id_1');
        $account2 = $this->createAccount('账号2', 'app_id_2');

        $order1 = new FundAuthOrder();
        $order1->setAccount($account1);
        $order1->setOutOrderNo('account1_order_1');
        $order1->setOutRequestNo('account1_request_1');
        $order1->setOrderTitle('账号1订单1');
        $order1->setAmount('100.00');
        $this->repository->save($order1);

        $order2 = new FundAuthOrder();
        $order2->setAccount($account1);
        $order2->setOutOrderNo('account1_order_2');
        $order2->setOutRequestNo('account1_request_2');
        $order2->setOrderTitle('账号1订单2');
        $order2->setAmount('150.00');
        $this->repository->save($order2);

        $order3 = new FundAuthOrder();
        $order3->setAccount($account2);
        $order3->setOutOrderNo('account2_order');
        $order3->setOutRequestNo('account2_request');
        $order3->setOrderTitle('账号2订单');
        $order3->setAmount('200.00');
        $this->repository->save($order3);

        $account1Count = $this->repository->count(['account' => $account1]);
        $this->assertSame(2, $account1Count);

        $account2Count = $this->repository->count(['account' => $account2]);
        $this->assertSame(1, $account2Count);
    }

    public function testFindByWithPayerUserIdNullFieldShouldSupportIsNullQueries(): void
    {
        $this->clearDatabase();

        $account = $this->createAccount();

        $orderWithPayerUserId = new FundAuthOrder();
        $orderWithPayerUserId->setAccount($account);
        $orderWithPayerUserId->setOutOrderNo('with_payer_order');
        $orderWithPayerUserId->setOutRequestNo('with_payer_request');
        $orderWithPayerUserId->setOrderTitle('有付款方用户号订单');
        $orderWithPayerUserId->setAmount('100.00');
        $orderWithPayerUserId->setPayerUserId('payer_123');
        $this->repository->save($orderWithPayerUserId);

        $orderWithoutPayerUserId = new FundAuthOrder();
        $orderWithoutPayerUserId->setAccount($account);
        $orderWithoutPayerUserId->setOutOrderNo('without_payer_order');
        $orderWithoutPayerUserId->setOutRequestNo('without_payer_request');
        $orderWithoutPayerUserId->setOrderTitle('无付款方用户号订单');
        $orderWithoutPayerUserId->setAmount('200.00');
        $this->repository->save($orderWithoutPayerUserId);

        $ordersWithoutPayerUserId = $this->repository->findBy(['payerUserId' => null]);
        $this->assertCount(1, $ordersWithoutPayerUserId);
        $this->assertSame('无付款方用户号订单', $ordersWithoutPayerUserId[0]->getOrderTitle());
    }

    public function testCountWithPayerUserIdNullFieldShouldSupportIsNullQueries(): void
    {
        $this->clearDatabase();

        $account = $this->createAccount();

        $orderWithPayerUserId = new FundAuthOrder();
        $orderWithPayerUserId->setAccount($account);
        $orderWithPayerUserId->setOutOrderNo('with_payer_order');
        $orderWithPayerUserId->setOutRequestNo('with_payer_request');
        $orderWithPayerUserId->setOrderTitle('有付款方用户号订单');
        $orderWithPayerUserId->setAmount('100.00');
        $orderWithPayerUserId->setPayerUserId('payer_123');
        $this->repository->save($orderWithPayerUserId);

        $orderWithoutPayerUserId = new FundAuthOrder();
        $orderWithoutPayerUserId->setAccount($account);
        $orderWithoutPayerUserId->setOutOrderNo('without_payer_order');
        $orderWithoutPayerUserId->setOutRequestNo('without_payer_request');
        $orderWithoutPayerUserId->setOrderTitle('无付款方用户号订单');
        $orderWithoutPayerUserId->setAmount('200.00');
        $this->repository->save($orderWithoutPayerUserId);

        $countWithoutPayerUserId = $this->repository->count(['payerUserId' => null]);
        $this->assertSame(1, $countWithoutPayerUserId);
    }

    public function testCountByAssociationAccountShouldReturnCorrectNumber(): void
    {
        $this->clearDatabase();

        $account1 = $this->createAccount('账号1', 'app_id_1');
        $account2 = $this->createAccount('账号2', 'app_id_2');

        for ($i = 1; $i <= 4; ++$i) {
            $order = new FundAuthOrder();
            $order->setAccount($account1);
            $order->setOutOrderNo("account1_order_{$i}");
            $order->setOutRequestNo("account1_request_{$i}");
            $order->setOrderTitle("账号1订单{$i}");
            $order->setAmount('100.00');
            $this->repository->save($order);
        }

        for ($i = 1; $i <= 2; ++$i) {
            $order = new FundAuthOrder();
            $order->setAccount($account2);
            $order->setOutOrderNo("account2_order_{$i}");
            $order->setOutRequestNo("account2_request_{$i}");
            $order->setOrderTitle("账号2订单{$i}");
            $order->setAmount('200.00');
            $this->repository->save($order);
        }

        $count = $this->repository->count(['account' => $account1]);
        $this->assertSame(4, $count);

        $count2 = $this->repository->count(['account' => $account2]);
        $this->assertSame(2, $count2);
    }

    public function testFindOneByAssociationAccountShouldReturnMatchingEntity(): void
    {
        $this->clearDatabase();

        $account1 = $this->createAccount('账号1', 'app_id_1');
        $account2 = $this->createAccount('账号2', 'app_id_2');

        $order1 = new FundAuthOrder();
        $order1->setAccount($account1);
        $order1->setOutOrderNo('account1_order');
        $order1->setOutRequestNo('account1_request');
        $order1->setOrderTitle('账号1的订单');
        $order1->setAmount('100.00');
        $this->repository->save($order1);

        $order2 = new FundAuthOrder();
        $order2->setAccount($account2);
        $order2->setOutOrderNo('account2_order');
        $order2->setOutRequestNo('account2_request');
        $order2->setOrderTitle('账号2的订单');
        $order2->setAmount('200.00');
        $this->repository->save($order2);

        $result = $this->repository->findOneBy(['account' => $account1]);
        $this->assertInstanceOf(FundAuthOrder::class, $result);
        $this->assertSame('账号1的订单', $result->getOrderTitle());
        $resultAccount = $result->getAccount();
        $this->assertInstanceOf(Account::class, $resultAccount);
        $this->assertSame($account1->getId(), $resultAccount->getId());
    }

    public function testCreateWithRelationsQueryBuilder(): void
    {
        $this->clearDatabase();

        $account = $this->createAccount();

        $order = new FundAuthOrder();
        $order->setAccount($account);
        $order->setOutOrderNo('test_order_qb');
        $order->setOutRequestNo('test_request_qb');
        $order->setOrderTitle('测试QueryBuilder订单');
        $order->setAmount('100.00');
        $order->setStatus(FundAuthOrderStatus::SUCCESS);
        $this->repository->save($order);

        $queryBuilder = $this->repository->createWithRelationsQueryBuilder();
        $this->assertInstanceOf(\Doctrine\ORM\QueryBuilder::class, $queryBuilder);

        $results = $queryBuilder->getQuery()->getResult();
        $this->assertCount(1, $results);
        $this->assertInstanceOf(FundAuthOrder::class, $results[0]);
        $this->assertSame('测试QueryBuilder订单', $results[0]->getOrderTitle());
        $this->assertInstanceOf(Account::class, $results[0]->getAccount());
    }

    public function testFindByAuthNo(): void
    {
        $this->clearDatabase();

        $account = $this->createAccount();

        $order1 = new FundAuthOrder();
        $order1->setAccount($account);
        $order1->setOutOrderNo('auth_order_1');
        $order1->setOutRequestNo('auth_request_1');
        $order1->setOrderTitle('有授权号订单1');
        $order1->setAmount('100.00');
        $order1->setAuthNo('auth_no_12345');
        $this->repository->save($order1);

        $order2 = new FundAuthOrder();
        $order2->setAccount($account);
        $order2->setOutOrderNo('auth_order_2');
        $order2->setOutRequestNo('auth_request_2');
        $order2->setOrderTitle('无授权号订单2');
        $order2->setAmount('200.00');
        $this->repository->save($order2);

        $foundOrder = $this->repository->findByAuthNo('auth_no_12345');
        $this->assertInstanceOf(FundAuthOrder::class, $foundOrder);
        $this->assertSame('有授权号订单1', $foundOrder->getOrderTitle());
        $this->assertSame('auth_no_12345', $foundOrder->getAuthNo());

        $notFoundOrder = $this->repository->findByAuthNo('non_existent_auth_no');
        $this->assertNull($notFoundOrder);
    }

    public function testFindByDateRange(): void
    {
        $this->clearDatabase();

        $account = $this->createAccount();

        $oldTime = new \DateTimeImmutable('-1 day');
        $oldOrder = new FundAuthOrder();
        $oldOrder->setAccount($account);
        $oldOrder->setOutOrderNo('old_order');
        $oldOrder->setOutRequestNo('old_request');
        $oldOrder->setOrderTitle('旧订单');
        $oldOrder->setAmount('100.00');
        $oldOrder->setGmtTrans($oldTime);
        $this->repository->save($oldOrder);

        $startTime = new \DateTimeImmutable('-1 hour');
        $now = new \DateTimeImmutable();

        $newOrder1 = new FundAuthOrder();
        $newOrder1->setAccount($account);
        $newOrder1->setOutOrderNo('new_order_1');
        $newOrder1->setOutRequestNo('new_request_1');
        $newOrder1->setOrderTitle('新订单1');
        $newOrder1->setAmount('200.00');
        $newOrder1->setGmtTrans($now);
        $this->repository->save($newOrder1);

        $newOrder2 = new FundAuthOrder();
        $newOrder2->setAccount($account);
        $newOrder2->setOutOrderNo('new_order_2');
        $newOrder2->setOutRequestNo('new_request_2');
        $newOrder2->setOrderTitle('新订单2');
        $newOrder2->setAmount('300.00');
        $newOrder2->setGmtTrans($now);
        $this->repository->save($newOrder2);

        $endTime = new \DateTimeImmutable('+1 hour');

        $results = $this->repository->findByDateRange($startTime, $endTime);
        $this->assertCount(2, $results);

        $titles = array_map(fn($order) => $order->getOrderTitle(), $results);
        $this->assertContains('新订单1', $titles);
        $this->assertContains('新订单2', $titles);
        $this->assertNotContains('旧订单', $titles);
    }

    public function testFindByOperationId(): void
    {
        $this->clearDatabase();

        $account = $this->createAccount();

        $order1 = new FundAuthOrder();
        $order1->setAccount($account);
        $order1->setOutOrderNo('op_order_1');
        $order1->setOutRequestNo('op_request_1');
        $order1->setOrderTitle('有操作ID订单');
        $order1->setAmount('100.00');
        $order1->setOperationId('op_id_67890');
        $this->repository->save($order1);

        $order2 = new FundAuthOrder();
        $order2->setAccount($account);
        $order2->setOutOrderNo('op_order_2');
        $order2->setOutRequestNo('op_request_2');
        $order2->setOrderTitle('无操作ID订单');
        $order2->setAmount('200.00');
        $this->repository->save($order2);

        $foundOrder = $this->repository->findByOperationId('op_id_67890');
        $this->assertInstanceOf(FundAuthOrder::class, $foundOrder);
        $this->assertSame('有操作ID订单', $foundOrder->getOrderTitle());
        $this->assertSame('op_id_67890', $foundOrder->getOperationId());

        $notFoundOrder = $this->repository->findByOperationId('non_existent_op_id');
        $this->assertNull($notFoundOrder);
    }

    public function testFindByOrderTitleKeyword(): void
    {
        $this->clearDatabase();

        $account = $this->createAccount();

        $order1 = new FundAuthOrder();
        $order1->setAccount($account);
        $order1->setOutOrderNo('keyword_order_1');
        $order1->setOutRequestNo('keyword_request_1');
        $order1->setOrderTitle('这是一个测试订单');
        $order1->setAmount('100.00');
        $this->repository->save($order1);

        $order2 = new FundAuthOrder();
        $order2->setAccount($account);
        $order2->setOutOrderNo('keyword_order_2');
        $order2->setOutRequestNo('keyword_request_2');
        $order2->setOrderTitle('这是另一个测试订单');
        $order2->setAmount('200.00');
        $this->repository->save($order2);

        $order3 = new FundAuthOrder();
        $order3->setAccount($account);
        $order3->setOutOrderNo('keyword_order_3');
        $order3->setOutRequestNo('keyword_request_3');
        $order3->setOrderTitle('完全不同的订单');
        $order3->setAmount('300.00');
        $this->repository->save($order3);

        $results = $this->repository->findByOrderTitleKeyword('测试');
        $this->assertCount(2, $results);

        $titles = array_map(fn($order) => $order->getOrderTitle(), $results);
        $this->assertContains('这是一个测试订单', $titles);
        $this->assertContains('这是另一个测试订单', $titles);
        $this->assertNotContains('完全不同的订单', $titles);
    }

    public function testFindByOutOrderNo(): void
    {
        $this->clearDatabase();

        $account = $this->createAccount();

        $order = new FundAuthOrder();
        $order->setAccount($account);
        $order->setOutOrderNo('out_order_unique_123');
        $order->setOutRequestNo('out_request_123');
        $order->setOrderTitle('根据OutOrderNo查找的订单');
        $order->setAmount('100.00');
        $this->repository->save($order);

        $foundOrder = $this->repository->findByOutOrderNo('out_order_unique_123');
        $this->assertInstanceOf(FundAuthOrder::class, $foundOrder);
        $this->assertSame('根据OutOrderNo查找的订单', $foundOrder->getOrderTitle());
        $this->assertSame('out_order_unique_123', $foundOrder->getOutOrderNo());

        $notFoundOrder = $this->repository->findByOutOrderNo('non_existent_out_order');
        $this->assertNull($notFoundOrder);
    }

    public function testFindByPayerUserId(): void
    {
        $this->clearDatabase();

        $account = $this->createAccount();

        $order1 = new FundAuthOrder();
        $order1->setAccount($account);
        $order1->setOutOrderNo('payer_order_1');
        $order1->setOutRequestNo('payer_request_1');
        $order1->setOrderTitle('付款用户1的订单1');
        $order1->setAmount('100.00');
        $order1->setPayerUserId('payer_user_123');
        $this->repository->save($order1);

        $order2 = new FundAuthOrder();
        $order2->setAccount($account);
        $order2->setOutOrderNo('payer_order_2');
        $order2->setOutRequestNo('payer_request_2');
        $order2->setOrderTitle('付款用户1的订单2');
        $order2->setAmount('150.00');
        $order2->setPayerUserId('payer_user_123');
        $this->repository->save($order2);

        $order3 = new FundAuthOrder();
        $order3->setAccount($account);
        $order3->setOutOrderNo('payer_order_3');
        $order3->setOutRequestNo('payer_request_3');
        $order3->setOrderTitle('付款用户2的订单');
        $order3->setAmount('200.00');
        $order3->setPayerUserId('payer_user_456');
        $this->repository->save($order3);

        $results = $this->repository->findByPayerUserId('payer_user_123');
        $this->assertCount(2, $results);

        $titles = array_map(fn($order) => $order->getOrderTitle(), $results);
        $this->assertContains('付款用户1的订单1', $titles);
        $this->assertContains('付款用户1的订单2', $titles);
        $this->assertNotContains('付款用户2的订单', $titles);
    }

    public function testFindExpiredOrders(): void
    {
        $this->clearDatabase();

        $account = $this->createAccount();

        // 过期订单：INIT 状态且没有交易时间（gmtTrans 为 null）
        $expiredOrder = new FundAuthOrder();
        $expiredOrder->setAccount($account);
        $expiredOrder->setOutOrderNo('expired_order');
        $expiredOrder->setOutRequestNo('expired_request');
        $expiredOrder->setOrderTitle('过期订单');
        $expiredOrder->setAmount('100.00');
        $expiredOrder->setStatus(FundAuthOrderStatus::INIT);
        // 不设置 gmtTrans，表示还未交易
        $this->repository->save($expiredOrder);

        $expireTime = new \DateTimeImmutable();

        // 已交易订单：INIT 状态但有交易时间，不应被视为过期
        $transactedOrder = new FundAuthOrder();
        $transactedOrder->setAccount($account);
        $transactedOrder->setOutOrderNo('transacted_order');
        $transactedOrder->setOutRequestNo('transacted_request');
        $transactedOrder->setOrderTitle('已交易订单');
        $transactedOrder->setAmount('200.00');
        $transactedOrder->setStatus(FundAuthOrderStatus::INIT);
        $transactedOrder->setGmtTrans(new \DateTimeImmutable());
        $this->repository->save($transactedOrder);

        // 成功订单：不应被包含在过期订单中
        $successOrder = new FundAuthOrder();
        $successOrder->setAccount($account);
        $successOrder->setOutOrderNo('success_order');
        $successOrder->setOutRequestNo('success_request');
        $successOrder->setOrderTitle('成功订单');
        $successOrder->setAmount('300.00');
        $successOrder->setStatus(FundAuthOrderStatus::SUCCESS);
        $this->repository->save($successOrder);

        $results = $this->repository->findExpiredOrders($expireTime);
        $this->assertCount(1, $results);
        $this->assertSame('过期订单', $results[0]->getOrderTitle());
        $this->assertSame(FundAuthOrderStatus::INIT, $results[0]->getStatus());
        $this->assertNull($results[0]->getGmtTrans());
    }

    public function testFindPendingOrders(): void
    {
        $this->clearDatabase();

        $account = $this->createAccount();

        $initOrder = new FundAuthOrder();
        $initOrder->setAccount($account);
        $initOrder->setOutOrderNo('init_order');
        $initOrder->setOutRequestNo('init_request');
        $initOrder->setOrderTitle('初始状态订单');
        $initOrder->setAmount('100.00');
        $initOrder->setStatus(FundAuthOrderStatus::INIT);
        $this->repository->save($initOrder);

        $closedOrder = new FundAuthOrder();
        $closedOrder->setAccount($account);
        $closedOrder->setOutOrderNo('closed_order');
        $closedOrder->setOutRequestNo('closed_request');
        $closedOrder->setOrderTitle('关闭状态订单');
        $closedOrder->setAmount('200.00');
        $closedOrder->setStatus(FundAuthOrderStatus::CLOSED);
        $this->repository->save($closedOrder);

        $successOrder = new FundAuthOrder();
        $successOrder->setAccount($account);
        $successOrder->setOutOrderNo('success_order_pending');
        $successOrder->setOutRequestNo('success_request_pending');
        $successOrder->setOrderTitle('成功状态订单');
        $successOrder->setAmount('300.00');
        $successOrder->setStatus(FundAuthOrderStatus::SUCCESS);
        $this->repository->save($successOrder);

        $results = $this->repository->findPendingOrders();
        $this->assertCount(2, $results);

        $titles = array_map(fn($order) => $order->getOrderTitle(), $results);
        $this->assertContains('初始状态订单', $titles);
        $this->assertContains('关闭状态订单', $titles);
        $this->assertNotContains('成功状态订单', $titles);
    }

    private function createAccount(string $name = '测试账号', string $appId = 'test_app_id'): Account
    {
        $account = new Account();
        $account->setName($name);
        $account->setAppId($appId . '_' . uniqid());
        $account->setValid(true);

        $this->accountRepository->save($account);

        return $account;
    }

    private function clearDatabase(): void
    {
        $entityManager = self::getEntityManager();
        $entityManager->createQuery('DELETE FROM ' . FundAuthOrder::class)->execute();
        $entityManager->createQuery('DELETE FROM ' . Account::class)->execute();
    }

    /**
     * @return ServiceEntityRepository<FundAuthOrder>
     */
    protected function getRepository(): ServiceEntityRepository
    {
        return $this->repository;
    }

    protected function createNewEntity(): object
    {
        // 先创建一个 Account 实体并持久化它
        $account = new Account();
        $account->setName('test_account_' . uniqid());
        $account->setAppId('test_app_' . uniqid());
        $account->setValid(true);

        // 手动持久化 Account 实体
        self::getEntityManager()->persist($account);
        self::getEntityManager()->flush();

        // 创建 FundAuthOrder 实体
        $order = new FundAuthOrder();
        $order->setAccount($account);
        $order->setOutOrderNo('test_order_' . uniqid());
        $order->setOutRequestNo('test_request_' . uniqid());
        $order->setOrderTitle('Test Order ' . uniqid());
        $order->setAmount('100.00');
        $order->setStatus(FundAuthOrderStatus::INIT);

        return $order;
    }
}
