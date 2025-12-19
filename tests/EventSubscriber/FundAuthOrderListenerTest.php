<?php

namespace AlipayFundAuthBundle\Tests\EventSubscriber;

use AlipayFundAuthBundle\Entity\Account;
use AlipayFundAuthBundle\Entity\FundAuthOrder;
use AlipayFundAuthBundle\EventSubscriber\FundAuthOrderListener;
use AlipayFundAuthBundle\Exception\InvalidFundAuthOrderException;
use AlipayFundAuthBundle\Service\SdkService;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\Assert;
use Psr\Log\LoggerInterface;
use Tourze\PHPUnitSymfonyKernelTest\AbstractIntegrationTestCase;

/**
 * @internal
 */
#[CoversClass(FundAuthOrderListener::class)]
#[RunTestsInSeparateProcesses]
final class FundAuthOrderListenerTest extends AbstractIntegrationTestCase
{
    protected function onSetUp(): void
    {
        // Services are auto-registered
    }

    /**
     * 测试监听器创建
     */
    public function testListenerCanBeInstantiated(): void
    {
        $listener = self::getService(FundAuthOrderListener::class);
        $this->assertInstanceOf(FundAuthOrderListener::class, $listener);
    }

    /**
     * 测试监听器注入服务
     */
    public function testListenerInjectsSdkServiceCorrectly(): void
    {
        $listener = self::getService(FundAuthOrderListener::class);

        $reflectionClass = new \ReflectionClass($listener);
        $sdkServiceProperty = $reflectionClass->getProperty('sdkService');
        $sdkServiceProperty->setAccessible(true);

        $this->assertInstanceOf(SdkService::class, $sdkServiceProperty->getValue($listener));
    }

    public function testPrePersistMethodExists(): void
    {
        $reflectionClass = new \ReflectionClass(FundAuthOrderListener::class);

        $this->assertTrue($reflectionClass->hasMethod('prePersist'));
        $method = $reflectionClass->getMethod('prePersist');
        $this->assertTrue($method->isPublic());
        $this->assertCount(1, $method->getParameters());
    }

    public function testListenerHasCorrectConstructorParameters(): void
    {
        $reflectionClass = new \ReflectionClass(FundAuthOrderListener::class);
        $constructor = $reflectionClass->getConstructor();

        $this->assertNotNull($constructor);
        $parameters = $constructor->getParameters();

        $this->assertGreaterThanOrEqual(3, count($parameters));

        // Check SdkService parameter
        $sdkServiceParam = $parameters[0];
        $this->assertEquals('sdkService', $sdkServiceParam->getName());
        $type = $sdkServiceParam->getType();
        $this->assertInstanceOf(\ReflectionNamedType::class, $type);
        $this->assertEquals(SdkService::class, $type->getName());

        // Check LoggerInterface parameter
        $loggerParam = $parameters[1];
        $this->assertEquals('logger', $loggerParam->getName());
        $type = $loggerParam->getType();
        $this->assertInstanceOf(\ReflectionNamedType::class, $type);
        $this->assertEquals(LoggerInterface::class, $type->getName());

        // Check environment parameter
        $envParam = $parameters[2];
        $this->assertEquals('environment', $envParam->getName());
        $type = $envParam->getType();
        $this->assertInstanceOf(\ReflectionNamedType::class, $type);
        $this->assertEquals('string', $type->getName());
    }
}
