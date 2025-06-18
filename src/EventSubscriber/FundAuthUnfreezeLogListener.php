<?php

namespace AlipayFundAuthBundle\EventSubscriber;

use Alipay\OpenAPISDK\Model\AlipayFundAuthOrderUnfreezeModel;
use AlipayFundAuthBundle\Entity\FundAuthUnfreezeLog;
use AlipayFundAuthBundle\Service\SdkService;
use Carbon\Carbon;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsEntityListener;
use Doctrine\ORM\Events;

#[AsEntityListener(event: Events::prePersist, method: 'prePersist', entity: FundAuthUnfreezeLog::class)]
class FundAuthUnfreezeLogListener
{
    public function __construct(private readonly SdkService $sdkService)
    {
    }

    /**
     * 创建本地前，同步一次到远程
     *
     * @see https://opendocs.alipay.com/open/064jhi?scene=common&pathHash=7435c8fd
     */
    public function prePersist(FundAuthUnfreezeLog $object): void
    {
        $api = $this->sdkService->getFundAuthOrderApi($object->getFundAuthOrder()->getAccount());

        $model = new AlipayFundAuthOrderUnfreezeModel();
        $model->setAuthNo($object->getFundAuthOrder()->getAuthNo());
        $model->setOutRequestNo($object->getOutRequestNo());
        $model->setAmount($object->getAmount());
        $model->setRemark($object->getRemark());
        if ($object->getExtraParam() !== null && $object->getExtraParam() !== []) {
            $model->setExtraParam(json_encode($object->getExtraParam()));
        }
        $result = $api->unfreeze($model);

        $object->setOperationId($result->getOperationId());
        $object->setStatus($result->getStatus());
        $object->setGmtTrans($result->getGmtTrans() !== null && $result->getGmtTrans() !== '' ? Carbon::parse($result->getGmtTrans()) : null);
        $object->setCreditAmount($result->getCreditAmount());
        $object->setFundAmount($result->getFundAmount());
    }
}
