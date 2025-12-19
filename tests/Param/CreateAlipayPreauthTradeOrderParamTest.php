<?php

declare(strict_types=1);

namespace AlipayFundAuthBundle\Tests\Param;

use AlipayFundAuthBundle\Param\CreateAlipayPreauthTradeOrderParam;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Tourze\JsonRPC\Core\Contracts\RpcParamInterface;

/**
 * CreateAlipayPreauthTradeOrderParam 单元测试
 *
 * @internal
 */
#[CoversClass(CreateAlipayPreauthTradeOrderParam::class)]
final class CreateAlipayPreauthTradeOrderParamTest extends TestCase
{
    public function testImplementsRpcParamInterface(): void
    {
        $param = new CreateAlipayPreauthTradeOrderParam();

        $this->assertInstanceOf(RpcParamInterface::class, $param);
    }

    public function testConstructorCreatesInstance(): void
    {
        $param = new CreateAlipayPreauthTradeOrderParam();

        $this->assertInstanceOf(CreateAlipayPreauthTradeOrderParam::class, $param);
    }

    public function testClassIsReadonly(): void
    {
        $reflection = new \ReflectionClass(CreateAlipayPreauthTradeOrderParam::class);

        $this->assertTrue($reflection->isReadOnly());
    }
}
