<?php

namespace AlipayFundAuthBundle\Tests\Entity;

use AlipayFundAuthBundle\Entity\TradeOrder;
use AlipayFundAuthBundle\Entity\TradeVoucherDetail;
use AlipayFundAuthBundle\Enum\VoucherType;
use PHPUnit\Framework\Attributes\CoversClass;
use Tourze\PHPUnitDoctrineEntity\AbstractEntityTestCase;

/**
 * @internal
 */
#[CoversClass(TradeVoucherDetail::class)]
final class TradeVoucherDetailTest extends AbstractEntityTestCase
{
    protected function createEntity(): object
    {
        $entity = new TradeVoucherDetail();
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
        yield 'voucherId' => ['voucherId', 'VOUCHER123456'];
        yield 'name' => ['name', 'Test Voucher'];
        yield 'type' => ['type', VoucherType::ALIPAY_CASH_VOUCHER];
        yield 'amount' => ['amount', '10.50'];
        yield 'merchantContribute' => ['merchantContribute', '5.00'];
        yield 'otherContribute' => ['otherContribute', '3.00'];
        yield 'memo' => ['memo', 'Test memo'];
        yield 'templateId' => ['templateId', 'TEMPLATE123456'];
        yield 'purchaseBuyerContribute' => ['purchaseBuyerContribute', '2.50'];
        yield 'purchaseMerchantContribute' => ['purchaseMerchantContribute', '1.50'];
        yield 'purchaseAntContribute' => ['purchaseAntContribute', '1.00'];
    }

    public function testToStringReturnsName(): void
    {
        $entity = new TradeVoucherDetail();
        $entity->setName('Test Voucher');

        $this->assertEquals('Test Voucher', (string) $entity);
    }

    public function testToStringReturnsVoucherIdWhenNameIsNull(): void
    {
        $entity = new TradeVoucherDetail();
        $entity->setVoucherId('VOUCHER123456');

        $this->assertEquals('VOUCHER123456', (string) $entity);
    }

    public function testToStringReturnsIdWhenBothNameAndVoucherIdAreNull(): void
    {
        $entity = new TradeVoucherDetail();
        $reflection = new \ReflectionClass($entity);
        $property = $reflection->getProperty('id');
        $property->setAccessible(true);
        $property->setValue($entity, 123);

        $this->assertEquals('123', (string) $entity);
    }
}
