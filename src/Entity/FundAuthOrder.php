<?php

namespace AlipayFundAuthBundle\Entity;

use AlipayFundAuthBundle\Enum\FundAuthOrderStatus;
use AlipayFundAuthBundle\Repository\FundAuthOrderRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Tourze\DoctrineSnowflakeBundle\Traits\SnowflakeKeyAware;

#[ORM\Entity(repositoryClass: FundAuthOrderRepository::class)]
#[ORM\Table(name: 'alipay_fund_auth_order', options: ['comment' => '预授权订单'])]
class FundAuthOrder implements \Stringable
{
    use SnowflakeKeyAware;

    public const PRODUCT_CODE_PREAUTH_PAY = 'PREAUTH_PAY';

    #[ORM\ManyToOne(cascade: ['persist'])]
    #[ORM\JoinColumn(nullable: false)]
    private ?Account $account = null;

    #[Assert\Length(max: 64)]
    #[ORM\Column(length: 64, options: ['comment' => '商户授权资金订单号'])]
    private ?string $outOrderNo = null;

    #[Assert\Length(max: 64)]
    #[ORM\Column(length: 64, options: ['comment' => '商户本次资金授权请求的请求流水号'])]
    private ?string $outRequestNo = null;

    #[Assert\Length(max: 100)]
    #[ORM\Column(length: 100, options: ['comment' => '业务订单的简单描述'])]
    private ?string $orderTitle = null;

