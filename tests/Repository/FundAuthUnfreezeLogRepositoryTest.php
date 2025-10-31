<?php

namespace AlipayFundAuthBundle\Tests\Repository;

use AlipayFundAuthBundle\Entity\Account;
use AlipayFundAuthBundle\Entity\FundAuthOrder;
use AlipayFundAuthBundle\Entity\FundAuthUnfreezeLog;
use AlipayFundAuthBundle\Repository\AccountRepository;
use AlipayFundAuthBundle\Repository\FundAuthOrderRepository;
use AlipayFundAuthBundle\Repository\FundAuthUnfreezeLogRepository;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\PHPUnitSymfonyKernelTest\AbstractRepositoryTestCase;

/**
 * @internal
 */
#[CoversClass(FundAuthUnfreezeLogRepository::class)]
#[RunTestsInSeparateProcesses]
final class FundAuthUnfreezeLogRepositoryTest extends AbstractRepositoryTestCase
{
    private FundAuthUnfreezeLogRepository $repository;

    private FundAuthOrderRepository $orderRepository;

    private AccountRepository $accountRepository;

    protected function onSetUp(): void
    {
        $this->repository = self::getService(FundAuthUnfreezeLogRepository::class);
        $this->orderRepository = self::getService(FundAuthOrderRepository::class);
        $this->accountRepository = self::getService(AccountRepository::class);
    }

    public function testSave(): void
    {
        $order = $this->createFundAuthOrder();

        $unfreezeLog = new FundAuthUnfreezeLog();
        $unfreezeLog->setFundAuthOrder($order);
        $unfreezeLog->setOutRequestNo('save_test_unfreeze');
        $unfreezeLog->setAmount('75.50');
        $unfreezeLog->setRemark('保存测试解冻记录');
        $unfreezeLog->setOperationId('op_123456');
        $unfreezeLog->setStatus('SUCCESS');
        $unfreezeLog->setCreditAmount('30.00');
        $unfreezeLog->setFundAmount('45.50');

        $this->repository->save($unfreezeLog);

        $this->assertNotNull($unfreezeLog->getId());

        $savedLog = $this->repository->find($unfreezeLog->getId());
        $this->assertInstanceOf(FundAuthUnfreezeLog::class, $savedLog);
        $this->assertSame('save_test_unfreeze', $savedLog->getOutRequestNo());
        $this->assertSame('75.50', $savedLog->getAmount());
        $this->assertSame('保存测试解冻记录', $savedLog->getRemark());
        $this->assertSame('op_123456', $savedLog->getOperationId());
        $this->assertSame('SUCCESS', $savedLog->getStatus());
        $this->assertSame('30.00', $savedLog->getCreditAmount());
        $this->assertSame('45.50', $savedLog->getFundAmount());
    }

    public function testRemove(): void
    {
        $order = $this->createFundAuthOrder();

        $unfreezeLog = new FundAuthUnfreezeLog();
        $unfreezeLog->setFundAuthOrder($order);
        $unfreezeLog->setOutRequestNo('remove_test_unfreeze');
        $unfreezeLog->setAmount('25.00');
        $unfreezeLog->setRemark('删除测试解冻记录');

        $this->repository->save($unfreezeLog);
        $savedId = $unfreezeLog->getId();

        $this->assertNotNull($this->repository->find($savedId));

        $this->repository->remove($unfreezeLog);

        $this->assertNull($this->repository->find($savedId));
    }

    public function testCountShouldReturnCorrectNumberOfRecords(): void
    {
        $this->clearDatabase();

        $order = $this->createFundAuthOrder();

        $log1 = new FundAuthUnfreezeLog();
        $log1->setFundAuthOrder($order);
        $log1->setOutRequestNo('count_test_1');
        $log1->setAmount('30.00');
        $log1->setRemark('计数测试记录1');
        $this->repository->save($log1);

        $log2 = new FundAuthUnfreezeLog();
        $log2->setFundAuthOrder($order);
        $log2->setOutRequestNo('count_test_2');
        $log2->setAmount('40.00');
        $log2->setRemark('计数测试记录2');
        $this->repository->save($log2);

        $totalCount = $this->repository->count([]);
        $this->assertSame(2, $totalCount);

        $specificCount = $this->repository->count(['amount' => '30.00']);
        $this->assertSame(1, $specificCount);
    }

