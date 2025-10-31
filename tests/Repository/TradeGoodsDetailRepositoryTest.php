<?php

namespace AlipayFundAuthBundle\Tests\Repository;

use AlipayFundAuthBundle\Entity\Account;
use AlipayFundAuthBundle\Entity\FundAuthOrder;
use AlipayFundAuthBundle\Entity\TradeGoodsDetail;
use AlipayFundAuthBundle\Entity\TradeOrder;
use AlipayFundAuthBundle\Enum\FundAuthOrderStatus;
use AlipayFundAuthBundle\Repository\AccountRepository;
use AlipayFundAuthBundle\Repository\FundAuthOrderRepository;
use AlipayFundAuthBundle\Repository\TradeGoodsDetailRepository;
use AlipayFundAuthBundle\Repository\TradeOrderRepository;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\PHPUnitSymfonyKernelTest\AbstractRepositoryTestCase;

/**
 * @internal
 */
#[CoversClass(TradeGoodsDetailRepository::class)]
#[RunTestsInSeparateProcesses]
final class TradeGoodsDetailRepositoryTest extends AbstractRepositoryTestCase
{
    private TradeGoodsDetailRepository $repository;

    private AccountRepository $accountRepository;

    private FundAuthOrderRepository $fundAuthOrderRepository;

    private TradeOrderRepository $tradeOrderRepository;

    protected function onSetUp(): void
    {
        $this->repository = self::getService(TradeGoodsDetailRepository::class);
        $this->accountRepository = self::getService(AccountRepository::class);
        $this->fundAuthOrderRepository = self::getService(FundAuthOrderRepository::class);
        $this->tradeOrderRepository = self::getService(TradeOrderRepository::class);
    }

    public function testFindOneByWithOrderByShouldRespectOrderParameter(): void
    {
        $this->clearDatabase();

        $tradeOrder = $this->createTradeOrder();

        $goodsDetail1 = new TradeGoodsDetail();
        $goodsDetail1->setTradeOrder($tradeOrder);
        $goodsDetail1->setGoodsId('goods_z');
        $goodsDetail1->setGoodsName('Z商品');
        $goodsDetail1->setQuantity(1);
        $goodsDetail1->setPrice('30.00');
        $this->repository->save($goodsDetail1);

        $goodsDetail2 = new TradeGoodsDetail();
        $goodsDetail2->setTradeOrder($tradeOrder);
        $goodsDetail2->setGoodsId('goods_a');
        $goodsDetail2->setGoodsName('A商品');
        $goodsDetail2->setQuantity(1);
        $goodsDetail2->setPrice('10.00');
        $this->repository->save($goodsDetail2);

        $firstGoodsDetail = $this->repository->findOneBy(['quantity' => 1], ['goodsName' => 'ASC']);
        $this->assertInstanceOf(TradeGoodsDetail::class, $firstGoodsDetail);
        $this->assertSame('A商品', $firstGoodsDetail->getGoodsName());

        $lastGoodsDetail = $this->repository->findOneBy(['quantity' => 1], ['goodsName' => 'DESC']);
        $this->assertInstanceOf(TradeGoodsDetail::class, $lastGoodsDetail);
        $this->assertSame('Z商品', $lastGoodsDetail->getGoodsName());
    }

    public function testFindByWithNullableFieldShouldSupportIsNullQueries(): void
    {
        $this->clearDatabase();

        $tradeOrder = $this->createTradeOrder();

        $goodsDetailWithCategory = new TradeGoodsDetail();
        $goodsDetailWithCategory->setTradeOrder($tradeOrder);
        $goodsDetailWithCategory->setGoodsId('with_category_goods');
        $goodsDetailWithCategory->setGoodsName('有类别商品');
        $goodsDetailWithCategory->setQuantity(1);
        $goodsDetailWithCategory->setPrice('10.00');
        $goodsDetailWithCategory->setGoodsCategory('电子产品');
        $this->repository->save($goodsDetailWithCategory);

        $goodsDetailWithoutCategory = new TradeGoodsDetail();
        $goodsDetailWithoutCategory->setTradeOrder($tradeOrder);
        $goodsDetailWithoutCategory->setGoodsId('without_category_goods');
        $goodsDetailWithoutCategory->setGoodsName('无类别商品');
        $goodsDetailWithoutCategory->setQuantity(1);
        $goodsDetailWithoutCategory->setPrice('15.00');
        $this->repository->save($goodsDetailWithoutCategory);

        $goodsDetailsWithoutCategory = $this->repository->findBy(['goodsCategory' => null]);
        $this->assertCount(1, $goodsDetailsWithoutCategory);
        $this->assertSame('无类别商品', $goodsDetailsWithoutCategory[0]->getGoodsName());
    }

