<?php

namespace AlipayFundAuthBundle\Entity;

use AlipayFundAuthBundle\Enum\VoucherType;
use AlipayFundAuthBundle\Repository\TradeVoucherDetailRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Tourze\EasyAdmin\Attribute\Column\ExportColumn;
use Tourze\EasyAdmin\Attribute\Column\ListColumn;

#[ORM\Entity(repositoryClass: TradeVoucherDetailRepository::class)]
#[ORM\Table(name: 'alipay_fund_auth_trade_voucher_detail', options: ['comment' => '优惠券信息'])]
class TradeVoucherDetail implements \Stringable
{
    #[ListColumn(order: -1)]
    #[ExportColumn]
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::INTEGER, options: ['comment' => 'ID'])]
    private ?int $id = 0;

    #[ORM\ManyToOne(inversedBy: 'voucherDetails')]
    #[ORM\JoinColumn(nullable: false)]
    private ?TradeOrder $tradeOrder = null;

    #[ORM\Column(length: 32, options: ['comment' => '券id'])]
    private ?string $voucherId = null;

    #[ORM\Column(length: 64, options: ['comment' => '券名称'])]
    private ?string $name = null;

    #[ORM\Column(length: 32, enumType: VoucherType::class, options: ['comment' => '券类型'])]
    private ?VoucherType $type = null;

    #[ORM\Column(length: 10, options: ['comment' => '优惠券面额'])]
    private ?string $amount = null;

    #[ORM\Column(length: 10, nullable: true, options: ['comment' => '商家出资'])]
    private ?string $merchantContribute = null;

    #[ORM\Column(length: 10, nullable: true, options: ['comment' => '其他出资方出资金额'])]
    private ?string $otherContribute = null;

    #[ORM\Column(length: 256, nullable: true, options: ['comment' => '优惠券备注信息'])]
    private ?string $memo = null;

    #[ORM\Column(length: 64, nullable: true, options: ['comment' => '券模板id'])]
    private ?string $templateId = null;

    #[ORM\Column(length: 10, nullable: true, options: ['comment' => '购买券时用户实际付款金额'])]
    private ?string $purchaseBuyerContribute = null;

    #[ORM\Column(length: 10, nullable: true, options: ['comment' => '购买券时商户优惠金额'])]
    private ?string $purchaseMerchantContribute = null;

    #[ORM\Column(length: 10, nullable: true, options: ['comment' => '购买券时平台优惠金额'])]
    private ?string $purchaseAntContribute = null;

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

    public function getVoucherId(): ?string
    {
        return $this->voucherId;
    }

    public function setVoucherId(string $voucherId): static
    {
        $this->voucherId = $voucherId;

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

    public function getType(): ?VoucherType
    {
        return $this->type;
    }

    public function setType(VoucherType $type): static
    {
        $this->type = $type;

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

    public function getMerchantContribute(): ?string
    {
        return $this->merchantContribute;
    }

    public function setMerchantContribute(?string $merchantContribute): static
    {
        $this->merchantContribute = $merchantContribute;

        return $this;
    }

    public function getOtherContribute(): ?string
    {
        return $this->otherContribute;
    }

    public function setOtherContribute(?string $otherContribute): static
    {
        $this->otherContribute = $otherContribute;

        return $this;
    }

    public function getMemo(): ?string
    {
        return $this->memo;
    }

    public function setMemo(?string $memo): static
    {
        $this->memo = $memo;

        return $this;
    }

    public function getTemplateId(): ?string
    {
        return $this->templateId;
    }

    public function setTemplateId(?string $templateId): static
    {
        $this->templateId = $templateId;

        return $this;
    }

    public function getPurchaseBuyerContribute(): ?string
    {
        return $this->purchaseBuyerContribute;
    }

    public function setPurchaseBuyerContribute(?string $purchaseBuyerContribute): static
    {
        $this->purchaseBuyerContribute = $purchaseBuyerContribute;

        return $this;
    }

    public function getPurchaseMerchantContribute(): ?string
    {
        return $this->purchaseMerchantContribute;
    }

    public function setPurchaseMerchantContribute(?string $purchaseMerchantContribute): static
    {
        $this->purchaseMerchantContribute = $purchaseMerchantContribute;

        return $this;
    }

    public function getPurchaseAntContribute(): ?string
    {
        return $this->purchaseAntContribute;
    }

    public function setPurchaseAntContribute(?string $purchaseAntContribute): static
    {
        $this->purchaseAntContribute = $purchaseAntContribute;

        return $this;
    }

    public function __toString(): string
    {
        return $this->name ?? ($this->voucherId ?? ($this->id !== null ? (string) $this->id : ''));
    }
}
