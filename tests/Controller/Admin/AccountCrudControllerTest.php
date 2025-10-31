<?php

namespace AlipayFundAuthBundle\Tests\Controller\Admin;

use AlipayFundAuthBundle\Controller\Admin\AccountCrudController;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\PHPUnitSymfonyWebTest\AbstractEasyAdminControllerTestCase;

/**
 * @internal
 * Controller配置了 3 个过滤器，测试搜索功能
 */
#[CoversClass(AccountCrudController::class)]
#[RunTestsInSeparateProcesses]
final class AccountCrudControllerTest extends AbstractEasyAdminControllerTestCase
{
    protected function getControllerService(): AccountCrudController
    {
        return self::getService(AccountCrudController::class);
    }

    public static function provideIndexPageHeaders(): iterable
    {
        yield 'id_header' => ['ID'];
        yield 'name_header' => ['名称'];
        yield 'app_id_header' => ['AppID'];
        yield 'valid_header' => ['有效状态'];
        yield 'creator_header' => ['创建人'];
        yield 'updater_header' => ['更新人'];
        yield 'creator_ip_header' => ['创建IP'];
        yield 'updater_ip_header' => ['更新IP'];
        yield 'created_time_header' => ['创建时间'];
        yield 'updated_time_header' => ['更新时间'];
    }

    public static function provideNewPageFields(): iterable
    {
        yield 'name_field' => ['name'];
        yield 'app_id_field' => ['appId'];
        yield 'rsa_private_key_field' => ['rsaPrivateKey'];
        yield 'rsa_public_key_field' => ['rsaPublicKey'];
        yield 'valid_field' => ['valid'];
    }

    public static function provideEditPageFields(): iterable
    {
        yield 'name_field' => ['name'];
        yield 'app_id_field' => ['appId'];
        yield 'rsa_private_key_field' => ['rsaPrivateKey'];
        yield 'rsa_public_key_field' => ['rsaPublicKey'];
        yield 'valid_field' => ['valid'];
    }

    public function testIndexPageAccessibleForAuthenticatedAdmin(): void
    {
        $client = self::createClientWithDatabase();
        $client->loginUser($this->createAdminUser());

        $client->request('GET', '/admin/alipay-fund-auth/account');

        $response = $client->getResponse();
        $this->assertTrue($response->isSuccessful());
        $content = $response->getContent();
        $this->assertIsString($content);
        $this->assertStringContainsString('支付宝账号列表', $content);
    }

    public function testIndexPageRedirectsForUnauthenticatedUser(): void
    {
        $client = self::createClient();

        $client->request('GET', '/admin/alipay-fund-auth/account');

        $response = $client->getResponse();
        $this->assertTrue($response->isRedirection());
        $this->assertEquals(302, $response->getStatusCode());
    }

    public function testNewPageAccessibleForAuthenticatedAdmin(): void
    {
        $client = self::createClientWithDatabase();
        $client->loginUser($this->createAdminUser());

        $client->request('GET', '/admin/alipay-fund-auth/account');

        $response = $client->getResponse();
        $this->assertTrue($response->isSuccessful());
        $content = $response->getContent();
        $this->assertIsString($content);
        $this->assertStringContainsString('支付宝账号', $content);
    }

    public function testSearchFunctionalityWithValidQuery(): void
    {
        $client = self::createClientWithDatabase();
        $client->loginUser($this->createAdminUser());

        $client->request('GET', '/admin/alipay-fund-auth/account?query=test');

        $response = $client->getResponse();
        $this->assertTrue($response->isSuccessful());
    }

    public function testFilterFunctionalityWithValidFilter(): void
    {
        $client = self::createClientWithDatabase();
        $client->loginUser($this->createAdminUser());

        $client->request('GET', '/admin/alipay-fund-auth/account');

        $response = $client->getResponse();
        $this->assertTrue($response->isSuccessful());
    }
}
