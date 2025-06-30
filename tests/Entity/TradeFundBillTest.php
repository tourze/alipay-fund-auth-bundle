<?php

namespace AlipayFundAuthBundle\Tests\Entity;

use AlipayFundAuthBundle\Entity\TradeFundBill;
use PHPUnit\Framework\TestCase;

class TradeFundBillTest extends TestCase
{
    private TradeFundBill $entity;

    protected function setUp(): void
    {
        $this->entity = new TradeFundBill();
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