    public function testFindOneByWithOrderByShouldRespectOrderParameter(): void
    {
        $this->clearDatabase();

        $order = $this->createFundAuthOrder();

        $log1 = new FundAuthUnfreezeLog();
        $log1->setFundAuthOrder($order);
        $log1->setOutRequestNo('unfreeze_z');
        $log1->setAmount('100.00');
        $log1->setRemark('Z解冻记录');
        $this->repository->save($log1);

        $log2 = new FundAuthUnfreezeLog();
        $log2->setFundAuthOrder($order);
        $log2->setOutRequestNo('unfreeze_a');
        $log2->setAmount('50.00');
        $log2->setRemark('A解冻记录');
        $this->repository->save($log2);

        $firstLog = $this->repository->findOneBy([], ['remark' => 'ASC']);
        $this->assertInstanceOf(FundAuthUnfreezeLog::class, $firstLog);
        $this->assertSame('A解冻记录', $firstLog->getRemark());

        $lastLog = $this->repository->findOneBy([], ['remark' => 'DESC']);
        $this->assertInstanceOf(FundAuthUnfreezeLog::class, $lastLog);
        $this->assertSame('Z解冻记录', $lastLog->getRemark());
    }

    public function testFindByWithNullableFieldShouldSupportIsNullQueries(): void
    {
        $this->clearDatabase();

        $order = $this->createFundAuthOrder();

        $logWithOperationId = new FundAuthUnfreezeLog();
        $logWithOperationId->setFundAuthOrder($order);
        $logWithOperationId->setOutRequestNo('with_operation_unfreeze');
        $logWithOperationId->setAmount('30.00');
        $logWithOperationId->setRemark('有操作流水号解冻记录');
        $logWithOperationId->setOperationId('operation_123');
        $this->repository->save($logWithOperationId);

        $logWithoutOperationId = new FundAuthUnfreezeLog();
        $logWithoutOperationId->setFundAuthOrder($order);
        $logWithoutOperationId->setOutRequestNo('without_operation_unfreeze');
        $logWithoutOperationId->setAmount('40.00');
        $logWithoutOperationId->setRemark('无操作流水号解冻记录');
        $this->repository->save($logWithoutOperationId);

        $logsWithoutOperationId = $this->repository->findBy(['operationId' => null]);
        $this->assertCount(1, $logsWithoutOperationId);
        $this->assertSame('无操作流水号解冻记录', $logsWithoutOperationId[0]->getRemark());
    }

    public function testCountWithNullableFieldShouldSupportIsNullQueries(): void
    {
        $this->clearDatabase();

        $order = $this->createFundAuthOrder();

        $logWithStatus = new FundAuthUnfreezeLog();
        $logWithStatus->setFundAuthOrder($order);
        $logWithStatus->setOutRequestNo('with_status_unfreeze');
        $logWithStatus->setAmount('30.00');
        $logWithStatus->setRemark('有状态解冻记录');
        $logWithStatus->setStatus('SUCCESS');
        $this->repository->save($logWithStatus);

        $logWithoutStatus = new FundAuthUnfreezeLog();
        $logWithoutStatus->setFundAuthOrder($order);
        $logWithoutStatus->setOutRequestNo('without_status_unfreeze');
        $logWithoutStatus->setAmount('40.00');
        $logWithoutStatus->setRemark('无状态解冻记录');
        $this->repository->save($logWithoutStatus);

        $countWithoutStatus = $this->repository->count(['status' => null]);
        $this->assertSame(1, $countWithoutStatus);

        $countWithoutCreditAmount = $this->repository->count(['creditAmount' => null]);
        $this->assertSame(2, $countWithoutCreditAmount);
    }

