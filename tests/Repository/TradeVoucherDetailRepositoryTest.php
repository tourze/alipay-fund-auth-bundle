<?php

namespace AlipayFundAuthBundle\Tests\Repository;

use AlipayFundAuthBundle\Entity\Account;
use AlipayFundAuthBundle\Entity\FundAuthOrder;
use AlipayFundAuthBundle\Entity\TradeOrder;
use AlipayFundAuthBundle\Entity\TradeVoucherDetail;
use AlipayFundAuthBundle\Enum\FundAuthOrderStatus;
use AlipayFundAuthBundle\Enum\VoucherType;
use AlipayFundAuthBundle\Repository\AccountRepository;
use AlipayFundAuthBundle\Repository\FundAuthOrderRepository;
use AlipayFundAuthBundle\Repository\TradeOrderRepository;
use AlipayFundAuthBundle\Repository\TradeVoucherDetailRepository;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\PHPUnitSymfonyKernelTest\AbstractRepositoryTestCase;

/**
 * @internal
 */
#[CoversClass(TradeVoucherDetailRepository::class)]
#[RunTestsInSeparateProcesses]
final class TradeVoucherDetailRepositoryTest extends AbstractRepositoryTestCase
{
    private TradeVoucherDetailRepository $repository;

    private AccountRepository $accountRepository;

    private FundAuthOrderRepository $fundAuthOrderRepository;

    private TradeOrderRepository $tradeOrderRepository;

    protected function onSetUp(): void
    {
        $this->repository = self::getService(TradeVoucherDetailRepository::class);
        $this->accountRepository = self::getService(AccountRepository::class);
        $this->fundAuthOrderRepository = self::getService(FundAuthOrderRepository::class);
        $this->tradeOrderRepository = self::getService(TradeOrderRepository::class);
    }

    public function testSave(): void
    {
        $tradeOrder = $this->createTradeOrder();

        $voucherDetail = new TradeVoucherDetail();
        $voucherDetail->setTradeOrder($tradeOrder);
        $voucherDetail->setVoucherId('save_test_voucher');
        $voucherDetail->setName('保存测试优惠券');
        $voucherDetail->setType(VoucherType::ALIPAY_FIX_VOUCHER);
        $voucherDetail->setAmount('25.50');
        $voucherDetail->setMerchantContribute('15.00');
        $voucherDetail->setOtherContribute('10.50');
        $voucherDetail->setMemo('测试备注');
        $voucherDetail->setTemplateId('template_123');

        $this->repository->save($voucherDetail);

        $this->assertNotNull($voucherDetail->getId());

        $savedVoucherDetail = $this->repository->find($voucherDetail->getId());
        $this->assertInstanceOf(TradeVoucherDetail::class, $savedVoucherDetail);
        $this->assertSame('save_test_voucher', $savedVoucherDetail->getVoucherId());
        $this->assertSame('保存测试优惠券', $savedVoucherDetail->getName());
        $this->assertSame(VoucherType::ALIPAY_FIX_VOUCHER, $savedVoucherDetail->getType());
        $this->assertSame('25.50', $savedVoucherDetail->getAmount());
        $this->assertSame('15.00', $savedVoucherDetail->getMerchantContribute());
        $this->assertSame('10.50', $savedVoucherDetail->getOtherContribute());
        $this->assertSame('测试备注', $savedVoucherDetail->getMemo());
        $this->assertSame('template_123', $savedVoucherDetail->getTemplateId());
    }

    public function testRemove(): void
    {
        $tradeOrder = $this->createTradeOrder();

        $voucherDetail = new TradeVoucherDetail();
        $voucherDetail->setTradeOrder($tradeOrder);
        $voucherDetail->setVoucherId('remove_test_voucher');
        $voucherDetail->setName('删除测试优惠券');
        $voucherDetail->setType(VoucherType::ALIPAY_DISCOUNT_VOUCHER);
        $voucherDetail->setAmount('5.00');

        $this->repository->save($voucherDetail);
        $savedId = $voucherDetail->getId();

        $this->assertNotNull($this->repository->find($savedId));

        $this->repository->remove($voucherDetail);

        $this->assertNull($this->repository->find($savedId));
    }

