<?php

declare(strict_types=1);

namespace AlipayFundAuthBundle\Service;

use Alipay\OpenAPISDK\Api\AlipayDataDataserviceBillDownloadurlApi;
use Alipay\OpenAPISDK\Api\AlipayFundAuthOrderApi;
use Alipay\OpenAPISDK\Api\AlipayTradeApi;
use Alipay\OpenAPISDK\Util\AlipayConfigUtil;
use Alipay\OpenAPISDK\Util\Model\AlipayConfig;
use AlipayFundAuthBundle\Entity\Account;
use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;

/**
 * 支付宝SDK服务
 *
 * 提供支付宝API客户端的统一管理和配置
 */
final readonly class SdkService
{
    public function __construct(
        private ClientInterface $client = new Client()
    ) {
    }

    /**
     * 获取支付宝配置工具类
     */
    public function getAlipayConfigUtil(Account $account): AlipayConfigUtil
    {
        $alipayConfig = $this->createAlipayConfig($account);
        return new AlipayConfigUtil($alipayConfig);
    }

    /**
     * 获取资金授权API客户端
     */
    public function getFundAuthOrderApi(Account $account): AlipayFundAuthOrderApi
    {
        $api = new AlipayFundAuthOrderApi($this->client);
        $api->setAlipayConfigUtil($this->getAlipayConfigUtil($account));

        return $api;
    }

    /**
     * 获取交易API客户端
     */
    public function getTradeApi(Account $account): AlipayTradeApi
    {
        $api = new AlipayTradeApi($this->client);
        $api->setAlipayConfigUtil($this->getAlipayConfigUtil($account));

        return $api;
    }

    /**
     * 获取账单下载API客户端
     */
    public function getBillDownloadurlApi(Account $account): AlipayDataDataserviceBillDownloadurlApi
    {
        $api = new AlipayDataDataserviceBillDownloadurlApi($this->client);
        $api->setAlipayConfigUtil($this->getAlipayConfigUtil($account));

        return $api;
    }

    /**
     * 创建支付宝配置
     */
    private function createAlipayConfig(Account $account): AlipayConfig
    {
        $alipayConfig = new AlipayConfig();
        $alipayConfig->setAppId($account->getAppId());
        $alipayConfig->setPrivateKey($account->getRsaPrivateKey());
        $alipayConfig->setAlipayPublicKey($account->getRsaPublicKey());

        return $alipayConfig;
    }
}
