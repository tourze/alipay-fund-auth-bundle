<?php

declare(strict_types=1);

namespace AlipayFundAuthBundle\Tests\Service;

use AlipayFundAuthBundle\Entity\TradeOrder;
use AlipayFundAuthBundle\Enum\AsyncPaymentMode;
use AlipayFundAuthBundle\Enum\AuthTradePayMode;
use AlipayFundAuthBundle\Service\TradeOrderResultUpdater;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
#[CoversClass(TradeOrderResultUpdater::class)]
final class TradeOrderResultUpdaterTest extends TestCase
{
    private TradeOrderResultUpdater $updater;

    protected function setUp(): void
    {
        $this->updater = new TradeOrderResultUpdater();
    }

    public function testUpdateFromResultWithNonObject(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Result must be an object');

        $tradeOrder = new TradeOrder();
        $this->updater->updateFromResult($tradeOrder, 'invalid');
    }

    public function testUpdateFromResultWithValidObject(): void
    {
        $tradeOrder = new TradeOrder();
        $result = new class {
            public function getTradeNo(): string
            {
                return 'test_trade_no';
            }

            public function getBuyerLogonId(): string
            {
                return 'test@example.com';
            }

            public function getReceiptAmount(): string
            {
                return '100.00';
            }
        };

        $this->updater->updateFromResult($tradeOrder, $result);

        $this->assertSame('test_trade_no', $tradeOrder->getTradeNo());
        $this->assertSame('test@example.com', $tradeOrder->getBuyerLogonId());
        $this->assertSame('100.00', $tradeOrder->getReceiptAmount());
    }

    public function testUpdateFromResultWithEnumFields(): void
    {
        $tradeOrder = new TradeOrder();
        $result = new class {
            public function getAsyncPaymentMode(): string
            {
                return 'ASYNC_DELAY_PAY';
            }

            public function getAuthTradePayMode(): string
            {
                return 'CREDIT_PREAUTH_PAY';
            }
        };

        $this->updater->updateFromResult($tradeOrder, $result);

        $this->assertSame(AsyncPaymentMode::ASYNC_DELAY_PAY, $tradeOrder->getAsyncPaymentMode());
        $this->assertSame(AuthTradePayMode::CREDIT_PREAUTH_PAY, $tradeOrder->getAuthTradePayMode());
    }

    public function testUpdateFromResultWithGmtPayment(): void
    {
        $tradeOrder = new TradeOrder();
        $result = new class {
            public function getGmtPayment(): string
            {
                return '2023-01-01 12:00:00';
            }
        };

        $this->updater->updateFromResult($tradeOrder, $result);

        $this->assertInstanceOf(\DateTimeImmutable::class, $tradeOrder->getGmtPayment());
    }
}
