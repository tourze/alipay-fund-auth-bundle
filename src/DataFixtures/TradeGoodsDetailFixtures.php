<?php

namespace AlipayFundAuthBundle\DataFixtures;

use AlipayFundAuthBundle\Entity\TradeGoodsDetail;
use AlipayFundAuthBundle\Entity\TradeOrder;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\Attribute\When;

#[When(env: 'test')]
#[When(env: 'dev')]
class TradeGoodsDetailFixtures extends Fixture implements DependentFixtureInterface
{
    public const TRADE_GOODS_DETAIL_TEST = 'trade-goods-detail-test';

    public function load(ObjectManager $manager): void
    {
        $tradeOrder = $this->getReference(TradeOrderFixtures::TRADE_ORDER_TEST, TradeOrder::class);

        $goodsDetail = new TradeGoodsDetail();
        $goodsDetail->setTradeOrder($tradeOrder);
        $goodsDetail->setGoodsId('GOODS001');
        $goodsDetail->setGoodsName('测试商品');
        $goodsDetail->setQuantity(1);
        $goodsDetail->setPrice('100.00');
        $goodsDetail->setGoodsCategory('HOTEL_SERVICE');

        $manager->persist($goodsDetail);
        $manager->flush();

        $this->addReference(self::TRADE_GOODS_DETAIL_TEST, $goodsDetail);
    }

    public function getDependencies(): array
    {
        return [
            TradeOrderFixtures::class,
        ];
    }
}
