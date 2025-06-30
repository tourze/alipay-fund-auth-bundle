<?php

namespace AlipayFundAuthBundle\Tests\Procedure;

use AlipayFundAuthBundle\Procedure\CreateAlipayPreauthTradeOrder;
use PHPUnit\Framework\TestCase;

class CreateAlipayPreauthTradeOrderTest extends TestCase
{
    private CreateAlipayPreauthTradeOrder $procedure;

    protected function setUp(): void
    {
        $sdkService = $this->createMock(\AlipayFundAuthBundle\Service\SdkService::class);
        $this->procedure = new CreateAlipayPreauthTradeOrder($sdkService);
    }

    /**
     * 测试 Procedure 创建
     */
    public function testCreateProcedure_createsSuccessfully(): void
    {
        $this->assertInstanceOf(CreateAlipayPreauthTradeOrder::class, $this->procedure);
    }
}