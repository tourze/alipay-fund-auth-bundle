<?php

namespace AlipayFundAuthBundle\DataFixtures;

use AlipayFundAuthBundle\Entity\Account;
use AlipayFundAuthBundle\Entity\FundAuthOrder;
use AlipayFundAuthBundle\Enum\FundAuthOrderStatus;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\Attribute\When;

#[When(env: 'test')]
#[When(env: 'dev')]
class FundAuthOrderFixtures extends Fixture implements DependentFixtureInterface
{
    public const FUND_AUTH_ORDER_TEST = 'fund-auth-order-test';

    public function load(ObjectManager $manager): void
    {
        $account = $this->getReference(AccountFixtures::ACCOUNT_TEST, Account::class);

        $fundAuthOrder = new FundAuthOrder();
        $fundAuthOrder->setAccount($account);
        $fundAuthOrder->setOutOrderNo('TEST_ORDER_' . time());
        $fundAuthOrder->setOutRequestNo('TEST_REQUEST_' . time());
        $fundAuthOrder->setOrderTitle('测试预授权订单');
        $fundAuthOrder->setAmount('100.00');
        $fundAuthOrder->setProductCode(FundAuthOrder::PRODUCT_CODE_PREAUTH_PAY);
        $fundAuthOrder->setPayeeUserId('2088000000000001');
        $fundAuthOrder->setPayeeLogonId('test@alipay.com');
        $fundAuthOrder->setPayTimeout('15m');
        $fundAuthOrder->setTimeExpress('30d');
        $fundAuthOrder->setSceneCode('HOTEL_DEPOSIT');
        $fundAuthOrder->setStatus(FundAuthOrderStatus::INIT);

        $manager->persist($fundAuthOrder);
        $manager->flush();

        $this->addReference(self::FUND_AUTH_ORDER_TEST, $fundAuthOrder);
    }

    public function getDependencies(): array
    {
        return [
            AccountFixtures::class,
        ];
    }
}
