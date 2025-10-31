<?php

namespace AlipayFundAuthBundle\Tests\Entity;

use AlipayFundAuthBundle\Entity\TradeOrder;
use AlipayFundAuthBundle\Entity\TradePromoParam;
use PHPUnit\Framework\Attributes\CoversClass;
use Tourze\PHPUnitDoctrineEntity\AbstractEntityTestCase;

/**
 * @internal
 */
#[CoversClass(TradePromoParam::class)]
final class TradePromoParamTest extends AbstractEntityTestCase
{
    protected function createEntity(): object
    {
        $entity = new TradePromoParam();
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
        yield 'actualOrderTime' => ['actualOrderTime', new \DateTimeImmutable()];
    }

    public function testToStringReturnsId(): void
    {
        $entity = new TradePromoParam();
        $reflection = new \ReflectionClass($entity);
        $property = $reflection->getProperty('id');
        $property->setAccessible(true);
        $property->setValue($entity, 123);

        $this->assertEquals('123', (string) $entity);
    }
}
