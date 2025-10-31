<?php

namespace AlipayFundAuthBundle\Entity;

use AlipayFundAuthBundle\Repository\TradeGoodsDetailRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Tourze\DoctrineSnowflakeBundle\Traits\SnowflakeKeyAware;

#[ORM\Entity(repositoryClass: TradeGoodsDetailRepository::class)]
#[ORM\Table(name: 'alipay_fund_auth_trade_goods_detail', options: ['comment' => '商品信息'])]
class TradeGoodsDetail implements \Stringable
{
    use SnowflakeKeyAware;

    #[ORM\Column(length: 64, options: ['comment' => '商品编号'])]
    #[Assert\NotBlank]
    #[Assert\Length(max: 64)]
    private ?string $goodsId = null;

    #[ORM\Column(length: 256, options: ['comment' => '商品名称'])]
    #[Assert\NotBlank]
    #[Assert\Length(max: 256)]
    private ?string $goodsName = null;

    #[ORM\Column(options: ['comment' => '商品数量'])]
    #[Assert\NotBlank]
    #[Assert\PositiveOrZero]
    private ?int $quantity = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 11, scale: 2, options: ['comment' => '商品单价'])]
    #[Assert\NotBlank]
    #[Assert\Length(max: 13)] // 11位整数+1位小数点+2位小数
    #[Assert\PositiveOrZero]
    #[Assert\Regex(pattern: '/^\d{1,9}(\.\d{1,2})?$/', message: '价格格式不正确')]
    private ?string $price = null;

    #[ORM\Column(length: 24, nullable: true, options: ['comment' => '商品类目'])]
    #[Assert\Length(max: 24)]
    private ?string $goodsCategory = null;

    #[ORM\Column(length: 128, nullable: true, options: ['comment' => '商品类目树'])]
    #[Assert\Length(max: 128)]
    private ?string $categoryTree = null;

    #[ORM\Column(length: 400, nullable: true, options: ['comment' => '商品的展示地址'])]
    #[Assert\Length(max: 400)]
    #[Assert\Url(message: '请输入有效的URL')]
    private ?string $showUrl = null;

    #[ORM\ManyToOne(inversedBy: 'goodsDetails', cascade: ['persist'])]
    #[ORM\JoinColumn(nullable: false)]
    private ?TradeOrder $tradeOrder = null;

    public function getGoodsId(): ?string
    {
        return $this->goodsId;
    }

    public function setGoodsId(string $goodsId): void
    {
        $this->goodsId = $goodsId;
    }

    public function getGoodsName(): ?string
    {
        return $this->goodsName;
    }

    public function setGoodsName(string $goodsName): void
    {
        $this->goodsName = $goodsName;
    }

    public function getQuantity(): ?int
    {
        return $this->quantity;
    }

    public function setQuantity(int $quantity): void
    {
        $this->quantity = $quantity;
    }

    public function getPrice(): ?string
    {
        return $this->price;
    }

    public function setPrice(string $price): void
    {
        $this->price = $price;
    }

    public function getGoodsCategory(): ?string
    {
        return $this->goodsCategory;
    }

    public function setGoodsCategory(?string $goodsCategory): void
    {
        $this->goodsCategory = $goodsCategory;
    }

    public function getCategoryTree(): ?string
    {
        return $this->categoryTree;
    }

    public function setCategoryTree(?string $categoryTree): void
    {
        $this->categoryTree = $categoryTree;
    }

    public function getShowUrl(): ?string
    {
        return $this->showUrl;
    }

    public function setShowUrl(?string $showUrl): void
    {
        $this->showUrl = $showUrl;
    }

    public function getTradeOrder(): ?TradeOrder
    {
        return $this->tradeOrder;
    }

    public function setTradeOrder(?TradeOrder $tradeOrder): void
    {
        $this->tradeOrder = $tradeOrder;
    }

    public function __toString(): string
    {
        return $this->goodsName ?? ($this->goodsId ?? ($this->id ?? ''));
    }
}
