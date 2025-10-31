<?php

namespace AlipayFundAuthBundle\Tests\Repository;

use AlipayFundAuthBundle\Entity\Account;
use AlipayFundAuthBundle\Entity\FundAuthOrder;
use AlipayFundAuthBundle\Entity\FundAuthPostPayment;
use AlipayFundAuthBundle\Enum\FundAuthOrderStatus;
use AlipayFundAuthBundle\Repository\AccountRepository;
use AlipayFundAuthBundle\Repository\FundAuthOrderRepository;
use AlipayFundAuthBundle\Repository\FundAuthPostPaymentRepository;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\PHPUnitSymfonyKernelTest\AbstractRepositoryTestCase;

/**
 * @internal
 */
#[CoversClass(FundAuthPostPaymentRepository::class)]
#[RunTestsInSeparateProcesses]
final class FundAuthPostPaymentRepositoryTest extends AbstractRepositoryTestCase
{
    private FundAuthPostPaymentRepository $repository;

    private FundAuthOrderRepository $orderRepository;

    private AccountRepository $accountRepository;

    protected function onSetUp(): void
    {
        $this->repository = self::getService(FundAuthPostPaymentRepository::class);
        $this->orderRepository = self::getService(FundAuthOrderRepository::class);
        $this->accountRepository = self::getService(AccountRepository::class);
    }

    public function testSave(): void
    {
        $order = $this->createFundAuthOrder();

        $postPayment = new FundAuthPostPayment();
        $postPayment->setFundAuthOrder($order);
        $postPayment->setName('保存测试后付费');
        $postPayment->setAmount('75.50');
        $postPayment->setDescription('保存测试描述');

        $this->repository->save($postPayment);

        $this->assertNotNull($postPayment->getId());

        $savedPayment = $this->repository->find($postPayment->getId());
        $this->assertInstanceOf(FundAuthPostPayment::class, $savedPayment);
        $this->assertSame('保存测试后付费', $savedPayment->getName());
        $this->assertSame('75.50', $savedPayment->getAmount());
        $this->assertSame('保存测试描述', $savedPayment->getDescription());
        $savedOrder = $savedPayment->getFundAuthOrder();
        $this->assertInstanceOf(FundAuthOrder::class, $savedOrder);
        $this->assertSame($order->getId(), $savedOrder->getId());
    }

    public function testRemove(): void
    {
        $order = $this->createFundAuthOrder();

        $postPayment = new FundAuthPostPayment();
        $postPayment->setFundAuthOrder($order);
        $postPayment->setName('删除测试后付费');
        $postPayment->setAmount('25.00');

        $this->repository->save($postPayment);
        $savedId = $postPayment->getId();

        $this->assertNotNull($this->repository->find($savedId));

        $this->repository->remove($postPayment);

        $this->assertNull($this->repository->find($savedId));
    }

    public function testFindByFundAuthOrderShouldReturnRelatedEntities(): void
    {
        $this->clearDatabase();

        $order1 = $this->createFundAuthOrder('order1');
        $order2 = $this->createFundAuthOrder('order2');

        $payment1 = new FundAuthPostPayment();
        $payment1->setFundAuthOrder($order1);
        $payment1->setName('订单1后付费');
        $payment1->setAmount('10.00');
        $this->repository->save($payment1);

        $payment2 = new FundAuthPostPayment();
        $payment2->setFundAuthOrder($order2);
        $payment2->setName('订单2后付费');
        $payment2->setAmount('20.00');
        $this->repository->save($payment2);

        $order1Payments = $this->repository->findBy(['fundAuthOrder' => $order1]);
        $this->assertCount(1, $order1Payments);
        $this->assertSame('订单1后付费', $order1Payments[0]->getName());

        $order2Payments = $this->repository->findBy(['fundAuthOrder' => $order2]);
        $this->assertCount(1, $order2Payments);
        $this->assertSame('订单2后付费', $order2Payments[0]->getName());
    }

