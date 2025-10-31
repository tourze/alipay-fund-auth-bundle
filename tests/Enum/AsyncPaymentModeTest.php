<?php

namespace AlipayFundAuthBundle\Tests\Enum;

use AlipayFundAuthBundle\Enum\AsyncPaymentMode;
use PHPUnit\Framework\Attributes\CoversClass;
use Tourze\PHPUnitEnum\AbstractEnumTestCase;

/**
 * @internal
 */
#[CoversClass(AsyncPaymentMode::class)]
final class AsyncPaymentModeTest extends AbstractEnumTestCase
{
    /**
     * 测试枚举值是否正确定义
     */
    public function testEnumValuesAreCorrectlyDefined(): void
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
    public function testGetLabelReturnsCorrectLabels(): void
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
    public function testToSelectItemReturnsSelectItem(): void
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
     * 测试从字符串创建枚举实例
     */
    public function testFromStringWithValidValueReturnsEnumCase(): void
    {
        $this->assertSame(AsyncPaymentMode::ASYNC_DELAY_PAY, AsyncPaymentMode::from('ASYNC_DELAY_PAY'));
        $this->assertSame(AsyncPaymentMode::ASYNC_REALTIME_PAY, AsyncPaymentMode::from('ASYNC_REALTIME_PAY'));
        $this->assertSame(AsyncPaymentMode::SYNC_DIRECT_PAY, AsyncPaymentMode::from('SYNC_DIRECT_PAY'));
        $this->assertSame(AsyncPaymentMode::NORMAL_ASYNC_PAY, AsyncPaymentMode::from('NORMAL_ASYNC_PAY'));
        $this->assertSame(AsyncPaymentMode::QUOTA_OCCUPYIED_ASYNC_PAY, AsyncPaymentMode::from('QUOTA_OCCUPYIED_ASYNC_PAY'));
    }

    /**
     * 测试尝试从字符串创建枚举实例
     */
    public function testTryFromWithValidValueReturnsEnumCase(): void
    {
        $this->assertSame(AsyncPaymentMode::ASYNC_DELAY_PAY, AsyncPaymentMode::tryFrom('ASYNC_DELAY_PAY'));
        $this->assertSame(AsyncPaymentMode::ASYNC_REALTIME_PAY, AsyncPaymentMode::tryFrom('ASYNC_REALTIME_PAY'));
    }

    /**
     * 测试转换为数组
     */
    public function testToArrayReturnsArray(): void
    {
        $result = AsyncPaymentMode::ASYNC_DELAY_PAY->toArray();
        $this->assertIsArray($result);
        $this->assertArrayHasKey('label', $result);
        $this->assertArrayHasKey('value', $result);
    }
}
