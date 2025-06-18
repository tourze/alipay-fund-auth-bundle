<?php

namespace AlipayFundAuthBundle\Entity;

use AlipayFundAuthBundle\Repository\TradeFundBillRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TradeFundBillRepository::class)]
#[ORM\Table(name: 'alipay_fund_auth_trade_fund_bill', options: ['comment' => '交易支付使用的资金渠道'])]
class TradeFundBill implements \Stringable
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::INTEGER, options: ['comment' => 'ID'])]
    private ?int $id = 0;

    #[ORM\ManyToOne(inversedBy: 'fundBills')]
    #[ORM\JoinColumn(nullable: false)]
    private ?TradeOrder $tradeOrder = null;

    #[ORM\Column(length: 32, options: ['comment' => '资金渠道'])]
    private ?string $fundChannel = null;

    #[ORM\Column(length: 20, options: ['comment' => '该支付工具类型的使用金额'])]
    private ?string $amount = null;

    #[ORM\Column(length: 20, nullable: true, options: ['comment' => '该支付工具类型的实际使用金额'])]
    private ?string $realAmount = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTradeOrder(): ?TradeOrder
    {
        return $this->tradeOrder;
    }

    public function setTradeOrder(?TradeOrder $tradeOrder): static
    {
        $this->tradeOrder = $tradeOrder;

        return $this;
    }

    public function getFundChannel(): ?string
    {
        return $this->fundChannel;
    }

    public function setFundChannel(string $fundChannel): static
    {
        $this->fundChannel = $fundChannel;

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

    public function getRealAmount(): ?string
    {
        return $this->realAmount;
    }

    public function setRealAmount(?string $realAmount): static
    {
        $this->realAmount = $realAmount;

        return $this;
    }

    public function __toString(): string
    {
        return $this->fundChannel ?? ($this->id !== null ? (string) $this->id : '');
    }
}
