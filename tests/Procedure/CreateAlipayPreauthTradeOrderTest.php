<?php

namespace AlipayFundAuthBundle\Tests\Procedure;

use AlipayFundAuthBundle\Procedure\CreateAlipayPreauthTradeOrder;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\PHPUnitJsonRPC\AbstractProcedureTestCase;

/**
 * @internal
 */
#[CoversClass(CreateAlipayPreauthTradeOrder::class)]
#[RunTestsInSeparateProcesses]
final class CreateAlipayPreauthTradeOrderTest extends AbstractProcedureTestCase
{
    protected function onSetUp(): void
    {
        // 不需要特殊的设置，使用父类的默认设置
    }

    /**
     * 测试 Procedure 创建
     */
    public function testCreateProcedureCreatesSuccessfully(): void
    {
        $procedure = self::getService(CreateAlipayPreauthTradeOrder::class);
        $this->assertInstanceOf(CreateAlipayPreauthTradeOrder::class, $procedure);
    }

    /**
     * 测试 execute 方法
     */
    public function testExecute(): void
    {
        // 从容器中获取 Procedure 实例
        $procedure = self::getService(CreateAlipayPreauthTradeOrder::class);

        // 检查 execute 方法存在且可调用
        $reflection = new \ReflectionMethod($procedure, 'execute');
        $this->assertTrue($reflection->isPublic());
        $this->assertSame(1, $reflection->getNumberOfParameters());
        $this->assertSame('execute', $reflection->getName());
    }
}
