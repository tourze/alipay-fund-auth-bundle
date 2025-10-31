<?php

namespace AlipayFundAuthBundle\Service;

use Alipay\OpenAPISDK\Api\AlipayDataDataserviceBillDownloadurlApi;
use Alipay\OpenAPISDK\Api\AlipayFundAuthOrderApi;
use Alipay\OpenAPISDK\Api\AlipayTradeApi;
use Alipay\OpenAPISDK\Util\AlipayConfigUtil;
use Alipay\OpenAPISDK\Util\Model\AlipayConfig;
use AlipayFundAuthBundle\Entity\Account;
use GuzzleHttp\Client;

class SdkService
{
    public function getAlipayConfigUtil(Account $account): AlipayConfigUtil
    {
        // 设置alipayConfig参数（全局设置一次）
        $alipayConfig = new AlipayConfig();
        // 设置应用ID
        $alipayConfig->setAppId($account->getAppId());
        // 设置应用私钥
        $alipayConfig->setPrivateKey($account->getRsaPrivateKey());
        // 设置支付宝公钥
        $alipayConfig->setAlipayPublicKey($account->getRsaPublicKey());

        return new AlipayConfigUtil($alipayConfig);
    }

    public function getFundAuthOrderApi(Account $account): AlipayFundAuthOrderApi
    {
        // 实例化客户端
        $api = new AlipayFundAuthOrderApi($this->getClient());
        $api->setAlipayConfigUtil($this->getAlipayConfigUtil($account));

        return $api;
    }

    public function getTradeApi(Account $account): AlipayTradeApi
    {
        // 实例化客户端
        $api = new AlipayTradeApi($this->getClient());
        $api->setAlipayConfigUtil($this->getAlipayConfigUtil($account));

        return $api;
    }

    public function getBillDownloadurlApi(Account $account): AlipayDataDataserviceBillDownloadurlApi
    {
        // 实例化客户端
        $api = new AlipayDataDataserviceBillDownloadurlApi($this->getClient());
        $api->setAlipayConfigUtil($this->getAlipayConfigUtil($account));

        return $api;
    }

    private function getClient(): Client
    {
        return new Client();
    }
}