    public function testFindOneByWithOrderByShouldRespectOrderParameter(): void
    {
        $this->clearDatabase();

        $tradeOrder = $this->createTradeOrder();

        $voucherDetail1 = new TradeVoucherDetail();
        $voucherDetail1->setTradeOrder($tradeOrder);
        $voucherDetail1->setVoucherId('voucher_z');
        $voucherDetail1->setName('Z优惠券');
        $voucherDetail1->setType(VoucherType::ALIPAY_FIX_VOUCHER);
        $voucherDetail1->setAmount('30.00');
        $this->repository->save($voucherDetail1);

        $voucherDetail2 = new TradeVoucherDetail();
        $voucherDetail2->setTradeOrder($tradeOrder);
        $voucherDetail2->setVoucherId('voucher_a');
        $voucherDetail2->setName('A优惠券');
        $voucherDetail2->setType(VoucherType::ALIPAY_FIX_VOUCHER);
        $voucherDetail2->setAmount('10.00');
        $this->repository->save($voucherDetail2);

        $firstVoucherDetail = $this->repository->findOneBy(['type' => VoucherType::ALIPAY_FIX_VOUCHER], ['name' => 'ASC']);
        $this->assertInstanceOf(TradeVoucherDetail::class, $firstVoucherDetail);
        $this->assertSame('A优惠券', $firstVoucherDetail->getName());

        $lastVoucherDetail = $this->repository->findOneBy(['type' => VoucherType::ALIPAY_FIX_VOUCHER], ['name' => 'DESC']);
        $this->assertInstanceOf(TradeVoucherDetail::class, $lastVoucherDetail);
        $this->assertSame('Z优惠券', $lastVoucherDetail->getName());
    }

    public function testFindByWithNullableFieldShouldSupportIsNullQueries(): void
    {
        $this->clearDatabase();

        $tradeOrder = $this->createTradeOrder();

        $voucherDetailWithMemo = new TradeVoucherDetail();
        $voucherDetailWithMemo->setTradeOrder($tradeOrder);
        $voucherDetailWithMemo->setVoucherId('with_memo_voucher');
        $voucherDetailWithMemo->setName('有备注优惠券');
        $voucherDetailWithMemo->setType(VoucherType::ALIPAY_FIX_VOUCHER);
        $voucherDetailWithMemo->setAmount('10.00');
        $voucherDetailWithMemo->setMemo('测试备注');
        $this->repository->save($voucherDetailWithMemo);

        $voucherDetailWithoutMemo = new TradeVoucherDetail();
        $voucherDetailWithoutMemo->setTradeOrder($tradeOrder);
        $voucherDetailWithoutMemo->setVoucherId('without_memo_voucher');
        $voucherDetailWithoutMemo->setName('无备注优惠券');
        $voucherDetailWithoutMemo->setType(VoucherType::ALIPAY_DISCOUNT_VOUCHER);
        $voucherDetailWithoutMemo->setAmount('15.00');
        $this->repository->save($voucherDetailWithoutMemo);

        $voucherDetailsWithoutMemo = $this->repository->findBy(['memo' => null]);
        $this->assertCount(1, $voucherDetailsWithoutMemo);
        $this->assertSame('无备注优惠券', $voucherDetailsWithoutMemo[0]->getName());
    }

