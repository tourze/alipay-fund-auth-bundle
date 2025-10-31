<?php

declare(strict_types=1);

namespace AlipayFundAuthBundle\Controller\Admin;

use AlipayFundAuthBundle\Entity\TradePromoParam;
use EasyCorp\Bundle\EasyAdminBundle\Attribute\AdminCrud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Filter\DateTimeFilter;

/**
 * @extends AbstractCrudController<TradePromoParam>
 */
#[AdminCrud(routePath: '/alipay-fund-auth/trade-promo-param', routeName: 'alipay_fund_auth_trade_promo_param')]
final class TradePromoParamCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return TradePromoParam::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('优惠参数')
            ->setEntityLabelInPlural('优惠参数')
            ->setPageTitle('index', '优惠参数列表')
            ->setPageTitle('detail', '优惠参数详情')
            ->setPageTitle('edit', '编辑优惠参数')
            ->setPageTitle('new', '新建优惠参数')
            ->setHelp('index', '管理交易优惠明细参数信息')
            ->setDefaultSort(['id' => 'DESC'])
            ->setSearchFields(['id'])
        ;
    }

    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id', 'ID')->setMaxLength(9999)->hideOnForm();
        yield AssociationField::new('tradeOrder', '关联订单');
        yield DateTimeField::new('actualOrderTime', '实际交易时间');
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions
            ->add(Crud::PAGE_INDEX, Action::DETAIL)
        ;
    }

    public function configureFilters(Filters $filters): Filters
    {
        return $filters
            ->add(DateTimeFilter::new('actualOrderTime', '实际交易时间'))
        ;
    }
}
