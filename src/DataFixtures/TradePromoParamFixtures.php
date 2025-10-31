<?php

namespace AlipayFundAuthBundle\DataFixtures;

use AlipayFundAuthBundle\Entity\TradeOrder;
use AlipayFundAuthBundle\Entity\TradePromoParam;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\Attribute\When;

#[When(env: 'test')]
#[When(env: 'dev')]
class TradePromoParamFixtures extends Fixture implements DependentFixtureInterface
{
    public const TRADE_PROMO_PARAM_TEST = 'trade-promo-param-test';

    public function load(ObjectManager $manager): void
    {
        $tradeOrder = $this->getReference(TradeOrderFixtures::TRADE_ORDER_TEST, TradeOrder::class);

        $promoParams = new TradePromoParam();
        $promoParams->setTradeOrder($tradeOrder);
        $promoParams->setActualOrderTime(new \DateTimeImmutable());

        $manager->persist($promoParams);
        $manager->flush();

        $this->addReference(self::TRADE_PROMO_PARAM_TEST, $promoParams);
    }

    public function getDependencies(): array
    {
        return [
            TradeOrderFixtures::class,
        ];
    }
}