    public function testCountWithNullableFieldShouldSupportIsNullQueries(): void
    {
        $this->clearDatabase();

        $tradeOrder = $this->createTradeOrder();

        $voucherDetailWithTemplateId = new TradeVoucherDetail();
        $voucherDetailWithTemplateId->setTradeOrder($tradeOrder);
        $voucherDetailWithTemplateId->setVoucherId('with_template_voucher');
        $voucherDetailWithTemplateId->setName('有模板优惠券');
        $voucherDetailWithTemplateId->setType(VoucherType::ALIPAY_FIX_VOUCHER);
        $voucherDetailWithTemplateId->setAmount('10.00');
        $voucherDetailWithTemplateId->setTemplateId('template_123');
        $this->repository->save($voucherDetailWithTemplateId);

        $voucherDetailWithoutTemplateId = new TradeVoucherDetail();
        $voucherDetailWithoutTemplateId->setTradeOrder($tradeOrder);
        $voucherDetailWithoutTemplateId->setVoucherId('without_template_voucher');
        $voucherDetailWithoutTemplateId->setName('无模板优惠券');
        $voucherDetailWithoutTemplateId->setType(VoucherType::ALIPAY_DISCOUNT_VOUCHER);
        $voucherDetailWithoutTemplateId->setAmount('15.00');
        $this->repository->save($voucherDetailWithoutTemplateId);

        $countWithoutTemplateId = $this->repository->count(['templateId' => null]);
        $this->assertSame(1, $countWithoutTemplateId);

        $countWithoutMerchantContribute = $this->repository->count(['merchantContribute' => null]);
        $this->assertSame(2, $countWithoutMerchantContribute);
    }

    public function testFindByTradeOrderRelationShouldReturnCorrectResults(): void
    {
        $this->clearDatabase();

        $tradeOrder1 = $this->createTradeOrder('order_1');
        $tradeOrder2 = $this->createTradeOrder('order_2');

        $voucherDetail1 = new TradeVoucherDetail();
        $voucherDetail1->setTradeOrder($tradeOrder1);
        $voucherDetail1->setVoucherId('voucher_1');
        $voucherDetail1->setName('订单1的优惠券');
        $voucherDetail1->setType(VoucherType::ALIPAY_FIX_VOUCHER);
        $voucherDetail1->setAmount('10.00');
        $this->repository->save($voucherDetail1);

        $voucherDetail2 = new TradeVoucherDetail();
        $voucherDetail2->setTradeOrder($tradeOrder2);
        $voucherDetail2->setVoucherId('voucher_2');
        $voucherDetail2->setName('订单2的优惠券');
        $voucherDetail2->setType(VoucherType::ALIPAY_DISCOUNT_VOUCHER);
        $voucherDetail2->setAmount('15.00');
        $this->repository->save($voucherDetail2);

        $tradeOrder1VoucherDetails = $this->repository->findBy(['tradeOrder' => $tradeOrder1]);
        $this->assertCount(1, $tradeOrder1VoucherDetails);
        $this->assertSame('订单1的优惠券', $tradeOrder1VoucherDetails[0]->getName());
    }

    public function testCountByTradeOrderRelationShouldReturnCorrectCount(): void
    {
        $this->clearDatabase();

        $tradeOrder1 = $this->createTradeOrder('order_1');
        $tradeOrder2 = $this->createTradeOrder('order_2');

        $voucherDetail1 = new TradeVoucherDetail();
        $voucherDetail1->setTradeOrder($tradeOrder1);
        $voucherDetail1->setVoucherId('voucher_1_1');
        $voucherDetail1->setName('订单1优惠券1');
        $voucherDetail1->setType(VoucherType::ALIPAY_FIX_VOUCHER);
        $voucherDetail1->setAmount('10.00');
        $this->repository->save($voucherDetail1);

        $voucherDetail2 = new TradeVoucherDetail();
        $voucherDetail2->setTradeOrder($tradeOrder1);
        $voucherDetail2->setVoucherId('voucher_1_2');
        $voucherDetail2->setName('订单1优惠券2');
        $voucherDetail2->setType(VoucherType::ALIPAY_DISCOUNT_VOUCHER);
        $voucherDetail2->setAmount('15.00');
        $this->repository->save($voucherDetail2);

        $voucherDetail3 = new TradeVoucherDetail();
        $voucherDetail3->setTradeOrder($tradeOrder2);
        $voucherDetail3->setVoucherId('voucher_2_1');
        $voucherDetail3->setName('订单2优惠券');
        $voucherDetail3->setType(VoucherType::ALIPAY_FIX_VOUCHER);
        $voucherDetail3->setAmount('20.00');
        $this->repository->save($voucherDetail3);

        $tradeOrder1Count = $this->repository->count(['tradeOrder' => $tradeOrder1]);
        $this->assertSame(2, $tradeOrder1Count);

        $tradeOrder2Count = $this->repository->count(['tradeOrder' => $tradeOrder2]);
        $this->assertSame(1, $tradeOrder2Count);
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
        $entityManager->createQuery('DELETE FROM ' . TradeVoucherDetail::class)->execute();
        $entityManager->createQuery('DELETE FROM ' . TradeOrder::class)->execute();
        $entityManager->createQuery('DELETE FROM ' . FundAuthOrder::class)->execute();
        $entityManager->createQuery('DELETE FROM ' . Account::class)->execute();
    }

