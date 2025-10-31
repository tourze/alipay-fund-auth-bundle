<?php

namespace AlipayFundAuthBundle\DataFixtures;

use AlipayFundAuthBundle\Entity\TradeOrder;
use AlipayFundAuthBundle\Entity\TradeVoucherDetail;
use AlipayFundAuthBundle\Enum\VoucherType;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\Attribute\When;

#[When(env: 'test')]
#[When(env: 'dev')]
class TradeVoucherDetailFixtures extends Fixture implements DependentFixtureInterface
{
    public const TRADE_VOUCHER_DETAIL_TEST = 'trade-voucher-detail-test';

    public function load(ObjectManager $manager): void
    {
        $tradeOrder = $this->getReference(TradeOrderFixtures::TRADE_ORDER_TEST, TradeOrder::class);

        $voucherDetail = new TradeVoucherDetail();
        $voucherDetail->setTradeOrder($tradeOrder);
        $voucherDetail->setVoucherId('VOUCHER001');
        $voucherDetail->setName('测试优惠券');
        $voucherDetail->setType(VoucherType::ALIPAY_FIX_VOUCHER);
        $voucherDetail->setAmount('10.00');
        $voucherDetail->setMerchantContribute('5.00');
        $voucherDetail->setOtherContribute('5.00');
        $voucherDetail->setMemo('测试优惠券使用');

        $manager->persist($voucherDetail);
        $manager->flush();

        $this->addReference(self::TRADE_VOUCHER_DETAIL_TEST, $voucherDetail);
    }

    public function getDependencies(): array
    {
        return [
            TradeOrderFixtures::class,
        ];
    }
}
