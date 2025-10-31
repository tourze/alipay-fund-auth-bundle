<?php

namespace AlipayFundAuthBundle\Controller\Admin;

use AlipayFundAuthBundle\Entity\FundAuthUnfreezeLog;
use EasyCorp\Bundle\EasyAdminBundle\Attribute\AdminCrud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\MoneyField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Filter\DateTimeFilter;
use EasyCorp\Bundle\EasyAdminBundle\Filter\EntityFilter;
use EasyCorp\Bundle\EasyAdminBundle\Filter\TextFilter;

/**
 * @extends AbstractCrudController<FundAuthUnfreezeLog>
 */
#[AdminCrud(routePath: '/alipay-fund-auth/fund-auth-unfreeze-log', routeName: 'alipay_fund_auth_fund_auth_unfreeze_log')]
final class FundAuthUnfreezeLogCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return FundAuthUnfreezeLog::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('解冻记录')
            ->setEntityLabelInPlural('解冻记录')
            ->setPageTitle('index', '解冻记录列表')
            ->setPageTitle('detail', '解冻记录详情')
            ->setPageTitle('edit', '编辑解冻记录')
            ->setPageTitle('new', '新建解冻记录')
            ->setHelp('index', '管理资金预授权解冻记录')
            ->setDefaultSort(['id' => 'DESC'])
            ->setSearchFields(['id', 'outRequestNo', 'remark', 'operationId'])
        ;
    }

    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id', 'ID')->setMaxLength(9999)->hideOnForm();
        yield AssociationField::new('fundAuthOrder', '预授权订单');
        yield TextField::new('outRequestNo', '商户请求号');
        yield MoneyField::new('amount', '解冻金额')->setCurrency('CNY');
        yield TextField::new('remark', '备注');
        yield TextField::new('operationId', '操作ID')->hideOnForm();
        yield TextField::new('status', '状态')->hideOnForm();
        yield DateTimeField::new('gmtTrans', '交易时间')->hideOnForm();
        yield MoneyField::new('creditAmount', '信用解冻金额')->setCurrency('CNY')->hideOnForm();
        yield MoneyField::new('fundAmount', '自有资金解冻金额')->setCurrency('CNY')->hideOnForm();
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
            ->add(EntityFilter::new('fundAuthOrder', '预授权订单'))
            ->add(TextFilter::new('outRequestNo', '商户请求号'))
            ->add(TextFilter::new('remark', '备注'))
            ->add(TextFilter::new('status', '状态'))
            ->add(DateTimeFilter::new('gmtTrans', '交易时间'))
        ;
    }
}
