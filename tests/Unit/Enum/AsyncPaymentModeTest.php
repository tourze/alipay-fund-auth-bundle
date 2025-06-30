<?php

namespace AlipayFundAuthBundle\Tests\Unit\Enum;

use AlipayFundAuthBundle\Enum\AsyncPaymentMode;
use PHPUnit\Framework\TestCase;

class AsyncPaymentModeTest extends TestCase
{
    /**
     * 测试枚举值是否正确定义
     */
    public function testEnumValues_areCorrectlyDefined(): void
    {
        $this->assertEquals('ASYNC_DELAY_PAY', AsyncPaymentMode::ASYNC_DELAY_PAY->value);
        $this->assertEquals('ASYNC_REALTIME_PAY', AsyncPaymentMode::ASYNC_REALTIME_PAY->value);
        $this->assertEquals('SYNC_DIRECT_PAY', AsyncPaymentMode::SYNC_DIRECT_PAY->value);
        $this->assertEquals('NORMAL_ASYNC_PAY', AsyncPaymentMode::NORMAL_ASYNC_PAY->value);
        $this->assertEquals('QUOTA_OCCUPYIED_ASYNC_PAY', AsyncPaymentMode::QUOTA_OCCUPYIED_ASYNC_PAY->value);
    }
    
    /**
     * 测试标签获取功能
     */
    public function testGetLabel_returnsCorrectLabels(): void
    {
        $this->assertEquals('异步延时付款', AsyncPaymentMode::ASYNC_DELAY_PAY->getLabel());
        $this->assertEquals('异步准实时付款', AsyncPaymentMode::ASYNC_REALTIME_PAY->getLabel());
        $this->assertEquals('同步直接扣款', AsyncPaymentMode::SYNC_DIRECT_PAY->getLabel());
        $this->assertEquals('纯异步付款', AsyncPaymentMode::NORMAL_ASYNC_PAY->getLabel());
        $this->assertEquals('异步支付并且预占了先享后付额度', AsyncPaymentMode::QUOTA_OCCUPYIED_ASYNC_PAY->getLabel());
    }
    
    /**
     * 测试转换为选项项
     */
    public function testToSelectItem_returnsSelectItem(): void
    {
        $expected = [
            'label' => '异步延时付款',
            'text' => '异步延时付款',
            'value' => 'ASYNC_DELAY_PAY',
            'name' => '异步延时付款',
        ];
        
        $this->assertEquals($expected, AsyncPaymentMode::ASYNC_DELAY_PAY->toSelectItem());
    }
    
    /**
     * 测试选项列表生成
     */
    public function testGenOptions_returnsOptions(): void
    {
        $options = AsyncPaymentMode::genOptions();
        
        $this->assertCount(5, $options);
        
        // 检查第一个选项
        $this->assertEquals([
            'label' => '异步延时付款',
            'text' => '异步延时付款',
            'value' => 'ASYNC_DELAY_PAY',
            'name' => '异步延时付款',
        ], $options[0]);
    }
    
    /**
     * 测试从字符串创建枚举实例
     */
    public function testFromString_withValidValue_returnsEnumCase(): void
    {
        $this->assertSame(AsyncPaymentMode::ASYNC_DELAY_PAY, AsyncPaymentMode::from('ASYNC_DELAY_PAY'));
        $this->assertSame(AsyncPaymentMode::ASYNC_REALTIME_PAY, AsyncPaymentMode::from('ASYNC_REALTIME_PAY'));
        $this->assertSame(AsyncPaymentMode::SYNC_DIRECT_PAY, AsyncPaymentMode::from('SYNC_DIRECT_PAY'));
        $this->assertSame(AsyncPaymentMode::NORMAL_ASYNC_PAY, AsyncPaymentMode::from('NORMAL_ASYNC_PAY'));
        $this->assertSame(AsyncPaymentMode::QUOTA_OCCUPYIED_ASYNC_PAY, AsyncPaymentMode::from('QUOTA_OCCUPYIED_ASYNC_PAY'));
    }
    
    /**
     * 测试从无效字符串创建枚举实例抛出异常
     */
    public function testFromString_withInvalidValue_throwsException(): void
    {
        $this->expectException(\ValueError::class);
        AsyncPaymentMode::from('INVALID_VALUE');
    }
    
    /**
     * 测试尝试从字符串创建枚举实例
     */
    public function testTryFrom_withValidValue_returnsEnumCase(): void
    {
        $this->assertSame(AsyncPaymentMode::ASYNC_DELAY_PAY, AsyncPaymentMode::tryFrom('ASYNC_DELAY_PAY'));
        $this->assertSame(AsyncPaymentMode::ASYNC_REALTIME_PAY, AsyncPaymentMode::tryFrom('ASYNC_REALTIME_PAY'));
    }
    
    /**
     * 测试尝试从无效字符串创建枚举实例返回 null
     */
    public function testTryFrom_withInvalidValue_returnsNull(): void
    {
        $this->assertNull(AsyncPaymentMode::tryFrom('INVALID_VALUE'));
    }
}