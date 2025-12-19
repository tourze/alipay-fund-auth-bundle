<?php

declare(strict_types=1);

namespace AlipayFundAuthBundle\Service;

use AlipayFundAuthBundle\Entity\Account;
use AlipayFundAuthBundle\Entity\FundAuthOrder;
use AlipayFundAuthBundle\Entity\FundAuthPostPayment;
use AlipayFundAuthBundle\Entity\FundAuthUnfreezeLog;
use AlipayFundAuthBundle\Entity\TradeExtendParam;
use AlipayFundAuthBundle\Entity\TradeFundBill;
use AlipayFundAuthBundle\Entity\TradeGoodsDetail;
use AlipayFundAuthBundle\Entity\TradeOrder;
use AlipayFundAuthBundle\Entity\TradePromoParam;
use AlipayFundAuthBundle\Entity\TradeVoucherDetail;
use Knp\Menu\ItemInterface;
use Tourze\EasyAdminMenuBundle\Service\LinkGeneratorInterface;
use Tourze\EasyAdminMenuBundle\Service\MenuProviderInterface;

/**
 * 支付宝资金预授权菜单服务
 *
 * 提供后台管理菜单的动态生成功能
 */
final readonly class AdminMenu implements MenuProviderInterface
{
    private const MAIN_MENU_NAME = '支付宝预授权';

    /**
     * 菜单项配置
     */
    private const MENU_ITEMS = [
        '支付宝账号' => [
            'entity' => Account::class,
            'icon' => 'fas fa-user-circle',
        ],
        '预授权订单' => [
            'entity' => FundAuthOrder::class,
            'icon' => 'fas fa-lock',
        ],
        '交易订单' => [
            'entity' => TradeOrder::class,
            'icon' => 'fas fa-file-invoice-dollar',
        ],
        '后付费项目' => [
            'entity' => FundAuthPostPayment::class,
            'icon' => 'fas fa-clock',
        ],
        '解冻记录' => [
            'entity' => FundAuthUnfreezeLog::class,
            'icon' => 'fas fa-unlock',
        ],
        '商品信息' => [
            'entity' => TradeGoodsDetail::class,
            'icon' => 'fas fa-box',
        ],
        '优惠券明细' => [
            'entity' => TradeVoucherDetail::class,
            'icon' => 'fas fa-ticket-alt',
        ],
        '资金渠道' => [
            'entity' => TradeFundBill::class,
            'icon' => 'fas fa-credit-card',
        ],
        '优惠参数' => [
            'entity' => TradePromoParam::class,
            'icon' => 'fas fa-percentage',
        ],
        '扩展参数' => [
            'entity' => TradeExtendParam::class,
            'icon' => 'fas fa-cog',
        ],
    ];

    public function __construct(
        private LinkGeneratorInterface $linkGenerator,
    ) {
    }

    public function __invoke(ItemInterface $item): void
    {
        $alipayMenu = $this->getOrCreateMainMenu($item);
        if (null === $alipayMenu) {
            return;
        }

        $this->addMenuItems($alipayMenu);
    }

    /**
     * 获取或创建主菜单
     */
    private function getOrCreateMainMenu(ItemInterface $item): ?ItemInterface
    {
        if (null === $item->getChild(self::MAIN_MENU_NAME)) {
            $item->addChild(self::MAIN_MENU_NAME);
        }

        return $item->getChild(self::MAIN_MENU_NAME);
    }

    /**
     * 添加菜单项
     */
    private function addMenuItems(ItemInterface $alipayMenu): void
    {
        foreach (self::MENU_ITEMS as $name => $config) {
            $alipayMenu->addChild($name)
                ->setUri($this->linkGenerator->getCurdListPage($config['entity']))
                ->setAttribute('icon', $config['icon']);
        }
    }
}
