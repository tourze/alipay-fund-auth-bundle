<?php

declare(strict_types=1);

namespace AlipayFundAuthBundle\Tests;

use AlipayFundAuthBundle\AlipayFundAuthBundle;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\PHPUnitSymfonyKernelTest\AbstractBundleTestCase;

/**
 * @internal
 */
#[CoversClass(AlipayFundAuthBundle::class)]
#[RunTestsInSeparateProcesses]
final class AlipayFundAuthBundleTest extends AbstractBundleTestCase
{
}
