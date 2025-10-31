<?php

namespace AlipayFundAuthBundle\Entity;

use AlipayFundAuthBundle\Enum\AliPayType;
use AlipayFundAuthBundle\Enum\AsyncPaymentMode;
use AlipayFundAuthBundle\Enum\AuthConfirmMode;
use AlipayFundAuthBundle\Enum\AuthTradePayMode;
use AlipayFundAuthBundle\Repository\TradeOrderRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Tourze\DoctrineSnowflakeBundle\Traits\SnowflakeKeyAware;
use Tourze\DoctrineTimestampBundle\Traits\TimestampableAware;

/**
 * @see https://opendocs.alipay.com/open/064jhk?scene=32f92b62c19b44cfaf3bf4d974fcbcf3&pathHash=1c57dd00
 */
#[ORM\Entity(repositoryClass: TradeOrderRepository::class)]
#[ORM\Table(name: 'alipay_fund_auth_trade_order', options: ['comment' => '统一交易单'])]
class TradeOrder implements \Stringable
{
    use TimestampableAware;
    use SnowflakeKeyAware;

    #[ORM\ManyToOne(cascade: ['persist'])]
    #[ORM\JoinColumn(nullable: false)]
    private ?Account $account = null;

    #[ORM\ManyToOne(inversedBy: 'trades', cascade: ['persist'])]
    #[ORM\JoinColumn(nullable: true)]
    private ?FundAuthOrder $fundAuthOrder = null;

