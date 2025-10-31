<?php

namespace AlipayFundAuthBundle\Tests\Entity;

use AlipayFundAuthBundle\Entity\TradeFundBill;
use AlipayFundAuthBundle\Entity\TradeOrder;
use PHPUnit\Framework\Attributes\CoversClass;
use Tourze\PHPUnitDoctrineEntity\AbstractEntityTestCase;

/**
 * @internal
 */
#[CoversClass(TradeFundBill::class)]
final class TradeFundBillTest extends AbstractEntityTestCase
{
    protected function createEntity(): object
    {
        $entity = new TradeFundBill();
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
        yield 'fundChannel' => ['fundChannel', 'ALIPAY'];
        yield 'amount' => ['amount', '10.50'];
        yield 'realAmount' => ['realAmount', '10.00'];
    }

    public function testToStringReturnsFundChannel(): void
    {
        $entity = new TradeFundBill();
        $entity->setFundChannel('ALIPAY');

        $this->assertEquals('ALIPAY', (string) $entity);
    }

    public function testToStringReturnsIdWhenFundChannelIsNull(): void
    {
        $entity = new TradeFundBill();
        $reflection = new \ReflectionClass($entity);
        $property = $reflection->getProperty('id');
        $property->setAccessible(true);
        $property->setValue($entity, 123);

        $this->assertEquals('123', (string) $entity);
    }
}
