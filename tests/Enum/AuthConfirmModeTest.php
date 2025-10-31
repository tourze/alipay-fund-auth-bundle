<?php

namespace AlipayFundAuthBundle\Tests\Enum;

use AlipayFundAuthBundle\Enum\AuthConfirmMode;
use PHPUnit\Framework\Attributes\CoversClass;
use Tourze\PHPUnitEnum\AbstractEnumTestCase;

/**
 * @internal
 */
#[CoversClass(AuthConfirmMode::class)]
final class AuthConfirmModeTest extends AbstractEnumTestCase
{
    /**
     * 测试枚举值是否正确定义
     */
    public function testEnumValuesAreCorrectlyDefined(): void
    {
        $this->assertEquals('NOT_COMPLETE', AuthConfirmMode::NOT_COMPLETE->value);
        $this->assertEquals('COMPLETE', AuthConfirmMode::COMPLETE->value);
    }

    /**
     * 测试标签获取功能
     */
    public function testGetLabelReturnsCorrectLabels(): void
    {
        $this->assertEquals('转交易完成后不解冻剩余冻结金额', AuthConfirmMode::NOT_COMPLETE->getLabel());
        $this->assertEquals('转交易完成后解冻剩余冻结金额', AuthConfirmMode::COMPLETE->getLabel());
    }

    /**
     * 测试转换为选项项
     */
    public function testToSelectItemReturnsSelectItem(): void
    {
        $expected = [
            'label' => '转交易完成后不解冻剩余冻结金额',
            'text' => '转交易完成后不解冻剩余冻结金额',
            'value' => 'NOT_COMPLETE',
            'name' => '转交易完成后不解冻剩余冻结金额',
        ];

        $this->assertEquals($expected, AuthConfirmMode::NOT_COMPLETE->toSelectItem());
    }

    /**
     * 测试选项列表生成
     */
    public function testGenOptionsReturnsOptions(): void
    {
        $expectedSelects = [
            [
                'label' => '转交易完成后不解冻剩余冻结金额',
                'text' => '转交易完成后不解冻剩余冻结金额',
                'value' => 'NOT_COMPLETE',
                'name' => '转交易完成后不解冻剩余冻结金额',
            ],
            [
                'label' => '转交易完成后解冻剩余冻结金额',
                'text' => '转交易完成后解冻剩余冻结金额',
                'value' => 'COMPLETE',
                'name' => '转交易完成后解冻剩余冻结金额',
            ],
        ];

        $this->assertEquals($expectedSelects, AuthConfirmMode::genOptions());
    }

    /**
     * 测试从字符串创建枚举实例
     */
    public function testFromStringWithValidValueReturnsEnumCase(): void
    {
        $this->assertSame(AuthConfirmMode::NOT_COMPLETE, AuthConfirmMode::from('NOT_COMPLETE'));
        $this->assertSame(AuthConfirmMode::COMPLETE, AuthConfirmMode::from('COMPLETE'));
    }

    /**
     * 测试尝试从字符串创建枚举实例
     */
    public function testTryFromWithValidValueReturnsEnumCase(): void
    {
        $this->assertSame(AuthConfirmMode::NOT_COMPLETE, AuthConfirmMode::tryFrom('NOT_COMPLETE'));
        $this->assertSame(AuthConfirmMode::COMPLETE, AuthConfirmMode::tryFrom('COMPLETE'));
    }

    /**
     * 测试转换为数组
     */
    public function testToArrayReturnsArray(): void
    {
        $result = AuthConfirmMode::NOT_COMPLETE->toArray();
        $this->assertIsArray($result);
        $this->assertArrayHasKey('label', $result);
        $this->assertArrayHasKey('value', $result);
    }
}
