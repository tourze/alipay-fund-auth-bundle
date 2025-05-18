<?php

namespace AlipayFundAuthBundle\Tests\Entity;

use AlipayFundAuthBundle\Entity\Account;
use AlipayFundAuthBundle\Entity\FundAuthOrder;
use AlipayFundAuthBundle\Entity\FundAuthPostPayment;
use AlipayFundAuthBundle\Entity\FundAuthUnfreezeLog;
use AlipayFundAuthBundle\Entity\TradeOrder;
use AlipayFundAuthBundle\Enum\FundAuthOrderStatus;
use PHPUnit\Framework\TestCase;

class FundAuthOrderTest extends TestCase
{
    private FundAuthOrder $fundAuthOrder;
    private Account $account;

    protected function setUp(): void
    {
        $this->fundAuthOrder = new FundAuthOrder();
        
        $this->account = new Account();
        $this->account->setId('123456789');
        $this->account->setName('Test Account');
        $this->account->setAppId('test_app_id');
    }

    /**
     * 测试设置和获取 ID
     */
    public function testSetAndGetId_withValidId_returnsId(): void
    {
        $id = '123456789';
        
        // 使用反射设置 ID，因为 setId 方法可能是私有的
        $reflectionClass = new \ReflectionClass(FundAuthOrder::class);
        $reflectionProperty = $reflectionClass->getProperty('id');
        $reflectionProperty->setAccessible(true);
        $reflectionProperty->setValue($this->fundAuthOrder, $id);
        
        $this->assertEquals($id, $this->fundAuthOrder->getId());
    }
    
    /**
     * 测试设置和获取账户
     */
    public function testSetAndGetAccount_withValidAccount_returnsAccount(): void
    {
        $this->fundAuthOrder->setAccount($this->account);
        
        $this->assertSame($this->account, $this->fundAuthOrder->getAccount());
    }
    
    /**
     * 测试设置和获取商户授权资金订单号
     */
    public function testSetAndGetOutOrderNo_withValidNo_returnsNo(): void
    {
        $outOrderNo = 'AUTH_123456';
        $this->fundAuthOrder->setOutOrderNo($outOrderNo);
        
        $this->assertEquals($outOrderNo, $this->fundAuthOrder->getOutOrderNo());
    }
    
    /**
     * 测试设置和获取商户请求号
     */
    public function testSetAndGetOutRequestNo_withValidNo_returnsNo(): void
    {
        $outRequestNo = 'REQ_123456';
        $this->fundAuthOrder->setOutRequestNo($outRequestNo);
        
        $this->assertEquals($outRequestNo, $this->fundAuthOrder->getOutRequestNo());
    }
    
    /**
     * 测试设置和获取订单标题
     */
    public function testSetAndGetOrderTitle_withValidTitle_returnsTitle(): void
    {
        $orderTitle = '测试预授权';
        $this->fundAuthOrder->setOrderTitle($orderTitle);
        
        $this->assertEquals($orderTitle, $this->fundAuthOrder->getOrderTitle());
    }
    
    /**
     * 测试设置和获取金额
     */
    public function testSetAndGetAmount_withValidAmount_returnsAmount(): void
    {
        $amount = '100.00';
        $this->fundAuthOrder->setAmount($amount);
        
        $this->assertEquals($amount, $this->fundAuthOrder->getAmount());
    }
    
    /**
     * 测试设置和获取产品码
     */
    public function testSetAndGetProductCode_withValidCode_returnsCode(): void
    {
        $productCode = 'PRE_AUTH';
        $this->fundAuthOrder->setProductCode($productCode);
        
        $this->assertEquals($productCode, $this->fundAuthOrder->getProductCode());
    }
    
    /**
     * 测试默认产品码
     */
    public function testDefaultProductCode_returnsPreAuthPay(): void
    {
        $this->assertEquals('PREAUTH_PAY', $this->fundAuthOrder->getProductCode());
    }
    
