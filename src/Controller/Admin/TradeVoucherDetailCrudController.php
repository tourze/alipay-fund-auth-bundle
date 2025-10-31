<?php

declare(strict_types=1);

namespace AlipayFundAuthBundle\Controller\Admin;

use AlipayFundAuthBundle\Entity\TradeVoucherDetail;
use AlipayFundAuthBundle\Enum\VoucherType;
use EasyCorp\Bundle\EasyAdminBundle\Attribute\AdminCrud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\MoneyField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Filter\TextFilter;
use Symfony\Component\Form\Extension\Core\Type\EnumType;

/**
 * @extends AbstractCrudController<TradeVoucherDetail>
 */
#[AdminCrud(routePath: '/alipay-fund-auth/trade-voucher-detail', routeName: 'alipay_fund_auth_trade_voucher_detail')]
final class TradeVoucherDetailCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return TradeVoucherDetail::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('优惠券明细')
            ->setEntityLabelInPlural('优惠券明细')
            ->setPageTitle('index', '优惠券明细列表')
            ->setPageTitle('detail', '优惠券明细详情')
            ->setPageTitle('edit', '编辑优惠券明细')
            ->setPageTitle('new', '新建优惠券明细')
            ->setHelp('index', '管理交易优惠券使用明细信息')
            ->setDefaultSort(['id' => 'DESC'])
            ->setSearchFields(['id', 'voucherId', 'name', 'templateId'])
        ;
    }

    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id', 'ID')->setMaxLength(9999)->hideOnForm();
        yield AssociationField::new('tradeOrder', '关联订单');
        yield TextField::new('voucherId', '券ID');
        yield TextField::new('name', '券名称');
        yield ChoiceField::new('type', '券类型')
            ->setFormType(EnumType::class)
            ->setFormTypeOptions(['class' => VoucherType::class])
            ->formatValue(function ($value) {
                return $value instanceof VoucherType ? $value->getLabel() : '';
            })
        ;
        yield MoneyField::new('amount', '优惠券面额')->setCurrency('CNY');
        yield MoneyField::new('merchantContribute', '商家出资')->setCurrency('CNY')->hideOnIndex();
        yield MoneyField::new('otherContribute', '其他出资')->setCurrency('CNY')->hideOnIndex();
        yield TextField::new('templateId', '券模板ID')->hideOnIndex();
        yield MoneyField::new('purchaseBuyerContribute', '用户实付')->setCurrency('CNY')->hideOnIndex();
        yield MoneyField::new('purchaseMerchantContribute', '购买商户优惠')->setCurrency('CNY')->hideOnIndex();
        yield MoneyField::new('purchaseAntContribute', '购买平台优惠')->setCurrency('CNY')->hideOnIndex();
        yield TextareaField::new('memo', '备注信息')->hideOnIndex();
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
            ->add(TextFilter::new('voucherId', '券ID'))
            ->add(TextFilter::new('name', '券名称'))
            ->add(TextFilter::new('templateId', '券模板ID'))
        ;
    }
}
