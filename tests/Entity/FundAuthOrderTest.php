<?php

namespace AlipayFundAuthBundle\Tests\Entity;

use AlipayFundAuthBundle\Entity\Account;
use AlipayFundAuthBundle\Entity\FundAuthOrder;
use AlipayFundAuthBundle\Entity\FundAuthPostPayment;
use AlipayFundAuthBundle\Entity\FundAuthUnfreezeLog;
use AlipayFundAuthBundle\Entity\TradeOrder;
use AlipayFundAuthBundle\Enum\FundAuthOrderStatus;
use PHPUnit\Framework\Attributes\CoversClass;
use Tourze\PHPUnitDoctrineEntity\AbstractEntityTestCase;

/**
 * @internal
 */
#[CoversClass(FundAuthOrder::class)]
final class FundAuthOrderTest extends AbstractEntityTestCase
{
    protected function createEntity(): FundAuthOrder
    {
        return new FundAuthOrder();
    }

    /**
     * 提供属性及其样本值的 Data Provider.
     *
     * @return iterable<array{string, mixed}>
     */
    public static function propertiesProvider(): iterable
    {
        yield 'account' => ['account', self::createAccount()];
        yield 'outOrderNo' => ['outOrderNo', 'AUTH_123456'];
        yield 'outRequestNo' => ['outRequestNo', 'REQ_123456'];
        yield 'orderTitle' => ['orderTitle', '测试预授权'];
        yield 'amount' => ['amount', '100.00'];
        yield 'productCode' => ['productCode', 'PRE_AUTH'];
        yield 'payeeUserId' => ['payeeUserId', 'payee_123456'];
        yield 'payeeLogonId' => ['payeeLogonId', 'test@example.com'];
        yield 'payTimeout' => ['payTimeout', '30m'];
        yield 'timeExpress' => ['timeExpress', '1d'];
        yield 'extraParam' => ['extraParam', ['key' => 'value']];
        yield 'businessParams' => ['businessParams', ['key' => 'value']];
        yield 'sceneCode' => ['sceneCode', 'INDUSTRY_CODE'];
        yield 'authNo' => ['authNo', 'AUTH_20230101'];
        yield 'operationId' => ['operationId', 'OP_123456'];
        yield 'status' => ['status', FundAuthOrderStatus::SUCCESS];
        yield 'gmtTrans' => ['gmtTrans', new \DateTime()];
        yield 'payerUserId' => ['payerUserId', 'payer_123456'];
    }

    /**
     * 测试默认产品码
     */
    public function testDefaultProductCodeReturnsPreAuthPay(): void
    {
        $fundAuthOrder = $this->createEntity();
        $this->assertEquals('PREAUTH_PAY', $fundAuthOrder->getProductCode());
    }

    /**
     * 测试默认状态
     */
    public function testDefaultStatusReturnsInit(): void
    {
        $fundAuthOrder = $this->createEntity();
        $this->assertEquals(FundAuthOrderStatus::INIT, $fundAuthOrder->getStatus());
    }

    /**
     * 测试添加和获取支付后收款
     */
    public function testAddAndGetPostPaymentsWithValidPaymentReturnsCollection(): void
    {
        $fundAuthOrder = $this->createEntity();
        $postPayment = new FundAuthPostPayment();
        $fundAuthOrder->addPostPayment($postPayment);

        $this->assertCount(1, $fundAuthOrder->getPostPayments());
        $this->assertTrue($fundAuthOrder->getPostPayments()->contains($postPayment));
    }

    /**
     * 测试添加和移除支付后收款
     */
    public function testRemovePostPaymentAfterAddingPaymentReturnsEmptyCollection(): void
    {
        $fundAuthOrder = $this->createEntity();
        $postPayment = new FundAuthPostPayment();
        $fundAuthOrder->addPostPayment($postPayment);
        $fundAuthOrder->removePostPayment($postPayment);

        $this->assertCount(0, $fundAuthOrder->getPostPayments());
        $this->assertFalse($fundAuthOrder->getPostPayments()->contains($postPayment));
    }

    /**
     * 测试添加和获取解冻日志
     */
    public function testAddAndGetUnfreezeLogsWithValidLogReturnsCollection(): void
    {
        $fundAuthOrder = $this->createEntity();
        $unfreezeLog = new FundAuthUnfreezeLog();
        $fundAuthOrder->addUnfreezeLog($unfreezeLog);

        $this->assertCount(1, $fundAuthOrder->getUnfreezeLogs());
        $this->assertTrue($fundAuthOrder->getUnfreezeLogs()->contains($unfreezeLog));
    }

    /**
     * 测试添加和移除解冻日志
     */
    public function testRemoveUnfreezeLogAfterAddingLogReturnsEmptyCollection(): void
    {
        $fundAuthOrder = $this->createEntity();
        $unfreezeLog = new FundAuthUnfreezeLog();
        $fundAuthOrder->addUnfreezeLog($unfreezeLog);
        $fundAuthOrder->removeUnfreezeLog($unfreezeLog);

        $this->assertCount(0, $fundAuthOrder->getUnfreezeLogs());
        $this->assertFalse($fundAuthOrder->getUnfreezeLogs()->contains($unfreezeLog));
    }

    /**
     * 测试添加和获取交易订单
     */
    public function testAddAndGetTradesWithValidTradeReturnsCollection(): void
    {
        $fundAuthOrder = $this->createEntity();
        $trade = new TradeOrder();
        $fundAuthOrder->addTrade($trade);

        $this->assertCount(1, $fundAuthOrder->getTrades());
        $this->assertTrue($fundAuthOrder->getTrades()->contains($trade));
    }

    /**
     * 测试添加和移除交易订单
     */
    public function testRemoveTradeAfterAddingTradeReturnsEmptyCollection(): void
    {
        $fundAuthOrder = $this->createEntity();
        $trade = new TradeOrder();
        $fundAuthOrder->addTrade($trade);
        $fundAuthOrder->removeTrade($trade);

        $this->assertCount(0, $fundAuthOrder->getTrades());
        $this->assertFalse($fundAuthOrder->getTrades()->contains($trade));
    }

    private static function createAccount(): Account
    {
        $account = new Account();
        $account->setId('123456789');
        $account->setName('Test Account');
        $account->setAppId('test_app_id');

        return $account;
    }
}
