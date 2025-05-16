<?php

namespace AlipayFundAuthBundle\Enum;

use Tourze\EnumExtra\Itemable;
use Tourze\EnumExtra\ItemTrait;
use Tourze\EnumExtra\Labelable;
use Tourze\EnumExtra\Selectable;
use Tourze\EnumExtra\SelectTrait;

enum AuthTradePayMode: string implements Labelable, Itemable, Selectable
{
    use ItemTrait;
    use SelectTrait;

    case CREDIT_PREAUTH_PAY = 'CREDIT_PREAUTH_PAY';

    public function getLabel(): string
    {
        return match ($this) {
            self::CREDIT_PREAUTH_PAY => '信用预授权支付',
        };
    }
}
