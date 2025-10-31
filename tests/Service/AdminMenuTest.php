<?php

namespace AlipayFundAuthBundle\Tests\Service;

use AlipayFundAuthBundle\Service\AdminMenu;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\PHPUnitSymfonyWebTest\AbstractEasyAdminMenuTestCase;

/**
 * @internal
 */
#[CoversClass(AdminMenu::class)]
#[RunTestsInSeparateProcesses]
final class AdminMenuTest extends AbstractEasyAdminMenuTestCase
{
    private AdminMenu $adminMenu;

    protected function onSetUp(): void
    {
        $this->adminMenu = self::getService(AdminMenu::class);
    }

    public function testServiceCreation(): void
    {
        $this->assertInstanceOf(AdminMenu::class, $this->adminMenu);
    }

    public function testServiceMethodExists(): void
    {
        // 检查方法存在
        $reflection = new \ReflectionClass($this->adminMenu);
        $this->assertTrue($reflection->hasMethod('__invoke'));

        // 检查方法参数数量
        $invokeMethod = $reflection->getMethod('__invoke');
        $this->assertSame(1, $invokeMethod->getNumberOfParameters());
    }
}
