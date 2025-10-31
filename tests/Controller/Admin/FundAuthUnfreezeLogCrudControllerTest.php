<?php

namespace AlipayFundAuthBundle\Tests\Controller\Admin;

use AlipayFundAuthBundle\Controller\Admin\FundAuthUnfreezeLogCrudController;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\PHPUnitSymfonyWebTest\AbstractEasyAdminControllerTestCase;

/**
 * @internal
 * Controller配置了 5 个过滤器，测试搜索功能
 */
#[CoversClass(FundAuthUnfreezeLogCrudController::class)]
#[RunTestsInSeparateProcesses]
final class FundAuthUnfreezeLogCrudControllerTest extends AbstractEasyAdminControllerTestCase
{
    protected function getControllerService(): FundAuthUnfreezeLogCrudController
    {
        return self::getService(FundAuthUnfreezeLogCrudController::class);
    }

    public static function provideIndexPageHeaders(): iterable
    {
        yield 'id_header' => ['ID'];
        yield 'fund_auth_order_header' => ['预授权订单'];
        yield 'out_request_no_header' => ['商户请求号'];
        yield 'amount_header' => ['解冻金额'];
        yield 'remark_header' => ['备注'];
        yield 'operator_id_header' => ['操作ID'];
        yield 'status_header' => ['状态'];
        yield 'trade_time_header' => ['交易时间'];
        yield 'credit_unfreeze_amount_header' => ['信用解冻金额'];
        yield 'own_unfreeze_amount_header' => ['自有资金解冻金额'];
    }

    public static function provideNewPageFields(): iterable
    {
        yield 'fund_auth_order_field' => ['fundAuthOrder'];
        yield 'out_request_no_field' => ['outRequestNo'];
        yield 'amount_field' => ['amount'];
        yield 'remark_field' => ['remark'];
    }

    public static function provideEditPageFields(): iterable
    {
        yield 'fund_auth_order_field' => ['fundAuthOrder'];
        yield 'out_request_no_field' => ['outRequestNo'];
        yield 'amount_field' => ['amount'];
        yield 'remark_field' => ['remark'];
    }

    public function testIndexPageAccessibleForAuthenticatedAdmin(): void
    {
        $client = self::createClientWithDatabase();
        $client->loginUser($this->createAdminUser());

        $client->request('GET', '/admin/alipay-fund-auth/fund-auth-unfreeze-log');

        $response = $client->getResponse();
        $this->assertTrue($response->isSuccessful());
    }

    public function testIndexPageRedirectsForUnauthenticatedUser(): void
    {
        $client = self::createClient();

        $client->request('GET', '/admin/alipay-fund-auth/fund-auth-unfreeze-log');

        $response = $client->getResponse();
        $this->assertTrue($response->isRedirection());
        $this->assertEquals(302, $response->getStatusCode());
    }

    public function testNewPageAccessibleForAuthenticatedAdmin(): void
    {
        $client = self::createClientWithDatabase();
        $client->loginUser($this->createAdminUser());

        $client->request('GET', '/admin/alipay-fund-auth/fund-auth-unfreeze-log?crudAction=new');

        $response = $client->getResponse();
        $this->assertTrue($response->isSuccessful());
    }

    public function testSearchFunctionalityWithValidQuery(): void
    {
        $client = self::createClientWithDatabase();
        $client->loginUser($this->createAdminUser());

        $client->request('GET', '/admin/alipay-fund-auth/fund-auth-unfreeze-log?query=test');

        $response = $client->getResponse();
        $this->assertTrue($response->isSuccessful());
    }

    public function testFilterFunctionalityWithValidFilter(): void
    {
        $client = self::createClientWithDatabase();
        $client->loginUser($this->createAdminUser());

        $client->request('GET', '/admin/alipay-fund-auth/fund-auth-unfreeze-log?filters[status][comparison]=eq&filters[status][value]=SUCCESS');

        $response = $client->getResponse();
        $this->assertTrue($response->isSuccessful());
    }
}
