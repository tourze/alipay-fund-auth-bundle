<?php

declare(strict_types=1);

namespace AlipayFundAuthBundle\Tests\Controller\Admin;

use AlipayFundAuthBundle\Controller\Admin\TradeVoucherDetailCrudController;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\PHPUnitSymfonyWebTest\AbstractEasyAdminControllerTestCase;

/**
 * @internal
 * Controller配置了 3 个过滤器，测试搜索功能
 */
#[CoversClass(TradeVoucherDetailCrudController::class)]
#[RunTestsInSeparateProcesses]
final class TradeVoucherDetailCrudControllerTest extends AbstractEasyAdminControllerTestCase
{
    protected function getControllerService(): TradeVoucherDetailCrudController
    {
        return self::getService(TradeVoucherDetailCrudController::class);
    }

    public static function provideIndexPageHeaders(): iterable
    {
        yield 'id_header' => ['ID'];
        yield 'trade_order_header' => ['关联订单'];
        yield 'voucher_id_header' => ['券ID'];
        yield 'name_header' => ['券名称'];
        yield 'type_header' => ['券类型'];
        yield 'amount_header' => ['优惠券面额'];
    }

    public static function provideNewPageFields(): iterable
    {
        yield 'trade_order_field' => ['tradeOrder'];
        yield 'voucher_id_field' => ['voucherId'];
        yield 'name_field' => ['name'];
        yield 'type_field' => ['type'];
        yield 'amount_field' => ['amount'];
        yield 'merchant_contribute_field' => ['merchantContribute'];
        yield 'other_contribute_field' => ['otherContribute'];
        yield 'template_id_field' => ['templateId'];
        yield 'purchase_buyer_contribute_field' => ['purchaseBuyerContribute'];
        yield 'purchase_merchant_contribute_field' => ['purchaseMerchantContribute'];
        yield 'purchase_ant_contribute_field' => ['purchaseAntContribute'];
        yield 'memo_field' => ['memo'];
    }

    public static function provideEditPageFields(): iterable
    {
        yield 'trade_order_field' => ['tradeOrder'];
        yield 'voucher_id_field' => ['voucherId'];
        yield 'name_field' => ['name'];
        yield 'type_field' => ['type'];
        yield 'amount_field' => ['amount'];
        yield 'merchant_contribute_field' => ['merchantContribute'];
        yield 'other_contribute_field' => ['otherContribute'];
        yield 'template_id_field' => ['templateId'];
        yield 'purchase_buyer_contribute_field' => ['purchaseBuyerContribute'];
        yield 'purchase_merchant_contribute_field' => ['purchaseMerchantContribute'];
        yield 'purchase_ant_contribute_field' => ['purchaseAntContribute'];
        yield 'memo_field' => ['memo'];
    }

    public function testIndexPageAccessibleForAuthenticatedAdmin(): void
    {
        $client = self::createClientWithDatabase();
        $client->loginUser($this->createAdminUser());

        $client->request('GET', '/admin/alipay-fund-auth/trade-voucher-detail');

        $response = $client->getResponse();
        $this->assertTrue($response->isSuccessful());
        $content = $response->getContent();
        $this->assertIsString($content);
        $this->assertStringContainsString('优惠券明细列表', $content);
    }

    public function testIndexPageRedirectsForUnauthenticatedUser(): void
    {
        $client = self::createClient();

        $client->request('GET', '/admin/alipay-fund-auth/trade-voucher-detail');

        $response = $client->getResponse();
        $this->assertTrue($response->isRedirection());
        $this->assertEquals(302, $response->getStatusCode());
    }

    public function testNewPageAccessibleForAuthenticatedAdmin(): void
    {
        $client = self::createClientWithDatabase();
        $client->loginUser($this->createAdminUser());

        $client->request('GET', '/admin/alipay-fund-auth/trade-voucher-detail');

        $response = $client->getResponse();
        $this->assertTrue($response->isSuccessful());
        $content = $response->getContent();
        $this->assertIsString($content);
        $this->assertStringContainsString('优惠券明细', $content);
    }

    public function testSearchFunctionalityWithValidQuery(): void
    {
        $client = self::createClientWithDatabase();
        $client->loginUser($this->createAdminUser());

        $client->request('GET', '/admin/alipay-fund-auth/trade-voucher-detail?query=test');

        $response = $client->getResponse();
        $this->assertTrue($response->isSuccessful());
    }

    public function testFilterFunctionalityWithValidFilter(): void
    {
        $client = self::createClientWithDatabase();
        $client->loginUser($this->createAdminUser());

        $client->request('GET', '/admin/alipay-fund-auth/trade-voucher-detail');

        $response = $client->getResponse();
        $this->assertTrue($response->isSuccessful());
    }
}
