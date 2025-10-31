<?php

namespace AlipayFundAuthBundle\Controller\Admin;

use AlipayFundAuthBundle\Entity\TradeGoodsDetail;
use EasyCorp\Bundle\EasyAdminBundle\Attribute\AdminCrud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\MoneyField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\UrlField;
use EasyCorp\Bundle\EasyAdminBundle\Filter\EntityFilter;
use EasyCorp\Bundle\EasyAdminBundle\Filter\NumericFilter;
use EasyCorp\Bundle\EasyAdminBundle\Filter\TextFilter;

/**
 * @extends AbstractCrudController<TradeGoodsDetail>
 */
#[AdminCrud(routePath: '/alipay-fund-auth/trade-goods-detail', routeName: 'alipay_fund_auth_trade_goods_detail')]
final class TradeGoodsDetailCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return TradeGoodsDetail::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('商品信息')
            ->setEntityLabelInPlural('商品信息')
            ->setPageTitle('index', '商品信息列表')
            ->setPageTitle('detail', '商品信息详情')
            ->setPageTitle('edit', '编辑商品信息')
            ->setPageTitle('new', '新建商品信息')
            ->setHelp('index', '管理交易订单的商品详细信息')
            ->setDefaultSort(['id' => 'DESC'])
            ->setSearchFields(['id', 'goodsId', 'goodsName', 'goodsCategory'])
        ;
    }

    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id', 'ID')->setMaxLength(9999)->hideOnForm();
        yield AssociationField::new('tradeOrder', '交易订单');
        yield TextField::new('goodsId', '商品编号');
        yield TextField::new('goodsName', '商品名称');
        yield IntegerField::new('quantity', '商品数量');
        yield MoneyField::new('price', '商品单价')->setCurrency('CNY');
        yield TextField::new('goodsCategory', '商品类目')->hideOnIndex();
        yield TextField::new('categoryTree', '商品类目树')->hideOnIndex();
        yield UrlField::new('showUrl', '商品展示地址')->hideOnIndex();
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
            ->add(EntityFilter::new('tradeOrder', '交易订单'))
            ->add(TextFilter::new('goodsId', '商品编号'))
            ->add(TextFilter::new('goodsName', '商品名称'))
            ->add(TextFilter::new('goodsCategory', '商品类目'))
            ->add(NumericFilter::new('quantity', '商品数量'))
            ->add(NumericFilter::new('price', '商品单价'))
        ;
    }
}
