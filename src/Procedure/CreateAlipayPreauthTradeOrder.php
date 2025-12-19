<?php

namespace AlipayFundAuthBundle\Procedure;

use Alipay\OpenAPISDK\Api\AlipayTradeApi;
use Alipay\OpenAPISDK\Model\AlipayTradePayDefaultResponse;
use Alipay\OpenAPISDK\Model\AlipayTradePayModel;
use Alipay\OpenAPISDK\Model\AlipayTradePayResponseModel;
use Alipay\OpenAPISDK\Model\GoodsDetail;
use AlipayFundAuthBundle\Entity\TradeGoodsDetail;
use AlipayFundAuthBundle\Entity\TradeOrder;
use AlipayFundAuthBundle\Exception\InvalidFundAuthOrderException;
use AlipayFundAuthBundle\Service\SdkService;
use AlipayFundAuthBundle\Service\TradeOrderResultUpdater;
use AlipayFundAuthBundle\Param\CreateAlipayPreauthTradeOrderParam;
use Tourze\JsonRPC\Core\Attribute\MethodDoc;
use Tourze\JsonRPC\Core\Attribute\MethodExpose;
use Tourze\JsonRPC\Core\Attribute\MethodTag;
use Tourze\JsonRPC\Core\Contracts\RpcParamInterface;
use Tourze\JsonRPC\Core\Result\ArrayResult;
use Tourze\JsonRPCLockBundle\Procedure\LockableProcedure;
use Tourze\JsonRPCLogBundle\Attribute\Log;

#[MethodTag(name: '支付宝')]
#[MethodDoc(summary: '创建预付款交易订单')]
#[MethodExpose(method: 'CreateAlipayPreauthTradeOrder')]
#[Log]
class CreateAlipayPreauthTradeOrder extends LockableProcedure
{
    public function __construct(
        private readonly SdkService $sdkService,
        private readonly TradeOrderResultUpdater $resultUpdater,
    ) {
    }

    /**
     * @phpstan-param CreateAlipayPreauthTradeOrderParam $param
     */
    public function execute(CreateAlipayPreauthTradeOrderParam|RpcParamInterface $param): ArrayResult
    {
        $tradeOrder = new TradeOrder();
        $this->validateTradeOrder($tradeOrder);

        $api = $this->getTradeApi($tradeOrder);
        $tradeOrder->setTradeStatus('NO_PAY');

        $model = $this->buildPaymentModel($tradeOrder);
        /** @var AlipayTradePayResponseModel<mixed, mixed>|AlipayTradePayDefaultResponse<mixed, mixed> $apiResult */
        $apiResult = $api->pay($model);

        $this->resultUpdater->updateFromResult($tradeOrder, $apiResult);

        return new ArrayResult(['__message' => '创建成功']);
    }

    private function validateTradeOrder(TradeOrder $tradeOrder): void
    {
        if (null === $tradeOrder->getAccount()) {
            throw new InvalidFundAuthOrderException('TradeOrder must have an account');
        }

        if (null === $tradeOrder->getFundAuthOrder()) {
            throw new InvalidFundAuthOrderException('TradeOrder must have a fundAuthOrder');
        }
    }

    /**
     * @return AlipayTradeApi
     */
    private function getTradeApi(TradeOrder $tradeOrder): AlipayTradeApi
    {
        $account = $tradeOrder->getAccount();
        if (null === $account) {
            throw new InvalidFundAuthOrderException('TradeOrder must have an account');
        }

        return $this->sdkService->getTradeApi($account);
    }

    /**
     * @return AlipayTradePayModel<mixed, mixed>
     */
    private function buildPaymentModel(TradeOrder $tradeOrder): AlipayTradePayModel
    {
        $model = new AlipayTradePayModel();
        $this->setBasicModelFields($model, $tradeOrder);
        $this->setModelGoodsDetails($model, $tradeOrder);
        $this->setModelStoreFields($model, $tradeOrder);

        return new ArrayResult($model);
    }

    /**
     * @param AlipayTradePayModel<mixed, mixed> $model
     */
    private function setBasicModelFields(AlipayTradePayModel $model, TradeOrder $tradeOrder): void
    {
        $fundAuthOrder = $tradeOrder->getFundAuthOrder();

        $model->setOutTradeNo($tradeOrder->getOutTradeNo());
        if (null !== $fundAuthOrder) {
            $model->setAuthNo($fundAuthOrder->getAuthNo());
        }
        $model->setTotalAmount($tradeOrder->getTotalAmount());
        $model->setSubject($tradeOrder->getSubject());
        $model->setProductCode($tradeOrder->getProductCode());
        $model->setAuthNo($tradeOrder->getAuthNo());

        if (null !== $tradeOrder->getAuthConfirmMode()) {
            $model->setAuthConfirmMode($tradeOrder->getAuthConfirmMode()->value);
        }
    }

    /**
     * @param AlipayTradePayModel<mixed, mixed> $model
     */
    private function setModelGoodsDetails(AlipayTradePayModel $model, TradeOrder $tradeOrder): void
    {
        $goodsDetails = [];
        foreach ($tradeOrder->getGoodsDetails() as $detail) {
            $goodsDetails[] = $this->createGoodsDetail($detail);
        }
        $model->setGoodsDetail($goodsDetails);
    }

    /**
     * @return GoodsDetail<mixed, mixed>
     */
    private function createGoodsDetail(TradeGoodsDetail $detail): GoodsDetail
    {
        $goodsDetail = new GoodsDetail();
        $goodsDetail->setGoodsId($detail->getGoodsId());
        $goodsDetail->setGoodsName($detail->getGoodsName());
        $goodsDetail->setQuantity($detail->getQuantity());
        $goodsDetail->setPrice($detail->getPrice());
        $goodsDetail->setGoodsCategory($detail->getGoodsCategory());
        $goodsDetail->setCategoriesTree($detail->getCategoryTree());
        $goodsDetail->setShowUrl($detail->getShowUrl());

        return new ArrayResult($goodsDetail);
    }

    /**
     * @param AlipayTradePayModel<mixed, mixed> $model
     */
    private function setModelStoreFields(AlipayTradePayModel $model, TradeOrder $tradeOrder): void
    {
        $model->setStoreId($tradeOrder->getStoreId());
        $model->setTerminalId($tradeOrder->getTerminalId());
    }
}
