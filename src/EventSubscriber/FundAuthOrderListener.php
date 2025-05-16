<?php

namespace AlipayFundAuthBundle\EventSubscriber;

use Alipay\OpenAPISDK\Model\AlipayFundAuthOrderAppFreezeModel;
use Alipay\OpenAPISDK\Model\PostPayment;
use AlipayFundAuthBundle\Entity\FundAuthOrder;
use AlipayFundAuthBundle\Enum\FundAuthOrderStatus;
use AlipayFundAuthBundle\Service\SdkService;
use Carbon\Carbon;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsEntityListener;
use Doctrine\ORM\Events;

#[AsEntityListener(event: Events::prePersist, method: 'prePersist', entity: FundAuthOrder::class)]
class FundAuthOrderListener
{
    public function __construct(private readonly SdkService $sdkService)
    {
    }

    /**
     * 创建本地前，同步一次到远程
     *
     * @see https://opendocs.alipay.com/open/064jhe?scene=2a9ad7e9012248b0acd5edd04c9f31b6&pathHash=629fa9a5
     */
    public function prePersist(FundAuthOrder $object): void
    {
        $api = $this->sdkService->getFundAuthOrderApi($object->getAccount());

        $model = new AlipayFundAuthOrderAppFreezeModel();
        $model->setOutOrderNo($object->getOutOrderNo());
        $model->setOutRequestNo($object->getOutRequestNo());
        $model->setOrderTitle($object->getOrderTitle());
        $model->setAmount($object->getAmount());
        $model->setProductCode($object->getProductCode());
        if ($object->getPayeeUserId()) {
            $model->setPayeeUserId($object->getPayeeUserId());
        }
        if ($object->getPayeeLogonId()) {
            $model->setPayeeLogonId($object->getPayeeLogonId());
        }
        if ($object->getPayTimeout()) {
            $model->setPayTimeout($object->getPayTimeout());
        }
        if ($object->getTimeExpress()) {
            $model->setTimeoutExpress($object->getTimeExpress());
        }
        if ($object->getExtraParam()) {
            $model->setExtraParam(json_encode($object->getExtraParam()));
        }
        if ($object->getBusinessParams()) {
            $model->setBusinessParams(json_encode($object->getBusinessParams()));
        }
        if ($object->getSceneCode()) {
            $model->setSceneCode($object->getSceneCode());
        }
        if ($object->getTransCurrency()) {
            $model->setTransCurrency($object->getTransCurrency());
        }
        if ($object->getSettleCurrency()) {
            $model->setSettleCurrency($object->getSettleCurrency());
        }
        $postPayments = [];
        foreach ($object->getPostPayments() as $postPayment) {
            $p = new PostPayment();
            $p->setName($postPayment->getName());
            $p->setAmount($postPayment->getAmount());
            $p->setDescription($postPayment->getDescription());
            $postPayments[] = $p;
        }
        $model->setPostPayments($postPayments);

        $result = $api->freeze($model);

        $object->setAuthNo($result->getAuthNo());
        $object->setOperationId($result->getOperationId());
        $object->setStatus(FundAuthOrderStatus::from($result->getStatus()));
        $object->setGmtTrans($result->getGmtTrans() ? Carbon::parse($result->getGmtTrans()) : null);
        $object->setPreAuthType($result->getPreAuthType());
        $object->setCreditAmount($result->getCreditAmount());
        $object->setFundAmount($result->getFundAmount());
        if ($result->getTransCurrency()) {
            $object->setTransCurrency($result->getTransCurrency());
        }
    }
}
