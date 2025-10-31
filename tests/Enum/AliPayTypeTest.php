<?php

namespace AlipayFundAuthBundle\Tests\Enum;

use AlipayFundAuthBundle\Enum\AliPayType;
use PHPUnit\Framework\Attributes\CoversClass;
use Tourze\PHPUnitEnum\AbstractEnumTestCase;

/**
 * @internal
 */
#[CoversClass(AliPayType::class)]
final class AliPayTypeTest extends AbstractEnumTestCase
{
    /**
     * 测试枚举值是否正确定义
     */
    public function testEnumValuesAreCorrectlyDefined(): void
    {
        $this->assertEquals('Alipay_AopWap', AliPayType::ALIPAY_AOPWAP->value);
        $this->assertEquals('Alipay_AopApp', AliPayType::ALIPAY_AOPAPP->value);
    }

    /**
     * 测试标签获取功能
     */
    public function testGetLabelReturnsCorrectLabels(): void
    {
        $this->assertEquals('H5支付', AliPayType::ALIPAY_AOPWAP->getLabel());
        $this->assertEquals('APP支付', AliPayType::ALIPAY_AOPAPP->getLabel());
    }

    /**
     * 测试转换为选项项
     */
    public function testToSelectItemReturnsSelectItem(): void
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
    public function testGenOptionsReturnsOptions(): void
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
    public function testFromStringWithValidValueReturnsEnumCase(): void
    {
        $this->assertSame(AliPayType::ALIPAY_AOPWAP, AliPayType::from('Alipay_AopWap'));
        $this->assertSame(AliPayType::ALIPAY_AOPAPP, AliPayType::from('Alipay_AopApp'));
    }

    /**
     * 测试尝试从字符串创建枚举实例
     */
    public function testTryFromWithValidValueReturnsEnumCase(): void
    {
        $this->assertSame(AliPayType::ALIPAY_AOPWAP, AliPayType::tryFrom('Alipay_AopWap'));
        $this->assertSame(AliPayType::ALIPAY_AOPAPP, AliPayType::tryFrom('Alipay_AopApp'));
    }

    /**
     * 测试转换为数组
     */
    public function testToArrayReturnsArray(): void
    {
        $result = AliPayType::ALIPAY_AOPWAP->toArray();
        $this->assertIsArray($result);
        $this->assertArrayHasKey('label', $result);
        $this->assertArrayHasKey('value', $result);
    }
}
