<?php

namespace AlipayFundAuthBundle\Tests\Exception;

use AlipayFundAuthBundle\Exception\InvalidFundAuthOrderException;
use PHPUnit\Framework\Attributes\CoversClass;
use Tourze\PHPUnitBase\AbstractExceptionTestCase;

/**
 * @internal
 */
#[CoversClass(InvalidFundAuthOrderException::class)]
final class InvalidFundAuthOrderExceptionTest extends AbstractExceptionTestCase
{
    public function testExceptionCreation(): void
    {
        $message = 'Test exception message';
        $exception = new InvalidFundAuthOrderException($message);

        $this->assertInstanceOf(InvalidFundAuthOrderException::class, $exception);
        $this->assertSame($message, $exception->getMessage());
        $this->assertInstanceOf(\Exception::class, $exception);
    }

    public function testExceptionWithCodeAndPrevious(): void
    {
        $message = 'Test exception message';
        $code = 123;
        $previous = new \RuntimeException('Previous exception');

        $exception = new InvalidFundAuthOrderException($message, $code, $previous);

        $this->assertSame($message, $exception->getMessage());
        $this->assertSame($code, $exception->getCode());
        $this->assertSame($previous, $exception->getPrevious());
    }
}
