<?php

namespace AlipayFundAuthBundle\Tests\Enum;

use AlipayFundAuthBundle\Enum\FundAuthOrderStatus;
use PHPUnit\Framework\TestCase;

class FundAuthOrderStatusTest extends TestCase
{
    /**
     * 测试枚举值是否正确定义
     */
    public function testEnumValues_areCorrectlyDefined(): void
    {
        $this->assertEquals('INIT', FundAuthOrderStatus::INIT->value);
        $this->assertEquals('SUCCESS', FundAuthOrderStatus::SUCCESS->value);
        $this->assertEquals('CLOSED', FundAuthOrderStatus::CLOSED->value);
    }
    
    /**
     * 测试标签获取功能
     */
    public function testGetLabel_returnsCorrectLabels(): void
    {
        $this->assertEquals('初始', FundAuthOrderStatus::INIT->getLabel());
        $this->assertEquals('成功', FundAuthOrderStatus::SUCCESS->getLabel());
        $this->assertEquals('关闭', FundAuthOrderStatus::CLOSED->getLabel());
    }
    
    /**
     * 测试转换为选项项
     */
    public function testToSelectItem_returnsSelectItem(): void
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
    public function testGenOptions_returnsOptions(): void
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
    public function testFromString_withValidValue_returnsEnumCase(): void
    {
        $this->assertSame(FundAuthOrderStatus::INIT, FundAuthOrderStatus::from('INIT'));
        $this->assertSame(FundAuthOrderStatus::SUCCESS, FundAuthOrderStatus::from('SUCCESS'));
        $this->assertSame(FundAuthOrderStatus::CLOSED, FundAuthOrderStatus::from('CLOSED'));
    }
    
    /**
     * 测试从无效字符串创建枚举实例抛出异常
     */
    public function testFromString_withInvalidValue_throwsException(): void
    {
        $this->expectException(\ValueError::class);
        FundAuthOrderStatus::from('INVALID_VALUE');
    }
    
    /**
     * 测试尝试从字符串创建枚举实例
     */
    public function testTryFrom_withValidValue_returnsEnumCase(): void
    {
        $this->assertSame(FundAuthOrderStatus::INIT, FundAuthOrderStatus::tryFrom('INIT'));
        $this->assertSame(FundAuthOrderStatus::SUCCESS, FundAuthOrderStatus::tryFrom('SUCCESS'));
        $this->assertSame(FundAuthOrderStatus::CLOSED, FundAuthOrderStatus::tryFrom('CLOSED'));
    }
    
    /**
     * 测试尝试从无效字符串创建枚举实例返回 null
     */
    public function testTryFrom_withInvalidValue_returnsNull(): void
    {
        $this->assertNull(FundAuthOrderStatus::tryFrom('INVALID_VALUE'));
    }
} 