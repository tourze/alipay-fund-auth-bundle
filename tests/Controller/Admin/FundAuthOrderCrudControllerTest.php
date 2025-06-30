<?php

namespace AlipayFundAuthBundle\Tests\Controller\Admin;

use AlipayFundAuthBundle\Controller\Admin\FundAuthOrderCrudController;
use AlipayFundAuthBundle\Entity\FundAuthOrder;
use PHPUnit\Framework\TestCase;

class FundAuthOrderCrudControllerTest extends TestCase
{

    /**
     * 测试获取实体类名
     */
    public function testGetEntityFqcn_returnsCorrectClass(): void
    {
        $this->assertEquals(FundAuthOrder::class, FundAuthOrderCrudController::getEntityFqcn());
    }
}