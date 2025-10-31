<?php

namespace AlipayFundAuthBundle\Tests\Enum;

use AlipayFundAuthBundle\Enum\FundAuthOrderStatus;
use PHPUnit\Framework\Attributes\CoversClass;
use Tourze\PHPUnitEnum\AbstractEnumTestCase;

/**
 * @internal
 */
#[CoversClass(FundAuthOrderStatus::class)]
final class FundAuthOrderStatusTest extends AbstractEnumTestCase
{
    /**
     * 测试枚举值是否正确定义
     */
    public function testEnumValuesAreCorrectlyDefined(): void
    {
        $this->assertEquals('INIT', FundAuthOrderStatus::INIT->value);
        $this->assertEquals('SUCCESS', FundAuthOrderStatus::SUCCESS->value);
        $this->assertEquals('CLOSED', FundAuthOrderStatus::CLOSED->value);
    }

    /**
     * 测试标签获取功能
     */
    public function testGetLabelReturnsCorrectLabels(): void
    {
        $this->assertEquals('初始', FundAuthOrderStatus::INIT->getLabel());
        $this->assertEquals('成功', FundAuthOrderStatus::SUCCESS->getLabel());
        $this->assertEquals('关闭', FundAuthOrderStatus::CLOSED->getLabel());
    }

    /**
     * 测试转换为选项项
     */
    public function testToSelectItemReturnsSelectItem(): void
    {
        $expected = [
            'label' => '初始',
            'text' => '初始',
            'value' => 'INIT',
            'name' => '初始',
        ];

        $this->assertEquals($expected, FundAuthOrderStatus::INIT->toSelectItem());
    }

    /**
     * 测试选项列表生成
     */
    public function testGenOptionsReturnsOptions(): void
    {
        $expectedSelects = [
            [
                'label' => '初始',
                'text' => '初始',
                'value' => 'INIT',
                'name' => '初始',
            ],
            [
                'label' => '成功',
                'text' => '成功',
                'value' => 'SUCCESS',
                'name' => '成功',
            ],
            [
                'label' => '关闭',
                'text' => '关闭',
                'value' => 'CLOSED',
                'name' => '关闭',
            ],
        ];

        $this->assertEquals($expectedSelects, FundAuthOrderStatus::genOptions());
    }

    /**
     * 测试从字符串创建枚举实例
     */
    public function testFromStringWithValidValueReturnsEnumCase(): void
    {
        $this->assertSame(FundAuthOrderStatus::INIT, FundAuthOrderStatus::from('INIT'));
        $this->assertSame(FundAuthOrderStatus::SUCCESS, FundAuthOrderStatus::from('SUCCESS'));
        $this->assertSame(FundAuthOrderStatus::CLOSED, FundAuthOrderStatus::from('CLOSED'));
    }

    /**
     * 测试尝试从字符串创建枚举实例
     */
    public function testTryFromWithValidValueReturnsEnumCase(): void
    {
        $this->assertSame(FundAuthOrderStatus::INIT, FundAuthOrderStatus::tryFrom('INIT'));
        $this->assertSame(FundAuthOrderStatus::SUCCESS, FundAuthOrderStatus::tryFrom('SUCCESS'));
        $this->assertSame(FundAuthOrderStatus::CLOSED, FundAuthOrderStatus::tryFrom('CLOSED'));
    }

    /**
     * 测试转换为数组
     */
    public function testToArrayReturnsArray(): void
    {
        $result = FundAuthOrderStatus::INIT->toArray();
        $this->assertIsArray($result);
        $this->assertArrayHasKey('label', $result);
        $this->assertArrayHasKey('value', $result);
    }
}
