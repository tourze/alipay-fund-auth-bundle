<?php

namespace AlipayFundAuthBundle\Tests\Entity;

use AlipayFundAuthBundle\Entity\TradeGoodsDetail;
use PHPUnit\Framework\TestCase;

class TradeGoodsDetailTest extends TestCase
{
    private TradeGoodsDetail $entity;

    protected function setUp(): void
    {
        $this->entity = new TradeGoodsDetail();
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