    public function testCountByAssociationTradeOrderShouldReturnCorrectNumber(): void
    {
        $this->clearDatabase();

        $tradeOrder1 = $this->createTradeOrder('order_1');
        $tradeOrder2 = $this->createTradeOrder('order_2');

        $voucher1 = new TradeVoucherDetail();
        $voucher1->setTradeOrder($tradeOrder1);
        $voucher1->setVoucherId('voucher_1_1');
        $voucher1->setName('订单1优惠券1');
        $voucher1->setType(VoucherType::ALIPAY_FIX_VOUCHER);
        $voucher1->setAmount('10.00');
        $this->repository->save($voucher1);

        $voucher2 = new TradeVoucherDetail();
        $voucher2->setTradeOrder($tradeOrder1);
        $voucher2->setVoucherId('voucher_1_2');
        $voucher2->setName('订单1优惠券2');
        $voucher2->setType(VoucherType::ALIPAY_DISCOUNT_VOUCHER);
        $voucher2->setAmount('15.00');
        $this->repository->save($voucher2);

        $voucher3 = new TradeVoucherDetail();
        $voucher3->setTradeOrder($tradeOrder2);
        $voucher3->setVoucherId('voucher_2_1');
        $voucher3->setName('订单2优惠券');
        $voucher3->setType(VoucherType::ALIPAY_FIX_VOUCHER);
        $voucher3->setAmount('20.00');
        $this->repository->save($voucher3);

        $order1Count = $this->repository->count(['tradeOrder' => $tradeOrder1]);
        $this->assertSame(2, $order1Count);

        $order2Count = $this->repository->count(['tradeOrder' => $tradeOrder2]);
        $this->assertSame(1, $order2Count);
    }

