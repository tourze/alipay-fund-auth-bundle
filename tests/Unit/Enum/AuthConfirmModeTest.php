<?php

namespace AlipayFundAuthBundle\Tests\Unit\Enum;

use AlipayFundAuthBundle\Enum\AuthConfirmMode;
use PHPUnit\Framework\TestCase;

class AuthConfirmModeTest extends TestCase
{
    /**
     * 测试枚举值是否正确定义
     */
    public function testEnumValues_areCorrectlyDefined(): void
    {
        $this->assertEquals('NOT_COMPLETE', AuthConfirmMode::NOT_COMPLETE->value);
        $this->assertEquals('COMPLETE', AuthConfirmMode::COMPLETE->value);
    }
    
    /**
     * 测试标签获取功能
     */
    public function testGetLabel_returnsCorrectLabels(): void
    {
        $this->assertEquals('转交易完成后不解冻剩余冻结金额', AuthConfirmMode::NOT_COMPLETE->getLabel());
        $this->assertEquals('转交易完成后解冻剩余冻结金额', AuthConfirmMode::COMPLETE->getLabel());
    }
    
    /**
     * 测试转换为选项项
     */
    public function testToSelectItem_returnsSelectItem(): void
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
    public function testGenOptions_returnsOptions(): void
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
    public function testFromString_withValidValue_returnsEnumCase(): void
    {
        $this->assertSame(AuthConfirmMode::NOT_COMPLETE, AuthConfirmMode::from('NOT_COMPLETE'));
        $this->assertSame(AuthConfirmMode::COMPLETE, AuthConfirmMode::from('COMPLETE'));
    }
    
    /**
     * 测试从无效字符串创建枚举实例抛出异常
     */
    public function testFromString_withInvalidValue_throwsException(): void
    {
        $this->expectException(\ValueError::class);
        AuthConfirmMode::from('INVALID_VALUE');
    }
    
    /**
     * 测试尝试从字符串创建枚举实例
     */
    public function testTryFrom_withValidValue_returnsEnumCase(): void
    {
        $this->assertSame(AuthConfirmMode::NOT_COMPLETE, AuthConfirmMode::tryFrom('NOT_COMPLETE'));
        $this->assertSame(AuthConfirmMode::COMPLETE, AuthConfirmMode::tryFrom('COMPLETE'));
    }
    
    /**
     * 测试尝试从无效字符串创建枚举实例返回 null
     */
    public function testTryFrom_withInvalidValue_returnsNull(): void
    {
        $this->assertNull(AuthConfirmMode::tryFrom('INVALID_VALUE'));
    }
}