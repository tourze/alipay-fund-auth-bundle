<?php

namespace AlipayFundAuthBundle\Entity;

use AlipayFundAuthBundle\Repository\TradeExtendParamRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: TradeExtendParamRepository::class)]
#[ORM\Table(name: 'alipay_fund_auth_trade_extend_params', options: ['comment' => '业务扩展参数'])]
class TradeExtendParam implements \Stringable
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::INTEGER, options: ['comment' => 'ID'])]
    #[Assert\PositiveOrZero]
    private ?int $id = 0;

    #[ORM\OneToOne(cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false)]
    private ?TradeOrder $tradeOrder = null;

    #[ORM\Column(length: 64, nullable: true, options: ['comment' => '系统商编号'])]
    #[Assert\Length(max: 64)]
    private ?string $sysServiceProviderId = null;

    #[ORM\Column(length: 32, nullable: true, options: ['comment' => '卖家名称'])]
    #[Assert\Length(max: 32)]
    private ?string $specifiedSellerName = null;

    #[ORM\Column(length: 64, nullable: true, options: ['comment' => '卡类型'])]
    #[Assert\Length(max: 64)]
    private ?string $cardType = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id): void
    {
        $this->id = $id;
    }

    public function getTradeOrder(): ?TradeOrder
    {
        return $this->tradeOrder;
    }

    public function setTradeOrder(TradeOrder $tradeOrder): void
    {
        $this->tradeOrder = $tradeOrder;
    }

    public function getSysServiceProviderId(): ?string
    {
        return $this->sysServiceProviderId;
    }

    public function setSysServiceProviderId(?string $sysServiceProviderId): void
    {
        $this->sysServiceProviderId = $sysServiceProviderId;
    }

    public function getSpecifiedSellerName(): ?string
    {
        return $this->specifiedSellerName;
    }

    public function setSpecifiedSellerName(?string $specifiedSellerName): void
    {
        $this->specifiedSellerName = $specifiedSellerName;
    }

    public function getCardType(): ?string
    {
        return $this->cardType;
    }

    public function setCardType(?string $cardType): void
    {
        $this->cardType = $cardType;
    }

    public function __toString(): string
    {
        return $this->specifiedSellerName ?? (null !== $this->id ? (string) $this->id : '');
    }
}
