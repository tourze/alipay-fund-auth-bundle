<?php

namespace AlipayFundAuthBundle\Entity;

use AlipayFundAuthBundle\Repository\TradeGoodsDetailRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Tourze\DoctrineSnowflakeBundle\Service\SnowflakeIdGenerator;
use Tourze\EasyAdmin\Attribute\Column\ExportColumn;
use Tourze\EasyAdmin\Attribute\Column\ListColumn;
use Tourze\EasyAdmin\Attribute\Permission\AsPermission;

#[AsPermission(title: '商品信息')]
#[ORM\Entity(repositoryClass: TradeGoodsDetailRepository::class)]
#[ORM\Table(name: 'alipay_fund_auth_trade_goods_detail', options: ['comment' => '商品信息'])]
class TradeGoodsDetail
{
    #[ExportColumn]
    #[ListColumn(order: -1, sorter: true)]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(SnowflakeIdGenerator::class)]
    #[ORM\Column(type: Types::BIGINT, nullable: false, options: ['comment' => 'ID'])]
    private ?string $id = null;

    #[ORM\Column(length: 64, options: ['comment' => '商品编号'])]
    private ?string $goodsId = null;

    #[ORM\Column(length: 256, options: ['comment' => '商品名称'])]
    private ?string $goodsName = null;

    #[ORM\Column(options: ['comment' => '商品数量'])]
    private ?int $quantity = null;

    /**
     * @var string|null 单位为元
     */
    #[ORM\Column(type: Types::DECIMAL, precision: 11, scale: 2, options: ['comment' => '商品单价'])]
    private ?string $price = null;

    #[ORM\Column(length: 24, nullable: true, options: ['comment' => '商品类目'])]
    private ?string $goodsCategory = null;

    /**
     * @var string|null 从商品类目根节点到叶子节点的类目id组成，类目id值使用|分割
     */
    #[ORM\Column(length: 128, nullable: true, options: ['comment' => '商品类目树'])]
    private ?string $categoryTree = null;

    #[ORM\Column(length: 400, nullable: true, options: ['comment' => '商品的展示地址'])]
    private ?string $showUrl = null;

    #[ORM\ManyToOne(inversedBy: 'goodsDetails')]
    #[ORM\JoinColumn(nullable: false)]
    private ?TradeOrder $tradeOrder = null;

    public function getId(): ?string
    {
        return $this->id;
    }

    public function getGoodsId(): ?string
    {
        return $this->goodsId;
    }

    public function setGoodsId(string $goodsId): static
    {
        $this->goodsId = $goodsId;

        return $this;
    }

    public function getGoodsName(): ?string
    {
        return $this->goodsName;
    }

    public function setGoodsName(string $goodsName): static
    {
        $this->goodsName = $goodsName;

        return $this;
    }

    public function getQuantity(): ?int
    {
        return $this->quantity;
    }

    public function setQuantity(int $quantity): static
    {
        $this->quantity = $quantity;

        return $this;
    }

    public function getPrice(): ?string
    {
        return $this->price;
    }

    public function setPrice(string $price): static
    {
        $this->price = $price;

        return $this;
    }

    public function getGoodsCategory(): ?string
    {
        return $this->goodsCategory;
    }

    public function setGoodsCategory(?string $goodsCategory): static
    {
        $this->goodsCategory = $goodsCategory;

        return $this;
    }

    public function getCategoryTree(): ?string
    {
        return $this->categoryTree;
    }

    public function setCategoryTree(?string $categoryTree): static
    {
        $this->categoryTree = $categoryTree;

        return $this;
    }

    public function getShowUrl(): ?string
    {
        return $this->showUrl;
    }

    public function setShowUrl(?string $showUrl): static
    {
        $this->showUrl = $showUrl;

        return $this;
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
}
