<?php

namespace AlipayFundAuthBundle\Tests\Entity;

use AlipayFundAuthBundle\Entity\FundAuthOrder;
use AlipayFundAuthBundle\Entity\FundAuthPostPayment;
use PHPUnit\Framework\Attributes\CoversClass;
use Tourze\PHPUnitDoctrineEntity\AbstractEntityTestCase;

/**
 * @internal
 */
#[CoversClass(FundAuthPostPayment::class)]
final class FundAuthPostPaymentTest extends AbstractEntityTestCase
{
    protected function createEntity(): object
    {
        $entity = new FundAuthPostPayment();
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
        yield 'name' => ['name', 'Test Post Payment'];
        yield 'amount' => ['amount', '10.50'];
        yield 'description' => ['description', 'Test description'];
    }

    public function testToStringReturnsName(): void
    {
        $entity = new FundAuthPostPayment();
        $entity->setName('Test Post Payment');

        $this->assertEquals('Test Post Payment', (string) $entity);
    }

    public function testToStringReturnsIdWhenNameIsNull(): void
    {
        $entity = new FundAuthPostPayment();
        $reflection = new \ReflectionClass($entity);
        $property = $reflection->getProperty('id');
        $property->setAccessible(true);
        $property->setValue($entity, '123');

        $this->assertEquals('123', (string) $entity);
    }

    public function testRetrievePlainArrayReturnsCorrectData(): void
    {
        $entity = new FundAuthPostPayment();
        $entity->setName('Test Post Payment');
        $entity->setAmount('10.50');
        $entity->setDescription('Test description');

        $expected = [
            'name' => 'Test Post Payment',
            'amount' => '10.50',
            'description' => 'Test description',
        ];

        $this->assertEquals($expected, $entity->retrievePlainArray());
    }
}