    public function testFindByNullableFieldsShouldSupportIsNullQueries(): void
    {
        $this->clearDatabase();

        $tradeOrder = $this->createTradeOrder();

        $voucherWithAllNullables = new TradeVoucherDetail();
        $voucherWithAllNullables->setTradeOrder($tradeOrder);
        $voucherWithAllNullables->setVoucherId('voucher_with_all');
        $voucherWithAllNullables->setName('有所有可空字段的优惠券');
        $voucherWithAllNullables->setType(VoucherType::ALIPAY_FIX_VOUCHER);
        $voucherWithAllNullables->setAmount('10.00');
        $voucherWithAllNullables->setMerchantContribute('5.00');
        $voucherWithAllNullables->setOtherContribute('5.00');
        $voucherWithAllNullables->setMemo('测试备注');
        $voucherWithAllNullables->setTemplateId('template_123');
        $voucherWithAllNullables->setPurchaseBuyerContribute('3.00');
        $voucherWithAllNullables->setPurchaseMerchantContribute('2.00');
        $voucherWithAllNullables->setPurchaseAntContribute('1.00');
        $this->repository->save($voucherWithAllNullables);

        $voucherWithoutNullables = new TradeVoucherDetail();
        $voucherWithoutNullables->setTradeOrder($tradeOrder);
        $voucherWithoutNullables->setVoucherId('voucher_without');
        $voucherWithoutNullables->setName('无可空字段的优惠券');
        $voucherWithoutNullables->setType(VoucherType::ALIPAY_DISCOUNT_VOUCHER);
        $voucherWithoutNullables->setAmount('20.00');
        $this->repository->save($voucherWithoutNullables);

        $vouchersWithoutMerchantContribute = $this->repository->findBy(['merchantContribute' => null]);
        $this->assertCount(1, $vouchersWithoutMerchantContribute);
        $this->assertSame('无可空字段的优惠券', $vouchersWithoutMerchantContribute[0]->getName());

        $vouchersWithoutOtherContribute = $this->repository->findBy(['otherContribute' => null]);
        $this->assertCount(1, $vouchersWithoutOtherContribute);
        $this->assertSame('无可空字段的优惠券', $vouchersWithoutOtherContribute[0]->getName());

        $vouchersWithoutMemo = $this->repository->findBy(['memo' => null]);
        $this->assertCount(1, $vouchersWithoutMemo);
        $this->assertSame('无可空字段的优惠券', $vouchersWithoutMemo[0]->getName());

        $vouchersWithoutTemplateId = $this->repository->findBy(['templateId' => null]);
        $this->assertCount(1, $vouchersWithoutTemplateId);
        $this->assertSame('无可空字段的优惠券', $vouchersWithoutTemplateId[0]->getName());

        $vouchersWithoutPurchaseBuyerContribute = $this->repository->findBy(['purchaseBuyerContribute' => null]);
        $this->assertCount(1, $vouchersWithoutPurchaseBuyerContribute);
        $this->assertSame('无可空字段的优惠券', $vouchersWithoutPurchaseBuyerContribute[0]->getName());

        $vouchersWithoutPurchaseMerchantContribute = $this->repository->findBy(['purchaseMerchantContribute' => null]);
        $this->assertCount(1, $vouchersWithoutPurchaseMerchantContribute);
        $this->assertSame('无可空字段的优惠券', $vouchersWithoutPurchaseMerchantContribute[0]->getName());

        $vouchersWithoutPurchaseAntContribute = $this->repository->findBy(['purchaseAntContribute' => null]);
        $this->assertCount(1, $vouchersWithoutPurchaseAntContribute);
        $this->assertSame('无可空字段的优惠券', $vouchersWithoutPurchaseAntContribute[0]->getName());
    }

    public function testFindOneByAssociationTradeOrderShouldReturnMatchingEntity(): void
    {
        $tradeOrder1 = $this->createTradeOrder('order_1');
        $tradeOrder2 = $this->createTradeOrder('order_2');

        $voucher1 = new TradeVoucherDetail();
        $voucher1->setTradeOrder($tradeOrder1);
        $voucher1->setVoucherId('voucher_1');
        $voucher1->setName('订单1的优惠券');
        $voucher1->setType(VoucherType::ALIPAY_FIX_VOUCHER);
        $voucher1->setAmount('10.00');
        $this->repository->save($voucher1);

        $voucher2 = new TradeVoucherDetail();
        $voucher2->setTradeOrder($tradeOrder2);
        $voucher2->setVoucherId('voucher_2');
        $voucher2->setName('订单2的优惠券');
        $voucher2->setType(VoucherType::ALIPAY_DISCOUNT_VOUCHER);
        $voucher2->setAmount('15.00');
        $this->repository->save($voucher2);

        $foundVoucher = $this->repository->findOneBy(['tradeOrder' => $tradeOrder1]);
        $this->assertInstanceOf(TradeVoucherDetail::class, $foundVoucher);
        $this->assertSame('订单1的优惠券', $foundVoucher->getName());
        $this->assertInstanceOf(TradeOrder::class, $foundVoucher->getTradeOrder());
        $this->assertSame($tradeOrder1->getId(), $foundVoucher->getTradeOrder()->getId());
    }

