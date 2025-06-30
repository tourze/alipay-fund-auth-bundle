<?php

namespace AlipayFundAuthBundle\Tests\Controller\Admin;

use AlipayFundAuthBundle\Controller\Admin\FundAuthUnfreezeLogCrudController;
use AlipayFundAuthBundle\Entity\FundAuthUnfreezeLog;
use PHPUnit\Framework\TestCase;

class FundAuthUnfreezeLogCrudControllerTest extends TestCase
{

    /**
     * 测试获取实体类名
     */
    public function testGetEntityFqcn_returnsCorrectClass(): void
    {
        $this->assertEquals(FundAuthUnfreezeLog::class, FundAuthUnfreezeLogCrudController::getEntityFqcn());
    }
}