<?php

namespace AlipayFundAuthBundle\Tests\Controller\Admin;

use AlipayFundAuthBundle\Controller\Admin\TradeGoodsDetailCrudController;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\PHPUnitSymfonyWebTest\AbstractEasyAdminControllerTestCase;

/**
 * @internal
 * Controller配置了 6 个过滤器，测试搜索功能
 */
#[CoversClass(TradeGoodsDetailCrudController::class)]
#[RunTestsInSeparateProcesses]
final class TradeGoodsDetailCrudControllerTest extends AbstractEasyAdminControllerTestCase
{
    protected function getControllerService(): TradeGoodsDetailCrudController
    {
        return self::getService(TradeGoodsDetailCrudController::class);
    }

    public static function provideIndexPageHeaders(): iterable
    {
        yield 'id_header' => ['ID'];
        yield 'trade_order_header' => ['交易订单'];
        yield 'goods_id_header' => ['商品编号'];
        yield 'goods_name_header' => ['商品名称'];
        yield 'quantity_header' => ['商品数量'];
        yield 'price_header' => ['商品单价'];
    }

    public static function provideNewPageFields(): iterable
    {
        yield 'trade_order_field' => ['tradeOrder'];
        yield 'goods_id_field' => ['goodsId'];
        yield 'goods_name_field' => ['goodsName'];
        yield 'quantity_field' => ['quantity'];
        yield 'price_field' => ['price'];
        yield 'goods_category_field' => ['goodsCategory'];
        yield 'category_tree_field' => ['categoryTree'];
        yield 'show_url_field' => ['showUrl'];
    }

    public static function provideEditPageFields(): iterable
    {
        yield 'trade_order_field' => ['tradeOrder'];
        yield 'goods_id_field' => ['goodsId'];
        yield 'goods_name_field' => ['goodsName'];
        yield 'quantity_field' => ['quantity'];
        yield 'price_field' => ['price'];
        yield 'goods_category_field' => ['goodsCategory'];
        yield 'category_tree_field' => ['categoryTree'];
        yield 'show_url_field' => ['showUrl'];
    }

    public function testIndexPageAccessibleForAuthenticatedAdmin(): void
    {
        $client = self::createClientWithDatabase();
        $client->loginUser($this->createAdminUser());

        $client->request('GET', '/admin/alipay-fund-auth/trade-goods-detail');

        $response = $client->getResponse();
        $this->assertTrue($response->isSuccessful());
        $content = $response->getContent();
        $this->assertIsString($content);
        $this->assertStringContainsString('商品信息列表', $content);
    }

    public function testIndexPageRedirectsForUnauthenticatedUser(): void
    {
        $client = self::createClient();

        $client->request('GET', '/admin/alipay-fund-auth/trade-goods-detail');

        $response = $client->getResponse();
        $this->assertTrue($response->isRedirection());
        $this->assertEquals(302, $response->getStatusCode());
    }

    public function testNewPageAccessibleForAuthenticatedAdmin(): void
    {
        $client = self::createClientWithDatabase();
        $client->loginUser($this->createAdminUser());

        $client->request('GET', '/admin/alipay-fund-auth/trade-goods-detail');

        $response = $client->getResponse();
        $this->assertTrue($response->isSuccessful());
        $content = $response->getContent();
        $this->assertIsString($content);
        $this->assertStringContainsString('商品信息', $content);
    }

    public function testSearchFunctionalityWithValidQuery(): void
    {
        $client = self::createClientWithDatabase();
        $client->loginUser($this->createAdminUser());

        $client->request('GET', '/admin/alipay-fund-auth/trade-goods-detail?query=test');

        $response = $client->getResponse();
        $this->assertTrue($response->isSuccessful());
    }

    public function testFilterFunctionalityWithValidFilter(): void
    {
        $client = self::createClientWithDatabase();
        $client->loginUser($this->createAdminUser());

        $client->request('GET', '/admin/alipay-fund-auth/trade-goods-detail?filters[goodsName][comparison]=like&filters[goodsName][value]=test');

        $response = $client->getResponse();
        $this->assertTrue($response->isSuccessful());
    }
}