    public function testFindByNullableDescriptionShouldReturnCorrectResults(): void
    {
        $this->clearDatabase();

        $order = $this->createFundAuthOrder();

        $paymentWithDesc = new FundAuthPostPayment();
        $paymentWithDesc->setFundAuthOrder($order);
        $paymentWithDesc->setName('有描述后付费');
        $paymentWithDesc->setAmount('30.00');
        $paymentWithDesc->setDescription('详细描述');
        $this->repository->save($paymentWithDesc);

        $paymentWithoutDesc = new FundAuthPostPayment();
        $paymentWithoutDesc->setFundAuthOrder($order);
        $paymentWithoutDesc->setName('无描述后付费');
        $paymentWithoutDesc->setAmount('40.00');
        $this->repository->save($paymentWithoutDesc);

        $paymentsWithDesc = $this->repository->findBy(['description' => '详细描述']);
        $this->assertCount(1, $paymentsWithDesc);
        $this->assertSame('有描述后付费', $paymentsWithDesc[0]->getName());

        $paymentsWithoutDesc = $this->repository->findBy(['description' => null]);
        $this->assertCount(1, $paymentsWithoutDesc);
        $this->assertSame('无描述后付费', $paymentsWithoutDesc[0]->getName());
    }

    public function testCountByAmountShouldReturnCorrectCount(): void
    {
        $this->clearDatabase();

        $order = $this->createFundAuthOrder();

        $payment1 = new FundAuthPostPayment();
        $payment1->setFundAuthOrder($order);
        $payment1->setName('50元后付费1');
        $payment1->setAmount('50.00');
        $this->repository->save($payment1);

        $payment2 = new FundAuthPostPayment();
        $payment2->setFundAuthOrder($order);
        $payment2->setName('50元后付费2');
        $payment2->setAmount('50.00');
        $this->repository->save($payment2);

        $payment3 = new FundAuthPostPayment();
        $payment3->setFundAuthOrder($order);
        $payment3->setName('100元后付费');
        $payment3->setAmount('100.00');
        $this->repository->save($payment3);

        $count50 = $this->repository->count(['amount' => '50.00']);
        $this->assertSame(2, $count50);

        $count100 = $this->repository->count(['amount' => '100.00']);
        $this->assertSame(1, $count100);
    }

    public function testFindWithOrderingShouldReturnOrderedResults(): void
    {
        $this->clearDatabase();

        $order = $this->createFundAuthOrder();

        $payment1 = new FundAuthPostPayment();
        $payment1->setFundAuthOrder($order);
        $payment1->setName('Z后付费');
        $payment1->setAmount('100.00');
        $this->repository->save($payment1);

        $payment2 = new FundAuthPostPayment();
        $payment2->setFundAuthOrder($order);
        $payment2->setName('A后付费');
        $payment2->setAmount('50.00');
        $this->repository->save($payment2);

        $orderedByName = $this->repository->findBy([], ['name' => 'ASC']);
        $this->assertCount(2, $orderedByName);
        $this->assertSame('A后付费', $orderedByName[0]->getName());
        $this->assertSame('Z后付费', $orderedByName[1]->getName());

        $orderedByAmount = $this->repository->findBy([], ['amount' => 'DESC']);
        $this->assertCount(2, $orderedByAmount);
        $this->assertSame('100.00', $orderedByAmount[0]->getAmount());
        $this->assertSame('50.00', $orderedByAmount[1]->getAmount());
    }

    public function testFindOneByWithOrderByShouldRespectOrderParameter(): void
    {
        $this->clearDatabase();

        $order = $this->createFundAuthOrder();

        $payment1 = new FundAuthPostPayment();
        $payment1->setFundAuthOrder($order);
        $payment1->setName('Z后付费');
        $payment1->setAmount('100.00');
        $this->repository->save($payment1);

        $payment2 = new FundAuthPostPayment();
        $payment2->setFundAuthOrder($order);
        $payment2->setName('A后付费');
        $payment2->setAmount('50.00');
        $this->repository->save($payment2);

        $firstPayment = $this->repository->findOneBy([], ['name' => 'ASC']);
        $this->assertInstanceOf(FundAuthPostPayment::class, $firstPayment);
        $this->assertSame('A后付费', $firstPayment->getName());

        $lastPayment = $this->repository->findOneBy([], ['name' => 'DESC']);
        $this->assertInstanceOf(FundAuthPostPayment::class, $lastPayment);
        $this->assertSame('Z后付费', $lastPayment->getName());
    }