    #[Assert\PositiveOrZero]
    #[Assert\Length(max: 10)]
    #[Assert\Regex(pattern: '/^\d+\.\d{2}$/')]
    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2, options: ['comment' => '需要冻结的金额'])]
    private ?string $amount = null;

    #[Assert\NotBlank]
    #[Assert\Length(max: 32)]
    #[ORM\Column(length: 32, options: ['comment' => '销售产品码'])]
    private string $productCode = self::PRODUCT_CODE_PREAUTH_PAY;

    #[Assert\Length(max: 32)]
    #[ORM\Column(length: 32, nullable: true, options: ['comment' => '收款方的支付宝用户号'])]
    private ?string $payeeUserId = null;

    #[Assert\Length(max: 100)]
    #[ORM\Column(length: 100, nullable: true, options: ['comment' => '收款方支付宝账号'])]
    private ?string $payeeLogonId = null;

    #[Assert\Length(max: 5)]
    #[ORM\Column(length: 5, nullable: true, options: ['comment' => '该笔订单允许的最晚付款时间'])]
    private ?string $payTimeout = null;

    #[Assert\Length(max: 5)]
    #[ORM\Column(length: 5, nullable: true, options: ['comment' => '冻结资金的有效期'])]
    private ?string $timeExpress = null;

    /**
     * @var array<string, mixed>|null
     */
    #[Assert\Type(type: 'array')]
    #[ORM\Column(nullable: true, options: ['comment' => '业务扩展参数'])]
    private ?array $extraParam = null;

    /**
     * @var array<string, mixed>|null
     */
    #[Assert\Type(type: 'array')]
    #[ORM\Column(nullable: true, options: ['comment' => '商户传入业务信息'])]
    private ?array $businessParams = null;

    #[Assert\Length(max: 128)]
    #[ORM\Column(length: 128, nullable: true, options: ['comment' => '预授权业务场景'])]
    private ?string $sceneCode = null;

    #[Assert\Length(max: 8)]
    #[ORM\Column(length: 8, nullable: true, options: ['comment' => '标价币种'])]
    private ?string $transCurrency = null;

    #[Assert\Length(max: 8)]
    #[ORM\Column(length: 8, nullable: true, options: ['comment' => '商户指定的结算币种'])]
    private ?string $settleCurrency = null;

    #[Assert\Length(max: 64)]
    #[ORM\Column(length: 64, nullable: true, options: ['comment' => '支付宝的资金授权订单号'])]
    private ?string $authNo = null;

    #[Assert\Length(max: 64)]
    #[ORM\Column(length: 64, nullable: true, options: ['comment' => '支付宝的资金操作流水号'])]
    private ?string $operationId = null;

    #[Assert\Choice(callback: [FundAuthOrderStatus::class, 'cases'])]
    #[ORM\Column(length: 20, nullable: true, enumType: FundAuthOrderStatus::class, options: ['comment' => '资金预授权明细的状态'])]
    private FundAuthOrderStatus $status = FundAuthOrderStatus::INIT;

    #[Assert\Type(type: '\DateTimeInterface')]
    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, nullable: true, options: ['comment' => '资金授权成功时间'])]
    private ?\DateTimeInterface $gmtTrans = null;

    #[Assert\Length(max: 32)]
    #[ORM\Column(length: 32, nullable: true, options: ['comment' => '付款方支付宝用户号'])]
    private ?string $payerUserId = null;

    #[Assert\Length(max: 20)]
    #[ORM\Column(length: 20, nullable: true, options: ['comment' => '预授权类型'])]
    private ?string $preAuthType = null;

    #[Assert\PositiveOrZero]
    #[Assert\Length(max: 10)]
    #[Assert\Regex(pattern: '/^\d+\.\d{2}$/')]
    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2, nullable: true, options: ['comment' => '信用冻结金额'])]
    private ?string $creditAmount = null;

    #[Assert\PositiveOrZero]
    #[Assert\Length(max: 10)]
    #[Assert\Regex(pattern: '/^\d+\.\d{2}$/')]
    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2, nullable: true, options: ['comment' => '自有资金冻结金额'])]
    private ?string $fundAmount = null;

    /**
     * @var Collection<int, FundAuthPostPayment>
     */
    #[ORM\OneToMany(mappedBy: 'fundAuthOrder', targetEntity: FundAuthPostPayment::class)]
    private Collection $postPayments;

    /**
     * @var Collection<int, FundAuthUnfreezeLog>
     */
    #[ORM\OneToMany(mappedBy: 'fundAuthOrder', targetEntity: FundAuthUnfreezeLog::class)]
    private Collection $unfreezeLogs;

    /**
     * @var Collection<int, TradeOrder>
     */
    #[ORM\OneToMany(mappedBy: 'fundAuthOrder', targetEntity: TradeOrder::class)]
    private Collection $trades;

    public function __construct()
    {
        $this->postPayments = new ArrayCollection();
        $this->unfreezeLogs = new ArrayCollection();
        $this->trades = new ArrayCollection();
    }

    public function __toString(): string
    {
        return $this->outOrderNo ?? ($this->id ?? '');
    }

    public function getAccount(): ?Account
    {
        return $this->account;
    }

    public function setAccount(?Account $account): void
    {
        $this->account = $account;
    }

    public function getOutOrderNo(): ?string
    {
        return $this->outOrderNo;
    }

    public function setOutOrderNo(string $outOrderNo): void
    {
        $this->outOrderNo = $outOrderNo;
    }

    public function getOutRequestNo(): ?string
    {
        return $this->outRequestNo;
    }

    public function setOutRequestNo(string $outRequestNo): void
    {
        $this->outRequestNo = $outRequestNo;
    }

    public function getOrderTitle(): ?string
    {
        return $this->orderTitle;
    }

    public function setOrderTitle(string $orderTitle): void
    {
        $this->orderTitle = $orderTitle;
    }

    public function getAmount(): ?string
    {
        return $this->amount;
    }

    public function setAmount(string $amount): void
    {
        $this->amount = $amount;
    }

    public function getProductCode(): string
    {
        return $this->productCode;
    }

    public function setProductCode(string $productCode): void
    {
        $this->productCode = $productCode;
    }

    public function getPayeeUserId(): ?string
    {
        return $this->payeeUserId;
    }

    public function setPayeeUserId(?string $payeeUserId): void
    {
        $this->payeeUserId = $payeeUserId;
    }

    public function getPayeeLogonId(): ?string
    {
        return $this->payeeLogonId;
    }

    public function setPayeeLogonId(?string $payeeLogonId): void
    {
        $this->payeeLogonId = $payeeLogonId;
    }

    public function getPayTimeout(): ?string
    {
        return $this->payTimeout;
    }

    public function setPayTimeout(?string $payTimeout): void
    {
        $this->payTimeout = $payTimeout;
    }

    public function getTimeExpress(): ?string
    {
        return $this->timeExpress;
    }

    public function setTimeExpress(?string $timeExpress): void
    {
        $this->timeExpress = $timeExpress;
    }

    /**
     * @return array<string, mixed>|null
     */
    public function getExtraParam(): ?array
    {
        return $this->extraParam;
    }

    /**
     * @param array<string, mixed>|null $extraParam
     */
    public function setExtraParam(?array $extraParam): void
    {
        $this->extraParam = $extraParam;
    }

    /**
     * @return array<string, mixed>|null
     */
    public function getBusinessParams(): ?array
    {
        return $this->businessParams;
    }

    /**
     * @param array<string, mixed>|null $businessParams
     */
    public function setBusinessParams(?array $businessParams): void
    {
        $this->businessParams = $businessParams;
    }

    public function getSceneCode(): ?string
    {
        return $this->sceneCode;
    }

    public function setSceneCode(?string $sceneCode): void
    {
        $this->sceneCode = $sceneCode;
    }

    public function getTransCurrency(): ?string
    {
        return $this->transCurrency;
    }

    public function setTransCurrency(?string $transCurrency): void
    {
        $this->transCurrency = $transCurrency;
    }

    public function getSettleCurrency(): ?string
    {
        return $this->settleCurrency;
    }

    public function setSettleCurrency(?string $settleCurrency): void
    {
        $this->settleCurrency = $settleCurrency;
    }

    public function getAuthNo(): ?string
    {
        return $this->authNo;
    }

    public function setAuthNo(?string $authNo): void
    {
        $this->authNo = $authNo;
    }

    public function getOperationId(): ?string
    {
        return $this->operationId;
    }

    public function setOperationId(?string $operationId): void
    {
        $this->operationId = $operationId;
    }

    public function getStatus(): FundAuthOrderStatus
    {
        return $this->status;
    }

    public function setStatus(FundAuthOrderStatus $status): void
    {
        $this->status = $status;
    }

    public function getGmtTrans(): ?\DateTimeInterface
    {
        return $this->gmtTrans;
    }

    public function setGmtTrans(?\DateTimeInterface $gmtTrans): void
    {
        $this->gmtTrans = $gmtTrans;
    }

    public function getPayerUserId(): ?string
    {
        return $this->payerUserId;
    }

    public function setPayerUserId(?string $payerUserId): void
    {
        $this->payerUserId = $payerUserId;
    }

    public function getPreAuthType(): ?string
    {
        return $this->preAuthType;
    }

    public function setPreAuthType(?string $preAuthType): void
    {
        $this->preAuthType = $preAuthType;
    }

    public function getCreditAmount(): ?string
    {
        return $this->creditAmount;
    }

    public function setCreditAmount(?string $creditAmount): void
    {
        $this->creditAmount = $creditAmount;
    }

    public function getFundAmount(): ?string
    {
        return $this->fundAmount;
    }

    public function setFundAmount(?string $fundAmount): void
    {
        $this->fundAmount = $fundAmount;
    }

    /**
     * @return Collection<int, FundAuthPostPayment>
     */
    public function getPostPayments(): Collection
    {
        return $this->postPayments;
    }

    public function addPostPayment(FundAuthPostPayment $postPayment): static
    {
        if (!$this->postPayments->contains($postPayment)) {
            $this->postPayments->add($postPayment);
            $postPayment->setFundAuthOrder($this);
        }

        return $this;
    }

    public function removePostPayment(FundAuthPostPayment $postPayment): static
    {
        if ($this->postPayments->removeElement($postPayment)) {
            // set the owning side to null (unless already changed)
            if ($postPayment->getFundAuthOrder() === $this) {
                $postPayment->setFundAuthOrder(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, FundAuthUnfreezeLog>
     */
    public function getUnfreezeLogs(): Collection
    {
        return $this->unfreezeLogs;
    }

    public function addUnfreezeLog(FundAuthUnfreezeLog $unfreezeLog): static
    {
        if (!$this->unfreezeLogs->contains($unfreezeLog)) {
            $this->unfreezeLogs->add($unfreezeLog);
            $unfreezeLog->setFundAuthOrder($this);
        }

        return $this;
    }

    public function removeUnfreezeLog(FundAuthUnfreezeLog $unfreezeLog): static
    {
        if ($this->unfreezeLogs->removeElement($unfreezeLog)) {
            // set the owning side to null (unless already changed)
            if ($unfreezeLog->getFundAuthOrder() === $this) {
                $unfreezeLog->setFundAuthOrder(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, TradeOrder>
     */
    public function getTrades(): Collection
    {
        return $this->trades;
    }

    public function addTrade(TradeOrder $trade): static
    {
        if (!$this->trades->contains($trade)) {
            $this->trades->add($trade);
            $trade->setFundAuthOrder($this);
        }

        return $this;
    }

    public function removeTrade(TradeOrder $trade): static
    {
        if ($this->trades->removeElement($trade)) {
            // set the owning side to null (unless already changed)
            if ($trade->getFundAuthOrder() === $this) {
                $trade->setFundAuthOrder(null);
            }
        }

        return $this;
    }
}
