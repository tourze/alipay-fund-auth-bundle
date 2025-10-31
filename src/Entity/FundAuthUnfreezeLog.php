<?php

namespace AlipayFundAuthBundle\Entity;

use AlipayFundAuthBundle\Repository\FundAuthUnfreezeLogRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Tourze\DoctrineSnowflakeBundle\Traits\SnowflakeKeyAware;

#[ORM\Entity(repositoryClass: FundAuthUnfreezeLogRepository::class)]
#[ORM\Table(name: 'alipay_fund_auth_unfreeze_log', options: ['comment' => '解冻记录'])]
class FundAuthUnfreezeLog implements \Stringable
{
    use SnowflakeKeyAware;

    #[ORM\ManyToOne(inversedBy: 'unfreezeLogs', cascade: ['persist'])]
    #[ORM\JoinColumn(nullable: false)]
    private ?FundAuthOrder $fundAuthOrder = null;

    #[Assert\NotBlank]
    #[Assert\Length(max: 64)]
    #[ORM\Column(length: 64, options: ['comment' => '商户本次资金操作的请求流水号'])]
    private ?string $outRequestNo = null;

    #[Assert\NotBlank]
    #[Assert\PositiveOrZero]
    #[Assert\Length(max: 10)]
    #[Assert\Regex(pattern: '/^\d+\.\d{2}$/')]
    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2, options: ['comment' => '本次操作解冻的金额'])]
    private ?string $amount = null;

    #[Assert\Length(max: 100)]
    #[ORM\Column(length: 100, options: ['comment' => '商户的附言信息'])]
    private ?string $remark = null;

    /**
     * @var array<string, mixed>|null
     */
    #[Assert\Type(type: 'array')]
    #[ORM\Column(nullable: true, options: ['comment' => '业务扩展参数'])]
    private ?array $extraParam = null;

    #[Assert\Length(max: 64)]
    #[ORM\Column(length: 64, nullable: true, options: ['comment' => '支付宝资金操作流水号'])]
    private ?string $operationId = null;

    #[Assert\Length(max: 10)]
    #[ORM\Column(length: 10, nullable: true, options: ['comment' => '本次操作的状态'])]
    private ?string $status = null;

    #[Assert\Type(type: '\DateTimeInterface')]
    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, nullable: true, options: ['comment' => '资金操作成功时间'])]
    private ?\DateTimeInterface $gmtTrans = null;

    #[Assert\PositiveOrZero]
    #[Assert\Length(max: 11)]
    #[Assert\Regex(pattern: '/^\d+\.\d{2}$/')]
    #[ORM\Column(type: Types::DECIMAL, precision: 11, scale: 2, nullable: true, options: ['comment' => '本次解冻操作中信用解冻金额'])]
    private ?string $creditAmount = null;

    #[Assert\PositiveOrZero]
    #[Assert\Length(max: 11)]
    #[Assert\Regex(pattern: '/^\d+\.\d{2}$/')]
    #[ORM\Column(type: Types::DECIMAL, precision: 11, scale: 2, nullable: true, options: ['comment' => '本次解冻操作中自有资金解冻金额'])]
    private ?string $fundAmount = null;

    public function getFundAuthOrder(): ?FundAuthOrder
    {
        return $this->fundAuthOrder;
    }

    public function setFundAuthOrder(?FundAuthOrder $fundAuthOrder): void
    {
        $this->fundAuthOrder = $fundAuthOrder;
    }

    public function getOutRequestNo(): ?string
    {
        return $this->outRequestNo;
    }

    public function setOutRequestNo(string $outRequestNo): void
    {
        $this->outRequestNo = $outRequestNo;
    }

    public function getAmount(): ?string
    {
        return $this->amount;
    }

    public function setAmount(string $amount): void
    {
        $this->amount = $amount;
    }

    public function getRemark(): ?string
    {
        return $this->remark;
    }

    public function setRemark(string $remark): void
    {
        $this->remark = $remark;
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

    public function getOperationId(): ?string
    {
        return $this->operationId;
    }

    public function setOperationId(?string $operationId): void
    {
        $this->operationId = $operationId;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(?string $status): void
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

    public function __toString(): string
    {
        return $this->outRequestNo ?? ($this->id ?? '');
    }
}
