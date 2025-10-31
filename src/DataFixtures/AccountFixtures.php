<?php

namespace AlipayFundAuthBundle\DataFixtures;

use AlipayFundAuthBundle\Entity\Account;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\Attribute\When;

#[When(env: 'test')]
#[When(env: 'dev')]
class AccountFixtures extends Fixture
{
    public const ACCOUNT_TEST = 'account-test';

    public function load(ObjectManager $manager): void
    {
        $account = new Account();
        $account->setName('测试支付宝账号');
        $account->setAppId('2021000000000001');
        $account->setRsaPrivateKey('-----BEGIN PRIVATE KEY-----
MIIEvQIBADANBgkqhkiG9w0BAQEFAASCBKcwggSjAgEAAoIBAQC...
-----END PRIVATE KEY-----');
        $account->setRsaPublicKey('-----BEGIN PUBLIC KEY-----
MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAwYVOBT...
-----END PUBLIC KEY-----');
        $account->setValid(true);

        $manager->persist($account);
        $manager->flush();

        $this->addReference(self::ACCOUNT_TEST, $account);
    }
}
