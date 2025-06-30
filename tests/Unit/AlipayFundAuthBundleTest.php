<?php

namespace AlipayFundAuthBundle\Tests\Unit;

use AlipayFundAuthBundle\AlipayFundAuthBundle;
use PHPUnit\Framework\TestCase;
use Tourze\BundleDependency\BundleDependencyInterface;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class AlipayFundAuthBundleTest extends TestCase
{
    private AlipayFundAuthBundle $bundle;

    protected function setUp(): void
    {
        $this->bundle = new AlipayFundAuthBundle();
    }

    /**
     * 测试Bundle继承关系
     */
    public function testBundle_extendsCorrectBaseClass(): void
    {
        $this->assertInstanceOf(Bundle::class, $this->bundle);
        $this->assertInstanceOf(BundleDependencyInterface::class, $this->bundle);
    }

    /**
     * 测试Bundle依赖项返回正确的依赖
     */
    public function testGetBundleDependencies_returnsCorrectDependencies(): void
    {
        $dependencies = AlipayFundAuthBundle::getBundleDependencies();
        
        $this->assertArrayHasKey(\Tourze\DoctrineTrackBundle\DoctrineTrackBundle::class, $dependencies);
        $this->assertArrayHasKey(\Tourze\DoctrineIndexedBundle\DoctrineIndexedBundle::class, $dependencies);
        $this->assertArrayHasKey(\Tourze\Symfony\CronJob\CronJobBundle::class, $dependencies);
        
        $this->assertEquals(['all' => true], $dependencies[\Tourze\DoctrineTrackBundle\DoctrineTrackBundle::class]);
        $this->assertEquals(['all' => true], $dependencies[\Tourze\DoctrineIndexedBundle\DoctrineIndexedBundle::class]);
        $this->assertEquals(['all' => true], $dependencies[\Tourze\Symfony\CronJob\CronJobBundle::class]);
    }

    /**
     * 测试Bundle类名
     */
    public function testGetClassName_returnsCorrectClassName(): void
    {
        $expectedClassName = 'AlipayFundAuthBundle\AlipayFundAuthBundle';
        $this->assertEquals($expectedClassName, $this->bundle::class);
    }
}