<?php

namespace AlipayFundAuthBundle\Enum;

use Tourze\EnumExtra\Itemable;
use Tourze\EnumExtra\ItemTrait;
use Tourze\EnumExtra\Labelable;
use Tourze\EnumExtra\Selectable;
use Tourze\EnumExtra\SelectTrait;

enum AliPayType: string implements Labelable, Itemable, Selectable
{
    use ItemTrait;
    use SelectTrait;

    case ALIPAY_AOPWAP = 'Alipay_AopWap';
    case ALIPAY_AOPAPP = 'Alipay_AopApp';

    public function getLabel(): string
    {
        return match ($this) {
            self::ALIPAY_AOPWAP => 'H5支付',
            self::ALIPAY_AOPAPP => 'APP支付',
        };
    }
}
