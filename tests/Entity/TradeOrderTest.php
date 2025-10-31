<?php

namespace AlipayFundAuthBundle\Tests\Entity;

use AlipayFundAuthBundle\Entity\Account;
use AlipayFundAuthBundle\Entity\FundAuthOrder;
use AlipayFundAuthBundle\Entity\TradeFundBill;
use AlipayFundAuthBundle\Entity\TradeGoodsDetail;
use AlipayFundAuthBundle\Entity\TradeOrder;
use AlipayFundAuthBundle\Entity\TradeVoucherDetail;
use AlipayFundAuthBundle\Enum\AliPayType;
use AlipayFundAuthBundle\Enum\AsyncPaymentMode;
use AlipayFundAuthBundle\Enum\AuthConfirmMode;
use AlipayFundAuthBundle\Enum\AuthTradePayMode;
use PHPUnit\Framework\Attributes\CoversClass;
use Tourze\PHPUnitDoctrineEntity\AbstractEntityTestCase;

/**
 * @internal
 */
#[CoversClass(TradeOrder::class)]
final class TradeOrderTest extends AbstractEntityTestCase
{
    protected function createEntity(): object
    {
        $entity = new TradeOrder();
        $account = new Account();
        $entity->setAccount($account);

        return $entity;
    }

    /**
     * @return iterable<array{string, mixed}>
     */
    public static function propertiesProvider(): iterable
    {
        $account = new Account();
        $fundAuthOrder = new FundAuthOrder();
        yield 'account' => ['account', $account];
        yield 'fundAuthOrder' => ['fundAuthOrder', $fundAuthOrder];
        yield 'outTradeNo' => ['outTradeNo', 'TEST123456'];
        yield 'totalAmount' => ['totalAmount', '100.50'];
        yield 'subject' => ['subject', 'Test Order'];
        yield 'productCode' => ['productCode', 'PREAUTH_PAY'];
        yield 'authNo' => ['authNo', 'AUTH123456'];
        yield 'authConfirmMode' => ['authConfirmMode', AuthConfirmMode::COMPLETE];
        yield 'storeId' => ['storeId', 'STORE001'];
        yield 'terminalId' => ['terminalId', 'TERM001'];
        yield 'tradeNo' => ['tradeNo', 'TRADE123456'];
        yield 'buyerLogonId' => ['buyerLogonId', 'buyer@example.com'];
        yield 'receiptAmount' => ['receiptAmount', '100.50'];
        yield 'buyerPayAmount' => ['buyerPayAmount', '100.50'];
        yield 'pointAmount' => ['pointAmount', '10.00'];
        yield 'invoiceAmount' => ['invoiceAmount', '100.50'];
        yield 'gmtPayment' => ['gmtPayment', new \DateTimeImmutable()];
        yield 'storeName' => ['storeName', 'Test Store'];
        yield 'buyerUserId' => ['buyerUserId', '2088123456789012'];
        yield 'buyerOpenId' => ['buyerOpenId', 'OPENID123456'];
        yield 'asyncPaymentMode' => ['asyncPaymentMode', AsyncPaymentMode::SYNC_DIRECT_PAY];
        yield 'authTradePayMode' => ['authTradePayMode', AuthTradePayMode::CREDIT_PREAUTH_PAY];
        yield 'payType' => ['payType', AliPayType::ALIPAY_AOPAPP];
        yield 'mdiscountAmount' => ['mdiscountAmount', '5.00'];
        yield 'discountAmount' => ['discountAmount', '10.00'];
        yield 'tradeStatus' => ['tradeStatus', 'TRADE_SUCCESS'];
        yield 'notifyPayload' => ['notifyPayload', ['key' => 'value']];
    }

    public function testToStringReturnsOutTradeNo(): void
    {
        $entity = new TradeOrder();
        $entity->setOutTradeNo('TEST123456');

        $this->assertEquals('TEST123456', (string) $entity);
    }

    public function testToStringReturnsIdWhenOutTradeNoIsNull(): void
    {
        $entity = new TradeOrder();
        $reflection = new \ReflectionClass($entity);
        $property = $reflection->getProperty('id');
        $property->setAccessible(true);
        $property->setValue($entity, '123');

        $this->assertEquals('123', (string) $entity);
    }

    public function testGoodsDetailsCollection(): void
    {
        $entity = new TradeOrder();
        $goodsDetail = new TradeGoodsDetail();

        $this->assertCount(0, $entity->getGoodsDetails());

        $entity->addGoodsDetail($goodsDetail);
        $this->assertCount(1, $entity->getGoodsDetails());
        $this->assertTrue($entity->getGoodsDetails()->contains($goodsDetail));

        $entity->removeGoodsDetail($goodsDetail);
        $this->assertCount(0, $entity->getGoodsDetails());
    }

    public function testFundBillsCollection(): void
    {
        $entity = new TradeOrder();
        $fundBill = new TradeFundBill();

        $this->assertCount(0, $entity->getFundBills());

        $entity->addFundBill($fundBill);
        $this->assertCount(1, $entity->getFundBills());
        $this->assertTrue($entity->getFundBills()->contains($fundBill));

        $entity->removeFundBill($fundBill);
        $this->assertCount(0, $entity->getFundBills());
    }

    public function testVoucherDetailsCollection(): void
    {
        $entity = new TradeOrder();
        $voucherDetail = new TradeVoucherDetail();

        $this->assertCount(0, $entity->getVoucherDetails());

        $entity->addVoucherDetail($voucherDetail);
        $this->assertCount(1, $entity->getVoucherDetails());
        $this->assertTrue($entity->getVoucherDetails()->contains($voucherDetail));

        $entity->removeVoucherDetail($voucherDetail);
        $this->assertCount(0, $entity->getVoucherDetails());
    }
}
