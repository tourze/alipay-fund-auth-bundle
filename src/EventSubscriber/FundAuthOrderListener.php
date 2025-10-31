<?php

namespace AlipayFundAuthBundle\EventSubscriber;

use Alipay\OpenAPISDK\Model\AlipayFundAuthOrderFreezeDefaultResponse;
use Alipay\OpenAPISDK\Model\AlipayFundAuthOrderFreezeModel;
use Alipay\OpenAPISDK\Model\AlipayFundAuthOrderFreezeResponseModel;
use Alipay\OpenAPISDK\Model\PostPayment;
use AlipayFundAuthBundle\Entity\FundAuthOrder;
use AlipayFundAuthBundle\Enum\FundAuthOrderStatus;
use AlipayFundAuthBundle\Exception\InvalidFundAuthOrderException;
use AlipayFundAuthBundle\Service\SdkService;
use Carbon\CarbonImmutable;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsEntityListener;
use Doctrine\ORM\Events;

#[AsEntityListener(event: Events::prePersist, method: 'prePersist', entity: FundAuthOrder::class)]
class FundAuthOrderListener
{
    public function __construct(private readonly SdkService $sdkService, private readonly string $environment = 'prod')
    {
    }

    /**
     * 创建本地前，同步一次到远程
     *
     * @see https://opendocs.alipay.com/open/064jhe?scene=2a9ad7e9012248b0acd5edd04c9f31b6&pathHash=629fa9a5
     */
    public function prePersist(FundAuthOrder $object): void
    {
        if (in_array($this->environment, ['test', 'dev'], true)) {
            return;
        }

        $account = $object->getAccount();
        if (null === $account) {
            throw new InvalidFundAuthOrderException('FundAuthOrder must have an account');
        }
        $api = $this->sdkService->getFundAuthOrderApi($account);
        $model = $this->buildFreezeModel($object);
        $result = $api->freeze($model);
        $this->updateOrderFromResult($object, $result);
    }

    /**
     * @return AlipayFundAuthOrderFreezeModel<mixed,mixed>
     */
    private function buildFreezeModel(FundAuthOrder $object): AlipayFundAuthOrderFreezeModel
    {
        $model = new AlipayFundAuthOrderFreezeModel();
        $this->setBasicFields($model, $object);
        $this->setOptionalFields($model, $object);
        $this->setPostPayments($model, $object);

        return $model;
    }

    /**
     * @param AlipayFundAuthOrderFreezeModel<mixed,mixed> $model
     */
    private function setBasicFields(AlipayFundAuthOrderFreezeModel $model, FundAuthOrder $object): void
    {
        $model->setOutOrderNo($object->getOutOrderNo());
        $model->setOutRequestNo($object->getOutRequestNo());
        $model->setOrderTitle($object->getOrderTitle());
        $model->setAmount($object->getAmount());
        $model->setProductCode($object->getProductCode());
    }

    /**
     * @param AlipayFundAuthOrderFreezeModel<mixed,mixed> $model
     */
    private function setOptionalFields(AlipayFundAuthOrderFreezeModel $model, FundAuthOrder $object): void
    {
        $this->setPayeeFields($model, $object);
        $this->setTimeoutFields($model, $object);
        $this->setCurrencyFields($model, $object);
        $this->setSceneField($model, $object);
        $this->setParamFields($model, $object);
    }

    /**
     * @param AlipayFundAuthOrderFreezeModel<mixed,mixed> $model
     */
    private function setPayeeFields(AlipayFundAuthOrderFreezeModel $model, FundAuthOrder $object): void
    {
        if (null !== $object->getPayeeUserId() && '' !== $object->getPayeeUserId()) {
            $model->setPayeeUserId($object->getPayeeUserId());
        }
        if (null !== $object->getPayeeLogonId() && '' !== $object->getPayeeLogonId()) {
            $model->setPayeeLogonId($object->getPayeeLogonId());
        }
    }

    /**
     * @param AlipayFundAuthOrderFreezeModel<mixed,mixed> $model
     */
    private function setTimeoutFields(AlipayFundAuthOrderFreezeModel $model, FundAuthOrder $object): void
    {
        if (null !== $object->getPayTimeout() && '' !== $object->getPayTimeout()) {
            $model->setPayTimeout($object->getPayTimeout());
        }
        if (null !== $object->getTimeExpress() && '' !== $object->getTimeExpress()) {
            $model->setTimeoutExpress($object->getTimeExpress());
        }
    }

    /**
     * @param AlipayFundAuthOrderFreezeModel<mixed,mixed> $model
     */
    private function setCurrencyFields(AlipayFundAuthOrderFreezeModel $model, FundAuthOrder $object): void
    {
        if (null !== $object->getTransCurrency() && '' !== $object->getTransCurrency()) {
            $model->setTransCurrency($object->getTransCurrency());
        }
        if (null !== $object->getSettleCurrency() && '' !== $object->getSettleCurrency()) {
            $model->setSettleCurrency($object->getSettleCurrency());
        }
    }

    /**
     * @param AlipayFundAuthOrderFreezeModel<mixed,mixed> $model
     */
    private function setSceneField(AlipayFundAuthOrderFreezeModel $model, FundAuthOrder $object): void
    {
        if (null !== $object->getSceneCode() && '' !== $object->getSceneCode()) {
            $model->setSceneCode($object->getSceneCode());
        }
    }

