<?php

namespace AlipayFundAuthBundle\Entity;

use AlipayFundAuthBundle\Repository\TradeExtendParamsRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Tourze\EasyAdmin\Attribute\Column\ExportColumn;
use Tourze\EasyAdmin\Attribute\Column\ListColumn;

#[ORM\Entity(repositoryClass: TradeExtendParamsRepository::class)]
#[ORM\Table(name: 'alipay_fund_auth_trade_extend_params', options: ['comment' => '业务扩展参数'])]
class TradeExtendParams
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
     * @var string|null 该参数作为系统商返佣数据提取的依据，请填写系统商签约协议的PID
     */
    #[ORM\Column(length: 64, nullable: true, options: ['comment' => '系统商编号'])]
    private ?string $sysServiceProviderId = null;

    /**
     * @var string|null 特殊场景下，允许商户指定交易展示的卖家名称
     */
    #[ORM\Column(length: 32, nullable: true, options: ['comment' => '卖家名称'])]
    private ?string $specifiedSellerName = null;

    #[ORM\Column(length: 64, nullable: true, options: ['comment' => '卡类型'])]
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

    public function setTradeOrder(TradeOrder $tradeOrder): static
    {
        $this->tradeOrder = $tradeOrder;

        return $this;
    }

    public function getSysServiceProviderId(): ?string
    {
        return $this->sysServiceProviderId;
    }

    public function setSysServiceProviderId(?string $sysServiceProviderId): static
    {
        $this->sysServiceProviderId = $sysServiceProviderId;

        return $this;
    }

    public function getSpecifiedSellerName(): ?string
    {
        return $this->specifiedSellerName;
    }

    public function setSpecifiedSellerName(?string $specifiedSellerName): static
    {
        $this->specifiedSellerName = $specifiedSellerName;

        return $this;
    }

    public function getCardType(): ?string
    {
        return $this->cardType;
    }

    public function setCardType(?string $cardType): static
    {
        $this->cardType = $cardType;

        return $this;
    }
}
