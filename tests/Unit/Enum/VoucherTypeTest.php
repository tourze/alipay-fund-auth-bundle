<?php

namespace AlipayFundAuthBundle\Tests\Unit\Enum;

use AlipayFundAuthBundle\Enum\VoucherType;
use PHPUnit\Framework\TestCase;

class VoucherTypeTest extends TestCase
{
    /**
     * 测试枚举值是否正确定义
     */
    public function testEnumValues_areCorrectlyDefined(): void
    {
        $this->assertEquals('ALIPAY_FIX_VOUCHER', VoucherType::ALIPAY_FIX_VOUCHER->value);
        $this->assertEquals('ALIPAY_DISCOUNT_VOUCHER', VoucherType::ALIPAY_DISCOUNT_VOUCHER->value);
        $this->assertEquals('ALIPAY_ITEM_VOUCHER', VoucherType::ALIPAY_ITEM_VOUCHER->value);
        $this->assertEquals('ALIPAY_CASH_VOUCHER', VoucherType::ALIPAY_CASH_VOUCHER->value);
        $this->assertEquals('ALIPAY_BIZ_VOUCHER', VoucherType::ALIPAY_BIZ_VOUCHER->value);
    }
    
    /**
     * 测试标签获取功能
     */
    public function testGetLabel_returnsCorrectLabels(): void
    {
        $this->assertEquals('全场代金券', VoucherType::ALIPAY_FIX_VOUCHER->getLabel());
        $this->assertEquals('折扣券', VoucherType::ALIPAY_DISCOUNT_VOUCHER->getLabel());
        $this->assertEquals('单品优惠券', VoucherType::ALIPAY_ITEM_VOUCHER->getLabel());
        $this->assertEquals('现金抵价券', VoucherType::ALIPAY_CASH_VOUCHER->getLabel());
        $this->assertEquals('商家全场券', VoucherType::ALIPAY_BIZ_VOUCHER->getLabel());
    }
    
    /**
     * 测试转换为选项项
     */
    public function testToSelectItem_returnsSelectItem(): void
    {
        $expected = [
            'label' => '全场代金券',
            'text' => '全场代金券',
            'value' => 'ALIPAY_FIX_VOUCHER',
            'name' => '全场代金券',
        ];
        
        $this->assertEquals($expected, VoucherType::ALIPAY_FIX_VOUCHER->toSelectItem());
    }
    
    /**
     * 测试选项列表生成
     */
    public function testGenOptions_returnsOptions(): void
    {
        $options = VoucherType::genOptions();
        
        $this->assertCount(5, $options);
        
        // 检查第一个选项
        $this->assertEquals([
            'label' => '全场代金券',
            'text' => '全场代金券',
            'value' => 'ALIPAY_FIX_VOUCHER',
            'name' => '全场代金券',
        ], $options[0]);
    }
    
    /**
     * 测试从字符串创建枚举实例
     */
    public function testFromString_withValidValue_returnsEnumCase(): void
    {
        $this->assertSame(VoucherType::ALIPAY_FIX_VOUCHER, VoucherType::from('ALIPAY_FIX_VOUCHER'));
        $this->assertSame(VoucherType::ALIPAY_DISCOUNT_VOUCHER, VoucherType::from('ALIPAY_DISCOUNT_VOUCHER'));
        $this->assertSame(VoucherType::ALIPAY_ITEM_VOUCHER, VoucherType::from('ALIPAY_ITEM_VOUCHER'));
        $this->assertSame(VoucherType::ALIPAY_CASH_VOUCHER, VoucherType::from('ALIPAY_CASH_VOUCHER'));
        $this->assertSame(VoucherType::ALIPAY_BIZ_VOUCHER, VoucherType::from('ALIPAY_BIZ_VOUCHER'));
    }
    
    /**
     * 测试从无效字符串创建枚举实例抛出异常
     */
    public function testFromString_withInvalidValue_throwsException(): void
    {
        $this->expectException(\ValueError::class);
        VoucherType::from('INVALID_VALUE');
    }
    
    /**
     * 测试尝试从字符串创建枚举实例
     */
    public function testTryFrom_withValidValue_returnsEnumCase(): void
    {
        $this->assertSame(VoucherType::ALIPAY_FIX_VOUCHER, VoucherType::tryFrom('ALIPAY_FIX_VOUCHER'));
        $this->assertSame(VoucherType::ALIPAY_DISCOUNT_VOUCHER, VoucherType::tryFrom('ALIPAY_DISCOUNT_VOUCHER'));
    }
    
    /**
     * 测试尝试从无效字符串创建枚举实例返回 null
     */
    public function testTryFrom_withInvalidValue_returnsNull(): void
    {
        $this->assertNull(VoucherType::tryFrom('INVALID_VALUE'));
    }
}