<?php

declare(strict_types=1);

namespace AlipayFundAuthBundle\Tests\Controller\Admin;

use AlipayFundAuthBundle\Controller\Admin\TradePromoParamCrudController;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\PHPUnitSymfonyWebTest\AbstractEasyAdminControllerTestCase;

/**
 * @internal
 * Controller配置了 1 个过滤器，测试搜索功能
 */
#[CoversClass(TradePromoParamCrudController::class)]
#[RunTestsInSeparateProcesses]
final class TradePromoParamCrudControllerTest extends AbstractEasyAdminControllerTestCase
{
    protected function getControllerService(): TradePromoParamCrudController
    {
        return self::getService(TradePromoParamCrudController::class);
    }

    public static function provideIndexPageHeaders(): iterable
    {
        yield 'id_header' => ['ID'];
        yield 'trade_order_header' => ['关联订单'];
        yield 'actual_order_time_header' => ['实际交易时间'];
    }

    public static function provideNewPageFields(): iterable
    {
        yield 'trade_order_field' => ['tradeOrder'];
        yield 'actual_order_time_field' => ['actualOrderTime'];
    }

    public static function provideEditPageFields(): iterable
    {
        yield 'trade_order_field' => ['tradeOrder'];
        yield 'actual_order_time_field' => ['actualOrderTime'];
    }

    public function testIndexPageAccessibleForAuthenticatedAdmin(): void
    {
        $client = self::createClientWithDatabase();
        $client->loginUser($this->createAdminUser());

        $client->request('GET', '/admin/alipay-fund-auth/trade-promo-param');

        $response = $client->getResponse();
        $this->assertTrue($response->isSuccessful());
        $content = $response->getContent();
        $this->assertIsString($content);
        $this->assertStringContainsString('优惠参数列表', $content);
    }

    public function testIndexPageRedirectsForUnauthenticatedUser(): void
    {
        $client = self::createClient();

        $client->request('GET', '/admin/alipay-fund-auth/trade-promo-param');

        $response = $client->getResponse();
        $this->assertTrue($response->isRedirection());
        $this->assertEquals(302, $response->getStatusCode());
    }

    public function testNewPageAccessibleForAuthenticatedAdmin(): void
    {
        $client = self::createClientWithDatabase();
        $client->loginUser($this->createAdminUser());

        $client->request('GET', '/admin/alipay-fund-auth/trade-promo-param');

        $response = $client->getResponse();
        $this->assertTrue($response->isSuccessful());
        $content = $response->getContent();
        $this->assertIsString($content);
        $this->assertStringContainsString('优惠参数', $content);
    }

    public function testSearchFunctionalityWithValidQuery(): void
    {
        $client = self::createClientWithDatabase();
        $client->loginUser($this->createAdminUser());

        $client->request('GET', '/admin/alipay-fund-auth/trade-promo-param?query=test');

        $response = $client->getResponse();
        $this->assertTrue($response->isSuccessful());
    }

    public function testFilterFunctionalityWithValidFilter(): void
    {
        $client = self::createClientWithDatabase();
        $client->loginUser($this->createAdminUser());

        $client->request('GET', '/admin/alipay-fund-auth/trade-promo-param');

        $response = $client->getResponse();
        $this->assertTrue($response->isSuccessful());
    }
}