    public function testCountByTradeOrderRelationShouldReturnCorrectCount(): void
    {
        $this->clearDatabase();

        $tradeOrder1 = $this->createTradeOrder('order_1');
        $tradeOrder2 = $this->createTradeOrder('order_2');

        $goodsDetail1 = new TradeGoodsDetail();
        $goodsDetail1->setTradeOrder($tradeOrder1);
        $goodsDetail1->setGoodsId('goods_1_1');
        $goodsDetail1->setGoodsName('订单1商品1');
        $goodsDetail1->setQuantity(1);
        $goodsDetail1->setPrice('10.00');
        $this->repository->save($goodsDetail1);

        $goodsDetail2 = new TradeGoodsDetail();
        $goodsDetail2->setTradeOrder($tradeOrder1);
        $goodsDetail2->setGoodsId('goods_1_2');
        $goodsDetail2->setGoodsName('订单1商品2');
        $goodsDetail2->setQuantity(2);
        $goodsDetail2->setPrice('20.00');
        $this->repository->save($goodsDetail2);

        $goodsDetail3 = new TradeGoodsDetail();
        $goodsDetail3->setTradeOrder($tradeOrder2);
        $goodsDetail3->setGoodsId('goods_2_1');
        $goodsDetail3->setGoodsName('订单2商品');
        $goodsDetail3->setQuantity(1);
        $goodsDetail3->setPrice('30.00');
        $this->repository->save($goodsDetail3);

        $tradeOrder1Count = $this->repository->count(['tradeOrder' => $tradeOrder1]);
        $this->assertSame(2, $tradeOrder1Count);

        $tradeOrder2Count = $this->repository->count(['tradeOrder' => $tradeOrder2]);
        $this->assertSame(1, $tradeOrder2Count);
    }

    public function testCountWithNullableFieldShouldSupportIsNullQueries(): void
    {
        $this->clearDatabase();

        $tradeOrder = $this->createTradeOrder();

        $goodsDetailWithShowUrl = new TradeGoodsDetail();
        $goodsDetailWithShowUrl->setTradeOrder($tradeOrder);
        $goodsDetailWithShowUrl->setGoodsId('with_url_goods');
        $goodsDetailWithShowUrl->setGoodsName('有展示地址商品');
        $goodsDetailWithShowUrl->setQuantity(1);
        $goodsDetailWithShowUrl->setPrice('10.00');
        $goodsDetailWithShowUrl->setShowUrl('https://example.com/goods/123');
        $this->repository->save($goodsDetailWithShowUrl);

        $goodsDetailWithoutShowUrl = new TradeGoodsDetail();
        $goodsDetailWithoutShowUrl->setTradeOrder($tradeOrder);
        $goodsDetailWithoutShowUrl->setGoodsId('without_url_goods');
        $goodsDetailWithoutShowUrl->setGoodsName('无展示地址商品');
        $goodsDetailWithoutShowUrl->setQuantity(1);
        $goodsDetailWithoutShowUrl->setPrice('15.00');
        $this->repository->save($goodsDetailWithoutShowUrl);

        $countWithoutShowUrl = $this->repository->count(['showUrl' => null]);
        $this->assertSame(1, $countWithoutShowUrl);

        $countWithoutCategoryTree = $this->repository->count(['categoryTree' => null]);
        $this->assertSame(2, $countWithoutCategoryTree);
    }

