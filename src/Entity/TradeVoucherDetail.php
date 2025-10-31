<?php

namespace AlipayFundAuthBundle\Entity;

use AlipayFundAuthBundle\Enum\VoucherType;
use AlipayFundAuthBundle\Repository\TradeVoucherDetailRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: TradeVoucherDetailRepository::class)]
#[ORM\Table(name: 'alipay_fund_auth_trade_voucher_detail', options: ['comment' => '优惠券信息'])]
class TradeVoucherDetail implements \Stringable
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::INTEGER, options: ['comment' => 'ID'])]
    private int $id = 0;

    #[ORM\ManyToOne(inversedBy: 'voucherDetails', cascade: ['persist'])]
    #[ORM\JoinColumn(nullable: false)]
    private ?TradeOrder $tradeOrder = null;

    #[Assert\Length(max: 32)]
    #[ORM\Column(length: 32, options: ['comment' => '券id'])]
    private ?string $voucherId = null;

    #[Assert\Length(max: 64)]
    #[ORM\Column(length: 64, options: ['comment' => '券名称'])]
    private ?string $name = null;

    #[Assert\NotNull]
    #[Assert\Choice(callback: [VoucherType::class, 'cases'])]
    #[ORM\Column(length: 32, enumType: VoucherType::class, options: ['comment' => '券类型'])]
    private ?VoucherType $type = null;

    #[Assert\PositiveOrZero]
    #[Assert\Length(max: 10)]
    #[ORM\Column(length: 10, options: ['comment' => '优惠券面额'])]
    private ?string $amount = null;

    #[Assert\PositiveOrZero]
    #[Assert\Length(max: 10)]
    #[ORM\Column(length: 10, nullable: true, options: ['comment' => '商家出资'])]
    private ?string $merchantContribute = null;

    #[Assert\PositiveOrZero]
    #[Assert\Length(max: 10)]
    #[ORM\Column(length: 10, nullable: true, options: ['comment' => '其他出资方出资金额'])]
    private ?string $otherContribute = null;

    #[Assert\Length(max: 256)]
    #[ORM\Column(length: 256, nullable: true, options: ['comment' => '优惠券备注信息'])]
    private ?string $memo = null;

    #[Assert\Length(max: 64)]
    #[ORM\Column(length: 64, nullable: true, options: ['comment' => '券模板id'])]
    private ?string $templateId = null;

    #[Assert\PositiveOrZero]
    #[Assert\Length(max: 10)]
    #[ORM\Column(length: 10, nullable: true, options: ['comment' => '购买券时用户实际付款金额'])]
    private ?string $purchaseBuyerContribute = null;

    #[Assert\PositiveOrZero]
    #[Assert\Length(max: 10)]
    #[ORM\Column(length: 10, nullable: true, options: ['comment' => '购买券时商户优惠金额'])]
    private ?string $purchaseMerchantContribute = null;

    #[Assert\PositiveOrZero]
    #[Assert\Length(max: 10)]
    #[ORM\Column(length: 10, nullable: true, options: ['comment' => '购买券时平台优惠金额'])]
    private ?string $purchaseAntContribute = null;

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

    public function getVoucherId(): ?string
    {
        return $this->voucherId;
    }

    public function setVoucherId(string $voucherId): void
    {
        $this->voucherId = $voucherId;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getType(): ?VoucherType
    {
        return $this->type;
    }

    public function setType(VoucherType $type): void
    {
        $this->type = $type;
    }

    public function getAmount(): ?string
    {
        return $this->amount;
    }

    public function setAmount(string $amount): void
    {
        $this->amount = $amount;
    }

    public function getMerchantContribute(): ?string
    {
        return $this->merchantContribute;
    }

    public function setMerchantContribute(?string $merchantContribute): void
    {
        $this->merchantContribute = $merchantContribute;
    }

    public function getOtherContribute(): ?string
    {
        return $this->otherContribute;
    }

    public function setOtherContribute(?string $otherContribute): void
    {
        $this->otherContribute = $otherContribute;
    }

    public function getMemo(): ?string
    {
        return $this->memo;
    }

    public function setMemo(?string $memo): void
    {
        $this->memo = $memo;
    }

    public function getTemplateId(): ?string
    {
        return $this->templateId;
    }

    public function setTemplateId(?string $templateId): void
    {
        $this->templateId = $templateId;
    }

    public function getPurchaseBuyerContribute(): ?string
    {
        return $this->purchaseBuyerContribute;
    }

    public function setPurchaseBuyerContribute(?string $purchaseBuyerContribute): void
    {
        $this->purchaseBuyerContribute = $purchaseBuyerContribute;
    }

    public function getPurchaseMerchantContribute(): ?string
    {
        return $this->purchaseMerchantContribute;
    }

    public function setPurchaseMerchantContribute(?string $purchaseMerchantContribute): void
    {
        $this->purchaseMerchantContribute = $purchaseMerchantContribute;
    }

    public function getPurchaseAntContribute(): ?string
    {
        return $this->purchaseAntContribute;
    }

    public function setPurchaseAntContribute(?string $purchaseAntContribute): void
    {
        $this->purchaseAntContribute = $purchaseAntContribute;
    }

    public function __toString(): string
    {
        return $this->name ?? ($this->voucherId ?? (string) $this->id);
    }
}
