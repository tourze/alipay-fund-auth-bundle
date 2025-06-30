<?php

namespace AlipayFundAuthBundle\Tests\Service;

use AlipayFundAuthBundle\Service\AdminMenu;
use PHPUnit\Framework\TestCase;

class AdminMenuTest extends TestCase
{

    /**
     * 测试 AdminMenu 创建
     */
    public function testInvoke_createsMenuSuccessfully(): void
    {
        $linkGenerator = $this->createMock(\Tourze\EasyAdminMenuBundle\Service\LinkGeneratorInterface::class);
        $linkGenerator->method('getCurdListPage')->willReturn('http://example.com');
        
        $adminMenu = new AdminMenu($linkGenerator);
        
        $item = $this->createMock(\Knp\Menu\ItemInterface::class);
        $alipayMenu = $this->createMock(\Knp\Menu\ItemInterface::class);
        
        $item->expects($this->exactly(2))->method('getChild')->with('支付宝预授权')->willReturnOnConsecutiveCalls(null, $alipayMenu);
        $item->expects($this->once())->method('addChild')->with('支付宝预授权')->willReturn($alipayMenu);
        
        $alipayMenu->expects($this->exactly(6))->method('addChild')->willReturn($alipayMenu);
        $alipayMenu->expects($this->exactly(6))->method('setUri')->willReturn($alipayMenu);
        $alipayMenu->expects($this->exactly(6))->method('setAttribute')->willReturn($alipayMenu);
        
        $adminMenu($item);
    }
}