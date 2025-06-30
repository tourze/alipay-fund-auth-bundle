<?php

namespace AlipayFundAuthBundle\Tests\Unit\Enum;

use AlipayFundAuthBundle\Enum\AuthTradePayMode;
use PHPUnit\Framework\TestCase;

class AuthTradePayModeTest extends TestCase
{
    /**
     * 测试枚举值是否正确定义
     */
    public function testEnumValues_areCorrectlyDefined(): void
    {
        $this->assertEquals('CREDIT_PREAUTH_PAY', AuthTradePayMode::CREDIT_PREAUTH_PAY->value);
    }
    
    /**
     * 测试标签获取功能
     */
    public function testGetLabel_returnsCorrectLabels(): void
    {
        $this->assertEquals('信用预授权支付', AuthTradePayMode::CREDIT_PREAUTH_PAY->getLabel());
    }
    
    /**
     * 测试转换为选项项
     */
    public function testToSelectItem_returnsSelectItem(): void
    {
        $expected = [
            'label' => '信用预授权支付',
            'text' => '信用预授权支付',
            'value' => 'CREDIT_PREAUTH_PAY',
            'name' => '信用预授权支付',
        ];
        
        $this->assertEquals($expected, AuthTradePayMode::CREDIT_PREAUTH_PAY->toSelectItem());
    }
    
    /**
     * 测试选项列表生成
     */
    public function testGenOptions_returnsOptions(): void
    {
        $expectedSelects = [
            [
                'label' => '信用预授权支付',
                'text' => '信用预授权支付',
                'value' => 'CREDIT_PREAUTH_PAY',
                'name' => '信用预授权支付',
            ],
        ];
        
        $this->assertEquals($expectedSelects, AuthTradePayMode::genOptions());
    }
    
    /**
     * 测试从字符串创建枚举实例
     */
    public function testFromString_withValidValue_returnsEnumCase(): void
    {
        $this->assertSame(AuthTradePayMode::CREDIT_PREAUTH_PAY, AuthTradePayMode::from('CREDIT_PREAUTH_PAY'));
    }
    
    /**
     * 测试从无效字符串创建枚举实例抛出异常
     */
    public function testFromString_withInvalidValue_throwsException(): void
    {
        $this->expectException(\ValueError::class);
        AuthTradePayMode::from('INVALID_VALUE');
    }
    
    /**
     * 测试尝试从字符串创建枚举实例
     */
    public function testTryFrom_withValidValue_returnsEnumCase(): void
    {
        $this->assertSame(AuthTradePayMode::CREDIT_PREAUTH_PAY, AuthTradePayMode::tryFrom('CREDIT_PREAUTH_PAY'));
    }
    
    /**
     * 测试尝试从无效字符串创建枚举实例返回 null
     */
    public function testTryFrom_withInvalidValue_returnsNull(): void
    {
        $this->assertNull(AuthTradePayMode::tryFrom('INVALID_VALUE'));
    }
}