    /**
     * 测试设置和获取收款方用户 ID
     */
    public function testSetAndGetPayeeUserId_withValidId_returnsId(): void
    {
        $payeeUserId = 'payee_123456';
        $this->fundAuthOrder->setPayeeUserId($payeeUserId);
        
        $this->assertEquals($payeeUserId, $this->fundAuthOrder->getPayeeUserId());
    }
    
    /**
     * 测试设置和获取收款方账号
     */
    public function testSetAndGetPayeeLogonId_withValidId_returnsId(): void
    {
        $payeeLogonId = 'test@example.com';
        $this->fundAuthOrder->setPayeeLogonId($payeeLogonId);
        
        $this->assertEquals($payeeLogonId, $this->fundAuthOrder->getPayeeLogonId());
    }
    
    /**
     * 测试设置和获取支付超时时间
     */
    public function testSetAndGetPayTimeout_withValidTimeout_returnsTimeout(): void
    {
        $payTimeout = '30m';
        $this->fundAuthOrder->setPayTimeout($payTimeout);
        
        $this->assertEquals($payTimeout, $this->fundAuthOrder->getPayTimeout());
    }
    
    /**
     * 测试设置和获取交易有效期
     */
    public function testSetAndGetTimeExpress_withValidExpress_returnsExpress(): void
    {
        $timeExpress = '1d';
        $this->fundAuthOrder->setTimeExpress($timeExpress);
        
        $this->assertEquals($timeExpress, $this->fundAuthOrder->getTimeExpress());
    }
    
    /**
     * 测试设置和获取扩展参数
     */
    public function testSetAndGetExtraParam_withValidParam_returnsParam(): void
    {
        $extraParam = ['key' => 'value'];
        $this->fundAuthOrder->setExtraParam($extraParam);
        
        $this->assertEquals($extraParam, $this->fundAuthOrder->getExtraParam());
    }
    
    /**
     * 测试设置和获取业务扩展参数
     */
    public function testSetAndGetBusinessParams_withValidParams_returnsParams(): void
    {
        $businessParams = ['key' => 'value'];
        $this->fundAuthOrder->setBusinessParams($businessParams);
        
        $this->assertEquals($businessParams, $this->fundAuthOrder->getBusinessParams());
    }
    
    /**
     * 测试设置和获取场景码
     */
    public function testSetAndGetSceneCode_withValidCode_returnsCode(): void
    {
        $sceneCode = 'INDUSTRY_CODE';
        $this->fundAuthOrder->setSceneCode($sceneCode);
        
        $this->assertEquals($sceneCode, $this->fundAuthOrder->getSceneCode());
    }
    
    /**
     * 测试设置和获取授权号
     */
    public function testSetAndGetAuthNo_withValidNo_returnsNo(): void
    {
        $authNo = 'AUTH_20230101';
        $this->fundAuthOrder->setAuthNo($authNo);
        
        $this->assertEquals($authNo, $this->fundAuthOrder->getAuthNo());
    }
    
    /**
     * 测试设置和获取操作 ID
     */
    public function testSetAndGetOperationId_withValidId_returnsId(): void
    {
        $operationId = 'OP_123456';
        $this->fundAuthOrder->setOperationId($operationId);
        
        $this->assertEquals($operationId, $this->fundAuthOrder->getOperationId());
    }
    
    /**
     * 测试设置和获取状态
     */
    public function testSetAndGetStatus_withValidStatus_returnsStatus(): void
    {
        $status = FundAuthOrderStatus::SUCCESS;
        $this->fundAuthOrder->setStatus($status);
        
        $this->assertEquals($status, $this->fundAuthOrder->getStatus());
    }
    
    /**
     * 测试默认状态
     */
    public function testDefaultStatus_returnsInit(): void
    {
        $this->assertEquals(FundAuthOrderStatus::INIT, $this->fundAuthOrder->getStatus());
    }
    
