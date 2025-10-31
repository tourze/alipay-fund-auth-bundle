<?php

namespace AlipayFundAuthBundle\DataFixtures;

use AlipayFundAuthBundle\Entity\TradeFundBill;
use AlipayFundAuthBundle\Entity\TradeOrder;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\Attribute\When;

#[When(env: 'test')]
#[When(env: 'dev')]
class TradeFundBillFixtures extends Fixture implements DependentFixtureInterface
{
    public const TRADE_FUND_BILL_TEST = 'trade-fund-bill-test';

    public function load(ObjectManager $manager): void
    {
        $tradeOrder = $this->getReference(TradeOrderFixtures::TRADE_ORDER_TEST, TradeOrder::class);

        $fundBill = new TradeFundBill();
        $fundBill->setTradeOrder($tradeOrder);
        $fundBill->setFundChannel('ALIPAYACCOUNT');
        $fundBill->setAmount('100.00');
        $fundBill->setRealAmount('100.00');

        $manager->persist($fundBill);
        $manager->flush();

        $this->addReference(self::TRADE_FUND_BILL_TEST, $fundBill);
    }

    public function getDependencies(): array
    {
        return [
            TradeOrderFixtures::class,
        ];
    }
}
