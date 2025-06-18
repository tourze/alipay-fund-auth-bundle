<?php

namespace AlipayFundAuthBundle\Entity;

use AlipayFundAuthBundle\Repository\TradePromoParamsRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TradePromoParamsRepository::class)]
#[ORM\Table(name: 'alipay_fund_auth_trade_promo_params', options: ['comment' => '优惠明细参数'])]
class TradePromoParams implements \Stringable
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::INTEGER, options: ['comment' => 'ID'])]
    private ?int $id = 0;

    #[ORM\OneToOne(cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false)]
    private ?TradeOrder $tradeOrder = null;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, nullable: true, options: ['comment' => '用户实际交易时间'])]
    private ?\DateTimeInterface $actualOrderTime = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTradeOrder(): ?TradeOrder
    {
        return $this->tradeOrder;
    }

    public function setTradeOrder(TradeOrder $tradeOrder): static
    {
        $this->tradeOrder = $tradeOrder;

        return $this;
    }

    public function getActualOrderTime(): ?\DateTimeInterface
    {
        return $this->actualOrderTime;
    }

    public function setActualOrderTime(?\DateTimeInterface $actualOrderTime): static
    {
        $this->actualOrderTime = $actualOrderTime;

        return $this;
    }

    public function __toString(): string
    {
        return $this->id !== null ? (string) $this->id : '';
    }
}
