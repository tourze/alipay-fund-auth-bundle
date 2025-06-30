<?php

namespace AlipayFundAuthBundle\Tests\Controller\Admin;

use AlipayFundAuthBundle\Controller\Admin\TradeOrderCrudController;
use AlipayFundAuthBundle\Entity\TradeOrder;
use PHPUnit\Framework\TestCase;

class TradeOrderCrudControllerTest extends TestCase
{

    /**
     * 测试获取实体类名
     */
    public function testGetEntityFqcn_returnsCorrectClass(): void
    {
        $this->assertEquals(TradeOrder::class, TradeOrderCrudController::getEntityFqcn());
    }
}