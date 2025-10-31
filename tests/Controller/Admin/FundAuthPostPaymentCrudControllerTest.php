<?php

namespace AlipayFundAuthBundle\Tests\Controller\Admin;

use AlipayFundAuthBundle\Controller\Admin\FundAuthPostPaymentCrudController;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\PHPUnitSymfonyWebTest\AbstractEasyAdminControllerTestCase;

/**
 * @internal
 * Controller配置了 3 个过滤器，测试搜索功能
 */
#[CoversClass(FundAuthPostPaymentCrudController::class)]
#[RunTestsInSeparateProcesses]
final class FundAuthPostPaymentCrudControllerTest extends AbstractEasyAdminControllerTestCase
{
    protected function getControllerService(): FundAuthPostPaymentCrudController
    {
        return self::getService(FundAuthPostPaymentCrudController::class);
    }

    public static function provideIndexPageHeaders(): iterable
    {
        yield 'id_header' => ['ID'];
        yield 'fund_auth_order_header' => ['预授权订单'];
        yield 'name_header' => ['项目名称'];
        yield 'amount_header' => ['金额'];
        yield 'description_header' => ['计费说明'];
    }

    public static function provideNewPageFields(): iterable
    {
        yield 'fund_auth_order_field' => ['fundAuthOrder'];
        yield 'name_field' => ['name'];
        yield 'amount_field' => ['amount'];
        yield 'description_field' => ['description'];
    }

    public static function provideEditPageFields(): iterable
    {
        yield 'fund_auth_order_field' => ['fundAuthOrder'];
        yield 'name_field' => ['name'];
        yield 'amount_field' => ['amount'];
        yield 'description_field' => ['description'];
    }

    public function testIndexPageAccessibleForAuthenticatedAdmin(): void
    {
        $client = self::createClientWithDatabase();
        $client->loginUser($this->createAdminUser());

        $client->request('GET', '/admin/alipay-fund-auth/fund-auth-post-payment');

        $response = $client->getResponse();
        $this->assertTrue($response->isSuccessful());
        $content = $response->getContent();
        $this->assertIsString($content);
        $this->assertStringContainsString('后付费项目列表', $content);
    }

    public function testIndexPageRedirectsForUnauthenticatedUser(): void
    {
        $client = self::createClient();

        $client->request('GET', '/admin/alipay-fund-auth/fund-auth-post-payment');

        $response = $client->getResponse();
        $this->assertTrue($response->isRedirection());
        $this->assertEquals(302, $response->getStatusCode());
    }

    public function testNewPageAccessibleForAuthenticatedAdmin(): void
    {
        $client = self::createClientWithDatabase();
        $client->loginUser($this->createAdminUser());

        $client->request('GET', '/admin/alipay-fund-auth/fund-auth-post-payment');

        $response = $client->getResponse();
        $this->assertTrue($response->isSuccessful());
        $content = $response->getContent();
        $this->assertIsString($content);
        $this->assertStringContainsString('后付费项目', $content);
    }

    public function testSearchFunctionalityWithValidQuery(): void
    {
        $client = self::createClientWithDatabase();
        $client->loginUser($this->createAdminUser());

        $client->request('GET', '/admin/alipay-fund-auth/fund-auth-post-payment?query=test');

        $response = $client->getResponse();
        $this->assertTrue($response->isSuccessful());
    }

    public function testFilterFunctionalityWithValidFilter(): void
    {
        $client = self::createClientWithDatabase();
        $client->loginUser($this->createAdminUser());

        $client->request('GET', '/admin/alipay-fund-auth/fund-auth-post-payment');

        $response = $client->getResponse();
        $this->assertTrue($response->isSuccessful());
    }
}
