<?php

namespace AlipayFundAuthBundle\Controller\Admin;

use AlipayFundAuthBundle\Entity\TradeOrder;
use AlipayFundAuthBundle\Enum\AliPayType;
use AlipayFundAuthBundle\Enum\AsyncPaymentMode;
use AlipayFundAuthBundle\Enum\AuthConfirmMode;
use AlipayFundAuthBundle\Enum\AuthTradePayMode;
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

/**
 * @extends AbstractCrudController<TradeOrder>
 */
#[AdminCrud(routePath: '/alipay-fund-auth/trade-order', routeName: 'alipay_fund_auth_trade_order')]
final class TradeOrderCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return TradeOrder::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('交易订单')
            ->setEntityLabelInPlural('交易订单')
            ->setPageTitle('index', '交易订单列表')
            ->setPageTitle('detail', '交易订单详情')
            ->setPageTitle('edit', '编辑交易订单')
            ->setPageTitle('new', '新建交易订单')
            ->setHelp('index', '管理支付宝交易订单')
            ->setDefaultSort(['id' => 'DESC'])
            ->setSearchFields(['id', 'outTradeNo', 'tradeNo', 'subject', 'buyerLogonId'])
        ;
    }

    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id', 'ID')->setMaxLength(9999)->hideOnForm();
        yield AssociationField::new('account', '支付宝账号');
        yield AssociationField::new('fundAuthOrder', '预授权订单')->hideOnIndex();
        yield TextField::new('outTradeNo', '商户订单号');
        yield MoneyField::new('totalAmount', '订单金额')->setCurrency('CNY');
        yield TextField::new('subject', '订单标题');
        yield TextField::new('productCode', '产品码');
        yield TextField::new('authNo', '授权号')->hideOnIndex();
        yield ChoiceField::new('authConfirmMode', '预授权确认模式')
            ->setFormType(EnumType::class)
            ->setFormTypeOptions(['class' => AuthConfirmMode::class])
            ->formatValue(function ($value) {
                return $value instanceof AuthConfirmMode ? $value->getLabel() : '';
            })
            ->hideOnIndex()
        ;
        yield TextField::new('storeId', '门店编号')->hideOnIndex();
        yield TextField::new('terminalId', '终端编号')->hideOnIndex();
        yield TextField::new('tradeNo', '支付宝交易号')->hideOnForm();
        yield TextField::new('buyerLogonId', '买家账号')->hideOnForm();
        yield MoneyField::new('receiptAmount', '实收金额')->setCurrency('CNY')->hideOnForm();
        yield MoneyField::new('buyerPayAmount', '买家付款金额')->setCurrency('CNY')->hideOnForm();
        yield MoneyField::new('pointAmount', '集分宝金额')->setCurrency('CNY')->hideOnForm();
        yield MoneyField::new('invoiceAmount', '可开票金额')->setCurrency('CNY')->hideOnForm();
        yield DateTimeField::new('gmtPayment', '支付时间')->hideOnForm();
        yield TextField::new('storeName', '门店名称')->hideOnForm();
        yield TextField::new('buyerUserId', '买家用户ID')->hideOnForm();
        yield TextField::new('buyerOpenId', '买家OpenID')->hideOnForm();
        yield ChoiceField::new('asyncPaymentMode', '异步支付模式')
            ->setFormType(EnumType::class)
            ->setFormTypeOptions(['class' => AsyncPaymentMode::class])
            ->formatValue(function ($value) {
                return $value instanceof AsyncPaymentMode ? $value->getLabel() : '';
            })
            ->hideOnIndex()
        ;
        yield ChoiceField::new('authTradePayMode', '预授权支付模式')
            ->setFormType(EnumType::class)
            ->setFormTypeOptions(['class' => AuthTradePayMode::class])
            ->formatValue(function ($value) {
                return $value instanceof AuthTradePayMode ? $value->getLabel() : '';
            })
            ->hideOnIndex()
        ;
        yield ChoiceField::new('payType', '支付类型')
            ->setFormType(EnumType::class)
            ->setFormTypeOptions(['class' => AliPayType::class])
            ->formatValue(function ($value) {
                return $value instanceof AliPayType ? $value->getLabel() : '';
            })
            ->hideOnIndex()
        ;
        yield MoneyField::new('mdiscountAmount', '商家优惠金额')->setCurrency('CNY')->hideOnForm();
        yield MoneyField::new('discountAmount', '平台优惠金额')->setCurrency('CNY')->hideOnForm();
        yield TextField::new('tradeStatus', '交易状态');
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
        $payTypeChoices = [];
        foreach (AliPayType::cases() as $case) {
            $payTypeChoices[$case->getLabel()] = $case->value;
        }

        return $filters
            ->add(EntityFilter::new('account', '支付宝账号'))
            ->add(EntityFilter::new('fundAuthOrder', '预授权订单'))
            ->add(TextFilter::new('outTradeNo', '商户订单号'))
            ->add(TextFilter::new('tradeNo', '支付宝交易号'))
            ->add(TextFilter::new('subject', '订单标题'))
            ->add(TextFilter::new('tradeStatus', '交易状态'))
            ->add(ChoiceFilter::new('payType', '支付类型')->setChoices($payTypeChoices))
            ->add(DateTimeFilter::new('gmtPayment', '支付时间'))
            ->add(DateTimeFilter::new('createTime', '创建时间'))
            ->add(DateTimeFilter::new('updateTime', '更新时间'))
        ;
    }
}
