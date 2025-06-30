<?php

namespace AlipayFundAuthBundle\Tests\Controller\Admin;

use AlipayFundAuthBundle\Controller\Admin\TradeGoodsDetailCrudController;
use AlipayFundAuthBundle\Entity\TradeGoodsDetail;
use PHPUnit\Framework\TestCase;

class TradeGoodsDetailCrudControllerTest extends TestCase
{

    /**
     * 测试获取实体类名
     */
    public function testGetEntityFqcn_returnsCorrectClass(): void
    {
        $this->assertEquals(TradeGoodsDetail::class, TradeGoodsDetailCrudController::getEntityFqcn());
    }
}