    #[ORM\Column(length: 64, options: ['comment' => '商户订单号'])]
    #[Assert\NotBlank]
    #[Assert\Length(max: 64)]
    private ?string $outTradeNo = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 11, scale: 2, options: ['comment' => '订单总金额'])]
    #[Assert\NotBlank]
    #[Assert\Length(max: 13)] // 11位整数+1位小数点+2位小数
    #[Assert\PositiveOrZero]
    #[Assert\Regex(pattern: '/^\d{1,9}(\.\d{1,2})?$/', message: '金额格式不正确')]
    private ?string $totalAmount = null;

    #[ORM\Column(length: 256, options: ['comment' => '订单标题'])]
    #[Assert\NotBlank]
    #[Assert\Length(max: 256)]
    private ?string $subject = null;

    #[ORM\Column(length: 64, options: ['comment' => '签约产品码'])]
    #[Assert\NotBlank]
    #[Assert\Length(max: 64)]
    private string $productCode = FundAuthOrder::PRODUCT_CODE_PREAUTH_PAY;

    #[ORM\Column(length: 64, nullable: true, options: ['comment' => '资金预授权单号'])]
    #[Assert\Length(max: 64)]
    private ?string $authNo = null;

    #[ORM\Column(length: 32, nullable: true, enumType: AuthConfirmMode::class, options: ['comment' => '预授权确认模式'])]
    #[Assert\Choice(callback: [AuthConfirmMode::class, 'cases'])]
    private ?AuthConfirmMode $authConfirmMode = null;

    #[ORM\Column(length: 32, nullable: true, options: ['comment' => '商户门店编号'])]
    #[Assert\Length(max: 32)]
    private ?string $storeId = null;

    #[ORM\Column(length: 32, nullable: true, options: ['comment' => '商户机具终端编号'])]
    #[Assert\Length(max: 32)]
    private ?string $terminalId = null;

    #[ORM\Column(length: 64, nullable: true, options: ['comment' => '支付宝交易号'])]
    #[Assert\Length(max: 64)]
    private ?string $tradeNo = null;

    #[ORM\Column(length: 100, nullable: true, options: ['comment' => '买家支付宝账号'])]
    #[Assert\Length(max: 100)]
    private ?string $buyerLogonId = null;

    #[ORM\Column(length: 11, nullable: true, options: ['comment' => '实收金额'])]
    #[Assert\Length(max: 11)]
    #[Assert\PositiveOrZero]
    private ?string $receiptAmount = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 11, scale: 2, nullable: true, options: ['comment' => '买家付款的金额'])]
    #[Assert\Length(max: 13)] // 11位整数+1位小数点+2位小数
    #[Assert\PositiveOrZero]
    #[Assert\Regex(pattern: '/^\d{1,9}(\.\d{1,2})?$/', message: '金额格式不正确')]
    private ?string $buyerPayAmount = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 11, scale: 2, nullable: true, options: ['comment' => '使用集分宝付款的金额'])]
    #[Assert\Length(max: 13)] // 11位整数+1位小数点+2位小数
    #[Assert\PositiveOrZero]
    #[Assert\Regex(pattern: '/^\d{1,9}(\.\d{1,2})?$/', message: '金额格式不正确')]
    private ?string $pointAmount = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 11, scale: 2, nullable: true, options: ['comment' => '交易中可给用户开具发票的金额'])]
    #[Assert\Length(max: 13)] // 11位整数+1位小数点+2位小数
    #[Assert\PositiveOrZero]
    #[Assert\Regex(pattern: '/^\d{1,9}(\.\d{1,2})?$/', message: '金额格式不正确')]
    private ?string $invoiceAmount = null;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, nullable: true, options: ['comment' => '交易支付时间'])]
    #[Assert\Type(type: '\DateTimeInterface')]
    private ?\DateTimeInterface $gmtPayment = null;

    #[ORM\Column(length: 512, nullable: true, options: ['comment' => '发生支付交易的商户门店名称'])]
    #[Assert\Length(max: 512)]
    private ?string $storeName = null;

    #[ORM\Column(length: 28, nullable: true, options: ['comment' => '买家在支付宝的用户id'])]
    #[Assert\Length(max: 28)]
    private ?string $buyerUserId = null;

    #[ORM\Column(length: 128, nullable: true, options: ['comment' => '买家支付宝用户唯一标识'])]
    #[Assert\Length(max: 128)]
    private ?string $buyerOpenId = null;

    #[ORM\Column(length: 20, nullable: true, enumType: AsyncPaymentMode::class, options: ['comment' => '异步支付模式'])]
    #[Assert\Choice(callback: [AsyncPaymentMode::class, 'cases'])]
    private ?AsyncPaymentMode $asyncPaymentMode = null;

    #[ORM\Column(length: 64, nullable: true, enumType: AuthTradePayMode::class, options: ['comment' => '预授权支付模式'])]
    #[Assert\Choice(callback: [AuthTradePayMode::class, 'cases'])]
    private ?AuthTradePayMode $authTradePayMode = null;

    #[ORM\Column(length: 64, nullable: true, enumType: AliPayType::class, options: ['comment' => '支付类型'])]
    #[Assert\Choice(callback: [AliPayType::class, 'cases'])]
    private ?AliPayType $payType = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 11, scale: 2, nullable: true, options: ['comment' => '商家优惠金额'])]
    #[Assert\Length(max: 13)] // 11位整数+1位小数点+2位小数
    #[Assert\PositiveOrZero]
    #[Assert\Regex(pattern: '/^\d{1,9}(\.\d{1,2})?$/', message: '金额格式不正确')]
    private ?string $mdiscountAmount = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 11, scale: 2, nullable: true, options: ['comment' => '平台优惠金额'])]
    #[Assert\Length(max: 13)] // 11位整数+1位小数点+2位小数
    #[Assert\PositiveOrZero]
    #[Assert\Regex(pattern: '/^\d{1,9}(\.\d{1,2})?$/', message: '金额格式不正确')]
    private ?string $discountAmount = null;

    #[ORM\Column(length: 64, options: ['default' => 'NO_PAY', 'comment' => '支付状态'])]
    #[Assert\NotBlank]
    #[Assert\Length(max: 64)]
    private string $tradeStatus = 'NO_PAY';

    /**
     * @var array<string, mixed>|null
     */
    #[ORM\Column(type: Types::JSON, nullable: true, options: ['comment' => '回调信息'])]
    #[Assert\Type(type: 'array')]
    private ?array $notifyPayload = null;

    /**
     * @var Collection<int, TradeGoodsDetail>
     */
    #[ORM\OneToMany(targetEntity: TradeGoodsDetail::class, mappedBy: 'tradeOrder')]
    private Collection $goodsDetails;

    /**
     * @var Collection<int, TradeFundBill>
     */
    #[ORM\OneToMany(targetEntity: TradeFundBill::class, mappedBy: 'tradeOrder')]
    private Collection $fundBills;

    /**
     * @var Collection<int, TradeVoucherDetail>
     */
    #[ORM\OneToMany(targetEntity: TradeVoucherDetail::class, mappedBy: 'tradeOrder')]
    private Collection $voucherDetails;

    public function __construct()
    {
        $this->goodsDetails = new ArrayCollection();
        $this->fundBills = new ArrayCollection();
        $this->voucherDetails = new ArrayCollection();
    }

    public function __toString(): string
    {
        return $this->outTradeNo ?? ($this->id ?? '');
    }

    public function getFundAuthOrder(): ?FundAuthOrder
    {
        return $this->fundAuthOrder;
    }

    public function setFundAuthOrder(?FundAuthOrder $fundAuthOrder): void
    {
        $this->fundAuthOrder = $fundAuthOrder;
    }

    public function getOutTradeNo(): ?string
    {
        return $this->outTradeNo;
    }

    public function setOutTradeNo(string $outTradeNo): void
    {
        $this->outTradeNo = $outTradeNo;
    }

    public function getTotalAmount(): ?string
    {
        return $this->totalAmount;
    }

    public function setTotalAmount(string $totalAmount): void
    {
        $this->totalAmount = $totalAmount;
    }

    public function getSubject(): ?string
    {
        return $this->subject;
    }

    public function setSubject(string $subject): void
    {
        $this->subject = $subject;
    }

    public function getProductCode(): string
    {
        return $this->productCode;
    }

    public function setProductCode(string $productCode): void
    {
        $this->productCode = $productCode;
    }

    public function getAuthNo(): ?string
    {
        return $this->authNo;
    }

    public function setAuthNo(string $authNo): void
    {
        $this->authNo = $authNo;
    }

    public function getAuthConfirmMode(): ?AuthConfirmMode
    {
        return $this->authConfirmMode;
    }

    public function setAuthConfirmMode(?AuthConfirmMode $authConfirmMode): void
    {
        $this->authConfirmMode = $authConfirmMode;
    }

    public function getStoreId(): ?string
    {
        return $this->storeId;
    }

    public function setStoreId(?string $storeId): void
    {
        $this->storeId = $storeId;
    }

    public function getTerminalId(): ?string
    {
        return $this->terminalId;
    }

    public function setTerminalId(?string $terminalId): void
    {
        $this->terminalId = $terminalId;
    }

    public function getTradeNo(): ?string
    {
        return $this->tradeNo;
    }

    public function setTradeNo(?string $tradeNo): void
    {
        $this->tradeNo = $tradeNo;
    }

    public function getBuyerLogonId(): ?string
    {
        return $this->buyerLogonId;
    }

    public function setBuyerLogonId(?string $buyerLogonId): void
    {
        $this->buyerLogonId = $buyerLogonId;
    }

    public function getReceiptAmount(): ?string
    {
        return $this->receiptAmount;
    }

    public function setReceiptAmount(?string $receiptAmount): void
    {
        $this->receiptAmount = $receiptAmount;
    }

    public function getBuyerPayAmount(): ?string
    {
        return $this->buyerPayAmount;
    }

    public function setBuyerPayAmount(?string $buyerPayAmount): void
    {
        $this->buyerPayAmount = $buyerPayAmount;
    }

    public function getPointAmount(): ?string
    {
        return $this->pointAmount;
    }

    public function setPointAmount(?string $pointAmount): void
    {
        $this->pointAmount = $pointAmount;
    }

    public function getInvoiceAmount(): ?string
    {
        return $this->invoiceAmount;
    }

    public function setInvoiceAmount(?string $invoiceAmount): void
    {
        $this->invoiceAmount = $invoiceAmount;
    }

    public function getGmtPayment(): ?\DateTimeInterface
    {
        return $this->gmtPayment;
    }

    public function setGmtPayment(\DateTimeInterface $gmtPayment): void
    {
        $this->gmtPayment = $gmtPayment;
    }

    public function getStoreName(): ?string
    {
        return $this->storeName;
    }

    public function setStoreName(?string $storeName): void
    {
        $this->storeName = $storeName;
    }

    public function getBuyerUserId(): ?string
    {
        return $this->buyerUserId;
    }

    public function setBuyerUserId(?string $buyerUserId): void
    {
        $this->buyerUserId = $buyerUserId;
    }

    public function getAsyncPaymentMode(): ?AsyncPaymentMode
    {
        return $this->asyncPaymentMode;
    }

    public function setAsyncPaymentMode(?AsyncPaymentMode $asyncPaymentMode): void
    {
        $this->asyncPaymentMode = $asyncPaymentMode;
    }

    public function getAuthTradePayMode(): ?AuthTradePayMode
    {
        return $this->authTradePayMode;
    }

    public function setAuthTradePayMode(?AuthTradePayMode $authTradePayMode): void
    {
        $this->authTradePayMode = $authTradePayMode;
    }

    public function getMdiscountAmount(): ?string
    {
        return $this->mdiscountAmount;
    }

    public function setMdiscountAmount(?string $mdiscountAmount): void
    {
        $this->mdiscountAmount = $mdiscountAmount;
    }

    public function getDiscountAmount(): ?string
    {
        return $this->discountAmount;
    }

    public function setDiscountAmount(?string $discountAmount): void
    {
        $this->discountAmount = $discountAmount;
    }

    /**
     * @return Collection<int, TradeGoodsDetail>
     */
    public function getGoodsDetails(): Collection
    {
        return $this->goodsDetails;
    }

    public function addGoodsDetail(TradeGoodsDetail $goodsDetail): void
    {
        if (!$this->goodsDetails->contains($goodsDetail)) {
            $this->goodsDetails->add($goodsDetail);
            $goodsDetail->setTradeOrder($this);
        }
    }

    public function removeGoodsDetail(TradeGoodsDetail $goodsDetail): void
    {
        if ($this->goodsDetails->removeElement($goodsDetail)) {
            // set the owning side to null (unless already changed)
            if ($goodsDetail->getTradeOrder() === $this) {
                $goodsDetail->setTradeOrder(null);
            }
        }
    }

    public function getAccount(): ?Account
    {
        return $this->account;
    }

    public function setAccount(?Account $account): void
    {
        $this->account = $account;
    }

    public function getBuyerOpenId(): ?string
    {
        return $this->buyerOpenId;
    }

    public function setBuyerOpenId(?string $buyerOpenId): void
    {
        $this->buyerOpenId = $buyerOpenId;
    }

    /**
     * @return Collection<int, TradeFundBill>
     */
    public function getFundBills(): Collection
    {
        return $this->fundBills;
    }

    public function addFundBill(TradeFundBill $fundBill): void
    {
        if (!$this->fundBills->contains($fundBill)) {
            $this->fundBills->add($fundBill);
            $fundBill->setTradeOrder($this);
        }
    }

    public function removeFundBill(TradeFundBill $fundBill): void
    {
        if ($this->fundBills->removeElement($fundBill)) {
            // set the owning side to null (unless already changed)
            if ($fundBill->getTradeOrder() === $this) {
                $fundBill->setTradeOrder(null);
            }
        }
    }

    /**
     * @return Collection<int, TradeVoucherDetail>
     */
    public function getVoucherDetails(): Collection
    {
        return $this->voucherDetails;
    }

    public function addVoucherDetail(TradeVoucherDetail $voucherDetail): void
    {
        if (!$this->voucherDetails->contains($voucherDetail)) {
            $this->voucherDetails->add($voucherDetail);
            $voucherDetail->setTradeOrder($this);
        }
    }

    public function removeVoucherDetail(TradeVoucherDetail $voucherDetail): void
    {
        if ($this->voucherDetails->removeElement($voucherDetail)) {
            // set the owning side to null (unless already changed)
            if ($voucherDetail->getTradeOrder() === $this) {
                $voucherDetail->setTradeOrder(null);
            }
        }
    }

    public function getPayType(): ?AliPayType
    {
        return $this->payType;
    }

    public function setPayType(?AliPayType $payType): void
    {
        $this->payType = $payType;
    }

    /**
     * @return array<string, mixed>|null
     */
    public function getNotifyPayload(): ?array
    {
        return $this->notifyPayload;
    }

    /**
     * @param array<string, mixed>|null $notifyPayload
     */
    public function setNotifyPayload(?array $notifyPayload): void
    {
        $this->notifyPayload = $notifyPayload;
    }

    public function getTradeStatus(): string
    {
        return $this->tradeStatus;
    }

    public function setTradeStatus(string $tradeStatus): void
    {
        $this->tradeStatus = $tradeStatus;
    }
}
