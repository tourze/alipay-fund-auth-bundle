<?php

declare(strict_types=1);

namespace AlipayFundAuthBundle\Service;

use AlipayFundAuthBundle\Entity\TradeOrder;
use AlipayFundAuthBundle\Enum\AsyncPaymentMode;
use AlipayFundAuthBundle\Enum\AuthTradePayMode;
use InvalidArgumentException;

/**
 * 交易订单结果更新器
 *
 * 负责将支付宝API返回的结果更新到TradeOrder实体中
 */
final readonly class TradeOrderResultUpdater
{
    /**
     * 从API结果更新交易订单
     *
     * @param TradeOrder $tradeOrder 交易订单实体
     * @param mixed $result API返回结果
     * @throws InvalidArgumentException 当结果格式无效时
     */
    public function updateFromResult(TradeOrder $tradeOrder, mixed $result): void
    {
        if (!is_object($result)) {
            throw new InvalidArgumentException('Result must be an object');
        }

        $this->setBasicFields($tradeOrder, $result);
        $this->setAmountFields($tradeOrder, $result);
        $this->setPaymentModeFields($tradeOrder, $result);
        $this->setDiscountFields($tradeOrder, $result);
    }

    /**
     * 设置基础字段
     */
    private function setBasicFields(TradeOrder $tradeOrder, object $result): void
    {
        $this->setTradeNo($tradeOrder, $result);
        $this->setBuyerInfo($tradeOrder, $result);
        $this->setStoreInfo($tradeOrder, $result);
        $this->setPaymentTime($tradeOrder, $result);
    }

    /**
     * 设置交易号
     */
    private function setTradeNo(TradeOrder $tradeOrder, object $result): void
    {
        $tradeNo = $this->getMethodResult($result, 'getTradeNo');
        if (is_string($tradeNo) && $tradeNo !== '') {
            $tradeOrder->setTradeNo($tradeNo);
        }
    }

    /**
     * 设置买家信息
     */
    private function setBuyerInfo(TradeOrder $tradeOrder, object $result): void
    {
        $buyerLogonId = $this->getMethodResult($result, 'getBuyerLogonId');
        if (is_string($buyerLogonId) && $buyerLogonId !== '') {
            $tradeOrder->setBuyerLogonId($buyerLogonId);
        }

        $buyerUserId = $this->getMethodResult($result, 'getBuyerUserId');
        if (is_string($buyerUserId) && $buyerUserId !== '') {
            $tradeOrder->setBuyerUserId($buyerUserId);
        }
    }

    /**
     * 设置店铺信息
     */
    private function setStoreInfo(TradeOrder $tradeOrder, object $result): void
    {
        $storeName = $this->getMethodResult($result, 'getStoreName');
        if (is_string($storeName) && $storeName !== '') {
            $tradeOrder->setStoreName($storeName);
        }
    }

    /**
     * 设置支付时间
     */
    private function setPaymentTime(TradeOrder $tradeOrder, object $result): void
    {
        $gmtPayment = $this->getMethodResult($result, 'getGmtPayment');
        if (is_string($gmtPayment) && $gmtPayment !== '') {
            try {
                $tradeOrder->setGmtPayment(new \DateTimeImmutable($gmtPayment));
            } catch (\Exception $e) {
                // 忽略无效的日期格式，避免因日期解析失败影响其他字段更新
            }
        }
    }

    /**
     * 设置金额相关字段
     */
    private function setAmountFields(TradeOrder $tradeOrder, object $result): void
    {
        $this->setReceiptAmount($tradeOrder, $result);
        $this->setBuyerPayAmount($tradeOrder, $result);
        $this->setPointAmount($tradeOrder, $result);
        $this->setInvoiceAmount($tradeOrder, $result);
    }

    /**
     * 设置实收金额
     */
    private function setReceiptAmount(TradeOrder $tradeOrder, object $result): void
    {
        $receiptAmount = $this->getMethodResult($result, 'getReceiptAmount');
        if (is_string($receiptAmount) && $receiptAmount !== '') {
            $tradeOrder->setReceiptAmount($receiptAmount);
        }
    }

    /**
     * 设置买家支付金额
     */
    private function setBuyerPayAmount(TradeOrder $tradeOrder, object $result): void
    {
        $buyerPayAmount = $this->getMethodResult($result, 'getBuyerPayAmount');
        if (is_string($buyerPayAmount) && $buyerPayAmount !== '') {
            $tradeOrder->setBuyerPayAmount($buyerPayAmount);
        }
    }

    /**
     * 设置积分金额
     */
    private function setPointAmount(TradeOrder $tradeOrder, object $result): void
    {
        $pointAmount = $this->getMethodResult($result, 'getPointAmount');
        if (is_string($pointAmount) && $pointAmount !== '') {
            $tradeOrder->setPointAmount($pointAmount);
        }
    }

    /**
     * 设置发票金额
     */
    private function setInvoiceAmount(TradeOrder $tradeOrder, object $result): void
    {
        $invoiceAmount = $this->getMethodResult($result, 'getInvoiceAmount');
        if (is_string($invoiceAmount) && $invoiceAmount !== '') {
            $tradeOrder->setInvoiceAmount($invoiceAmount);
        }
    }

    /**
     * 设置支付模式相关字段
     */
    private function setPaymentModeFields(TradeOrder $tradeOrder, object $result): void
    {
        $this->setAsyncPaymentMode($tradeOrder, $result);
        $this->setAuthTradePayMode($tradeOrder, $result);
    }

    /**
     * 设置异步支付模式
     */
    private function setAsyncPaymentMode(TradeOrder $tradeOrder, object $result): void
    {
        $asyncPaymentMode = $this->getMethodResult($result, 'getAsyncPaymentMode');
        if (is_string($asyncPaymentMode) && $asyncPaymentMode !== '') {
            $tradeOrder->setAsyncPaymentMode(AsyncPaymentMode::tryFrom($asyncPaymentMode));
        }
    }

    /**
     * 设置授权交易支付模式
     */
    private function setAuthTradePayMode(TradeOrder $tradeOrder, object $result): void
    {
        $authTradePayMode = $this->getMethodResult($result, 'getAuthTradePayMode');
        if (is_string($authTradePayMode) && $authTradePayMode !== '') {
            $tradeOrder->setAuthTradePayMode(AuthTradePayMode::tryFrom($authTradePayMode));
        }
    }

    /**
     * 设置折扣相关字段
     */
    private function setDiscountFields(TradeOrder $tradeOrder, object $result): void
    {
        $this->setMdiscountAmount($tradeOrder, $result);
        $this->setDiscountAmount($tradeOrder, $result);
    }

    /**
     * 设置商家折扣金额
     */
    private function setMdiscountAmount(TradeOrder $tradeOrder, object $result): void
    {
        $mdiscountAmount = $this->getMethodResult($result, 'getMdiscountAmount');
        if (is_string($mdiscountAmount) && $mdiscountAmount !== '') {
            $tradeOrder->setMdiscountAmount($mdiscountAmount);
        }
    }

    /**
     * 设置折扣金额
     */
    private function setDiscountAmount(TradeOrder $tradeOrder, object $result): void
    {
        $discountAmount = $this->getMethodResult($result, 'getDiscountAmount');
        if (is_string($discountAmount) && $discountAmount !== '') {
            $tradeOrder->setDiscountAmount($discountAmount);
        }
    }

    /**
     * 安全地调用对象方法并返回结果
     *
     * @param object $object 目标对象
     * @param string $methodName 方法名
     * @return mixed 方法返回值，如果方法不存在则返回null
     */
    private function getMethodResult(object $object, string $methodName): mixed
    {
        if (!method_exists($object, $methodName)) {
            return null;
        }
        $callable = [$object, $methodName];
        assert(is_callable($callable));

        return $callable();
    }
}