    public function testFindByTradeOrderRelationShouldReturnCorrectResults(): void
    {
        $this->clearDatabase();

        $tradeOrder1 = $this->createTradeOrder('order_1');
        $tradeOrder2 = $this->createTradeOrder('order_2');

        $goodsDetail1 = new TradeGoodsDetail();
        $goodsDetail1->setTradeOrder($tradeOrder1);
        $goodsDetail1->setGoodsId('goods_1');
        $goodsDetail1->setGoodsName('订单1的商品');
        $goodsDetail1->setQuantity(1);
        $goodsDetail1->setPrice('10.00');
        $this->repository->save($goodsDetail1);

        $goodsDetail2 = new TradeGoodsDetail();
        $goodsDetail2->setTradeOrder($tradeOrder2);
        $goodsDetail2->setGoodsId('goods_2');
        $goodsDetail2->setGoodsName('订单2的商品');
        $goodsDetail2->setQuantity(2);
        $goodsDetail2->setPrice('15.00');
        $this->repository->save($goodsDetail2);

        $tradeOrder1GoodsDetails = $this->repository->findBy(['tradeOrder' => $tradeOrder1]);
        $this->assertCount(1, $tradeOrder1GoodsDetails);
        $this->assertSame('订单1的商品', $tradeOrder1GoodsDetails[0]->getGoodsName());
    }

    public function testFindOneByAssociationTradeOrderShouldReturnMatchingEntity(): void
    {
        $tradeOrder1 = $this->createTradeOrder('order_1');
        $tradeOrder2 = $this->createTradeOrder('order_2');

        $goodsDetail1 = new TradeGoodsDetail();
        $goodsDetail1->setTradeOrder($tradeOrder1);
        $goodsDetail1->setGoodsId('goods_1');
        $goodsDetail1->setGoodsName('订单1的商品');
        $goodsDetail1->setQuantity(1);
        $goodsDetail1->setPrice('10.00');
        $this->repository->save($goodsDetail1);

        $goodsDetail2 = new TradeGoodsDetail();
        $goodsDetail2->setTradeOrder($tradeOrder2);
        $goodsDetail2->setGoodsId('goods_2');
        $goodsDetail2->setGoodsName('订单2的商品');
        $goodsDetail2->setQuantity(2);
        $goodsDetail2->setPrice('15.00');
        $this->repository->save($goodsDetail2);

        $foundGoodsDetail = $this->repository->findOneBy(['tradeOrder' => $tradeOrder1]);
        $this->assertInstanceOf(TradeGoodsDetail::class, $foundGoodsDetail);
        $this->assertSame('订单1的商品', $foundGoodsDetail->getGoodsName());
        $this->assertInstanceOf(TradeOrder::class, $foundGoodsDetail->getTradeOrder());
        $this->assertSame($tradeOrder1->getId(), $foundGoodsDetail->getTradeOrder()->getId());
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
        $entityManager->createQuery('DELETE FROM ' . TradeGoodsDetail::class)->execute();
        $entityManager->createQuery('DELETE FROM ' . TradeOrder::class)->execute();
        $entityManager->createQuery('DELETE FROM ' . FundAuthOrder::class)->execute();
        $entityManager->createQuery('DELETE FROM ' . Account::class)->execute();
    }

