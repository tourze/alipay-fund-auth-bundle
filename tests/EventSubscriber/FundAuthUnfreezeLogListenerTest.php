<?php

namespace AlipayFundAuthBundle\Tests\EventSubscriber;

use AlipayFundAuthBundle\EventSubscriber\FundAuthUnfreezeLogListener;
use AlipayFundAuthBundle\Service\SdkService;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\PHPUnitSymfonyKernelTest\AbstractIntegrationTestCase;

/**
 * @internal
 */
#[CoversClass(FundAuthUnfreezeLogListener::class)]
#[RunTestsInSeparateProcesses]
final class FundAuthUnfreezeLogListenerTest extends AbstractIntegrationTestCase
{
    protected function onSetUp(): void
    {
        // 集成测试设置
    }

    /**
     * 测试监听器创建
     */
    public function testListenerCanBeInstantiated(): void
    {
        // 从容器中获取监听器服务
        $listener = self::getService(FundAuthUnfreezeLogListener::class);
        $this->assertInstanceOf(FundAuthUnfreezeLogListener::class, $listener);
    }

    /**
     * 测试监听器注入服务
     */
    public function testListenerInjectsSdkServiceCorrectly(): void
    {
        // 从容器中获取监听器服务
        $listener = self::getService(FundAuthUnfreezeLogListener::class);

        $reflectionClass = new \ReflectionClass($listener);
        $sdkServiceProperty = $reflectionClass->getProperty('sdkService');
        $sdkServiceProperty->setAccessible(true);

        // 验证注入的 SdkService 是正确的实例
        $this->assertInstanceOf(SdkService::class, $sdkServiceProperty->getValue($listener));
    }

    /**
     * 测试prePersist方法存在
     */
    public function testListenerHasPrePersistMethod(): void
    {
        // 从容器中获取监听器服务
        $listener = self::getService(FundAuthUnfreezeLogListener::class);

        $reflectionMethod = new \ReflectionMethod($listener, 'prePersist');
        $this->assertTrue($reflectionMethod->isPublic());
        $this->assertEquals(1, $reflectionMethod->getNumberOfParameters());
    }

    /**
     * 测试prePersist方法
     */
    public function testPrePersist(): void
    {
        // 从容器中获取监听器服务
        $listener = self::getService(FundAuthUnfreezeLogListener::class);

        // 检查 prePersist 方法存在且可调用，但不执行实际的 API 调用
        $reflection = new \ReflectionMethod($listener, 'prePersist');
        $this->assertTrue($reflection->isPublic());
        $this->assertSame(1, $reflection->getNumberOfParameters());
        $this->assertSame('prePersist', $reflection->getName());
    }
}
