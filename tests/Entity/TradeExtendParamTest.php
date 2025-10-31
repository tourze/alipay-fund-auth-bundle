<?php

namespace AlipayFundAuthBundle\Tests\Entity;

use AlipayFundAuthBundle\Entity\TradeExtendParam;
use AlipayFundAuthBundle\Entity\TradeOrder;
use PHPUnit\Framework\Attributes\CoversClass;
use Tourze\PHPUnitDoctrineEntity\AbstractEntityTestCase;

/**
 * @internal
 */
#[CoversClass(TradeExtendParam::class)]
final class TradeExtendParamTest extends AbstractEntityTestCase
{
    protected function createEntity(): object
    {
        $entity = new TradeExtendParam();
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
        yield 'sysServiceProviderId' => ['sysServiceProviderId', 'SYS123456'];
        yield 'specifiedSellerName' => ['specifiedSellerName', 'Test Seller'];
        yield 'cardType' => ['cardType', 'DEBIT'];
    }

    public function testToStringReturnsSpecifiedSellerName(): void
    {
        $entity = new TradeExtendParam();
        $entity->setSpecifiedSellerName('Test Seller');

        $this->assertEquals('Test Seller', (string) $entity);
    }

    public function testToStringReturnsIdWhenSpecifiedSellerNameIsNull(): void
    {
        $entity = new TradeExtendParam();
        $reflection = new \ReflectionClass($entity);
        $property = $reflection->getProperty('id');
        $property->setAccessible(true);
        $property->setValue($entity, 123);

        $this->assertEquals('123', (string) $entity);
    }
}
