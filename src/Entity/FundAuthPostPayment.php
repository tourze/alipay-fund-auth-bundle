<?php

namespace AlipayFundAuthBundle\Entity;

use AlipayFundAuthBundle\Repository\FundAuthPostPaymentRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Tourze\Arrayable\PlainArrayInterface;
use Tourze\DoctrineSnowflakeBundle\Service\SnowflakeIdGenerator;
use Tourze\DoctrineSnowflakeBundle\Traits\SnowflakeKeyAware;

#[ORM\Entity(repositoryClass: FundAuthPostPaymentRepository::class)]
#[ORM\Table(name: 'alipay_fund_auth_post_payment', options: ['comment' => '后付费项目'])]
class FundAuthPostPayment implements PlainArrayInterface, \Stringable
{
    use SnowflakeKeyAware;

    #[ORM\ManyToOne(inversedBy: 'postPayments')]
    #[ORM\JoinColumn(nullable: false)]
    private ?FundAuthOrder $fundAuthOrder = null;

    #[ORM\Column(length: 32, options: ['comment' => '后付费项目名称'])]
    private ?string $name = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2, options: ['comment' => '后付费金额'])]
    private ?string $amount = null;

    #[ORM\Column(length: 64, nullable: true, options: ['comment' => '计费说明'])]
    private ?string $description = null;


    public function getFundAuthOrder(): ?FundAuthOrder
    {
        return $this->fundAuthOrder;
    }

    public function setFundAuthOrder(?FundAuthOrder $fundAuthOrder): static
    {
        $this->fundAuthOrder = $fundAuthOrder;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getAmount(): ?string
    {
        return $this->amount;
    }

    public function setAmount(string $amount): static
    {
        $this->amount = $amount;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function __toString(): string
    {
        return $this->name ?? ($this->id ?? '');
    }

    public function retrievePlainArray(): array
    {
        return [
            'name' => $this->getName(),
            'amount' => $this->getAmount(),
            'description' => $this->getDescription(),
        ];
    }
}
