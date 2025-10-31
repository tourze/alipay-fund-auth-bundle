<?php

namespace AlipayFundAuthBundle\Tests\Enum;

use AlipayFundAuthBundle\Enum\AuthTradePayMode;
use PHPUnit\Framework\Attributes\CoversClass;
use Tourze\PHPUnitEnum\AbstractEnumTestCase;

/**
 * @internal
 */
#[CoversClass(AuthTradePayMode::class)]
final class AuthTradePayModeTest extends AbstractEnumTestCase
{
    /**
     * 测试枚举值是否正确定义
     */
    public function testEnumValuesAreCorrectlyDefined(): void
    {
        $this->assertEquals('CREDIT_PREAUTH_PAY', AuthTradePayMode::CREDIT_PREAUTH_PAY->value);
    }

    /**
     * 测试标签获取功能
     */
    public function testGetLabelReturnsCorrectLabels(): void
    {
        $this->assertEquals('信用预授权支付', AuthTradePayMode::CREDIT_PREAUTH_PAY->getLabel());
    }

    /**
     * 测试转换为选项项
     */
    public function testToSelectItemReturnsSelectItem(): void
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
    public function testGenOptionsReturnsOptions(): void
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
    public function testFromStringWithValidValueReturnsEnumCase(): void
    {
        $this->assertSame(AuthTradePayMode::CREDIT_PREAUTH_PAY, AuthTradePayMode::from('CREDIT_PREAUTH_PAY'));
    }

    /**
     * 测试尝试从字符串创建枚举实例
     */
    public function testTryFromWithValidValueReturnsEnumCase(): void
    {
        $this->assertSame(AuthTradePayMode::CREDIT_PREAUTH_PAY, AuthTradePayMode::tryFrom('CREDIT_PREAUTH_PAY'));
    }

    /**
     * 测试转换为数组
     */
    public function testToArrayReturnsArray(): void
    {
        $result = AuthTradePayMode::CREDIT_PREAUTH_PAY->toArray();
        $this->assertIsArray($result);
        $this->assertArrayHasKey('label', $result);
        $this->assertArrayHasKey('value', $result);
    }
}
