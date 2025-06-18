<?php

namespace AlipayFundAuthBundle\Service;

use AlipayFundAuthBundle\Entity\Account;
use AlipayFundAuthBundle\Entity\FundAuthOrder;
use AlipayFundAuthBundle\Entity\FundAuthPostPayment;
use AlipayFundAuthBundle\Entity\FundAuthUnfreezeLog;
use AlipayFundAuthBundle\Entity\TradeGoodsDetail;
use AlipayFundAuthBundle\Entity\TradeOrder;
use Knp\Menu\ItemInterface;
use Tourze\EasyAdminMenuBundle\Service\LinkGeneratorInterface;
use Tourze\EasyAdminMenuBundle\Service\MenuProviderInterface;

/**
 * 支付宝资金预授权菜单服务
 */
class AdminMenu implements MenuProviderInterface
{
    public function __construct(
        private readonly LinkGeneratorInterface $linkGenerator,
    ) {
    }

    public function __invoke(ItemInterface $item): void
    {
        if ($item->getChild('支付宝预授权') === null) {
            $item->addChild('支付宝预授权');
        }

        $alipayMenu = $item->getChild('支付宝预授权');
        
        // 支付宝账号管理菜单
        $alipayMenu->addChild('支付宝账号')
            ->setUri($this->linkGenerator->getCurdListPage(Account::class))
            ->setAttribute('icon', 'fas fa-user-circle');
        
        // 预授权订单菜单
        $alipayMenu->addChild('预授权订单')
            ->setUri($this->linkGenerator->getCurdListPage(FundAuthOrder::class))
            ->setAttribute('icon', 'fas fa-lock');
        
        // 交易订单菜单
        $alipayMenu->addChild('交易订单')
            ->setUri($this->linkGenerator->getCurdListPage(TradeOrder::class))
            ->setAttribute('icon', 'fas fa-file-invoice-dollar');

        // 后付费项目菜单
        $alipayMenu->addChild('后付费项目')
            ->setUri($this->linkGenerator->getCurdListPage(FundAuthPostPayment::class))
            ->setAttribute('icon', 'fas fa-clock');
        
        // 解冻记录菜单
        $alipayMenu->addChild('解冻记录')
            ->setUri($this->linkGenerator->getCurdListPage(FundAuthUnfreezeLog::class))
            ->setAttribute('icon', 'fas fa-unlock');
        
        // 商品信息菜单
        $alipayMenu->addChild('商品信息')
            ->setUri($this->linkGenerator->getCurdListPage(TradeGoodsDetail::class))
            ->setAttribute('icon', 'fas fa-box');
    }
} 