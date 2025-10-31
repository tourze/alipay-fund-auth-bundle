<?php

namespace AlipayFundAuthBundle\Entity;

use AlipayFundAuthBundle\Repository\TradeFundBillRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: TradeFundBillRepository::class)]
#[ORM\Table(name: 'alipay_fund_auth_trade_fund_bill', options: ['comment' => '交易支付使用的资金渠道'])]
class TradeFundBill implements \Stringable
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::INTEGER, options: ['comment' => 'ID'])]
    #[Assert\PositiveOrZero]
    private int $id = 0;

    #[ORM\ManyToOne(inversedBy: 'fundBills', cascade: ['persist'])]
    #[ORM\JoinColumn(nullable: false)]
    private ?TradeOrder $tradeOrder = null;

    #[ORM\Column(length: 32, options: ['comment' => '资金渠道'])]
    #[Assert\NotBlank]
    #[Assert\Length(max: 32)]
    private ?string $fundChannel = null;

    #[ORM\Column(length: 20, options: ['comment' => '该支付工具类型的使用金额'])]
    #[Assert\NotBlank]
    #[Assert\Length(max: 20)]
    #[Assert\PositiveOrZero]
    private ?string $amount = null;

    #[ORM\Column(length: 20, nullable: true, options: ['comment' => '该支付工具类型的实际使用金额'])]
    #[Assert\Length(max: 20)]
    #[Assert\PositiveOrZero]
    private ?string $realAmount = null;

    public function getId(): int
    {
        return $this->id;
    }

    public function getTradeOrder(): ?TradeOrder
    {
        return $this->tradeOrder;
    }

    public function setTradeOrder(?TradeOrder $tradeOrder): void
    {
        $this->tradeOrder = $tradeOrder;
    }

    public function getFundChannel(): ?string
    {
        return $this->fundChannel;
    }

    public function setFundChannel(string $fundChannel): void
    {
        $this->fundChannel = $fundChannel;
    }

    public function getAmount(): ?string
    {
        return $this->amount;
    }

    public function setAmount(string $amount): void
    {
        $this->amount = $amount;
    }

    public function getRealAmount(): ?string
    {
        return $this->realAmount;
    }

    public function setRealAmount(?string $realAmount): void
    {
        $this->realAmount = $realAmount;
    }

    public function __toString(): string
    {
        return $this->fundChannel ?? (string) $this->id;
    }
}
