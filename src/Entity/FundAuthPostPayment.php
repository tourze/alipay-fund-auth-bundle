<?php

namespace AlipayFundAuthBundle\Entity;

use AlipayFundAuthBundle\Repository\FundAuthPostPaymentRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Tourze\Arrayable\PlainArrayInterface;
use Tourze\DoctrineSnowflakeBundle\Traits\SnowflakeKeyAware;

/**
 * @implements PlainArrayInterface<string, mixed>
 */
#[ORM\Entity(repositoryClass: FundAuthPostPaymentRepository::class)]
#[ORM\Table(name: 'alipay_fund_auth_post_payment', options: ['comment' => '后付费项目'])]
class FundAuthPostPayment implements PlainArrayInterface, \Stringable
{
    use SnowflakeKeyAware;

    #[ORM\ManyToOne(inversedBy: 'postPayments', cascade: ['persist'])]
    #[ORM\JoinColumn(nullable: false)]
    private ?FundAuthOrder $fundAuthOrder = null;

    #[Assert\NotBlank]
    #[Assert\Length(max: 32)]
    #[ORM\Column(length: 32, options: ['comment' => '后付费项目名称'])]
    private ?string $name = null;

    #[Assert\NotBlank]
    #[Assert\PositiveOrZero]
    #[Assert\Length(max: 10)]
    #[Assert\Regex(pattern: '/^\d+\.\d{2}$/')]
    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2, options: ['comment' => '后付费金额'])]
    private ?string $amount = null;

    #[Assert\Length(max: 64)]
    #[ORM\Column(length: 64, nullable: true, options: ['comment' => '计费说明'])]
    private ?string $description = null;

    public function getFundAuthOrder(): ?FundAuthOrder
    {
        return $this->fundAuthOrder;
    }

    public function setFundAuthOrder(?FundAuthOrder $fundAuthOrder): void
    {
        $this->fundAuthOrder = $fundAuthOrder;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getAmount(): ?string
    {
        return $this->amount;
    }

    public function setAmount(string $amount): void
    {
        $this->amount = $amount;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): void
    {
        $this->description = $description;
    }

    public function __toString(): string
    {
        return $this->name ?? ($this->id ?? '');
    }

    /**
     * @return array<string, string|null>
     */
    public function retrievePlainArray(): array
    {
        return [
            'name' => $this->getName(),
            'amount' => $this->getAmount(),
            'description' => $this->getDescription(),
        ];
    }
}
