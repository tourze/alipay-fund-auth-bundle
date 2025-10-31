<?php

namespace AlipayFundAuthBundle\Tests\Repository;

use AlipayFundAuthBundle\Entity\Account;
use AlipayFundAuthBundle\Repository\AccountRepository;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\PHPUnitSymfonyKernelTest\AbstractRepositoryTestCase;

/**
 * @internal
 */
#[CoversClass(AccountRepository::class)]
#[RunTestsInSeparateProcesses]
final class AccountRepositoryTest extends AbstractRepositoryTestCase
{
    private AccountRepository $repository;

    protected function onSetUp(): void
    {
        $this->repository = self::getService(AccountRepository::class);
    }

    public function testSave(): void
    {
        $account = new Account();
        $account->setName('保存测试账号');
        $account->setAppId('save_test_app_id');
        $account->setValid(true);
        $account->setRsaPrivateKey('private_key_content');
        $account->setRsaPublicKey('public_key_content');

        $this->repository->save($account);

        $this->assertNotNull($account->getId());

        $savedAccount = $this->repository->find($account->getId());
        $this->assertInstanceOf(Account::class, $savedAccount);
        $this->assertSame('保存测试账号', $savedAccount->getName());
        $this->assertSame('save_test_app_id', $savedAccount->getAppId());
        $this->assertTrue($savedAccount->isValid());
        $this->assertSame('private_key_content', $savedAccount->getRsaPrivateKey());
        $this->assertSame('public_key_content', $savedAccount->getRsaPublicKey());
    }

    public function testRemove(): void
    {
        $account = new Account();
        $account->setName('删除测试账号');
        $account->setAppId('remove_test_app_id');

        $this->repository->save($account);
        $savedId = $account->getId();

        $this->assertNotNull($this->repository->find($savedId));

        $this->repository->remove($account);

        $this->assertNull($this->repository->find($savedId));
    }

    public function testFindOneByWithValidFieldShouldReturnEntity(): void
    {
        $account = new Account();
        $account->setName('查询测试账号');
        $account->setAppId('query_test_app_id');

        $this->repository->save($account);

        $foundAccount = $this->repository->findOneBy(['name' => '查询测试账号']);

        $this->assertInstanceOf(Account::class, $foundAccount);
        $this->assertSame('查询测试账号', $foundAccount->getName());
        $this->assertSame('query_test_app_id', $foundAccount->getAppId());
    }

    public function testFindByWithValidNullableFieldShouldReturnCorrectResults(): void
    {
        $this->clearDatabase();

        $accountWithKey = new Account();
        $accountWithKey->setName('有密钥账号');
        $accountWithKey->setAppId('with_key_app_id');
        $accountWithKey->setRsaPrivateKey('some_private_key');
        $this->repository->save($accountWithKey);

        $accountWithoutKey = new Account();
        $accountWithoutKey->setName('无密钥账号');
        $accountWithoutKey->setAppId('without_key_app_id');
        $this->repository->save($accountWithoutKey);

        $accountsWithKey = $this->repository->findBy(['rsaPrivateKey' => 'some_private_key']);
        $this->assertCount(1, $accountsWithKey);
        $this->assertSame('有密钥账号', $accountsWithKey[0]->getName());

        $accountsWithoutKey = $this->repository->findBy(['rsaPrivateKey' => null]);
        $this->assertCount(1, $accountsWithoutKey);
        $this->assertSame('无密钥账号', $accountsWithoutKey[0]->getName());
    }

    public function testCountByValidFieldShouldReturnCorrectCount(): void
    {
        $this->clearDatabase();

        $validAccount = new Account();
        $validAccount->setName('有效账号');
        $validAccount->setAppId('valid_app_id');
        $validAccount->setValid(true);
        $this->repository->save($validAccount);

        $invalidAccount = new Account();
        $invalidAccount->setName('无效账号');
        $invalidAccount->setAppId('invalid_app_id');
        $invalidAccount->setValid(false);
        $this->repository->save($invalidAccount);

        $validCount = $this->repository->count(['valid' => true]);
        $this->assertSame(1, $validCount);

        $invalidCount = $this->repository->count(['valid' => false]);
        $this->assertSame(1, $invalidCount);
    }

    public function testFindWithOrderingShouldReturnOrderedResults(): void
    {
        $this->clearDatabase();

        $account1 = new Account();
        $account1->setName('Z账号');
        $account1->setAppId('z_app_id');
        $this->repository->save($account1);

        $account2 = new Account();
        $account2->setName('A账号');
        $account2->setAppId('a_app_id');
        $this->repository->save($account2);

        $orderedAccounts = $this->repository->findBy([], ['name' => 'ASC']);

        $this->assertCount(2, $orderedAccounts);
        $this->assertSame('A账号', $orderedAccounts[0]->getName());
        $this->assertSame('Z账号', $orderedAccounts[1]->getName());
    }

    public function testFindOneByWithOrderByShouldRespectOrderParameter(): void
    {
        $this->clearDatabase();

        $account1 = new Account();
        $account1->setName('Z账号');
        $account1->setAppId('z_app_id');
        $account1->setValid(true);
        $this->repository->save($account1);

        $account2 = new Account();
        $account2->setName('A账号');
        $account2->setAppId('a_app_id');
        $account2->setValid(true);
        $this->repository->save($account2);

        $firstAccount = $this->repository->findOneBy(['valid' => true], ['name' => 'ASC']);
        $this->assertInstanceOf(Account::class, $firstAccount);
        $this->assertSame('A账号', $firstAccount->getName());

        $lastAccount = $this->repository->findOneBy(['valid' => true], ['name' => 'DESC']);
        $this->assertInstanceOf(Account::class, $lastAccount);
        $this->assertSame('Z账号', $lastAccount->getName());
    }