    public function testFindByFundAuthOrderRelationShouldReturnCorrectResults(): void
    {
        $this->clearDatabase();

        $order1 = $this->createFundAuthOrder('order1');
        $order2 = $this->createFundAuthOrder('order2');

        $log1 = new FundAuthUnfreezeLog();
        $log1->setFundAuthOrder($order1);
        $log1->setOutRequestNo('order1_unfreeze');
        $log1->setAmount('30.00');
        $log1->setRemark('订单1的解冻记录');
        $this->repository->save($log1);

        $log2 = new FundAuthUnfreezeLog();
        $log2->setFundAuthOrder($order2);
        $log2->setOutRequestNo('order2_unfreeze');
        $log2->setAmount('40.00');
        $log2->setRemark('订单2的解冻记录');
        $this->repository->save($log2);

        $order1Logs = $this->repository->findBy(['fundAuthOrder' => $order1]);
        $this->assertCount(1, $order1Logs);
        $this->assertSame('订单1的解冻记录', $order1Logs[0]->getRemark());
    }

    public function testCountByFundAuthOrderRelationShouldReturnCorrectCount(): void
    {
        $this->clearDatabase();

        $order1 = $this->createFundAuthOrder('order1');
        $order2 = $this->createFundAuthOrder('order2');

        $log1 = new FundAuthUnfreezeLog();
        $log1->setFundAuthOrder($order1);
        $log1->setOutRequestNo('order1_unfreeze_1');
        $log1->setAmount('30.00');
        $log1->setRemark('订单1解冻记录1');
        $this->repository->save($log1);

        $log2 = new FundAuthUnfreezeLog();
        $log2->setFundAuthOrder($order1);
        $log2->setOutRequestNo('order1_unfreeze_2');
        $log2->setAmount('40.00');
        $log2->setRemark('订单1解冻记录2');
        $this->repository->save($log2);

        $log3 = new FundAuthUnfreezeLog();
        $log3->setFundAuthOrder($order2);
        $log3->setOutRequestNo('order2_unfreeze');
        $log3->setAmount('50.00');
        $log3->setRemark('订单2解冻记录');
        $this->repository->save($log3);

        $order1Count = $this->repository->count(['fundAuthOrder' => $order1]);
        $this->assertSame(2, $order1Count);

        $order2Count = $this->repository->count(['fundAuthOrder' => $order2]);
        $this->assertSame(1, $order2Count);
    }

    private function createFundAuthOrder(string $suffix = ''): FundAuthOrder
    {
        $account = $this->createAccount($suffix);

        $order = new FundAuthOrder();
        $order->setAccount($account);
        $order->setOutOrderNo('test_order_' . $suffix . '_' . uniqid());
        $order->setOutRequestNo('test_request_' . $suffix . '_' . uniqid());
        $order->setOrderTitle('测试订单' . $suffix);
        $order->setAmount('100.00');

        $this->orderRepository->save($order);

        return $order;
    }

    private function createAccount(string $suffix = ''): Account
    {
        $account = new Account();
        $account->setName('测试账号' . $suffix);
        $account->setAppId('test_app_id_' . $suffix . '_' . uniqid());
        $account->setValid(true);

        $this->accountRepository->save($account);

        return $account;
    }

    private function clearDatabase(): void
    {
        $entityManager = self::getEntityManager();
        $entityManager->createQuery('DELETE FROM ' . FundAuthUnfreezeLog::class)->execute();
        $entityManager->createQuery('DELETE FROM ' . FundAuthOrder::class)->execute();
        $entityManager->createQuery('DELETE FROM ' . Account::class)->execute();
    }

