<?php

namespace AlipayFundAuthBundle\Tests\Entity;

use AlipayFundAuthBundle\Entity\FundAuthOrder;
use AlipayFundAuthBundle\Entity\FundAuthUnfreezeLog;
use PHPUnit\Framework\Attributes\CoversClass;
use Tourze\PHPUnitDoctrineEntity\AbstractEntityTestCase;

/**
 * @internal
 */
#[CoversClass(FundAuthUnfreezeLog::class)]
final class FundAuthUnfreezeLogTest extends AbstractEntityTestCase
{
    protected function createEntity(): object
    {
        $entity = new FundAuthUnfreezeLog();
        $fundAuthOrder = new FundAuthOrder();
        $entity->setFundAuthOrder($fundAuthOrder);

        return $entity;
    }

    /**
     * @return iterable<array{string, mixed}>
     */
    public static function propertiesProvider(): iterable
    {
        $fundAuthOrder = new FundAuthOrder();
        yield 'fundAuthOrder' => ['fundAuthOrder', $fundAuthOrder];
        yield 'outRequestNo' => ['outRequestNo', 'TEST123456'];
        yield 'amount' => ['amount', '10.50'];
        yield 'remark' => ['remark', 'Test remark'];
        yield 'extraParam' => ['extraParam', ['key' => 'value']];
        yield 'operationId' => ['operationId', 'OP123456'];
        yield 'status' => ['status', 'SUCCESS'];
        yield 'gmtTrans' => ['gmtTrans', new \DateTimeImmutable()];
        yield 'creditAmount' => ['creditAmount', '5.25'];
        yield 'fundAmount' => ['fundAmount', '5.25'];
    }

    public function testToStringReturnsOutRequestNo(): void
    {
        $entity = new FundAuthUnfreezeLog();
        $entity->setOutRequestNo('TEST123456');

        $this->assertEquals('TEST123456', (string) $entity);
    }

    public function testToStringReturnsIdWhenOutRequestNoIsNull(): void
    {
        $entity = new FundAuthUnfreezeLog();
        $reflection = new \ReflectionClass($entity);
        $property = $reflection->getProperty('id');
        $property->setAccessible(true);
        $property->setValue($entity, '123');

        $this->assertEquals('123', (string) $entity);
    }
}