    /**
     * 测试设置和获取交易时间
     */
    public function testSetAndGetGmtTrans_withValidTime_returnsTime(): void
    {
        $gmtTrans = new \DateTime();
        $this->fundAuthOrder->setGmtTrans($gmtTrans);
        
        $this->assertSame($gmtTrans, $this->fundAuthOrder->getGmtTrans());
    }
    
    /**
     * 测试设置和获取付款方用户 ID
     */
    public function testSetAndGetPayerUserId_withValidId_returnsId(): void
    {
        $payerUserId = 'payer_123456';
        $this->fundAuthOrder->setPayerUserId($payerUserId);
        
        $this->assertEquals($payerUserId, $this->fundAuthOrder->getPayerUserId());
    }
    
    /**
     * 测试添加和获取支付后收款
     */
    public function testAddAndGetPostPayments_withValidPayment_returnsCollection(): void
    {
        $postPayment = $this->createMock(FundAuthPostPayment::class);
        $this->fundAuthOrder->addPostPayment($postPayment);
        
        $this->assertCount(1, $this->fundAuthOrder->getPostPayments());
        $this->assertTrue($this->fundAuthOrder->getPostPayments()->contains($postPayment));
    }
    
    /**
     * 测试添加和移除支付后收款
     */
    public function testRemovePostPayment_afterAddingPayment_returnsEmptyCollection(): void
    {
        $postPayment = $this->createMock(FundAuthPostPayment::class);
        $this->fundAuthOrder->addPostPayment($postPayment);
        $this->fundAuthOrder->removePostPayment($postPayment);
        
        $this->assertCount(0, $this->fundAuthOrder->getPostPayments());
        $this->assertFalse($this->fundAuthOrder->getPostPayments()->contains($postPayment));
    }
    
    /**
     * 测试添加和获取解冻日志
     */
    public function testAddAndGetUnfreezeLogs_withValidLog_returnsCollection(): void
    {
        $unfreezeLog = $this->createMock(FundAuthUnfreezeLog::class);
        $this->fundAuthOrder->addUnfreezeLog($unfreezeLog);
        
        $this->assertCount(1, $this->fundAuthOrder->getUnfreezeLogs());
        $this->assertTrue($this->fundAuthOrder->getUnfreezeLogs()->contains($unfreezeLog));
    }
    
    /**
     * 测试添加和移除解冻日志
     */
    public function testRemoveUnfreezeLog_afterAddingLog_returnsEmptyCollection(): void
    {
        $unfreezeLog = $this->createMock(FundAuthUnfreezeLog::class);
        $this->fundAuthOrder->addUnfreezeLog($unfreezeLog);
        $this->fundAuthOrder->removeUnfreezeLog($unfreezeLog);
        
        $this->assertCount(0, $this->fundAuthOrder->getUnfreezeLogs());
        $this->assertFalse($this->fundAuthOrder->getUnfreezeLogs()->contains($unfreezeLog));
    }
    
    /**
     * 测试添加和获取交易订单
     */
    public function testAddAndGetTrades_withValidTrade_returnsCollection(): void
    {
        $trade = $this->createMock(TradeOrder::class);
        $this->fundAuthOrder->addTrade($trade);
        
        $this->assertCount(1, $this->fundAuthOrder->getTrades());
        $this->assertTrue($this->fundAuthOrder->getTrades()->contains($trade));
    }
    
    /**
     * 测试添加和移除交易订单
     */
    public function testRemoveTrade_afterAddingTrade_returnsEmptyCollection(): void
    {
        $trade = $this->createMock(TradeOrder::class);
        $this->fundAuthOrder->addTrade($trade);
        $this->fundAuthOrder->removeTrade($trade);
        
        $this->assertCount(0, $this->fundAuthOrder->getTrades());
        $this->assertFalse($this->fundAuthOrder->getTrades()->contains($trade));
    }
} 