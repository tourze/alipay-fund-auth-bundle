<?php

namespace AlipayFundAuthBundle\DataFixtures;

use AlipayFundAuthBundle\Entity\FundAuthOrder;
use AlipayFundAuthBundle\Entity\FundAuthPostPayment;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\Attribute\When;

#[When(env: 'test')]
#[When(env: 'dev')]
class FundAuthPostPaymentFixtures extends Fixture implements DependentFixtureInterface
{
    public const FUND_AUTH_POST_PAYMENT_TEST = 'fund-auth-post-payment-test';

    public function load(ObjectManager $manager): void
    {
        $fundAuthOrder = $this->getReference(FundAuthOrderFixtures::FUND_AUTH_ORDER_TEST, FundAuthOrder::class);

        $postPayment = new FundAuthPostPayment();
        $postPayment->setFundAuthOrder($fundAuthOrder);
        $postPayment->setName('房间迷你吧消费');
        $postPayment->setAmount('25.50');
        $postPayment->setDescription('房间内迷你吧消费账单');

        $manager->persist($postPayment);
        $manager->flush();

        $this->addReference(self::FUND_AUTH_POST_PAYMENT_TEST, $postPayment);
    }

    public function getDependencies(): array
    {
        return [
            FundAuthOrderFixtures::class,
        ];
    }
}
