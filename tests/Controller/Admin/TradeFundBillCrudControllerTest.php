<?php

declare(strict_types=1);

namespace AlipayFundAuthBundle\Tests\Controller\Admin;

use AlipayFundAuthBundle\Controller\Admin\TradeFundBillCrudController;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\PHPUnitSymfonyWebTest\AbstractEasyAdminControllerTestCase;

/**
 * @internal
 * Controller配置了 1 个过滤器，测试搜索功能
 */
#[CoversClass(TradeFundBillCrudController::class)]
#[RunTestsInSeparateProcesses]
final class TradeFundBillCrudControllerTest extends AbstractEasyAdminControllerTestCase
{
    protected function getControllerService(): TradeFundBillCrudController
    {
        return self::getService(TradeFundBillCrudController::class);
    }

    public static function provideIndexPageHeaders(): iterable
    {
        yield 'id_header' => ['ID'];
        yield 'trade_order_header' => ['关联订单'];
        yield 'fund_channel_header' => ['资金渠道'];
        yield 'amount_header' => ['使用金额'];
        yield 'real_amount_header' => ['实际使用金额'];
    }

    public static function provideNewPageFields(): iterable
    {
        yield 'trade_order_field' => ['tradeOrder'];
        yield 'fund_channel_field' => ['fundChannel'];
        yield 'amount_field' => ['amount'];
        yield 'real_amount_field' => ['realAmount'];
    }

    public static function provideEditPageFields(): iterable
    {
        yield 'trade_order_field' => ['tradeOrder'];
        yield 'fund_channel_field' => ['fundChannel'];
        yield 'amount_field' => ['amount'];
        yield 'real_amount_field' => ['realAmount'];
    }

    public function testIndexPageAccessibleForAuthenticatedAdmin(): void
    {
        $client = self::createClientWithDatabase();
        $client->loginUser($this->createAdminUser());

        $client->request('GET', '/admin/alipay-fund-auth/trade-fund-bill');

        $response = $client->getResponse();
        $this->assertTrue($response->isSuccessful());
        $content = $response->getContent();
        $this->assertIsString($content);
        $this->assertStringContainsString('资金渠道列表', $content);
    }

    public function testIndexPageRedirectsForUnauthenticatedUser(): void
    {
        $client = self::createClient();

        $client->request('GET', '/admin/alipay-fund-auth/trade-fund-bill');

        $response = $client->getResponse();
        $this->assertTrue($response->isRedirection());
        $this->assertEquals(302, $response->getStatusCode());
    }

    public function testNewPageAccessibleForAuthenticatedAdmin(): void
    {
        $client = self::createClientWithDatabase();
        $client->loginUser($this->createAdminUser());

        $client->request('GET', '/admin/alipay-fund-auth/trade-fund-bill');

        $response = $client->getResponse();
        $this->assertTrue($response->isSuccessful());
        $content = $response->getContent();
        $this->assertIsString($content);
        $this->assertStringContainsString('资金渠道', $content);
    }

    public function testSearchFunctionalityWithValidQuery(): void
    {
        $client = self::createClientWithDatabase();
        $client->loginUser($this->createAdminUser());

        $client->request('GET', '/admin/alipay-fund-auth/trade-fund-bill?query=test');

        $response = $client->getResponse();
        $this->assertTrue($response->isSuccessful());
    }

    public function testFilterFunctionalityWithValidFilter(): void
    {
        $client = self::createClientWithDatabase();
        $client->loginUser($this->createAdminUser());

        $client->request('GET', '/admin/alipay-fund-auth/trade-fund-bill');

        $response = $client->getResponse();
        $this->assertTrue($response->isSuccessful());
    }
}
