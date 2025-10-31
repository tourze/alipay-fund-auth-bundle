<?php

namespace AlipayFundAuthBundle\Tests\Repository;

use AlipayFundAuthBundle\Entity\Account;
use AlipayFundAuthBundle\Entity\TradeExtendParam;
use AlipayFundAuthBundle\Entity\TradeOrder;
use AlipayFundAuthBundle\Repository\AccountRepository;
use AlipayFundAuthBundle\Repository\TradeExtendParamRepository;
use AlipayFundAuthBundle\Repository\TradeOrderRepository;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\PHPUnitSymfonyKernelTest\AbstractRepositoryTestCase;

/**
 * @internal
 */
#[CoversClass(TradeExtendParamRepository::class)]
#[RunTestsInSeparateProcesses]
final class TradeExtendParamRepositoryTest extends AbstractRepositoryTestCase
{
    private TradeExtendParamRepository $repository;

    private TradeOrderRepository $tradeOrderRepository;

    private AccountRepository $accountRepository;

    protected function onSetUp(): void
    {
        $this->repository = self::getService(TradeExtendParamRepository::class);
        $this->tradeOrderRepository = self::getService(TradeOrderRepository::class);
        $this->accountRepository = self::getService(AccountRepository::class);
    }

    public function testFindOneByWithOrderByShouldRespectOrderParameter(): void
    {
        $this->clearDatabase();

        $tradeOrder1 = $this->createTradeOrder();
        $tradeOrder2 = $this->createTradeOrder();

        $param1 = new TradeExtendParam();
        $param1->setTradeOrder($tradeOrder1);
        $param1->setSpecifiedSellerName('Z卖家');
        $param1->setCardType('DEBIT');
        $this->repository->save($param1);

        $param2 = new TradeExtendParam();
        $param2->setTradeOrder($tradeOrder2);
        $param2->setSpecifiedSellerName('A卖家');
        $param2->setCardType('CREDIT');
        $this->repository->save($param2);

        $firstParam = $this->repository->findOneBy([], ['specifiedSellerName' => 'ASC']);
        $this->assertInstanceOf(TradeExtendParam::class, $firstParam);
        $this->assertSame('A卖家', $firstParam->getSpecifiedSellerName());

        $lastParam = $this->repository->findOneBy([], ['specifiedSellerName' => 'DESC']);
        $this->assertInstanceOf(TradeExtendParam::class, $lastParam);
        $this->assertSame('Z卖家', $lastParam->getSpecifiedSellerName());
    }

    public function testFindByWithNullableFieldShouldSupportIsNullQueries(): void
    {
        $this->clearDatabase();

        $tradeOrder1 = $this->createTradeOrder();
        $tradeOrder2 = $this->createTradeOrder();

        $paramWithProvider = new TradeExtendParam();
        $paramWithProvider->setTradeOrder($tradeOrder1);
        $paramWithProvider->setSysServiceProviderId('provider_123');
        $paramWithProvider->setSpecifiedSellerName('有服务商卖家');
        $this->repository->save($paramWithProvider);

        $paramWithoutProvider = new TradeExtendParam();
        $paramWithoutProvider->setTradeOrder($tradeOrder2);
        $paramWithoutProvider->setSpecifiedSellerName('无服务商卖家');
        $this->repository->save($paramWithoutProvider);

        $paramsWithoutProvider = $this->repository->findBy(['sysServiceProviderId' => null]);
        $this->assertCount(1, $paramsWithoutProvider);
        $this->assertSame('无服务商卖家', $paramsWithoutProvider[0]->getSpecifiedSellerName());
    }

    public function testCountByTradeOrderRelationShouldReturnCorrectCount(): void
    {
        $this->clearDatabase();

        $tradeOrder1 = $this->createTradeOrder();
        $tradeOrder2 = $this->createTradeOrder();

        $param1 = new TradeExtendParam();
        $param1->setTradeOrder($tradeOrder1);
        $param1->setSpecifiedSellerName('订单1卖家');
        $this->repository->save($param1);

        $param2 = new TradeExtendParam();
        $param2->setTradeOrder($tradeOrder2);
        $param2->setSpecifiedSellerName('订单2卖家');
        $this->repository->save($param2);

        $order1Count = $this->repository->count(['tradeOrder' => $tradeOrder1]);
        $this->assertSame(1, $order1Count);

        $order2Count = $this->repository->count(['tradeOrder' => $tradeOrder2]);
        $this->assertSame(1, $order2Count);
    }

    public function testCountWithNullableFieldShouldSupportIsNullQueries(): void
    {
        $this->clearDatabase();

        $tradeOrder1 = $this->createTradeOrder();
        $tradeOrder2 = $this->createTradeOrder();

        $paramWithCardType = new TradeExtendParam();
        $paramWithCardType->setTradeOrder($tradeOrder1);
        $paramWithCardType->setCardType('DEBIT');
        $paramWithCardType->setSpecifiedSellerName('有卡类型卖家');
        $this->repository->save($paramWithCardType);

        $paramWithoutCardType = new TradeExtendParam();
        $paramWithoutCardType->setTradeOrder($tradeOrder2);
        $paramWithoutCardType->setSpecifiedSellerName('无卡类型卖家');
        $this->repository->save($paramWithoutCardType);

        $countWithoutCardType = $this->repository->count(['cardType' => null]);
        $this->assertSame(1, $countWithoutCardType);

        $countWithoutProvider = $this->repository->count(['sysServiceProviderId' => null]);
        $this->assertSame(2, $countWithoutProvider);
    }

