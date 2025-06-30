<?php

namespace AlipayFundAuthBundle\Tests\DependencyInjection;

use AlipayFundAuthBundle\DependencyInjection\AlipayFundAuthExtension;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class AlipayFundAuthExtensionTest extends TestCase
{
    private AlipayFundAuthExtension $extension;
    private ContainerBuilder $container;

    protected function setUp(): void
    {
        $this->extension = new AlipayFundAuthExtension();
        $this->container = new ContainerBuilder();
    }

    /**
     * 测试扩展加载
     */
    public function testLoad_withEmptyConfig_loadsSuccessfully(): void
    {
        $this->extension->load([], $this->container);
        
        // 检查是否加载了服务配置
        $this->assertNotEmpty($this->container->getDefinitions());
    }
}