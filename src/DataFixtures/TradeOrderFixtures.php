<?php

namespace AlipayFundAuthBundle\DataFixtures;

use AlipayFundAuthBundle\Entity\Account;
use AlipayFundAuthBundle\Entity\FundAuthOrder;
use AlipayFundAuthBundle\Entity\TradeOrder;
use AlipayFundAuthBundle\Enum\AuthConfirmMode;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\Attribute\When;

#[When(env: 'test')]
#[When(env: 'dev')]
class TradeOrderFixtures extends Fixture implements DependentFixtureInterface
{
    public const TRADE_ORDER_TEST = 'trade-order-test';

    public function load(ObjectManager $manager): void
    {
        $account = $this->getReference(AccountFixtures::ACCOUNT_TEST, Account::class);
        $fundAuthOrder = $this->getReference(FundAuthOrderFixtures::FUND_AUTH_ORDER_TEST, FundAuthOrder::class);

        $tradeOrder = new TradeOrder();
        $tradeOrder->setAccount($account);
        $tradeOrder->setFundAuthOrder($fundAuthOrder);
        $tradeOrder->setOutTradeNo('TRADE_' . time());
        $tradeOrder->setTotalAmount('100.00');
        $tradeOrder->setSubject('测试预授权交易订单');
        $tradeOrder->setProductCode(FundAuthOrder::PRODUCT_CODE_PREAUTH_PAY);
        $tradeOrder->setAuthConfirmMode(AuthConfirmMode::COMPLETE);
        $tradeOrder->setStoreId('STORE001');
        $tradeOrder->setTerminalId('TERM001');

        $manager->persist($tradeOrder);
        $manager->flush();

        $this->addReference(self::TRADE_ORDER_TEST, $tradeOrder);
    }

    public function getDependencies(): array
    {
        return [
            AccountFixtures::class,
            FundAuthOrderFixtures::class,
        ];
    }
}
