<?php

namespace AlipayFundAuthBundle\Enum;

use Tourze\EnumExtra\Itemable;
use Tourze\EnumExtra\ItemTrait;
use Tourze\EnumExtra\Labelable;
use Tourze\EnumExtra\Selectable;
use Tourze\EnumExtra\SelectTrait;

enum VoucherType: string implements Labelable, Itemable, Selectable
{
    use ItemTrait;
    use SelectTrait;

    case ALIPAY_FIX_VOUCHER = 'ALIPAY_FIX_VOUCHER';
    case ALIPAY_DISCOUNT_VOUCHER = 'ALIPAY_DISCOUNT_VOUCHER';
    case ALIPAY_ITEM_VOUCHER = 'ALIPAY_ITEM_VOUCHER';
    case ALIPAY_CASH_VOUCHER = 'ALIPAY_CASH_VOUCHER';
    case ALIPAY_BIZ_VOUCHER = 'ALIPAY_BIZ_VOUCHER';

    public function getLabel(): string
    {
        return match ($this) {
            self::ALIPAY_FIX_VOUCHER => '全场代金券',
            self::ALIPAY_DISCOUNT_VOUCHER => '折扣券',
            self::ALIPAY_ITEM_VOUCHER => '单品优惠券',
            self::ALIPAY_CASH_VOUCHER => '现金抵价券',
            self::ALIPAY_BIZ_VOUCHER => '商家全场券',
        };
    }
}
