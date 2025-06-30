<?php

namespace AlipayFundAuthBundle\Tests\Controller\Admin;

use AlipayFundAuthBundle\Controller\Admin\AccountCrudController;
use AlipayFundAuthBundle\Entity\Account;
use PHPUnit\Framework\TestCase;

class AccountCrudControllerTest extends TestCase
{

    /**
     * 测试获取实体类名
     */
    public function testGetEntityFqcn_returnsCorrectClass(): void
    {
        $this->assertEquals(Account::class, AccountCrudController::getEntityFqcn());
    }
}