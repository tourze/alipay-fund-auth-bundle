<?php

namespace AlipayFundAuthBundle\Entity;

use AlipayFundAuthBundle\Repository\TradePromoParamsRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Tourze\EasyAdmin\Attribute\Column\ExportColumn;
use Tourze\EasyAdmin\Attribute\Column\ListColumn;

#[ORM\Entity(repositoryClass: TradePromoParamsRepository::class)]
#[ORM\Table(name: 'alipay_fund_auth_trade_promo_params', options: ['comment' => '优惠明细参数'])]
class TradePromoParams
{
    #[ListColumn(order: -1)]
    #[ExportColumn]
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::INTEGER, options: ['comment' => 'ID'])]
    private ?int $id = 0;

    #[ORM\OneToOne(cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false)]
    private ?TradeOrder $tradeOrder = null;

    /**
     * 存在延迟扣款这一类的场景，用这个时间表明用户发生交易的时间，比如说，在公交地铁场景，用户刷码出站的时间，和商户上送交易的时间是不一样的。
     */
    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
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
}
