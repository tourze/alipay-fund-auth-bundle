<?php

namespace AlipayFundAuthBundle\DataFixtures;

use AlipayFundAuthBundle\Entity\TradeExtendParam;
use AlipayFundAuthBundle\Entity\TradeOrder;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\Attribute\When;

#[When(env: 'test')]
#[When(env: 'dev')]
class TradeExtendParamFixtures extends Fixture implements DependentFixtureInterface
{
    public const TRADE_EXTEND_PARAM_TEST = 'trade-extend-param-test';

    public function load(ObjectManager $manager): void
    {
        $tradeOrder = $this->getReference(TradeOrderFixtures::TRADE_ORDER_TEST, TradeOrder::class);

        $extendParams = new TradeExtendParam();
        $extendParams->setTradeOrder($tradeOrder);
        $extendParams->setSysServiceProviderId('SYS001');
        $extendParams->setSpecifiedSellerName('测试商户');
        $extendParams->setCardType('DEBIT');

        $manager->persist($extendParams);
        $manager->flush();

        $this->addReference(self::TRADE_EXTEND_PARAM_TEST, $extendParams);
    }

    public function getDependencies(): array
    {
        return [
            TradeOrderFixtures::class,
        ];
    }
}
