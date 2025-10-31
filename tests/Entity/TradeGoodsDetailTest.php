<?php

namespace AlipayFundAuthBundle\Tests\Entity;

use AlipayFundAuthBundle\Entity\TradeGoodsDetail;
use AlipayFundAuthBundle\Entity\TradeOrder;
use PHPUnit\Framework\Attributes\CoversClass;
use Tourze\PHPUnitDoctrineEntity\AbstractEntityTestCase;

/**
 * @internal
 */
#[CoversClass(TradeGoodsDetail::class)]
final class TradeGoodsDetailTest extends AbstractEntityTestCase
{
    protected function createEntity(): object
    {
        $entity = new TradeGoodsDetail();
        $tradeOrder = new TradeOrder();
        $entity->setTradeOrder($tradeOrder);

        return $entity;
    }

    /**
     * @return iterable<array{string, mixed}>
     */
    public static function propertiesProvider(): iterable
    {
        $tradeOrder = new TradeOrder();
        yield 'tradeOrder' => ['tradeOrder', $tradeOrder];
        yield 'goodsId' => ['goodsId', 'GOODS123456'];
        yield 'goodsName' => ['goodsName', 'Test Product'];
        yield 'quantity' => ['quantity', 5];
        yield 'price' => ['price', '10.50'];
        yield 'goodsCategory' => ['goodsCategory', 'ELECTRONICS'];
        yield 'categoryTree' => ['categoryTree', 'ELECTRONICS>PHONE'];
        yield 'showUrl' => ['showUrl', 'https://example.com/product'];
    }

    public function testToStringReturnsGoodsName(): void
    {
        $entity = new TradeGoodsDetail();
        $entity->setGoodsName('Test Product');

        $this->assertEquals('Test Product', (string) $entity);
    }

    public function testToStringReturnsGoodsIdWhenGoodsNameIsNull(): void
    {
        $entity = new TradeGoodsDetail();
        $entity->setGoodsId('GOODS123456');

        $this->assertEquals('GOODS123456', (string) $entity);
    }

    public function testToStringReturnsIdWhenBothGoodsNameAndGoodsIdAreNull(): void
    {
        $entity = new TradeGoodsDetail();
        $reflection = new \ReflectionClass($entity);
        $property = $reflection->getProperty('id');
        $property->setAccessible(true);
        $property->setValue($entity, '123');

        $this->assertEquals('123', (string) $entity);
    }
}