    public function testFindOneByOrderByShouldTestAllFieldsSorting(): void
    {
        $this->clearDatabase();

        $tradeOrder1 = $this->createTradeOrder('order_1');
        $tradeOrder2 = $this->createTradeOrder('order_2');

        $goods1 = new TradeGoodsDetail();
        $goods1->setTradeOrder($tradeOrder1);
        $goods1->setGoodsId('goods_z');
        $goods1->setGoodsName('A商品');
        $goods1->setQuantity(2);
        $goods1->setPrice('50.00');
        $goods1->setGoodsCategory('电子产品');
        $goods1->setCategoryTree('数码/电子产品');
        $goods1->setShowUrl('https://example.com/goods/z');
        $this->repository->save($goods1);

        $goods2 = new TradeGoodsDetail();
        $goods2->setTradeOrder($tradeOrder2);
        $goods2->setGoodsId('goods_a');
        $goods2->setGoodsName('Z商品');
        $goods2->setQuantity(1);
        $goods2->setPrice('30.00');
        $goods2->setGoodsCategory('书籍');
        $goods2->setCategoryTree('教育/书籍');
        $goods2->setShowUrl('https://example.com/goods/a');
        $this->repository->save($goods2);

        $firstByGoodsId = $this->repository->findOneBy([], ['goodsId' => 'ASC']);
        $this->assertInstanceOf(TradeGoodsDetail::class, $firstByGoodsId);
        $this->assertSame('goods_a', $firstByGoodsId->getGoodsId());

        $firstByGoodsName = $this->repository->findOneBy([], ['goodsName' => 'ASC']);
        $this->assertInstanceOf(TradeGoodsDetail::class, $firstByGoodsName);
        $this->assertSame('A商品', $firstByGoodsName->getGoodsName());

        $firstByQuantity = $this->repository->findOneBy([], ['quantity' => 'ASC']);
        $this->assertInstanceOf(TradeGoodsDetail::class, $firstByQuantity);
        $this->assertSame(1, $firstByQuantity->getQuantity());

        $firstByPrice = $this->repository->findOneBy([], ['price' => 'ASC']);
        $this->assertInstanceOf(TradeGoodsDetail::class, $firstByPrice);
        $this->assertSame('30.00', $firstByPrice->getPrice());

        $firstByGoodsCategory = $this->repository->findOneBy([], ['goodsCategory' => 'ASC']);
        $this->assertInstanceOf(TradeGoodsDetail::class, $firstByGoodsCategory);
        $this->assertSame('书籍', $firstByGoodsCategory->getGoodsCategory());

        $firstByCategoryTree = $this->repository->findOneBy([], ['categoryTree' => 'ASC']);
        $this->assertInstanceOf(TradeGoodsDetail::class, $firstByCategoryTree);
        $this->assertSame('教育/书籍', $firstByCategoryTree->getCategoryTree());

        $firstByShowUrl = $this->repository->findOneBy([], ['showUrl' => 'ASC']);
        $this->assertInstanceOf(TradeGoodsDetail::class, $firstByShowUrl);
        $this->assertSame('https://example.com/goods/a', $firstByShowUrl->getShowUrl());
    }

    public function testCountByAssociationTradeOrderShouldReturnCorrectNumber(): void
    {
        $this->clearDatabase();

        $tradeOrder1 = $this->createTradeOrder('order_1');
        $tradeOrder2 = $this->createTradeOrder('order_2');

        $goods1 = new TradeGoodsDetail();
        $goods1->setTradeOrder($tradeOrder1);
        $goods1->setGoodsId('goods_1_1');
        $goods1->setGoodsName('订单1商品1');
        $goods1->setQuantity(1);
        $goods1->setPrice('10.00');
        $this->repository->save($goods1);

        $goods2 = new TradeGoodsDetail();
        $goods2->setTradeOrder($tradeOrder1);
        $goods2->setGoodsId('goods_1_2');
        $goods2->setGoodsName('订单1商品2');
        $goods2->setQuantity(2);
        $goods2->setPrice('20.00');
        $this->repository->save($goods2);

        $goods3 = new TradeGoodsDetail();
        $goods3->setTradeOrder($tradeOrder2);
        $goods3->setGoodsId('goods_2_1');
        $goods3->setGoodsName('订单2商品');
        $goods3->setQuantity(1);
        $goods3->setPrice('30.00');
        $this->repository->save($goods3);

        $order1Count = $this->repository->count(['tradeOrder' => $tradeOrder1]);
        $this->assertSame(2, $order1Count);

        $order2Count = $this->repository->count(['tradeOrder' => $tradeOrder2]);
        $this->assertSame(1, $order2Count);
    }

