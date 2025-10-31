<?php

namespace AlipayFundAuthBundle\Controller\Admin;

use AlipayFundAuthBundle\Entity\FundAuthPostPayment;
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
use EasyCorp\Bundle\EasyAdminBundle\Filter\EntityFilter;
use EasyCorp\Bundle\EasyAdminBundle\Filter\TextFilter;

/**
 * @extends AbstractCrudController<FundAuthPostPayment>
 */
#[AdminCrud(routePath: '/alipay-fund-auth/fund-auth-post-payment', routeName: 'alipay_fund_auth_fund_auth_post_payment')]
final class FundAuthPostPaymentCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return FundAuthPostPayment::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('后付费项目')
            ->setEntityLabelInPlural('后付费项目')
            ->setPageTitle('index', '后付费项目列表')
            ->setPageTitle('detail', '后付费项目详情')
            ->setPageTitle('edit', '编辑后付费项目')
            ->setPageTitle('new', '新建后付费项目')
            ->setHelp('index', '管理预授权订单的后付费项目')
            ->setDefaultSort(['id' => 'DESC'])
            ->setSearchFields(['id', 'name', 'description'])
        ;
    }

    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id', 'ID')->setMaxLength(9999)->hideOnForm();
        yield AssociationField::new('fundAuthOrder', '预授权订单');
        yield TextField::new('name', '项目名称');
        yield MoneyField::new('amount', '金额')->setCurrency('CNY');
        yield TextField::new('description', '计费说明');
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
            ->add(TextFilter::new('name', '项目名称'))
            ->add(TextFilter::new('description', '计费说明'))
        ;
    }
}