    public function testCreateWithRelationsQueryBuilderShouldFetchWithJoin(): void
    {
        $this->clearDatabase();

        $tradeOrder = $this->createTradeOrder();

        $voucherDetail = new TradeVoucherDetail();
        $voucherDetail->setTradeOrder($tradeOrder);
        $voucherDetail->setVoucherId('test_voucher_with_relation');
        $voucherDetail->setName('测试关联查询优惠券');
        $voucherDetail->setType(VoucherType::ALIPAY_FIX_VOUCHER);
        $voucherDetail->setAmount('50.00');
        $this->repository->save($voucherDetail);

        $queryBuilder = $this->repository->createWithRelationsQueryBuilder();
        $results = $queryBuilder->getQuery()->getResult();

        $this->assertCount(1, $results);
        $this->assertInstanceOf(TradeVoucherDetail::class, $results[0]);
        $this->assertSame('test_voucher_with_relation', $results[0]->getVoucherId());

        $tradeOrderFromResult = $results[0]->getTradeOrder();
        $this->assertInstanceOf(TradeOrder::class, $tradeOrderFromResult);
        $this->assertSame($tradeOrder->getId(), $tradeOrderFromResult->getId());
    }

    public function testFindByVoucherIdShouldReturnMatchingRecords(): void
    {
        $this->clearDatabase();

        $tradeOrder = $this->createTradeOrder();

        $voucherDetail1 = new TradeVoucherDetail();
        $voucherDetail1->setTradeOrder($tradeOrder);
        $voucherDetail1->setVoucherId('duplicate_voucher_id');
        $voucherDetail1->setName('优惠券1');
        $voucherDetail1->setType(VoucherType::ALIPAY_FIX_VOUCHER);
        $voucherDetail1->setAmount('10.00');
        $this->repository->save($voucherDetail1);

        $voucherDetail2 = new TradeVoucherDetail();
        $voucherDetail2->setTradeOrder($tradeOrder);
        $voucherDetail2->setVoucherId('duplicate_voucher_id');
        $voucherDetail2->setName('优惠券2');
        $voucherDetail2->setType(VoucherType::ALIPAY_DISCOUNT_VOUCHER);
        $voucherDetail2->setAmount('20.00');
        $this->repository->save($voucherDetail2);

        $voucherDetail3 = new TradeVoucherDetail();
        $voucherDetail3->setTradeOrder($tradeOrder);
        $voucherDetail3->setVoucherId('unique_voucher_id');
        $voucherDetail3->setName('优惠券3');
        $voucherDetail3->setType(VoucherType::ALIPAY_FIX_VOUCHER);
        $voucherDetail3->setAmount('30.00');
        $this->repository->save($voucherDetail3);

        $duplicateResults = $this->repository->findByVoucherId('duplicate_voucher_id');
        $this->assertCount(2, $duplicateResults);
        $this->assertContainsOnlyInstancesOf(TradeVoucherDetail::class, $duplicateResults);

        $voucherIds = array_map(fn (TradeVoucherDetail $v) => $v->getVoucherId(), $duplicateResults);
        $this->assertSame(['duplicate_voucher_id', 'duplicate_voucher_id'], $voucherIds);

        $uniqueResults = $this->repository->findByVoucherId('unique_voucher_id');
        $this->assertCount(1, $uniqueResults);
        $this->assertSame('unique_voucher_id', $uniqueResults[0]->getVoucherId());
        $this->assertSame('优惠券3', $uniqueResults[0]->getName());

        $notFoundResults = $this->repository->findByVoucherId('non_existent_voucher');
        $this->assertCount(0, $notFoundResults);
        $this->assertSame([], $notFoundResults);
    }

    /**
     * @return ServiceEntityRepository<TradeVoucherDetail>
     */
    protected function getRepository(): ServiceEntityRepository
    {
        return $this->repository;
    }

    protected function createNewEntity(): TradeVoucherDetail
    {
        $tradeOrder = $this->createTradeOrder();

        $voucherDetail = new TradeVoucherDetail();
        $voucherDetail->setTradeOrder($tradeOrder);
        $voucherDetail->setVoucherId('test_voucher_' . uniqid());
        $voucherDetail->setName('测试优惠券');
        $voucherDetail->setType(VoucherType::ALIPAY_FIX_VOUCHER);
        $voucherDetail->setAmount('10.00');

        return $voucherDetail;
    }
}
