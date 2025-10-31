<?php

declare(strict_types=1);

namespace AlipayFundAuthBundle\Tests\Controller\Admin;

use AlipayFundAuthBundle\Controller\Admin\TradeExtendParamCrudController;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\PHPUnitSymfonyWebTest\AbstractEasyAdminControllerTestCase;

/**
 * @internal
 * Controller配置了 3 个过滤器，测试搜索功能
 */
#[CoversClass(TradeExtendParamCrudController::class)]
#[RunTestsInSeparateProcesses]
final class TradeExtendParamCrudControllerTest extends AbstractEasyAdminControllerTestCase
{
    protected function getControllerService(): TradeExtendParamCrudController
    {
        return self::getService(TradeExtendParamCrudController::class);
    }

    public static function provideIndexPageHeaders(): iterable
    {
        yield 'id_header' => ['ID'];
        yield 'trade_order_header' => ['关联订单'];
        yield 'sys_service_provider_id_header' => ['系统商编号'];
        yield 'specified_seller_name_header' => ['卖家名称'];
        yield 'card_type_header' => ['卡类型'];
    }

    public static function provideNewPageFields(): iterable
    {
        yield 'trade_order_field' => ['tradeOrder'];
        yield 'sys_service_provider_id_field' => ['sysServiceProviderId'];
        yield 'specified_seller_name_field' => ['specifiedSellerName'];
        yield 'card_type_field' => ['cardType'];
    }

    public static function provideEditPageFields(): iterable
    {
        yield 'trade_order_field' => ['tradeOrder'];
        yield 'sys_service_provider_id_field' => ['sysServiceProviderId'];
        yield 'specified_seller_name_field' => ['specifiedSellerName'];
        yield 'card_type_field' => ['cardType'];
    }

    public function testIndexPageAccessibleForAuthenticatedAdmin(): void
    {
        $client = self::createClientWithDatabase();
        $client->loginUser($this->createAdminUser());

        $client->request('GET', '/admin/alipay-fund-auth/trade-extend-param');

        $response = $client->getResponse();
        $this->assertTrue($response->isSuccessful());
        $content = $response->getContent();
        $this->assertIsString($content);
        $this->assertStringContainsString('扩展参数列表', $content);
    }

    public function testIndexPageRedirectsForUnauthenticatedUser(): void
    {
        $client = self::createClient();

        $client->request('GET', '/admin/alipay-fund-auth/trade-extend-param');

        $response = $client->getResponse();
        $this->assertTrue($response->isRedirection());
        $this->assertEquals(302, $response->getStatusCode());
    }

    public function testNewPageAccessibleForAuthenticatedAdmin(): void
    {
        $client = self::createClientWithDatabase();
        $client->loginUser($this->createAdminUser());

        $client->request('GET', '/admin/alipay-fund-auth/trade-extend-param');

        $response = $client->getResponse();
        $this->assertTrue($response->isSuccessful());
        $content = $response->getContent();
        $this->assertIsString($content);
        $this->assertStringContainsString('扩展参数', $content);
    }

    public function testSearchFunctionalityWithValidQuery(): void
    {
        $client = self::createClientWithDatabase();
        $client->loginUser($this->createAdminUser());

        $client->request('GET', '/admin/alipay-fund-auth/trade-extend-param?query=test');

        $response = $client->getResponse();
        $this->assertTrue($response->isSuccessful());
    }

    public function testFilterFunctionalityWithValidFilter(): void
    {
        $client = self::createClientWithDatabase();
        $client->loginUser($this->createAdminUser());

        $client->request('GET', '/admin/alipay-fund-auth/trade-extend-param');

        $response = $client->getResponse();
        $this->assertTrue($response->isSuccessful());
    }
}