    public function testFindByWithNullableFieldShouldSupportIsNullQueries(): void
    {
        $this->clearDatabase();

        $order = $this->createFundAuthOrder();

        $paymentWithDescription = new FundAuthPostPayment();
        $paymentWithDescription->setFundAuthOrder($order);
        $paymentWithDescription->setName('有描述后付费');
        $paymentWithDescription->setAmount('30.00');
        $paymentWithDescription->setDescription('详细描述内容');
        $this->repository->save($paymentWithDescription);

        $paymentWithoutDescription = new FundAuthPostPayment();
        $paymentWithoutDescription->setFundAuthOrder($order);
        $paymentWithoutDescription->setName('无描述后付费');
        $paymentWithoutDescription->setAmount('40.00');
        $this->repository->save($paymentWithoutDescription);

        $paymentsWithoutDescription = $this->repository->findBy(['description' => null]);
        $this->assertCount(1, $paymentsWithoutDescription);
        $this->assertSame('无描述后付费', $paymentsWithoutDescription[0]->getName());
    }

    public function testCountWithNullableFieldShouldSupportIsNullQueries(): void
    {
        $this->clearDatabase();

        $order = $this->createFundAuthOrder();

        $paymentWithDescription = new FundAuthPostPayment();
        $paymentWithDescription->setFundAuthOrder($order);
        $paymentWithDescription->setName('有描述后付费');
        $paymentWithDescription->setAmount('30.00');
        $paymentWithDescription->setDescription('详细描述');
        $this->repository->save($paymentWithDescription);

        $paymentWithoutDescription = new FundAuthPostPayment();
        $paymentWithoutDescription->setFundAuthOrder($order);
        $paymentWithoutDescription->setName('无描述后付费');
        $paymentWithoutDescription->setAmount('40.00');
        $this->repository->save($paymentWithoutDescription);

        $countWithoutDescription = $this->repository->count(['description' => null]);
        $this->assertSame(1, $countWithoutDescription);
    }

    public function testFindByFundAuthOrderRelationShouldReturnCorrectResults(): void
    {
        $this->clearDatabase();

        $order1 = $this->createFundAuthOrder('order1');
        $order2 = $this->createFundAuthOrder('order2');

        $payment1 = new FundAuthPostPayment();
        $payment1->setFundAuthOrder($order1);
        $payment1->setName('订单1的后付费');
        $payment1->setAmount('30.00');
        $this->repository->save($payment1);

        $payment2 = new FundAuthPostPayment();
        $payment2->setFundAuthOrder($order2);
        $payment2->setName('订单2的后付费');
        $payment2->setAmount('40.00');
        $this->repository->save($payment2);

        $order1Payments = $this->repository->findBy(['fundAuthOrder' => $order1]);
        $this->assertCount(1, $order1Payments);
        $this->assertSame('订单1的后付费', $order1Payments[0]->getName());
    }

    public function testCountByFundAuthOrderRelationShouldReturnCorrectCount(): void
    {
        $this->clearDatabase();

        $order1 = $this->createFundAuthOrder('order1');
        $order2 = $this->createFundAuthOrder('order2');

        $payment1 = new FundAuthPostPayment();
        $payment1->setFundAuthOrder($order1);
        $payment1->setName('订单1后付费1');
        $payment1->setAmount('30.00');
        $this->repository->save($payment1);

        $payment2 = new FundAuthPostPayment();
        $payment2->setFundAuthOrder($order1);
        $payment2->setName('订单1后付费2');
        $payment2->setAmount('40.00');
        $this->repository->save($payment2);

        $payment3 = new FundAuthPostPayment();
        $payment3->setFundAuthOrder($order2);
        $payment3->setName('订单2后付费');
        $payment3->setAmount('50.00');
        $this->repository->save($payment3);

        $order1Count = $this->repository->count(['fundAuthOrder' => $order1]);
        $this->assertSame(2, $order1Count);

        $order2Count = $this->repository->count(['fundAuthOrder' => $order2]);
        $this->assertSame(1, $order2Count);
    }

