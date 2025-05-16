<?php

namespace AlipayFundAuthBundle\Entity;

use AlipayFundAuthBundle\Enum\FundAuthOrderStatus;
use AlipayFundAuthBundle\Repository\FundAuthOrderRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Tourze\DoctrineSnowflakeBundle\Service\SnowflakeIdGenerator;
use Tourze\EasyAdmin\Attribute\Column\ExportColumn;
use Tourze\EasyAdmin\Attribute\Column\ListColumn;
use Tourze\EasyAdmin\Attribute\Permission\AsPermission;

#[AsPermission(title: '预授权订单')]
#[ORM\Entity(repositoryClass: FundAuthOrderRepository::class)]
#[ORM\Table(name: 'alipay_fund_auth_order', options: ['comment' => '预授权订单'])]
class FundAuthOrder
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

    /**
     * 商家自定义需保证在商户端不重复。仅支持字母、数字、下划线。
     */
    #[ORM\Column(length: 64, options: ['comment' => '商户授权资金订单号'])]
    private ?string $outOrderNo = null;

    #[ORM\Column(length: 64)]
    private ?string $outRequestNo = null;

    #[ORM\Column(length: 100)]
    private ?string $orderTitle = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    private ?string $amount = null;

    #[ORM\Column(length: 32)]
    private string $productCode = 'PREAUTH_PAY';

    #[ORM\Column(length: 32, nullable: true)]
    private ?string $payeeUserId = null;

    #[ORM\Column(length: 100, nullable: true)]
    private ?string $payeeLogonId = null;

    #[ORM\Column(length: 5, nullable: true)]
    private ?string $payTimeout = null;

    #[ORM\Column(length: 5, nullable: true)]
    private ?string $timeExpress = null;

    #[ORM\Column(nullable: true)]
    private ?array $extraParam = null;

    #[ORM\Column(nullable: true)]
    private ?array $businessParams = null;

    #[ORM\Column(length: 128, nullable: true)]
    private ?string $sceneCode = null;

    #[ORM\Column(length: 8, nullable: true, options: ['comment' => '标价币种'])]
    private ?string $transCurrency = null;

    #[ORM\Column(length: 8, nullable: true)]
    private ?string $settleCurrency = null;

    #[ORM\Column(length: 64, nullable: true, options: ['comment' => '支付宝的资金授权订单号'])]
    private ?string $authNo = null;

    #[ORM\Column(length: 64, nullable: true, options: ['comment' => '支付宝的资金操作流水号'])]
    private ?string $operationId = null;

    #[ORM\Column(length: 20, nullable: true, enumType: FundAuthOrderStatus::class, options: ['comment' => '资金预授权明细的状态'])]
    private FundAuthOrderStatus $status = FundAuthOrderStatus::INIT;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true, options: ['comment' => '资金授权成功时间'])]
    private ?\DateTimeInterface $gmtTrans = null;

    #[ORM\Column(length: 32, nullable: true, options: ['comment' => '付款方支付宝用户号'])]
    private ?string $payerUserId = null;

    #[ORM\Column(length: 20, nullable: true, options: ['comment' => '预授权类型'])]
    private ?string $preAuthType = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2, nullable: true, options: ['comment' => '信用冻结金额'])]
    private ?string $creditAmount = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2, nullable: true, options: ['comment' => '自有资金冻结金额'])]
    private ?string $fundAmount = null;

    #[ORM\OneToMany(mappedBy: 'fundAuthOrder', targetEntity: FundAuthPostPayment::class)]
    private Collection $postPayments;

    #[ORM\OneToMany(mappedBy: 'fundAuthOrder', targetEntity: FundAuthUnfreezeLog::class)]
    private Collection $unfreezeLogs;

    #[ORM\OneToMany(mappedBy: 'fundAuthOrder', targetEntity: TradeOrder::class)]
    private Collection $trades;

    public function __construct()
    {
        $this->postPayments = new ArrayCollection();
        $this->unfreezeLogs = new ArrayCollection();
        $this->trades = new ArrayCollection();
    }

    public function getId(): ?string
    {
        return $this->id;
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

    public function getOutOrderNo(): ?string
    {
        return $this->outOrderNo;
    }

    public function setOutOrderNo(string $outOrderNo): static
    {
        $this->outOrderNo = $outOrderNo;

        return $this;
    }

    public function getOutRequestNo(): ?string
    {
        return $this->outRequestNo;
    }

    public function setOutRequestNo(string $outRequestNo): static
    {
        $this->outRequestNo = $outRequestNo;

        return $this;
    }

    public function getOrderTitle(): ?string
    {
        return $this->orderTitle;
    }

    public function setOrderTitle(string $orderTitle): static
    {
        $this->orderTitle = $orderTitle;

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

    public function getProductCode(): string
    {
        return $this->productCode;
    }

    public function setProductCode(string $productCode): static
    {
        $this->productCode = $productCode;

        return $this;
    }

    public function getPayeeUserId(): ?string
    {
        return $this->payeeUserId;
    }

    public function setPayeeUserId(?string $payeeUserId): static
    {
        $this->payeeUserId = $payeeUserId;

        return $this;
    }

    public function getPayeeLogonId(): ?string
    {
        return $this->payeeLogonId;
    }

    public function setPayeeLogonId(?string $payeeLogonId): static
    {
        $this->payeeLogonId = $payeeLogonId;

        return $this;
    }

    public function getPayTimeout(): ?string
    {
        return $this->payTimeout;
    }

    public function setPayTimeout(?string $payTimeout): static
    {
        $this->payTimeout = $payTimeout;

        return $this;
    }

    public function getTimeExpress(): ?string
    {
        return $this->timeExpress;
    }

    public function setTimeExpress(?string $timeExpress): static
    {
        $this->timeExpress = $timeExpress;

        return $this;
    }

    public function getExtraParam(): ?array
    {
        return $this->extraParam;
    }

    public function setExtraParam(?array $extraParam): static
    {
        $this->extraParam = $extraParam;

        return $this;
    }

    public function getBusinessParams(): ?array
    {
        return $this->businessParams;
    }

    public function setBusinessParams(?array $businessParams): static
    {
        $this->businessParams = $businessParams;

        return $this;
    }

    public function getSceneCode(): ?string
    {
        return $this->sceneCode;
    }

    public function setSceneCode(?string $sceneCode): static
    {
        $this->sceneCode = $sceneCode;

        return $this;
    }

    public function getTransCurrency(): ?string
    {
        return $this->transCurrency;
    }

    public function setTransCurrency(?string $transCurrency): static
    {
        $this->transCurrency = $transCurrency;

        return $this;
    }

    public function getSettleCurrency(): ?string
    {
        return $this->settleCurrency;
    }

    public function setSettleCurrency(?string $settleCurrency): static
    {
        $this->settleCurrency = $settleCurrency;

        return $this;
    }

    public function getAuthNo(): ?string
    {
        return $this->authNo;
    }

    public function setAuthNo(?string $authNo): static
    {
        $this->authNo = $authNo;

        return $this;
    }

    public function getOperationId(): ?string
    {
        return $this->operationId;
    }

    public function setOperationId(?string $operationId): static
    {
        $this->operationId = $operationId;

        return $this;
    }

    public function getStatus(): FundAuthOrderStatus
    {
        return $this->status;
    }

    public function setStatus(FundAuthOrderStatus $status): static
    {
        $this->status = $status;

        return $this;
    }

    public function getGmtTrans(): ?\DateTimeInterface
    {
        return $this->gmtTrans;
    }

    public function setGmtTrans(?\DateTimeInterface $gmtTrans): static
    {
        $this->gmtTrans = $gmtTrans;

        return $this;
    }

    public function getPayerUserId(): ?string
    {
        return $this->payerUserId;
    }

    public function setPayerUserId(?string $payerUserId): static
    {
        $this->payerUserId = $payerUserId;

        return $this;
    }

    public function getPreAuthType(): ?string
    {
        return $this->preAuthType;
    }

    public function setPreAuthType(?string $preAuthType): static
    {
        $this->preAuthType = $preAuthType;

        return $this;
    }

    public function getCreditAmount(): ?string
    {
        return $this->creditAmount;
    }

    public function setCreditAmount(?string $creditAmount): static
    {
        $this->creditAmount = $creditAmount;

        return $this;
    }

    public function getFundAmount(): ?string
    {
        return $this->fundAmount;
    }

    public function setFundAmount(?string $fundAmount): static
    {
        $this->fundAmount = $fundAmount;

        return $this;
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
