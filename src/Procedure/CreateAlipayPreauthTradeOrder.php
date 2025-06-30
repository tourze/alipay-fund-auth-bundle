<?php

namespace AlipayFundAuthBundle\Procedure;

use Alipay\OpenAPISDK\Model\AlipayTradePayModel;
use Alipay\OpenAPISDK\Model\GoodsDetail;
use AlipayFundAuthBundle\Entity\TradeOrder;
use AlipayFundAuthBundle\Service\SdkService;
use Carbon\CarbonImmutable;
use Tourze\JsonRPC\Core\Attribute\MethodDoc;
use Tourze\JsonRPC\Core\Attribute\MethodExpose;
use Tourze\JsonRPC\Core\Attribute\MethodTag;
use Tourze\JsonRPCLockBundle\Procedure\LockableProcedure;
use Tourze\JsonRPCLogBundle\Attribute\Log;

#[MethodTag(name: '支付宝')]
#[MethodDoc(summary: '创建预付款交易订单')]
#[MethodExpose(method: 'CreateAlipayPreauthTradeOrder')]
#[Log]
class CreateAlipayPreauthTradeOrder extends LockableProcedure
{
    public function __construct(private readonly SdkService $sdkService)
    {
    }

    public function execute(): array
    {
        // TODO 创建订单
        $object = new TradeOrder();

        $api = $this->sdkService->getTradeApi($object->getAccount());
        $object->setTradeStatus('NO_PAY');
        $model = new AlipayTradePayModel();
        $model->setOutTradeNo($object->getOutTradeNo());
        $model->setAuthNo($object->getFundAuthOrder()->getAuthNo());
        $model->setTotalAmount($object->getTotalAmount());
        $model->setSubject($object->getSubject());
        $model->setProductCode($object->getProductCode());
        $model->setAuthNo($object->getAuthNo());
        if ($object->getAuthConfirmMode() !== null) {
            $model->setAuthConfirmMode($object->getAuthConfirmMode()->value);
        }

        $goodsDetails = [];
        foreach ($object->getGoodsDetails() as $detail) {
            $g = new GoodsDetail();
            $g->setGoodsId($detail->getGoodsId());
            $g->setGoodsName($detail->getGoodsName());
            $g->setQuantity($detail->getQuantity());
            $g->setPrice($detail->getPrice());
            $g->setGoodsCategory($detail->getGoodsCategory());
            $g->setCategoriesTree($detail->getCategoryTree());
            $g->setShowUrl($detail->getShowUrl());
            $goodsDetails[] = $g;
        }
        $model->setGoodsDetail($goodsDetails);

        $model->setStoreId($object->getStoreId());
        $model->setTerminalId($object->getTerminalId());

        $result = $api->pay($model);

        $object->setTradeNo($result->getTradeNo());
        $object->setBuyerLogonId($result->getBuyerLogonId());
        $object->setBuyerUserId($result->getBuyerUserId());
        $object->setReceiptAmount($result->getReceiptAmount());
        $object->setBuyerPayAmount($result->getBuyerPayAmount());
        $object->setPointAmount($result->getPointAmount());
        $object->setInvoiceAmount($result->getInvoiceAmount());
        $object->setGmtPayment(CarbonImmutable::parse($result->getGmtPayment()));
        $object->setStoreName($result->getStoreName());
        if ($result->getAsyncPaymentMode() !== null) {
            $object->setAsyncPaymentMode(\AlipayFundAuthBundle\Enum\AsyncPaymentMode::tryFrom($result->getAsyncPaymentMode()));
        }
        if ($result->getAuthTradePayMode() !== null) {
            $object->setAuthTradePayMode(\AlipayFundAuthBundle\Enum\AuthTradePayMode::tryFrom($result->getAuthTradePayMode()));
        }
        $object->setMdiscountAmount($result->getMdiscountAmount());
        $object->setDiscountAmount($result->getDiscountAmount());

        $result = [];
        $result['__message'] = '创建成功';

        return $result;
    }
}
