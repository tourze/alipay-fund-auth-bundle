<?php

declare(strict_types=1);

namespace AlipayFundAuthBundle\Controller\Admin;

use AlipayFundAuthBundle\Entity\TradeExtendParam;
use EasyCorp\Bundle\EasyAdminBundle\Attribute\AdminCrud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Filter\TextFilter;

/**
 * @extends AbstractCrudController<TradeExtendParam>
 */
#[AdminCrud(routePath: '/alipay-fund-auth/trade-extend-param', routeName: 'alipay_fund_auth_trade_extend_param')]
final class TradeExtendParamCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return TradeExtendParam::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('扩展参数')
            ->setEntityLabelInPlural('扩展参数')
            ->setPageTitle('index', '扩展参数列表')
            ->setPageTitle('detail', '扩展参数详情')
            ->setPageTitle('edit', '编辑扩展参数')
            ->setPageTitle('new', '新建扩展参数')
            ->setHelp('index', '管理交易业务扩展参数信息')
            ->setDefaultSort(['id' => 'DESC'])
            ->setSearchFields(['id', 'sysServiceProviderId', 'specifiedSellerName', 'cardType'])
        ;
    }

    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id', 'ID')->setMaxLength(9999)->hideOnForm();
        yield AssociationField::new('tradeOrder', '关联订单');
        yield TextField::new('sysServiceProviderId', '系统商编号');
        yield TextField::new('specifiedSellerName', '卖家名称');
        yield TextField::new('cardType', '卡类型');
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
            ->add(TextFilter::new('sysServiceProviderId', '系统商编号'))
            ->add(TextFilter::new('specifiedSellerName', '卖家名称'))
            ->add(TextFilter::new('cardType', '卡类型'))
        ;
    }
}
