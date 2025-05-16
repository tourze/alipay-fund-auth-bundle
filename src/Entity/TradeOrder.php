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
use Tourze\DoctrineIndexedBundle\Attribute\IndexColumn;
use Tourze\DoctrineSnowflakeBundle\Service\SnowflakeIdGenerator;
use Tourze\DoctrineTimestampBundle\Attribute\CreateTimeColumn;
use Tourze\DoctrineTimestampBundle\Attribute\UpdateTimeColumn;
use Tourze\EasyAdmin\Attribute\Column\ExportColumn;
use Tourze\EasyAdmin\Attribute\Column\ListColumn;
use Tourze\EasyAdmin\Attribute\Filter\Filterable;
use Tourze\EasyAdmin\Attribute\Permission\AsPermission;

/**
 * @see https://opendocs.alipay.com/open/064jhk?scene=32f92b62c19b44cfaf3bf4d974fcbcf3&pathHash=1c57dd00
 */
#[AsPermission(title: '统一交易订单')]
#[ORM\Entity(repositoryClass: TradeOrderRepository::class)]
#[ORM\Table(name: 'alipay_fund_auth_trade_order', options: ['comment' => '统一交易单'])]
class TradeOrder
{
    #[ExportColumn]
    #[ListColumn(order: -1, sorter: true)]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(SnowflakeIdGenerator::class)]
    #[ORM\Column(type: Types::BIGINT, nullable: false, options: ['comment' => 'ID'])]
    private ?string $id = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Account $account = null;

    #[ORM\ManyToOne(inversedBy: 'trades')]
    #[ORM\JoinColumn(nullable: true)]
    private ?FundAuthOrder $fundAuthOrder = null;

    #[ORM\Column(length: 64, options: ['comment' => '商户订单号'])]
    private ?string $outTradeNo = null;

    /**
     * @var string|null 单位为元，精确到小数点后两位，取值范围：[0.01,100000000]
     */
    #[ORM\Column(type: Types::DECIMAL, precision: 11, scale: 2, options: ['comment' => '订单总金额'])]
    private ?string $totalAmount = null;

    /**
     * @var string|null 注意：不可使用特殊字符，如 /，=，& 等
     */
    #[ORM\Column(length: 256, options: ['comment' => '订单标题'])]
    private ?string $subject = null;

    #[ORM\Column(length: 64, options: ['comment' => '签约产品码'])]
    private string $productCode = 'PREAUTH_PAY';

    /**
     * @var string|null 支付宝预授权和新当面资金授权场景下必填
     */
    #[ORM\Column(length: 64, nullable: true, options: ['comment' => '资金预授权单号'])]
    private ?string $authNo = null;

    #[ORM\Column(length: 32, nullable: true, enumType: AuthConfirmMode::class, options: ['comment' => '预授权确认模式'])]
    private ?AuthConfirmMode $authConfirmMode = null;

    /**
     * @var string|null 指商户创建门店时输入的门店编号
     */
    #[ORM\Column(length: 32, nullable: true, options: ['comment' => '商户门店编号'])]
    private ?string $storeId = null;

    #[ORM\Column(length: 32, nullable: true, options: ['comment' => '商户机具终端编号'])]
    private ?string $terminalId = null;

    #[ORM\Column(length: 64, nullable: true, options: ['comment' => '支付宝交易号'])]
    private ?string $tradeNo = null;

    #[ORM\Column(length: 100, nullable: true, options: ['comment' => '买家支付宝账号'])]
    private ?string $buyerLogonId = null;

