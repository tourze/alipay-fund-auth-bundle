<?php

namespace AlipayFundAuthBundle\Tests\Entity;

use AlipayFundAuthBundle\Entity\TradeVoucherDetail;
use PHPUnit\Framework\TestCase;

class TradeVoucherDetailTest extends TestCase
{
    private TradeVoucherDetail $entity;

    protected function setUp(): void
    {
        $this->entity = new TradeVoucherDetail();
    }

    /**
     * 测试 ID 设置与获取
     */
    public function testSetAndGetId_withValidId_returnsId(): void
    {
        $id = 123;
        $reflection = new \ReflectionClass($this->entity);
        $property = $reflection->getProperty('id');
        $property->setAccessible(true);
        $property->setValue($this->entity, $id);
        
        $this->assertEquals($id, $this->entity->getId());
    }
}