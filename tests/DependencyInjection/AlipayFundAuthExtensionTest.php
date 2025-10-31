<?php

namespace AlipayFundAuthBundle\Tests\DependencyInjection;

use AlipayFundAuthBundle\DependencyInjection\AlipayFundAuthExtension;
use PHPUnit\Framework\Attributes\CoversClass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Tourze\PHPUnitSymfonyUnitTest\AbstractDependencyInjectionExtensionTestCase;

/**
 * @internal
 */
#[CoversClass(AlipayFundAuthExtension::class)]
final class AlipayFundAuthExtensionTest extends AbstractDependencyInjectionExtensionTestCase
{
    /**
     * 测试扩展加载
     */
    public function testLoadWithEmptyConfigLoadsSuccessfully(): void
    {
        $extension = new AlipayFundAuthExtension();
        $container = new ContainerBuilder();
        $container->setParameter('kernel.environment', 'test');

        $extension->load([], $container);

        $this->assertFileExists(
            __DIR__ . '/../../src/Resources/config/services.yaml',
            'services.yaml 配置文件应该存在'
        );

        $this->assertNotEmpty(
            $container->getDefinitions(),
            'Container 不应为空，即使没有配置'
        );
    }
}
