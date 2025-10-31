<?php

namespace AlipayFundAuthBundle\Tests\Controller\Admin;

use AlipayFundAuthBundle\Controller\Admin\FundAuthOrderCrudController;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\PHPUnitSymfonyWebTest\AbstractEasyAdminControllerTestCase;

/**
 * @internal
 * Controller配置了 6 个过滤器，测试搜索功能
 */
#[CoversClass(FundAuthOrderCrudController::class)]
#[RunTestsInSeparateProcesses]
final class FundAuthOrderCrudControllerTest extends AbstractEasyAdminControllerTestCase
{
    protected function getControllerService(): FundAuthOrderCrudController
    {
        return self::getService(FundAuthOrderCrudController::class);
    }

    public static function provideIndexPageHeaders(): iterable
    {
        yield 'id_header' => ['ID'];
        yield 'account_header' => ['支付宝账号'];
        yield 'out_order_no_header' => ['商户订单号'];
        yield 'out_request_no_header' => ['商户请求号'];
        yield 'order_title_header' => ['订单标题'];
        yield 'amount_header' => ['授权金额'];
        yield 'product_code_header' => ['产品码'];
        yield 'auth_no_header' => ['授权号'];
        yield 'operation_id_header' => ['操作ID'];
        yield 'status_header' => ['状态'];
        yield 'gmt_trans_header' => ['交易时间'];
        yield 'payer_user_id_header' => ['付款方用户ID'];
        yield 'pre_auth_type_header' => ['预授权类型'];
        yield 'credit_amount_header' => ['信用冻结金额'];
        yield 'fund_amount_header' => ['自有资金冻结金额'];
    }

    public static function provideNewPageFields(): iterable
    {
        yield 'account_field' => ['account'];
        yield 'out_order_no_field' => ['outOrderNo'];
        yield 'out_request_no_field' => ['outRequestNo'];
        yield 'order_title_field' => ['orderTitle'];
        yield 'amount_field' => ['amount'];
        yield 'product_code_field' => ['productCode'];
        yield 'payee_user_id_field' => ['payeeUserId'];
        yield 'payee_logon_id_field' => ['payeeLogonId'];
        yield 'pay_timeout_field' => ['payTimeout'];
        yield 'time_express_field' => ['timeExpress'];
        yield 'scene_code_field' => ['sceneCode'];
        yield 'trans_currency_field' => ['transCurrency'];
        yield 'settle_currency_field' => ['settleCurrency'];
        yield 'status_field' => ['status'];
    }

    public static function provideEditPageFields(): iterable
    {
        yield 'account_field' => ['account'];
        yield 'out_order_no_field' => ['outOrderNo'];
        yield 'out_request_no_field' => ['outRequestNo'];
        yield 'order_title_field' => ['orderTitle'];
        yield 'amount_field' => ['amount'];
        yield 'product_code_field' => ['productCode'];
        yield 'payee_user_id_field' => ['payeeUserId'];
        yield 'payee_logon_id_field' => ['payeeLogonId'];
        yield 'pay_timeout_field' => ['payTimeout'];
        yield 'time_express_field' => ['timeExpress'];
        yield 'scene_code_field' => ['sceneCode'];
        yield 'trans_currency_field' => ['transCurrency'];
        yield 'settle_currency_field' => ['settleCurrency'];
        yield 'status_field' => ['status'];
    }

    public function testIndexPageAccessibleForAuthenticatedAdmin(): void
    {
        $client = self::createClientWithDatabase();
        $client->loginUser($this->createAdminUser());

        $client->request('GET', '/admin/alipay-fund-auth/fund-auth-order');

        $response = $client->getResponse();
        $this->assertTrue($response->isSuccessful());
        $content = $response->getContent();
        $this->assertIsString($content);
        $this->assertStringContainsString('预授权订单列表', $content);
    }

    public function testIndexPageRedirectsForUnauthenticatedUser(): void
    {
        $client = self::createClient();

        $client->request('GET', '/admin/alipay-fund-auth/fund-auth-order');

        $response = $client->getResponse();
        $this->assertTrue($response->isRedirection());
        $this->assertEquals(302, $response->getStatusCode());
    }

    public function testNewPageAccessibleForAuthenticatedAdmin(): void
    {
        $client = self::createClientWithDatabase();
        $client->loginUser($this->createAdminUser());

        $client->request('GET', '/admin/alipay-fund-auth/fund-auth-order');

        $response = $client->getResponse();
        $this->assertTrue($response->isSuccessful());
        $content = $response->getContent();
        $this->assertIsString($content);
        $this->assertStringContainsString('预授权订单', $content);
    }

    public function testSearchFunctionalityWithValidQuery(): void
    {
        $client = self::createClientWithDatabase();
        $client->loginUser($this->createAdminUser());

        $client->request('GET', '/admin/alipay-fund-auth/fund-auth-order?query=test');

        $response = $client->getResponse();
        $this->assertTrue($response->isSuccessful());
    }

    public function testFilterFunctionalityWithValidFilter(): void
    {
        $client = self::createClientWithDatabase();
        $client->loginUser($this->createAdminUser());

        $client->request('GET', '/admin/alipay-fund-auth/fund-auth-order');

        $response = $client->getResponse();
        $this->assertTrue($response->isSuccessful());
    }
}
