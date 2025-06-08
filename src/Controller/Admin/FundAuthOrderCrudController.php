<?php

namespace AlipayFundAuthBundle\Controller\Admin;

use AlipayFundAuthBundle\Entity\FundAuthOrder;
use AlipayFundAuthBundle\Enum\FundAuthOrderStatus;
use EasyCorp\Bundle\EasyAdminBundle\Attribute\AdminCrud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\MoneyField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Filter\ChoiceFilter;
use EasyCorp\Bundle\EasyAdminBundle\Filter\DateTimeFilter;
use EasyCorp\Bundle\EasyAdminBundle\Filter\EntityFilter;
use EasyCorp\Bundle\EasyAdminBundle\Filter\TextFilter;
use Symfony\Component\Form\Extension\Core\Type\EnumType;

#[AdminCrud(routePath: '/alipay-fund-auth/fund-auth-order', routeName: 'alipay_fund_auth_fund_auth_order')]
class FundAuthOrderCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return FundAuthOrder::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('预授权订单')
            ->setEntityLabelInPlural('预授权订单')
            ->setPageTitle('index', '预授权订单列表')
            ->setPageTitle('detail', '预授权订单详情')
            ->setPageTitle('edit', '编辑预授权订单')
            ->setPageTitle('new', '新建预授权订单')
            ->setHelp('index', '管理支付宝资金预授权订单')
            ->setDefaultSort(['id' => 'DESC'])
            ->setSearchFields(['id', 'outOrderNo', 'outRequestNo', 'orderTitle', 'authNo']);
    }

    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id', 'ID')->setMaxLength(9999)->hideOnForm();
        yield AssociationField::new('account', '支付宝账号');
        yield TextField::new('outOrderNo', '商户订单号');
        yield TextField::new('outRequestNo', '商户请求号');
        yield TextField::new('orderTitle', '订单标题');
        yield MoneyField::new('amount', '授权金额')->setCurrency('CNY');
        yield TextField::new('productCode', '产品码');
        yield TextField::new('payeeUserId', '收款方用户ID')->hideOnIndex();
        yield TextField::new('payeeLogonId', '收款方登录ID')->hideOnIndex();
        yield TextField::new('payTimeout', '支付超时时间')->hideOnIndex();
        yield TextField::new('timeExpress', '有效期')->hideOnIndex();
        yield TextField::new('sceneCode', '场景码')->hideOnIndex();
        yield TextField::new('transCurrency', '交易币种')->hideOnIndex();
        yield TextField::new('settleCurrency', '结算币种')->hideOnIndex();
        yield TextField::new('authNo', '授权号')->hideOnForm();
        yield TextField::new('operationId', '操作ID')->hideOnForm();
        yield ChoiceField::new('status', '状态')
            ->setFormType(EnumType::class)
            ->setFormTypeOptions(['class' => FundAuthOrderStatus::class])
            ->formatValue(function ($value) {
                return $value instanceof FundAuthOrderStatus ? $value->getLabel() : '';
            });
        yield DateTimeField::new('gmtTrans', '交易时间')->hideOnForm();
        yield TextField::new('payerUserId', '付款方用户ID')->hideOnForm();
        yield TextField::new('preAuthType', '预授权类型')->hideOnForm();
        yield MoneyField::new('creditAmount', '信用冻结金额')->setCurrency('CNY')->hideOnForm();
        yield MoneyField::new('fundAmount', '自有资金冻结金额')->setCurrency('CNY')->hideOnForm();
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions
            ->add(Crud::PAGE_INDEX, Action::DETAIL)
            ->reorder(Crud::PAGE_INDEX, [Action::DETAIL, Action::EDIT, Action::DELETE]);
    }

    public function configureFilters(Filters $filters): Filters
    {
        $statusChoices = [];
        foreach (FundAuthOrderStatus::cases() as $case) {
            $statusChoices[$case->getLabel()] = $case->value;
        }

        return $filters
            ->add(EntityFilter::new('account', '支付宝账号'))
            ->add(TextFilter::new('outOrderNo', '商户订单号'))
            ->add(TextFilter::new('outRequestNo', '商户请求号'))
            ->add(TextFilter::new('orderTitle', '订单标题'))
            ->add(ChoiceFilter::new('status', '状态')->setChoices($statusChoices))
            ->add(DateTimeFilter::new('gmtTrans', '交易时间'));
    }
} 