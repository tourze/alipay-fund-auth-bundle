<?php

namespace AlipayFundAuthBundle\Tests\Controller\Admin;

use AlipayFundAuthBundle\Controller\Admin\TradeOrderCrudController;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\PHPUnitSymfonyWebTest\AbstractEasyAdminControllerTestCase;

/**
 * @internal
 * Controller配置了 10 个过滤器，测试搜索功能
 */
#[CoversClass(TradeOrderCrudController::class)]
#[RunTestsInSeparateProcesses]
final class TradeOrderCrudControllerTest extends AbstractEasyAdminControllerTestCase
{
    protected function getControllerService(): TradeOrderCrudController
    {
        return self::getService(TradeOrderCrudController::class);
    }

    public static function provideIndexPageHeaders(): iterable
    {
        yield 'id_header' => ['ID'];
        yield 'account_header' => ['支付宝账号'];
        yield 'out_trade_no_header' => ['商户订单号'];
        yield 'total_amount_header' => ['订单金额'];
        yield 'subject_header' => ['订单标题'];
        yield 'product_code_header' => ['产品码'];
        yield 'trade_no_header' => ['支付宝交易号'];
        yield 'buyer_logon_id_header' => ['买家账号'];
        yield 'receipt_amount_header' => ['实收金额'];
        yield 'buyer_pay_amount_header' => ['买家付款金额'];
        yield 'point_amount_header' => ['集分宝金额'];
        yield 'invoice_amount_header' => ['可开票金额'];
        yield 'gmt_payment_header' => ['支付时间'];
        yield 'store_name_header' => ['门店名称'];
        yield 'buyer_user_id_header' => ['买家用户ID'];
        yield 'buyer_open_id_header' => ['买家OpenID'];
        yield 'mdiscount_amount_header' => ['商家优惠金额'];
        yield 'discount_amount_header' => ['平台优惠金额'];
        yield 'trade_status_header' => ['交易状态'];
        yield 'create_time_header' => ['创建时间'];
        yield 'update_time_header' => ['更新时间'];
    }

    public static function provideNewPageFields(): iterable
    {
        yield 'account_field' => ['account'];
        yield 'fund_auth_order_field' => ['fundAuthOrder'];
        yield 'out_trade_no_field' => ['outTradeNo'];
        yield 'total_amount_field' => ['totalAmount'];
        yield 'subject_field' => ['subject'];
        yield 'product_code_field' => ['productCode'];
        yield 'auth_no_field' => ['authNo'];
        yield 'auth_confirm_mode_field' => ['authConfirmMode'];
        yield 'store_id_field' => ['storeId'];
        yield 'terminal_id_field' => ['terminalId'];
        yield 'pay_type_field' => ['payType'];
        yield 'trade_status_field' => ['tradeStatus'];
    }

    public static function provideEditPageFields(): iterable
    {
        yield 'account_field' => ['account'];
        yield 'fund_auth_order_field' => ['fundAuthOrder'];
        yield 'out_trade_no_field' => ['outTradeNo'];
        yield 'total_amount_field' => ['totalAmount'];
        yield 'subject_field' => ['subject'];
        yield 'product_code_field' => ['productCode'];
        yield 'auth_no_field' => ['authNo'];
        yield 'auth_confirm_mode_field' => ['authConfirmMode'];
        yield 'store_id_field' => ['storeId'];
        yield 'terminal_id_field' => ['terminalId'];
        yield 'pay_type_field' => ['payType'];
        yield 'trade_status_field' => ['tradeStatus'];
    }

    public function testIndexPageAccessibleForAuthenticatedAdmin(): void
    {
        $client = self::createClientWithDatabase();
        $client->loginUser($this->createAdminUser());

        $client->request('GET', '/admin/alipay-fund-auth/trade-order');

        $response = $client->getResponse();
        $this->assertTrue($response->isSuccessful());
        $content = $response->getContent();
        $this->assertIsString($content);
        $this->assertStringContainsString('交易订单列表', $content);
    }

    public function testIndexPageRedirectsForUnauthenticatedUser(): void
    {
        $client = self::createClient();

        $client->request('GET', '/admin/alipay-fund-auth/trade-order');

        $response = $client->getResponse();
        $this->assertTrue($response->isRedirection());
        $this->assertEquals(302, $response->getStatusCode());
    }

    public function testNewPageAccessibleForAuthenticatedAdmin(): void
    {
        $client = self::createClientWithDatabase();
        $client->loginUser($this->createAdminUser());

        $client->request('GET', '/admin/alipay-fund-auth/trade-order');

        $response = $client->getResponse();
        $this->assertTrue($response->isSuccessful());
        $content = $response->getContent();
        $this->assertIsString($content);
        $this->assertStringContainsString('交易订单', $content);
    }

    public function testSearchFunctionalityWithValidQuery(): void
    {
        $client = self::createClientWithDatabase();
        $client->loginUser($this->createAdminUser());

        $client->request('GET', '/admin/alipay-fund-auth/trade-order?query=test');

        $response = $client->getResponse();
        $this->assertTrue($response->isSuccessful());
    }

    public function testFilterFunctionalityWithValidFilter(): void
    {
        $client = self::createClientWithDatabase();
        $client->loginUser($this->createAdminUser());

        $client->request('GET', '/admin/alipay-fund-auth/trade-order?filters[tradeNo][comparison]=like&filters[tradeNo][value]=test');

        $response = $client->getResponse();
        $this->assertTrue($response->isSuccessful());
    }
}
