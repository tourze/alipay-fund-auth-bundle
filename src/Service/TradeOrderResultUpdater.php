<?php

declare(strict_types=1);

namespace AlipayFundAuthBundle\Service;

use AlipayFundAuthBundle\Entity\TradeOrder;
use AlipayFundAuthBundle\Enum\AsyncPaymentMode;
use AlipayFundAuthBundle\Enum\AuthTradePayMode;

class TradeOrderResultUpdater
{
    public function updateFromResult(TradeOrder $tradeOrder, mixed $result): void
    {
        if (!is_object($result)) {
            return;
        }

        $this->setBasicFields($tradeOrder, $result);
        $this->setAmountFields($tradeOrder, $result);
        $this->setPaymentModeFields($tradeOrder, $result);
        $this->setDiscountFields($tradeOrder, $result);
    }

    private function setBasicFields(TradeOrder $tradeOrder, object $result): void
    {
        $this->setTradeNo($tradeOrder, $result);
        $this->setBuyerInfo($tradeOrder, $result);
        $this->setStoreInfo($tradeOrder, $result);
        $this->setPaymentTime($tradeOrder, $result);
    }

    private function setTradeNo(TradeOrder $tradeOrder, object $result): void
    {
        if (method_exists($result, 'getTradeNo')) {
            $tradeNo = $result->getTradeNo();
            if (is_string($tradeNo)) {
                $tradeOrder->setTradeNo($tradeNo);
            }
        }
    }

    private function setBuyerInfo(TradeOrder $tradeOrder, object $result): void
    {
        if (method_exists($result, 'getBuyerLogonId')) {
            $buyerLogonId = $result->getBuyerLogonId();
            if (is_string($buyerLogonId)) {
                $tradeOrder->setBuyerLogonId($buyerLogonId);
            }
        }

        if (method_exists($result, 'getBuyerUserId')) {
            $buyerUserId = $result->getBuyerUserId();
            if (is_string($buyerUserId)) {
                $tradeOrder->setBuyerUserId($buyerUserId);
            }
        }
    }

    private function setStoreInfo(TradeOrder $tradeOrder, object $result): void
    {
        if (method_exists($result, 'getStoreName')) {
            $storeName = $result->getStoreName();
            if (is_string($storeName)) {
                $tradeOrder->setStoreName($storeName);
            }
        }
    }

    private function setPaymentTime(TradeOrder $tradeOrder, object $result): void
    {
        if (method_exists($result, 'getGmtPayment')) {
            $gmtPayment = $result->getGmtPayment();
            if (null !== $gmtPayment && is_string($gmtPayment)) {
                $tradeOrder->setGmtPayment(new \DateTimeImmutable($gmtPayment));
            }
        }
    }

    private function setAmountFields(TradeOrder $tradeOrder, object $result): void
    {
        $this->setReceiptAmount($tradeOrder, $result);
        $this->setBuyerPayAmount($tradeOrder, $result);
        $this->setPointAmount($tradeOrder, $result);
        $this->setInvoiceAmount($tradeOrder, $result);
    }

    private function setReceiptAmount(TradeOrder $tradeOrder, object $result): void
    {
        if (method_exists($result, 'getReceiptAmount')) {
            $receiptAmount = $result->getReceiptAmount();
            if (is_string($receiptAmount)) {
                $tradeOrder->setReceiptAmount($receiptAmount);
            }
        }
    }

    private function setBuyerPayAmount(TradeOrder $tradeOrder, object $result): void
    {
        if (method_exists($result, 'getBuyerPayAmount')) {
            $buyerPayAmount = $result->getBuyerPayAmount();
            if (is_string($buyerPayAmount)) {
                $tradeOrder->setBuyerPayAmount($buyerPayAmount);
            }
        }
    }

    private function setPointAmount(TradeOrder $tradeOrder, object $result): void
    {
        if (method_exists($result, 'getPointAmount')) {
            $pointAmount = $result->getPointAmount();
            if (is_string($pointAmount)) {
                $tradeOrder->setPointAmount($pointAmount);
            }
        }
    }

    private function setInvoiceAmount(TradeOrder $tradeOrder, object $result): void
    {
        if (method_exists($result, 'getInvoiceAmount')) {
            $invoiceAmount = $result->getInvoiceAmount();
            if (is_string($invoiceAmount)) {
                $tradeOrder->setInvoiceAmount($invoiceAmount);
            }
        }
    }

    private function setPaymentModeFields(TradeOrder $tradeOrder, object $result): void
    {
        $this->setAsyncPaymentMode($tradeOrder, $result);
        $this->setAuthTradePayMode($tradeOrder, $result);
    }

    private function setAsyncPaymentMode(TradeOrder $tradeOrder, object $result): void
    {
        if (method_exists($result, 'getAsyncPaymentMode')) {
            $asyncPaymentMode = $result->getAsyncPaymentMode();
            if (null !== $asyncPaymentMode && is_string($asyncPaymentMode)) {
                $tradeOrder->setAsyncPaymentMode(AsyncPaymentMode::tryFrom($asyncPaymentMode));
            }
        }
    }

    private function setAuthTradePayMode(TradeOrder $tradeOrder, object $result): void
    {
        if (method_exists($result, 'getAuthTradePayMode')) {
            $authTradePayMode = $result->getAuthTradePayMode();
            if (null !== $authTradePayMode && is_string($authTradePayMode)) {
                $tradeOrder->setAuthTradePayMode(AuthTradePayMode::tryFrom($authTradePayMode));
            }
        }
    }

    private function setDiscountFields(TradeOrder $tradeOrder, object $result): void
    {
        $this->setMdiscountAmount($tradeOrder, $result);
        $this->setDiscountAmount($tradeOrder, $result);
    }

    private function setMdiscountAmount(TradeOrder $tradeOrder, object $result): void
    {
        if (method_exists($result, 'getMdiscountAmount')) {
            $mdiscountAmount = $result->getMdiscountAmount();
            if (is_string($mdiscountAmount)) {
                $tradeOrder->setMdiscountAmount($mdiscountAmount);
            }
        }
    }

    private function setDiscountAmount(TradeOrder $tradeOrder, object $result): void
    {
        if (method_exists($result, 'getDiscountAmount')) {
            $discountAmount = $result->getDiscountAmount();
            if (is_string($discountAmount)) {
                $tradeOrder->setDiscountAmount($discountAmount);
            }
        }
    }
}
