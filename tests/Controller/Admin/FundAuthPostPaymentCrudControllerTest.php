<?php

namespace AlipayFundAuthBundle\Tests\Controller\Admin;

use AlipayFundAuthBundle\Controller\Admin\FundAuthPostPaymentCrudController;
use AlipayFundAuthBundle\Entity\FundAuthPostPayment;
use PHPUnit\Framework\TestCase;

class FundAuthPostPaymentCrudControllerTest extends TestCase
{

    /**
     * 测试获取实体类名
     */
    public function testGetEntityFqcn_returnsCorrectClass(): void
    {
        $this->assertEquals(FundAuthPostPayment::class, FundAuthPostPaymentCrudController::getEntityFqcn());
    }
}