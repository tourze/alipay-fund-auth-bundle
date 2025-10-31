<?php

namespace AlipayFundAuthBundle\EventSubscriber;

use Alipay\OpenAPISDK\Api\AlipayFundAuthOrderApi;
use Alipay\OpenAPISDK\Model\AlipayFundAuthOrderUnfreezeDefaultResponse;
use Alipay\OpenAPISDK\Model\AlipayFundAuthOrderUnfreezeModel;
use Alipay\OpenAPISDK\Model\AlipayFundAuthOrderUnfreezeResponseModel;
use AlipayFundAuthBundle\Entity\FundAuthUnfreezeLog;
use AlipayFundAuthBundle\Exception\InvalidFundAuthOrderException;
use AlipayFundAuthBundle\Service\SdkService;
use Carbon\CarbonImmutable;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsEntityListener;
use Doctrine\ORM\Events;

#[AsEntityListener(event: Events::prePersist, method: 'prePersist', entity: FundAuthUnfreezeLog::class)]
class FundAuthUnfreezeLogListener
{
    public function __construct(private readonly SdkService $sdkService, private readonly string $environment = 'prod')
    {
    }

    /**
     * 创建本地前，同步一次到远程
     *
     * @see https://opendocs.alipay.com/open/064jhi?scene=common&pathHash=7435c8fd
     */
    public function prePersist(FundAuthUnfreezeLog $object): void
    {
        if (in_array($this->environment, ['test', 'dev'], true)) {
            return;
        }

        $this->validateRequiredEntities($object);
        $api = $this->getAuthOrderApi($object);
        $model = $this->buildUnfreezeModel($object);
        /** @var AlipayFundAuthOrderUnfreezeResponseModel<mixed, mixed>|AlipayFundAuthOrderUnfreezeDefaultResponse<mixed, mixed> $result */
        $result = $api->unfreeze($model);
        $this->updateObjectFromResult($object, $result);
    }

    private function validateRequiredEntities(FundAuthUnfreezeLog $object): void
    {
        $fundAuthOrder = $object->getFundAuthOrder();
        if (null === $fundAuthOrder) {
            throw new InvalidFundAuthOrderException('FundAuthUnfreezeLog must have a fundAuthOrder');
        }

        if (null === $fundAuthOrder->getAccount()) {
            throw new InvalidFundAuthOrderException('FundAuthOrder must have an account');
        }
    }

    /**
     * @return AlipayFundAuthOrderApi
     */
    private function getAuthOrderApi(FundAuthUnfreezeLog $object): AlipayFundAuthOrderApi
    {
        $fundAuthOrder = $object->getFundAuthOrder();
        if (null === $fundAuthOrder) {
            throw new InvalidFundAuthOrderException('FundAuthOrder is required');
        }

        $account = $fundAuthOrder->getAccount();
        if (null === $account) {
            throw new InvalidFundAuthOrderException('Account is required');
        }

        return $this->sdkService->getFundAuthOrderApi($account);
    }

    /**
     * @return AlipayFundAuthOrderUnfreezeModel<mixed, mixed>
     */
    private function buildUnfreezeModel(FundAuthUnfreezeLog $object): AlipayFundAuthOrderUnfreezeModel
    {
        $fundAuthOrder = $object->getFundAuthOrder();
        if (null === $fundAuthOrder) {
            throw new InvalidFundAuthOrderException('FundAuthOrder is required');
        }

        $model = new AlipayFundAuthOrderUnfreezeModel();
        $model->setAuthNo($fundAuthOrder->getAuthNo());
        $model->setOutRequestNo($object->getOutRequestNo());
        $model->setAmount($object->getAmount());
        $model->setRemark($object->getRemark());

        $this->setExtraParamIfPresent($model, $object);

        return $model;
    }

    /**
     * @param AlipayFundAuthOrderUnfreezeModel<mixed, mixed> $model
     */
    private function setExtraParamIfPresent(AlipayFundAuthOrderUnfreezeModel $model, FundAuthUnfreezeLog $object): void
    {
        $extraParam = $object->getExtraParam();
        if (null === $extraParam || [] === $extraParam) {
            return;
        }

        $jsonExtraParam = json_encode($extraParam);
        if (false !== $jsonExtraParam) {
            $model->setExtraParam($jsonExtraParam);
        }
    }

    private function updateObjectFromResult(FundAuthUnfreezeLog $object, mixed $result): void
    {
        if (!is_object($result)) {
            return;
        }

        $this->setOperationIdFromResult($object, $result);
        $this->setStatusFromResult($object, $result);
        $this->setGmtTransIfPresent($object, $result);
        $this->setAmountFieldsFromResult($object, $result);
    }

    private function setOperationIdFromResult(FundAuthUnfreezeLog $object, object $result): void
    {
        if (method_exists($result, 'getOperationId')) {
            $operationId = $result->getOperationId();
            if (is_string($operationId)) {
                $object->setOperationId($operationId);
            }
        }
    }

    private function setStatusFromResult(FundAuthUnfreezeLog $object, object $result): void
    {
        if (method_exists($result, 'getStatus')) {
            $status = $result->getStatus();
            if (is_string($status)) {
                $object->setStatus($status);
            }
        }
    }

    private function setAmountFieldsFromResult(FundAuthUnfreezeLog $object, object $result): void
    {
        if (method_exists($result, 'getCreditAmount')) {
            $creditAmount = $result->getCreditAmount();
            if (is_string($creditAmount)) {
                $object->setCreditAmount($creditAmount);
            }
        }

        if (method_exists($result, 'getFundAmount')) {
            $fundAmount = $result->getFundAmount();
            if (is_string($fundAmount)) {
                $object->setFundAmount($fundAmount);
            }
        }
    }

    private function setGmtTransIfPresent(FundAuthUnfreezeLog $object, mixed $result): void
    {
        if (!is_object($result) || !method_exists($result, 'getGmtTrans')) {
            return;
        }

        $gmtTrans = $result->getGmtTrans();
        $parsedGmtTrans = (null !== $gmtTrans && '' !== $gmtTrans && is_string($gmtTrans))
            ? CarbonImmutable::parse($gmtTrans)
            : null;

        $object->setGmtTrans($parsedGmtTrans);
    }
}
