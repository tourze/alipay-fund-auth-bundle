<?php

namespace AlipayFundAuthBundle\DataFixtures;

use AlipayFundAuthBundle\Entity\FundAuthOrder;
use AlipayFundAuthBundle\Entity\FundAuthUnfreezeLog;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\Attribute\When;

#[When(env: 'test')]
#[When(env: 'dev')]
class FundAuthUnfreezeLogFixtures extends Fixture implements DependentFixtureInterface
{
    public const FUND_AUTH_UNFREEZE_LOG_TEST = 'fund-auth-unfreeze-log-test';

    public function load(ObjectManager $manager): void
    {
        $fundAuthOrder = $this->getReference(FundAuthOrderFixtures::FUND_AUTH_ORDER_TEST, FundAuthOrder::class);

        $unfreezeLog = new FundAuthUnfreezeLog();
        $unfreezeLog->setFundAuthOrder($fundAuthOrder);
        $unfreezeLog->setOutRequestNo('UNFREEZE_' . time());
        $unfreezeLog->setAmount('50.00');
        $unfreezeLog->setRemark('部分解冻测试');
        $unfreezeLog->setStatus('SUCCESS');
        $unfreezeLog->setGmtTrans(new \DateTimeImmutable());
        $unfreezeLog->setCreditAmount('30.00');
        $unfreezeLog->setFundAmount('20.00');

        $manager->persist($unfreezeLog);
        $manager->flush();

        $this->addReference(self::FUND_AUTH_UNFREEZE_LOG_TEST, $unfreezeLog);
    }

    public function getDependencies(): array
    {
        return [
            FundAuthOrderFixtures::class,
        ];
    }
}