    public function testFindByAmountRangeShouldReturnCorrectResults(): void
    {
        $this->clearDatabase();

        $order = $this->createFundAuthOrder();

        $lowAmountLog = new FundAuthUnfreezeLog();
        $lowAmountLog->setFundAuthOrder($order);
        $lowAmountLog->setOutRequestNo('low_amount');
        $lowAmountLog->setAmount('10.00');
        $lowAmountLog->setRemark('低金额记录');
        $this->repository->save($lowAmountLog);

        $mediumAmountLog = new FundAuthUnfreezeLog();
        $mediumAmountLog->setFundAuthOrder($order);
        $mediumAmountLog->setOutRequestNo('medium_amount');
        $mediumAmountLog->setAmount('50.00');
        $mediumAmountLog->setRemark('中等金额记录');
        $this->repository->save($mediumAmountLog);

        $highAmountLog = new FundAuthUnfreezeLog();
        $highAmountLog->setFundAuthOrder($order);
        $highAmountLog->setOutRequestNo('high_amount');
        $highAmountLog->setAmount('100.00');
        $highAmountLog->setRemark('高金额记录');
        $this->repository->save($highAmountLog);

        $qb = $this->repository->createQueryBuilder('f');
        $results = $qb
            ->where('f.amount >= :minAmount')
            ->andWhere('f.amount <= :maxAmount')
            ->setParameter('minAmount', '20.00')
            ->setParameter('maxAmount', '80.00')
            ->getQuery()
            ->getResult()
        ;

        $this->assertIsArray($results);
        $this->assertCount(1, $results);
        $this->assertInstanceOf(FundAuthUnfreezeLog::class, $results[0]);
        $this->assertSame('中等金额记录', $results[0]->getRemark());
    }

    public function testCountByAssociationFundAuthOrderShouldReturnCorrectNumber(): void
    {
        $this->clearDatabase();

        $order1 = $this->createFundAuthOrder('auth_order_1');
        $order2 = $this->createFundAuthOrder('auth_order_2');

        $log1 = new FundAuthUnfreezeLog();
        $log1->setFundAuthOrder($order1);
        $log1->setOutRequestNo('req_1_1');
        $log1->setAmount('10.00');
        $log1->setRemark('订单1解冻记录1');
        $this->repository->save($log1);

        $log2 = new FundAuthUnfreezeLog();
        $log2->setFundAuthOrder($order1);
        $log2->setOutRequestNo('req_1_2');
        $log2->setAmount('20.00');
        $log2->setRemark('订单1解冻记录2');
        $this->repository->save($log2);

        $log3 = new FundAuthUnfreezeLog();
        $log3->setFundAuthOrder($order2);
        $log3->setOutRequestNo('req_2_1');
        $log3->setAmount('30.00');
        $log3->setRemark('订单2解冻记录');
        $this->repository->save($log3);

        $order1Count = $this->repository->count(['fundAuthOrder' => $order1]);
        $this->assertSame(2, $order1Count);

        $order2Count = $this->repository->count(['fundAuthOrder' => $order2]);
        $this->assertSame(1, $order2Count);
    }

    public function testFindOneByAssociationFundAuthOrderShouldReturnMatchingEntity(): void
    {
        $order1 = $this->createFundAuthOrder('auth_order_1');
        $order2 = $this->createFundAuthOrder('auth_order_2');

        $log1 = new FundAuthUnfreezeLog();
        $log1->setFundAuthOrder($order1);
        $log1->setOutRequestNo('req_1');
        $log1->setAmount('10.00');
        $log1->setRemark('订单1的解冻记录');
        $this->repository->save($log1);

        $log2 = new FundAuthUnfreezeLog();
        $log2->setFundAuthOrder($order2);
        $log2->setOutRequestNo('req_2');
        $log2->setAmount('20.00');
        $log2->setRemark('订单2的解冻记录');
        $this->repository->save($log2);

        $foundLog = $this->repository->findOneBy(['fundAuthOrder' => $order1]);
        $this->assertInstanceOf(FundAuthUnfreezeLog::class, $foundLog);
        $this->assertSame('订单1的解冻记录', $foundLog->getRemark());
        $foundOrder = $foundLog->getFundAuthOrder();
        $this->assertInstanceOf(FundAuthOrder::class, $foundOrder);
        $this->assertSame($order1->getId(), $foundOrder->getId());
    }

    /**
     * @return ServiceEntityRepository<FundAuthUnfreezeLog>
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

        $order = new FundAuthOrder();
        $order->setAccount($account);
        $order->setOutOrderNo('test_order_' . uniqid());
        $order->setOutRequestNo('test_request_' . uniqid());
        $order->setOrderTitle('测试订单');
        $order->setAmount('100.00');

        $entity = new FundAuthUnfreezeLog();
        $entity->setFundAuthOrder($order);
        $entity->setOutRequestNo('test_unfreeze_' . uniqid());
        $entity->setAmount('50.00');
        $entity->setRemark('测试解冻记录');

        return $entity;
    }
}
