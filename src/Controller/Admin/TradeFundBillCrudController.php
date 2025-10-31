<?php

declare(strict_types=1);

namespace AlipayFundAuthBundle\Controller\Admin;

use AlipayFundAuthBundle\Entity\TradeFundBill;
use EasyCorp\Bundle\EasyAdminBundle\Attribute\AdminCrud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\MoneyField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Filter\TextFilter;

/**
 * @extends AbstractCrudController<TradeFundBill>
 */
#[AdminCrud(routePath: '/alipay-fund-auth/trade-fund-bill', routeName: 'alipay_fund_auth_trade_fund_bill')]
final class TradeFundBillCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return TradeFundBill::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('资金渠道')
            ->setEntityLabelInPlural('资金渠道')
            ->setPageTitle('index', '资金渠道列表')
            ->setPageTitle('detail', '资金渠道详情')
            ->setPageTitle('edit', '编辑资金渠道')
            ->setPageTitle('new', '新建资金渠道')
            ->setHelp('index', '管理交易支付使用的资金渠道信息')
            ->setDefaultSort(['id' => 'DESC'])
            ->setSearchFields(['id', 'fundChannel'])
        ;
    }

    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id', 'ID')->setMaxLength(9999)->hideOnForm();
        yield AssociationField::new('tradeOrder', '关联订单');
        yield TextField::new('fundChannel', '资金渠道');
        yield MoneyField::new('amount', '使用金额')->setCurrency('CNY');
        yield MoneyField::new('realAmount', '实际使用金额')->setCurrency('CNY');
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
            ->add(TextFilter::new('fundChannel', '资金渠道'))
        ;
    }
}
