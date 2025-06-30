<?php

namespace AlipayFundAuthBundle\Tests\Integration\EventSubscriber;

use AlipayFundAuthBundle\EventSubscriber\FundAuthUnfreezeLogListener;
use AlipayFundAuthBundle\Service\SdkService;
use PHPUnit\Framework\TestCase;

class FundAuthUnfreezeLogListenerTest extends TestCase
{
    private SdkService $sdkService;
    private FundAuthUnfreezeLogListener $listener;

    protected function setUp(): void
    {
        $this->sdkService = $this->createMock(SdkService::class);
        $this->listener = new FundAuthUnfreezeLogListener($this->sdkService);
    }

    /**
     * 测试监听器是否正确初始化
     */
    public function testListener_canBeInstantiated(): void
    {
        $this->assertInstanceOf(FundAuthUnfreezeLogListener::class, $this->listener);
    }

    /**
     * 测试监听器是否正确注入了SdkService依赖
     */
    public function testListener_injectsSdkServiceCorrectly(): void
    {
        $reflectionClass = new \ReflectionClass($this->listener);
        $sdkServiceProperty = $reflectionClass->getProperty('sdkService');
        $sdkServiceProperty->setAccessible(true);
        
        $this->assertSame($this->sdkService, $sdkServiceProperty->getValue($this->listener));
    }

    /**
     * 测试监听器是否有prePersist方法  
     */
    public function testListener_hasPrePersistMethod(): void
    {
        $reflectionMethod = new \ReflectionMethod($this->listener, 'prePersist');
        $this->assertTrue($reflectionMethod->isPublic());
        $this->assertEquals(1, $reflectionMethod->getNumberOfParameters());
    }
}