    public function testSave(): void
    {
        $tradeOrder = $this->createTradeOrder();

        $param = new TradeExtendParam();
        $param->setTradeOrder($tradeOrder);
        $param->setSysServiceProviderId('save_provider_123');
        $param->setSpecifiedSellerName('保存测试卖家');
        $param->setCardType('CREDIT');

        $this->repository->save($param);

        $this->assertNotNull($param->getId());

        $savedParam = $this->repository->find($param->getId());
        $this->assertInstanceOf(TradeExtendParam::class, $savedParam);
        $this->assertSame('save_provider_123', $savedParam->getSysServiceProviderId());
        $this->assertSame('保存测试卖家', $savedParam->getSpecifiedSellerName());
        $this->assertSame('CREDIT', $savedParam->getCardType());
    }

    public function testRemove(): void
    {
        $tradeOrder = $this->createTradeOrder();

        $param = new TradeExtendParam();
        $param->setTradeOrder($tradeOrder);
        $param->setSysServiceProviderId('remove_provider_123');
        $param->setSpecifiedSellerName('删除测试卖家');
        $param->setCardType('DEBIT');

        $this->repository->save($param);
        $savedId = $param->getId();

        $this->assertNotNull($this->repository->find($savedId));

        $this->repository->remove($param);

        $this->assertNull($this->repository->find($savedId));
    }

    public function testFindByTradeOrderRelationShouldReturnCorrectResults(): void
    {
        $this->clearDatabase();

        $tradeOrder1 = $this->createTradeOrder();
        $tradeOrder2 = $this->createTradeOrder();

        $param1 = new TradeExtendParam();
        $param1->setTradeOrder($tradeOrder1);
        $param1->setSpecifiedSellerName('订单1卖家');
        $this->repository->save($param1);

        $param2 = new TradeExtendParam();
        $param2->setTradeOrder($tradeOrder2);
        $param2->setSpecifiedSellerName('订单2卖家');
        $this->repository->save($param2);

        $order1Params = $this->repository->findBy(['tradeOrder' => $tradeOrder1]);
        $this->assertCount(1, $order1Params);
        $this->assertSame('订单1卖家', $order1Params[0]->getSpecifiedSellerName());
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
        $entityManager->createQuery('DELETE FROM ' . TradeExtendParam::class)->execute();
        $entityManager->createQuery('DELETE FROM ' . TradeOrder::class)->execute();
        $entityManager->createQuery('DELETE FROM ' . Account::class)->execute();
    }

    public function testCountByAssociationTradeOrderShouldReturnCorrectNumber(): void
    {
        $this->clearDatabase();

        $tradeOrder1 = $this->createTradeOrder();
        $tradeOrder2 = $this->createTradeOrder();

        $param1 = new TradeExtendParam();
        $param1->setTradeOrder($tradeOrder1);
        $param1->setSpecifiedSellerName('订单1的卖家');
        $this->repository->save($param1);

        $param2 = new TradeExtendParam();
        $param2->setTradeOrder($tradeOrder2);
        $param2->setSpecifiedSellerName('订单2的卖家');
        $this->repository->save($param2);

        $order1Count = $this->repository->count(['tradeOrder' => $tradeOrder1]);
        $this->assertSame(1, $order1Count);

        $order2Count = $this->repository->count(['tradeOrder' => $tradeOrder2]);
        $this->assertSame(1, $order2Count);
    }

    public function testFindOneByAssociationTradeOrderShouldReturnMatchingEntity(): void
    {
        $tradeOrder1 = $this->createTradeOrder();
        $tradeOrder2 = $this->createTradeOrder();

        $param1 = new TradeExtendParam();
        $param1->setTradeOrder($tradeOrder1);
        $param1->setSpecifiedSellerName('订单1的卖家');
        $this->repository->save($param1);

        $param2 = new TradeExtendParam();
        $param2->setTradeOrder($tradeOrder2);
        $param2->setSpecifiedSellerName('订单2的卖家');
        $this->repository->save($param2);

        $foundParam = $this->repository->findOneBy(['tradeOrder' => $tradeOrder1]);
        $this->assertInstanceOf(TradeExtendParam::class, $foundParam);
        $this->assertSame('订单1的卖家', $foundParam->getSpecifiedSellerName());
        $this->assertInstanceOf(TradeOrder::class, $foundParam->getTradeOrder());
        $this->assertSame($tradeOrder1->getId(), $foundParam->getTradeOrder()->getId());
    }

    public function testFindBySpecifiedSellerNameAsNullShouldReturnMatchingEntity(): void
    {
        $this->clearDatabase();

        $tradeOrder1 = $this->createTradeOrder();
        $tradeOrder2 = $this->createTradeOrder();

        $paramWithName = new TradeExtendParam();
        $paramWithName->setTradeOrder($tradeOrder1);
        $paramWithName->setSpecifiedSellerName('有名称卖家');
        $this->repository->save($paramWithName);

        $paramWithoutName = new TradeExtendParam();
        $paramWithoutName->setTradeOrder($tradeOrder2);
        $this->repository->save($paramWithoutName);

        $foundParam = $this->repository->findOneBy(['specifiedSellerName' => null]);
        $this->assertInstanceOf(TradeExtendParam::class, $foundParam);
    }

    /**
     * @return ServiceEntityRepository<TradeExtendParam>
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

        $entity = new TradeExtendParam();
        $entity->setTradeOrder($tradeOrder);
        $entity->setSysServiceProviderId('test_provider_' . uniqid());
        $entity->setSpecifiedSellerName('测试卖家');
        $entity->setCardType('DEBIT');

        return $entity;
    }
}
