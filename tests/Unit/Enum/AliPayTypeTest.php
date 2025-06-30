<?php

namespace AlipayFundAuthBundle\Tests\Unit\Enum;

use AlipayFundAuthBundle\Enum\AliPayType;
use PHPUnit\Framework\TestCase;

class AliPayTypeTest extends TestCase
{
    /**
     * 测试枚举值是否正确定义
     */
    public function testEnumValues_areCorrectlyDefined(): void
    {
        $this->assertEquals('Alipay_AopWap', AliPayType::ALIPAY_AOPWAP->value);
        $this->assertEquals('Alipay_AopApp', AliPayType::ALIPAY_AOPAPP->value);
    }
    
    /**
     * 测试标签获取功能
     */
    public function testGetLabel_returnsCorrectLabels(): void
    {
        $this->assertEquals('H5支付', AliPayType::ALIPAY_AOPWAP->getLabel());
        $this->assertEquals('APP支付', AliPayType::ALIPAY_AOPAPP->getLabel());
    }
    
    /**
     * 测试转换为选项项
     */
    public function testToSelectItem_returnsSelectItem(): void
    {
        $expected = [
            'label' => 'H5支付',
            'text' => 'H5支付',
            'value' => 'Alipay_AopWap',
            'name' => 'H5支付',
        ];
        
        $this->assertEquals($expected, AliPayType::ALIPAY_AOPWAP->toSelectItem());
    }
    
    /**
     * 测试选项列表生成
     */
    public function testGenOptions_returnsOptions(): void
    {
        $expectedSelects = [
            [
                'label' => 'H5支付',
                'text' => 'H5支付',
                'value' => 'Alipay_AopWap',
                'name' => 'H5支付',
            ],
            [
                'label' => 'APP支付',
                'text' => 'APP支付',
                'value' => 'Alipay_AopApp',
                'name' => 'APP支付',
            ],
        ];
        
        $this->assertEquals($expectedSelects, AliPayType::genOptions());
    }
    
    /**
     * 测试从字符串创建枚举实例
     */
    public function testFromString_withValidValue_returnsEnumCase(): void
    {
        $this->assertSame(AliPayType::ALIPAY_AOPWAP, AliPayType::from('Alipay_AopWap'));
        $this->assertSame(AliPayType::ALIPAY_AOPAPP, AliPayType::from('Alipay_AopApp'));
    }
    
    /**
     * 测试从无效字符串创建枚举实例抛出异常
     */
    public function testFromString_withInvalidValue_throwsException(): void
    {
        $this->expectException(\ValueError::class);
        AliPayType::from('INVALID_VALUE');
    }
    
    /**
     * 测试尝试从字符串创建枚举实例
     */
    public function testTryFrom_withValidValue_returnsEnumCase(): void
    {
        $this->assertSame(AliPayType::ALIPAY_AOPWAP, AliPayType::tryFrom('Alipay_AopWap'));
        $this->assertSame(AliPayType::ALIPAY_AOPAPP, AliPayType::tryFrom('Alipay_AopApp'));
    }
    
    /**
     * 测试尝试从无效字符串创建枚举实例返回 null
     */
    public function testTryFrom_withInvalidValue_returnsNull(): void
    {
        $this->assertNull(AliPayType::tryFrom('INVALID_VALUE'));
    }
}