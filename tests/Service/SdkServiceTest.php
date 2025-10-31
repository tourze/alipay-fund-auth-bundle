<?php

namespace AlipayFundAuthBundle\Tests\Service;

use Alipay\OpenAPISDK\Util\AlipayConfigUtil;
use AlipayFundAuthBundle\Entity\Account;
use AlipayFundAuthBundle\Service\SdkService;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\PHPUnitSymfonyKernelTest\AbstractIntegrationTestCase;

/**
 * @internal
 */
#[CoversClass(SdkService::class)]
#[RunTestsInSeparateProcesses]
final class SdkServiceTest extends AbstractIntegrationTestCase
{
    protected function onSetUp(): void
    {
        // 集成测试设置
    }

    /**
     * 测试获取支付宝配置工具类
     */
    public function testGetAlipayConfigUtilWithValidAccountReturnsConfigUtil(): void
    {
        $sdkService = self::getService(SdkService::class);
        $account = new Account();
        $account->setAppId('test_app_id');
        $account->setRsaPrivateKey('test_private_key');
        $account->setRsaPublicKey('test_public_key');

        $result = $sdkService->getAlipayConfigUtil($account);

        $this->assertInstanceOf(AlipayConfigUtil::class, $result);

        // 验证通过构造函数设置的配置是正确的
        $this->assertEquals('test_app_id', $account->getAppId());
        $this->assertEquals('test_private_key', $account->getRsaPrivateKey());
        $this->assertEquals('test_public_key', $account->getRsaPublicKey());
    }
}
