<?php

namespace AlipayFundAuthBundle\Tests\Entity;

use AlipayFundAuthBundle\Entity\Account;
use PHPUnit\Framework\Attributes\CoversClass;
use Tourze\PHPUnitDoctrineEntity\AbstractEntityTestCase;

/**
 * @internal
 */
#[CoversClass(Account::class)]
final class AccountTest extends AbstractEntityTestCase
{
    protected function createEntity(): object
    {
        return new Account();
    }

    /**
     * @return iterable<array{string, mixed}>
     */
    public static function propertiesProvider(): iterable
    {
        yield 'name' => ['name', 'Test Account'];
        yield 'appId' => ['appId', 'test_app_123'];
        yield 'rsaPrivateKey' => ['rsaPrivateKey', '-----BEGIN PRIVATE KEY-----\nMIIEvQIBADANB\n-----END PRIVATE KEY-----'];
        yield 'rsaPublicKey' => ['rsaPublicKey', '-----BEGIN PUBLIC KEY-----\nMIIBIjANBgkqhki\n-----END PUBLIC KEY-----'];
        yield 'valid' => ['valid', true];
        yield 'createdBy' => ['createdBy', 'admin'];
        yield 'updatedBy' => ['updatedBy', 'admin'];
        yield 'createTime' => ['createTime', new \DateTimeImmutable()];
        yield 'updateTime' => ['updateTime', new \DateTimeImmutable()];
        yield 'createdFromIp' => ['createdFromIp', '192.168.1.1'];
        yield 'updatedFromIp' => ['updatedFromIp', '192.168.1.2'];
    }

    public function testToStringWithNameAndIdReturnsName(): void
    {
        $account = new Account();
        $name = 'Test Account';
        $id = '123456789';

        $account->setId($id);
        $account->setName($name);

        $this->assertEquals($name, (string) $account);
    }

    public function testToStringWithoutIdReturnsEmptyString(): void
    {
        $account = new Account();
        $account->setName('Test Account');

        $this->assertEquals('', (string) $account);
    }
}