    public function testCountByAssociationFundAuthOrderShouldReturnCorrectNumber(): void
    {
        $this->clearDatabase();

        $order1 = $this->createFundAuthOrder('order1');
        $order2 = $this->createFundAuthOrder('order2');

        for ($i = 1; $i <= 4; ++$i) {
            $payment = new FundAuthPostPayment();
            $payment->setFundAuthOrder($order1);
            $payment->setName("订单1后付费{$i}");
            $payment->setAmount("1{$i}.00");
            $this->repository->save($payment);
        }

        for ($i = 1; $i <= 2; ++$i) {
            $payment = new FundAuthPostPayment();
            $payment->setFundAuthOrder($order2);
            $payment->setName("订单2后付费{$i}");
            $payment->setAmount("2{$i}.00");
            $this->repository->save($payment);
        }

        $count = $this->repository->count(['fundAuthOrder' => $order1]);
        $this->assertSame(4, $count);

        $count2 = $this->repository->count(['fundAuthOrder' => $order2]);
        $this->assertSame(2, $count2);
    }

    public function testFindOneByAssociationFundAuthOrderShouldReturnMatchingEntity(): void
    {
        $this->clearDatabase();

        $order1 = $this->createFundAuthOrder('order1');
        $order2 = $this->createFundAuthOrder('order2');

        $payment1 = new FundAuthPostPayment();
        $payment1->setFundAuthOrder($order1);
        $payment1->setName('订单1的后付费');
        $payment1->setAmount('30.00');
        $this->repository->save($payment1);

        $payment2 = new FundAuthPostPayment();
        $payment2->setFundAuthOrder($order2);
        $payment2->setName('订单2的后付费');
        $payment2->setAmount('40.00');
        $this->repository->save($payment2);

        $result = $this->repository->findOneBy(['fundAuthOrder' => $order1]);
        $this->assertInstanceOf(FundAuthPostPayment::class, $result);
        $this->assertSame('订单1的后付费', $result->getName());
        $resultOrder = $result->getFundAuthOrder();
        $this->assertInstanceOf(FundAuthOrder::class, $resultOrder);
        $this->assertSame($order1->getId(), $resultOrder->getId());
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
        $order->setStatus(FundAuthOrderStatus::INIT);

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
        $entityManager->createQuery('DELETE FROM ' . FundAuthPostPayment::class)->execute();
        $entityManager->createQuery('DELETE FROM ' . FundAuthOrder::class)->execute();
        $entityManager->createQuery('DELETE FROM ' . Account::class)->execute();
    }

    /**
     * @return ServiceEntityRepository<FundAuthPostPayment>
     */
    protected function getRepository(): ServiceEntityRepository
    {
        return $this->repository;
    }

    protected function createNewEntity(): FundAuthPostPayment
    {
        $account = new Account();
        $account->setName('test_account_' . uniqid());
        $account->setAppId('test_app_' . uniqid());
        $account->setValid(true);

        $fundAuthOrder = new FundAuthOrder();
        $fundAuthOrder->setAccount($account);
        $fundAuthOrder->setOutOrderNo('test_order_' . uniqid());
        $fundAuthOrder->setOutRequestNo('test_request_' . uniqid());
        $fundAuthOrder->setOrderTitle('Test Order ' . uniqid());
        $fundAuthOrder->setAmount('100.00');
        $fundAuthOrder->setStatus(FundAuthOrderStatus::INIT);

        $postPayment = new FundAuthPostPayment();
        $postPayment->setFundAuthOrder($fundAuthOrder);
        $postPayment->setName('Test Post Payment ' . uniqid());
        $postPayment->setAmount('50.00');
        $postPayment->setDescription('Test description');

        return $postPayment;
    }
}
