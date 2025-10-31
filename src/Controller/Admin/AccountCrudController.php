<?php

namespace AlipayFundAuthBundle\Controller\Admin;

use AlipayFundAuthBundle\Entity\Account;
use EasyCorp\Bundle\EasyAdminBundle\Attribute\AdminCrud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Filter\BooleanFilter;
use EasyCorp\Bundle\EasyAdminBundle\Filter\DateTimeFilter;
use EasyCorp\Bundle\EasyAdminBundle\Filter\TextFilter;

/**
 * @extends AbstractCrudController<Account>
 */
#[AdminCrud(routePath: '/alipay-fund-auth/account', routeName: 'alipay_fund_auth_account')]
final class AccountCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Account::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('支付宝账号')
            ->setEntityLabelInPlural('支付宝账号')
            ->setPageTitle('index', '支付宝账号列表')
            ->setPageTitle('detail', '支付宝账号详情')
            ->setPageTitle('edit', '编辑支付宝账号')
            ->setPageTitle('new', '新建支付宝账号')
            ->setHelp('index', '管理支付宝支付账号配置信息')
            ->setDefaultSort(['id' => 'DESC'])
            ->setSearchFields(['id', 'name', 'appId'])
        ;
    }

    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id', 'ID')->setMaxLength(9999)->hideOnForm();
        yield TextField::new('name', '名称');
        yield TextField::new('appId', 'AppID');
        yield TextareaField::new('rsaPrivateKey', 'RSA私钥')->hideOnIndex();
        yield TextareaField::new('rsaPublicKey', 'RSA公钥')->hideOnIndex();
        yield BooleanField::new('valid', '有效状态');
        yield TextField::new('createdBy', '创建人')->hideOnForm();
        yield TextField::new('updatedBy', '更新人')->hideOnForm();
        yield TextField::new('createdFromIp', '创建IP')->hideOnForm();
        yield TextField::new('updatedFromIp', '更新IP')->hideOnForm();
        yield DateTimeField::new('createTime', '创建时间')->hideOnForm();
        yield DateTimeField::new('updateTime', '更新时间')->hideOnForm();
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
            ->add(TextFilter::new('name', '名称'))
            ->add(TextFilter::new('appId', 'AppID'))
            ->add(BooleanFilter::new('valid', '有效状态'))
            ->add(DateTimeFilter::new('createTime', '创建时间'))
            ->add(DateTimeFilter::new('updateTime', '更新时间'))
        ;
    }
}
