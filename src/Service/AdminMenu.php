<?php

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
 */
readonly class AdminMenu implements MenuProviderInterface
{
    public function __construct(
        private LinkGeneratorInterface $linkGenerator,
    ) {
    }

    public function __invoke(ItemInterface $item): void
    {
        if (null === $item->getChild('支付宝预授权')) {
            $item->addChild('支付宝预授权');
        }

        $alipayMenu = $item->getChild('支付宝预授权');
        if (null === $alipayMenu) {
            return;
        }

        // 支付宝账号管理菜单
        $alipayMenu->addChild('支付宝账号')
            ->setUri($this->linkGenerator->getCurdListPage(Account::class))
            ->setAttribute('icon', 'fas fa-user-circle')
        ;

        // 预授权订单菜单
        $alipayMenu->addChild('预授权订单')
            ->setUri($this->linkGenerator->getCurdListPage(FundAuthOrder::class))
            ->setAttribute('icon', 'fas fa-lock')
        ;

        // 交易订单菜单
        $alipayMenu->addChild('交易订单')
            ->setUri($this->linkGenerator->getCurdListPage(TradeOrder::class))
            ->setAttribute('icon', 'fas fa-file-invoice-dollar')
        ;

        // 后付费项目菜单
        $alipayMenu->addChild('后付费项目')
            ->setUri($this->linkGenerator->getCurdListPage(FundAuthPostPayment::class))
            ->setAttribute('icon', 'fas fa-clock')
        ;

        // 解冻记录菜单
        $alipayMenu->addChild('解冻记录')
            ->setUri($this->linkGenerator->getCurdListPage(FundAuthUnfreezeLog::class))
            ->setAttribute('icon', 'fas fa-unlock')
        ;

        // 商品信息菜单
        $alipayMenu->addChild('商品信息')
            ->setUri($this->linkGenerator->getCurdListPage(TradeGoodsDetail::class))
            ->setAttribute('icon', 'fas fa-box')
        ;

        // 优惠券明细菜单
        $alipayMenu->addChild('优惠券明细')
            ->setUri($this->linkGenerator->getCurdListPage(TradeVoucherDetail::class))
            ->setAttribute('icon', 'fas fa-ticket-alt')
        ;

        // 资金渠道菜单
        $alipayMenu->addChild('资金渠道')
            ->setUri($this->linkGenerator->getCurdListPage(TradeFundBill::class))
            ->setAttribute('icon', 'fas fa-credit-card')
        ;

        // 优惠参数菜单
        $alipayMenu->addChild('优惠参数')
            ->setUri($this->linkGenerator->getCurdListPage(TradePromoParam::class))
            ->setAttribute('icon', 'fas fa-percentage')
        ;

        // 扩展参数菜单
        $alipayMenu->addChild('扩展参数')
            ->setUri($this->linkGenerator->getCurdListPage(TradeExtendParam::class))
            ->setAttribute('icon', 'fas fa-cog')
        ;
    }
}