    public function testFindByWithNullableFieldShouldSupportIsNullQueries(): void
    {
        $this->clearDatabase();

        $accountWithKey = new Account();
        $accountWithKey->setName('有密钥账号');
        $accountWithKey->setAppId('with_key_app_id');
        $accountWithKey->setRsaPrivateKey('some_private_key');
        $this->repository->save($accountWithKey);

        $accountWithoutKey = new Account();
        $accountWithoutKey->setName('无密钥账号');
        $accountWithoutKey->setAppId('without_key_app_id');
        $this->repository->save($accountWithoutKey);

        $accountsWithoutKey = $this->repository->findBy(['rsaPrivateKey' => null]);
        $this->assertCount(1, $accountsWithoutKey);
        $this->assertSame('无密钥账号', $accountsWithoutKey[0]->getName());
    }

    public function testCountWithNullableFieldShouldSupportIsNullQueries(): void
    {
        $this->clearDatabase();

        $accountWithKey = new Account();
        $accountWithKey->setName('有密钥账号');
        $accountWithKey->setAppId('with_key_app_id');
        $accountWithKey->setRsaPrivateKey('some_private_key');
        $this->repository->save($accountWithKey);

        $accountWithoutKey = new Account();
        $accountWithoutKey->setName('无密钥账号');
        $accountWithoutKey->setAppId('without_key_app_id');
        $this->repository->save($accountWithoutKey);

        $countWithoutKey = $this->repository->count(['rsaPrivateKey' => null]);
        $this->assertSame(1, $countWithoutKey);

        $countWithKey = $this->repository->count(['rsaPublicKey' => null]);
        $this->assertSame(2, $countWithKey);
    }

    public function testFindByWithValidNullFieldShouldSupportIsNullQueries(): void
    {
        $this->clearDatabase();

        $accountWithValid = new Account();
        $accountWithValid->setName('有效状态账号');
        $accountWithValid->setAppId('test_app_id_1');
        $accountWithValid->setValid(true);
        $this->repository->save($accountWithValid);

        $accountWithoutValid = new Account();
        $accountWithoutValid->setName('无有效状态账号');
        $accountWithoutValid->setAppId('test_app_id_2');
        $accountWithoutValid->setValid(null);
        $this->repository->save($accountWithoutValid);

        $accountsWithoutValid = $this->repository->findBy(['valid' => null]);
        $this->assertCount(1, $accountsWithoutValid);
        $this->assertSame('无有效状态账号', $accountsWithoutValid[0]->getName());
    }

    public function testCountWithValidFieldNullShouldSupportIsNullQueries(): void
    {
        $this->clearDatabase();

        $accountWithValid = new Account();
        $accountWithValid->setName('有效状态账号');
        $accountWithValid->setAppId('valid_app_id');
        $accountWithValid->setValid(true);
        $this->repository->save($accountWithValid);

        $accountWithoutValid = new Account();
        $accountWithoutValid->setName('无效状态账号');
        $accountWithoutValid->setAppId('invalid_app_id');
        $accountWithoutValid->setValid(null);
        $this->repository->save($accountWithoutValid);

        $countWithNullValid = $this->repository->count(['valid' => null]);
        $this->assertSame(1, $countWithNullValid);
    }

    public function testFindByWithRsaPublicKeyNullFieldShouldSupportIsNullQueries(): void
    {
        $this->clearDatabase();

        $accountWithPublicKey = new Account();
        $accountWithPublicKey->setName('有公钥账号');
        $accountWithPublicKey->setAppId('with_public_key_app_id');
        $accountWithPublicKey->setRsaPublicKey('public_key_content');
        $this->repository->save($accountWithPublicKey);

        $accountWithoutPublicKey = new Account();
        $accountWithoutPublicKey->setName('无公钥账号');
        $accountWithoutPublicKey->setAppId('without_public_key_app_id');
        $this->repository->save($accountWithoutPublicKey);

        $accountsWithoutPublicKey = $this->repository->findBy(['rsaPublicKey' => null]);
        $this->assertCount(1, $accountsWithoutPublicKey);
        $this->assertSame('无公钥账号', $accountsWithoutPublicKey[0]->getName());
    }

    public function testCountWithRsaPrivateKeyNullFieldShouldSupportIsNullQueries(): void
    {
        $this->clearDatabase();

        $accountWithKey = new Account();
        $accountWithKey->setName('有私钥账号');
        $accountWithKey->setAppId('test_app_id_with_key');
        $accountWithKey->setRsaPrivateKey('some_private_key');
        $this->repository->save($accountWithKey);

        $accountWithoutKey = new Account();
        $accountWithoutKey->setName('无私钥账号');
        $accountWithoutKey->setAppId('test_app_id_without_key');
        $this->repository->save($accountWithoutKey);

        $countWithoutKey = $this->repository->count(['rsaPrivateKey' => null]);
        $this->assertSame(1, $countWithoutKey);
    }

    private function clearDatabase(): void
    {
        $entityManager = self::getEntityManager();
        $entityManager->createQuery('DELETE FROM ' . Account::class)->execute();
    }

    /**
     * @return ServiceEntityRepository<Account>
     */
    protected function getRepository(): ServiceEntityRepository
    {
        return $this->repository;
    }

    protected function createNewEntity(): object
    {
        $account = new Account();
        $account->setName('test_account_' . uniqid());
        $account->setAppId('test_app_' . uniqid());
        $account->setValid(true);

        return $account;
    }
}
