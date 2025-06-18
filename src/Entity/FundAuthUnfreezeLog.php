<?php

namespace AlipayFundAuthBundle\Entity;

use AlipayFundAuthBundle\Repository\FundAuthUnfreezeLogRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Tourze\DoctrineSnowflakeBundle\Service\SnowflakeIdGenerator;
use Tourze\EasyAdmin\Attribute\Column\ExportColumn;
use Tourze\EasyAdmin\Attribute\Column\ListColumn;
use Tourze\EasyAdmin\Attribute\Permission\AsPermission;

#[AsPermission(title: '解冻记录')]
#[ORM\Entity(repositoryClass: FundAuthUnfreezeLogRepository::class)]
#[ORM\Table(name: 'alipay_fund_auth_unfreeze_log', options: ['comment' => '解冻记录'])]
class FundAuthUnfreezeLog implements \Stringable
{
    #[ExportColumn]
    #[ListColumn(order: -1, sorter: true)]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(SnowflakeIdGenerator::class)]
    #[ORM\Column(type: Types::BIGINT, nullable: false, options: ['comment' => 'ID'])]
    private ?string $id = null;

    #[ORM\ManyToOne(inversedBy: 'unfreezeLogs')]
    #[ORM\JoinColumn(nullable: false)]
    private ?FundAuthOrder $fundAuthOrder = null;

    #[ORM\Column(length: 64, options: ['comment' => '商户本次资金操作的请求流水号'])]
    private ?string $outRequestNo = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2, options: ['comment' => '本次操作解冻的金额'])]
    private ?string $amount = null;

    #[ORM\Column(length: 100, options: ['comment' => '商户的附言信息'])]
    private ?string $remark = null;

    #[ORM\Column(nullable: true, options: ['comment' => '业务扩展参数'])]
    private ?array $extraParam = null;

    #[ORM\Column(length: 64, nullable: true, options: ['comment' => '支付宝资金操作流水号'])]
    private ?string $operationId = null;

    #[ORM\Column(length: 10, nullable: true, options: ['comment' => '本次操作的状态'])]
    private ?string $status = null;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, nullable: true, options: ['comment' => '资金操作成功时间'])]
    private ?\DateTimeInterface $gmtTrans = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 11, scale: 2, nullable: true, options: ['comment' => '本次解冻操作中信用解冻金额'])]
    private ?string $creditAmount = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 11, scale: 2, nullable: true, options: ['comment' => '本次解冻操作中自有资金解冻金额'])]
    private ?string $fundAmount = null;

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

    public function getOutRequestNo(): ?string
    {
        return $this->outRequestNo;
    }

    public function setOutRequestNo(string $outRequestNo): static
    {
        $this->outRequestNo = $outRequestNo;

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

    public function getRemark(): ?string
    {
        return $this->remark;
    }

    public function setRemark(string $remark): static
    {
        $this->remark = $remark;

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

    public function getOperationId(): ?string
    {
        return $this->operationId;
    }

    public function setOperationId(?string $operationId): static
    {
        $this->operationId = $operationId;

        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(?string $status): static
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

    public function __toString(): string
    {
        return $this->outRequestNo ?? ($this->id ?? '');
    }
}