    /**
     * @param AlipayFundAuthOrderFreezeModel<mixed,mixed> $model
     */
    private function setParamFields(AlipayFundAuthOrderFreezeModel $model, FundAuthOrder $object): void
    {
        $extraParam = $object->getExtraParam();
        if (null !== $extraParam && [] !== $extraParam) {
            $jsonExtraParam = json_encode($extraParam);
            if (false !== $jsonExtraParam) {
                $model->setExtraParam($jsonExtraParam);
            }
        }

        $businessParams = $object->getBusinessParams();
        if (null !== $businessParams && [] !== $businessParams) {
            $jsonBusinessParams = json_encode($businessParams);
            if (false !== $jsonBusinessParams) {
                $model->setBusinessParams($jsonBusinessParams);
            }
        }
    }

    /**
     * @param AlipayFundAuthOrderFreezeModel<mixed,mixed> $model
     */
    private function setPostPayments(AlipayFundAuthOrderFreezeModel $model, FundAuthOrder $object): void
    {
        $postPayments = [];
        foreach ($object->getPostPayments() as $postPayment) {
            $p = new PostPayment();
            $p->setName($postPayment->getName());
            $p->setAmount($postPayment->getAmount());
            $p->setDescription($postPayment->getDescription());
            $postPayments[] = $p;
        }
        $model->setPostPayments($postPayments);
    }

    /**
     * @param AlipayFundAuthOrderFreezeResponseModel<mixed, mixed>|AlipayFundAuthOrderFreezeDefaultResponse<mixed, mixed> $result
     */
    private function updateOrderFromResult(FundAuthOrder $object, $result): void
    {
        // Only AlipayFundAuthOrderFreezeResponseModel has the getter methods
        if (!$result instanceof AlipayFundAuthOrderFreezeResponseModel) {
            return;
        }

        $this->setAuthNo($object, $result);
        $this->setOperationId($object, $result);
        $this->setStatusFromResult($object, $result);
        $this->setGmtTransFromResult($object, $result);
        $this->setPreAuthType($object, $result);
        $this->setCreditAmount($object, $result);
        $this->setFundAmount($object, $result);
        $this->setTransCurrencyFromResult($object, $result);
    }

    /**
     * @param AlipayFundAuthOrderFreezeResponseModel<mixed, mixed> $result
     */
    private function setAuthNo(FundAuthOrder $object, AlipayFundAuthOrderFreezeResponseModel $result): void
    {
        $authNo = $result->getAuthNo();
        if (is_string($authNo)) {
            $object->setAuthNo($authNo);
        }
    }

    /**
     * @param AlipayFundAuthOrderFreezeResponseModel<mixed, mixed> $result
     */
    private function setOperationId(FundAuthOrder $object, AlipayFundAuthOrderFreezeResponseModel $result): void
    {
        $operationId = $result->getOperationId();
        if (is_string($operationId)) {
            $object->setOperationId($operationId);
        }
    }

    /**
     * @param AlipayFundAuthOrderFreezeResponseModel<mixed, mixed> $result
     */
    private function setStatusFromResult(FundAuthOrder $object, AlipayFundAuthOrderFreezeResponseModel $result): void
    {
        $status = $result->getStatus();
        if (is_string($status)) {
            $object->setStatus(FundAuthOrderStatus::from($status));
        }
    }

    /**
     * @param AlipayFundAuthOrderFreezeResponseModel<mixed, mixed> $result
     */
    private function setGmtTransFromResult(FundAuthOrder $object, AlipayFundAuthOrderFreezeResponseModel $result): void
    {
        $gmtTrans = $result->getGmtTrans();
        $object->setGmtTrans(null !== $gmtTrans && '' !== $gmtTrans && is_string($gmtTrans) ? CarbonImmutable::parse($gmtTrans) : null);
    }

    /**
     * @param AlipayFundAuthOrderFreezeResponseModel<mixed, mixed> $result
     */
    private function setPreAuthType(FundAuthOrder $object, AlipayFundAuthOrderFreezeResponseModel $result): void
    {
        $preAuthType = $result->getPreAuthType();
        if (is_string($preAuthType)) {
            $object->setPreAuthType($preAuthType);
        }
    }

    /**
     * @param AlipayFundAuthOrderFreezeResponseModel<mixed, mixed> $result
     */
    private function setCreditAmount(FundAuthOrder $object, AlipayFundAuthOrderFreezeResponseModel $result): void
    {
        $creditAmount = $result->getCreditAmount();
        if (is_string($creditAmount)) {
            $object->setCreditAmount($creditAmount);
        }
    }

    /**
     * @param AlipayFundAuthOrderFreezeResponseModel<mixed, mixed> $result
     */
    private function setFundAmount(FundAuthOrder $object, AlipayFundAuthOrderFreezeResponseModel $result): void
    {
        $fundAmount = $result->getFundAmount();
        if (is_string($fundAmount)) {
            $object->setFundAmount($fundAmount);
        }
    }

    /**
     * @param AlipayFundAuthOrderFreezeResponseModel<mixed, mixed> $result
     */
    private function setTransCurrencyFromResult(FundAuthOrder $object, AlipayFundAuthOrderFreezeResponseModel $result): void
    {
        $transCurrency = $result->getTransCurrency();
        if (null !== $transCurrency && '' !== $transCurrency && is_string($transCurrency)) {
            $object->setTransCurrency($transCurrency);
        }
    }
}