    public function testFindByNullableFieldsShouldSupportIsNullQueries(): void
    {
        $this->clearDatabase();

        $tradeOrder = $this->createTradeOrder();

        $goodsWithAllNullables = new TradeGoodsDetail();
        $goodsWithAllNullables->setTradeOrder($tradeOrder);
        $goodsWithAllNullables->setGoodsId('goods_with_all');
        $goodsWithAllNullables->setGoodsName('有所有可空字段的商品');
        $goodsWithAllNullables->setQuantity(1);
        $goodsWithAllNullables->setPrice('10.00');
        $goodsWithAllNullables->setGoodsCategory('电子产品');
        $goodsWithAllNullables->setCategoryTree('数码/电子产品');
        $goodsWithAllNullables->setShowUrl('https://example.com/goods/with_all');
        $this->repository->save($goodsWithAllNullables);

        $goodsWithoutNullables = new TradeGoodsDetail();
        $goodsWithoutNullables->setTradeOrder($tradeOrder);
        $goodsWithoutNullables->setGoodsId('goods_without');
        $goodsWithoutNullables->setGoodsName('无可空字段的商品');
        $goodsWithoutNullables->setQuantity(2);
        $goodsWithoutNullables->setPrice('20.00');
        $this->repository->save($goodsWithoutNullables);

        $goodsWithoutGoodsCategory = $this->repository->findBy(['goodsCategory' => null]);
        $this->assertCount(1, $goodsWithoutGoodsCategory);
        $this->assertSame('无可空字段的商品', $goodsWithoutGoodsCategory[0]->getGoodsName());

        $goodsWithoutCategoryTree = $this->repository->findBy(['categoryTree' => null]);
        $this->assertCount(1, $goodsWithoutCategoryTree);
        $this->assertSame('无可空字段的商品', $goodsWithoutCategoryTree[0]->getGoodsName());

        $goodsWithoutShowUrl = $this->repository->findBy(['showUrl' => null]);
        $this->assertCount(1, $goodsWithoutShowUrl);
        $this->assertSame('无可空字段的商品', $goodsWithoutShowUrl[0]->getGoodsName());
    }

    public function testCountByNullableFieldsShouldSupportIsNullQueries(): void
    {
        $this->clearDatabase();

        $tradeOrder = $this->createTradeOrder();

        $goodsWithAllNullables = new TradeGoodsDetail();
        $goodsWithAllNullables->setTradeOrder($tradeOrder);
        $goodsWithAllNullables->setGoodsId('goods_with_all');
        $goodsWithAllNullables->setGoodsName('有所有可空字段的商品');
        $goodsWithAllNullables->setQuantity(1);
        $goodsWithAllNullables->setPrice('10.00');
        $goodsWithAllNullables->setGoodsCategory('电子产品');
        $goodsWithAllNullables->setCategoryTree('数码/电子产品');
        $goodsWithAllNullables->setShowUrl('https://example.com/goods/with_all');
        $this->repository->save($goodsWithAllNullables);

        $goodsWithoutNullables = new TradeGoodsDetail();
        $goodsWithoutNullables->setTradeOrder($tradeOrder);
        $goodsWithoutNullables->setGoodsId('goods_without');
        $goodsWithoutNullables->setGoodsName('无可空字段的商品');
        $goodsWithoutNullables->setQuantity(2);
        $goodsWithoutNullables->setPrice('20.00');
        $this->repository->save($goodsWithoutNullables);

        $countWithoutGoodsCategory = $this->repository->count(['goodsCategory' => null]);
        $this->assertSame(1, $countWithoutGoodsCategory);

        $countWithoutCategoryTree = $this->repository->count(['categoryTree' => null]);
        $this->assertSame(1, $countWithoutCategoryTree);

        $countWithoutShowUrl = $this->repository->count(['showUrl' => null]);
        $this->assertSame(1, $countWithoutShowUrl);
    }

    /**
     * @return ServiceEntityRepository<TradeGoodsDetail>
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

        $fundAuthOrder = new FundAuthOrder();
        $fundAuthOrder->setAccount($account);
        $fundAuthOrder->setOutOrderNo('test_fund_order_' . uniqid());
        $fundAuthOrder->setOutRequestNo('test_fund_request_' . uniqid());
        $fundAuthOrder->setOrderTitle('测试预授权订单');
        $fundAuthOrder->setAmount('100.00');
        $fundAuthOrder->setStatus(FundAuthOrderStatus::INIT);

        $tradeOrder = new TradeOrder();
        $tradeOrder->setAccount($account);
        $tradeOrder->setFundAuthOrder($fundAuthOrder);
        $tradeOrder->setOutTradeNo('test_trade_' . uniqid());
        $tradeOrder->setSubject('测试交易订单');
        $tradeOrder->setTotalAmount('100.00');

        $entity = new TradeGoodsDetail();
        $entity->setTradeOrder($tradeOrder);
        $entity->setGoodsId('test_goods_' . uniqid());
        $entity->setGoodsName('测试商品');
        $entity->setQuantity(1);
        $entity->setPrice('50.00');
        $entity->setGoodsCategory('电子产品');
        $entity->setCategoryTree('数码/电子产品');
        $entity->setShowUrl('https://example.com/goods/test');

        return $entity;
    }
}