    #[ORM\Column(length: 11, nullable: true, options: ['comment' => '实收金额'])]
    private ?string $receiptAmount = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 11, scale: 2, nullable: true, options: ['comment' => '买家付款的金额'])]
    private ?string $buyerPayAmount = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 11, scale: 2, nullable: true, options: ['comment' => '使用集分宝付款的金额'])]
    private ?string $pointAmount = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 11, scale: 2, nullable: true, options: ['comment' => '交易中可给用户开具发票的金额'])]
    private ?string $invoiceAmount = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, options: ['comment' => '交易支付时间'])]
    private ?\DateTimeInterface $gmtPayment = null;

    #[ORM\Column(length: 512, nullable: true, options: ['comment' => '发生支付交易的商户门店名称'])]
    private ?string $storeName = null;

    #[ORM\Column(length: 28, nullable: true, options: ['comment' => '买家在支付宝的用户id'])]
    private ?string $buyerUserId = null;

    /**
     * @see https://opendocs.alipay.com/mini/0ai2i6?pathHash=13dd5946
     */
    #[ORM\Column(length: 128, nullable: true, options: ['comment' => '买家支付宝用户唯一标识'])]
    private ?string $buyerOpenId = null;

    #[ORM\Column(length: 20, nullable: true, enumType: AsyncPaymentMode::class, options: ['comment' => '异步支付模式'])]
    private ?AsyncPaymentMode $asyncPaymentMode = null;

    /**
     * @var AuthTradePayMode|null 该参数仅在信用预授权支付场景下返回
     */
    #[ORM\Column(length: 64, nullable: true, enumType: AuthTradePayMode::class, options: ['comment' => '预授权支付模式'])]
    private ?AuthTradePayMode $authTradePayMode = null;

    #[ORM\Column(length: 64, nullable: true, enumType: AliPayType::class, options: ['comment' => '支付类型'])]
    private ?AliPayType $payType = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 11, scale: 2, nullable: true, options: ['comment' => '商家优惠金额'])]
    private ?string $mdiscountAmount = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 11, scale: 2, nullable: true, options: ['comment' => '平台优惠金额'])]
    private ?string $discountAmount = null;

    #[ORM\Column(length: 64, options: ['default' => 'NO_PAY', 'comment' => '支付状态'])]
    private string $tradeStatus;

    #[ORM\Column(type: Types::JSON, nullable: true, options: ['comment' => '回调信息'])]
    private ?array $notifyPayload = null;

    #[ORM\OneToMany(targetEntity: TradeGoodsDetail::class, mappedBy: 'tradeOrder')]
    private Collection $goodsDetails;

    #[ORM\OneToMany(targetEntity: TradeFundBill::class, mappedBy: 'tradeOrder')]
    private Collection $fundBills;

    #[ORM\OneToMany(targetEntity: TradeVoucherDetail::class, mappedBy: 'tradeOrder')]
    private Collection $voucherDetails;

    #[Filterable]
    #[IndexColumn]
    #[ListColumn(order: 98, sorter: true)]
    #[ExportColumn]
    #[CreateTimeColumn]
    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true, options: ['comment' => '创建时间'])]
    private ?\DateTimeInterface $createTime = null;

    #[UpdateTimeColumn]
    #[ListColumn(order: 99, sorter: true)]
    #[Filterable]
    #[ExportColumn]
    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true, options: ['comment' => '更新时间'])]
    private ?\DateTimeInterface $updateTime = null;

    public function setCreateTime(?\DateTimeInterface $createdAt): void
    {
        $this->createTime = $createdAt;
    }

    public function getCreateTime(): ?\DateTimeInterface
    {
        return $this->createTime;
    }

    public function setUpdateTime(?\DateTimeInterface $updateTime): void
    {
        $this->updateTime = $updateTime;
    }

    public function getUpdateTime(): ?\DateTimeInterface
    {
        return $this->updateTime;
    }

    public function __construct()
    {
        $this->goodsDetails = new ArrayCollection();
        $this->fundBills = new ArrayCollection();
        $this->voucherDetails = new ArrayCollection();
    }

    public function getId(): ?string
    {
        return $this->id;
    }

    public function getFundAuthOrder(): ?FundAuthOrder
    {
        return $this->fundAuthOrder;
    }

    public function setFundAuthOrder(?FundAuthOrder $fundAuthOrder): static
    {
        $this->fundAuthOrder = $fundAuthOrder;

        return $this;
    }

    public function getOutTradeNo(): ?string
    {
        return $this->outTradeNo;
    }

    public function setOutTradeNo(string $outTradeNo): static
    {
        $this->outTradeNo = $outTradeNo;

        return $this;
    }

    public function getTotalAmount(): ?string
    {
        return $this->totalAmount;
    }

    public function setTotalAmount(string $totalAmount): static
    {
        $this->totalAmount = $totalAmount;

        return $this;
    }

    public function getSubject(): ?string
    {
        return $this->subject;
    }

    public function setSubject(string $subject): static
    {
        $this->subject = $subject;

        return $this;
    }

    public function getProductCode(): string
    {
        return $this->productCode;
    }

    public function setProductCode(string $productCode): static
    {
        $this->productCode = $productCode;

        return $this;
    }

    public function getAuthNo(): ?string
    {
        return $this->authNo;
    }

    public function setAuthNo(string $authNo): static
    {
        $this->authNo = $authNo;

        return $this;
    }

    public function getAuthConfirmMode(): ?AuthConfirmMode
    {
        return $this->authConfirmMode;
    }

    public function setAuthConfirmMode(?AuthConfirmMode $authConfirmMode): static
    {
        $this->authConfirmMode = $authConfirmMode;

        return $this;
    }

    public function getStoreId(): ?string
    {
        return $this->storeId;
    }

    public function setStoreId(?string $storeId): static
    {
        $this->storeId = $storeId;

        return $this;
    }

    public function getTerminalId(): ?string
    {
        return $this->terminalId;
    }

    public function setTerminalId(?string $terminalId): static
    {
        $this->terminalId = $terminalId;

        return $this;
    }

    public function getTradeNo(): ?string
    {
        return $this->tradeNo;
    }

    public function setTradeNo(?string $tradeNo): static
    {
        $this->tradeNo = $tradeNo;

        return $this;
    }

    public function getBuyerLogonId(): ?string
    {
        return $this->buyerLogonId;
    }

    public function setBuyerLogonId(?string $buyerLogonId): static
    {
        $this->buyerLogonId = $buyerLogonId;

        return $this;
    }

    public function getReceiptAmount(): ?string
    {
        return $this->receiptAmount;
    }

    public function setReceiptAmount(?string $receiptAmount): static
    {
        $this->receiptAmount = $receiptAmount;

        return $this;
    }

    public function getBuyerPayAmount(): ?string
    {
        return $this->buyerPayAmount;
    }

    public function setBuyerPayAmount(?string $buyerPayAmount): static
    {
        $this->buyerPayAmount = $buyerPayAmount;

        return $this;
    }

    public function getPointAmount(): ?string
    {
        return $this->pointAmount;
    }

    public function setPointAmount(?string $pointAmount): static
    {
        $this->pointAmount = $pointAmount;

        return $this;
    }

    public function getInvoiceAmount(): ?string
    {
        return $this->invoiceAmount;
    }

    public function setInvoiceAmount(?string $invoiceAmount): static
    {
        $this->invoiceAmount = $invoiceAmount;

        return $this;
    }

    public function getGmtPayment(): ?\DateTimeInterface
    {
        return $this->gmtPayment;
    }

    public function setGmtPayment(\DateTimeInterface $gmtPayment): static
    {
        $this->gmtPayment = $gmtPayment;

        return $this;
    }

    public function getStoreName(): ?string
    {
        return $this->storeName;
    }

    public function setStoreName(?string $storeName): static
    {
        $this->storeName = $storeName;

        return $this;
    }

    public function getBuyerUserId(): ?string
    {
        return $this->buyerUserId;
    }

    public function setBuyerUserId(?string $buyerUserId): static
    {
        $this->buyerUserId = $buyerUserId;

        return $this;
    }

    public function getAsyncPaymentMode(): ?AsyncPaymentMode
    {
        return $this->asyncPaymentMode;
    }

    public function setAsyncPaymentMode(?AsyncPaymentMode $asyncPaymentMode): static
    {
        $this->asyncPaymentMode = $asyncPaymentMode;

        return $this;
    }

    public function getAuthTradePayMode(): ?AuthTradePayMode
    {
        return $this->authTradePayMode;
    }

    public function setAuthTradePayMode(?AuthTradePayMode $authTradePayMode): static
    {
        $this->authTradePayMode = $authTradePayMode;

        return $this;
    }

    public function getMdiscountAmount(): ?string
    {
        return $this->mdiscountAmount;
    }

    public function setMdiscountAmount(?string $mdiscountAmount): static
    {
        $this->mdiscountAmount = $mdiscountAmount;

        return $this;
    }

    public function getDiscountAmount(): ?string
    {
        return $this->discountAmount;
    }

    public function setDiscountAmount(?string $discountAmount): static
    {
        $this->discountAmount = $discountAmount;

        return $this;
    }

    /**
     * @return Collection<int, TradeGoodsDetail>
     */
    public function getGoodsDetails(): Collection
    {
        return $this->goodsDetails;
    }

    public function addGoodsDetail(TradeGoodsDetail $goodsDetail): static
    {
        if (!$this->goodsDetails->contains($goodsDetail)) {
            $this->goodsDetails->add($goodsDetail);
            $goodsDetail->setTradeOrder($this);
        }

        return $this;
    }

    public function removeGoodsDetail(TradeGoodsDetail $goodsDetail): static
    {
        if ($this->goodsDetails->removeElement($goodsDetail)) {
            // set the owning side to null (unless already changed)
            if ($goodsDetail->getTradeOrder() === $this) {
                $goodsDetail->setTradeOrder(null);
            }
        }

        return $this;
    }

    public function getAccount(): ?Account
    {
        return $this->account;
    }

    public function setAccount(?Account $account): static
    {
        $this->account = $account;

        return $this;
    }

    public function getBuyerOpenId(): ?string
    {
        return $this->buyerOpenId;
    }

    public function setBuyerOpenId(?string $buyerOpenId): static
    {
        $this->buyerOpenId = $buyerOpenId;

        return $this;
    }

    /**
     * @return Collection<int, TradeFundBill>
     */
    public function getFundBills(): Collection
    {
        return $this->fundBills;
    }

    public function addFundBill(TradeFundBill $fundBill): static
    {
        if (!$this->fundBills->contains($fundBill)) {
            $this->fundBills->add($fundBill);
            $fundBill->setTradeOrder($this);
        }

        return $this;
    }

    public function removeFundBill(TradeFundBill $fundBill): static
    {
        if ($this->fundBills->removeElement($fundBill)) {
            // set the owning side to null (unless already changed)
            if ($fundBill->getTradeOrder() === $this) {
                $fundBill->setTradeOrder(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, TradeVoucherDetail>
     */
    public function getVoucherDetails(): Collection
    {
        return $this->voucherDetails;
    }

    public function addVoucherDetail(TradeVoucherDetail $voucherDetail): static
    {
        if (!$this->voucherDetails->contains($voucherDetail)) {
            $this->voucherDetails->add($voucherDetail);
            $voucherDetail->setTradeOrder($this);
        }

        return $this;
    }

    public function removeVoucherDetail(TradeVoucherDetail $voucherDetail): static
    {
        if ($this->voucherDetails->removeElement($voucherDetail)) {
            // set the owning side to null (unless already changed)
            if ($voucherDetail->getTradeOrder() === $this) {
                $voucherDetail->setTradeOrder(null);
            }
        }

        return $this;
    }

    public function getPayType(): ?AliPayType
    {
        return $this->payType;
    }

    public function setPayType(?AliPayType $payType): void
    {
        $this->payType = $payType;
    }

    public function getNotifyPayload(): ?array
    {
        return $this->notifyPayload;
    }

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
