<?php

namespace AlipayFundAuthBundle\Enum;

use Tourze\EnumExtra\Itemable;
use Tourze\EnumExtra\ItemTrait;
use Tourze\EnumExtra\Labelable;
use Tourze\EnumExtra\Selectable;
use Tourze\EnumExtra\SelectTrait;

enum AsyncPaymentMode: string implements Labelable, Itemable, Selectable
{
    use ItemTrait;
    use SelectTrait;

    case ASYNC_DELAY_PAY = 'ASYNC_DELAY_PAY';
    case ASYNC_REALTIME_PAY = 'ASYNC_REALTIME_PAY';
    case SYNC_DIRECT_PAY = 'SYNC_DIRECT_PAY';
    case NORMAL_ASYNC_PAY = 'NORMAL_ASYNC_PAY';
    case QUOTA_OCCUPYIED_ASYNC_PAY = 'QUOTA_OCCUPYIED_ASYNC_PAY';

    public function getLabel(): string
    {
        return match ($this) {
            self::ASYNC_DELAY_PAY => '异步延时付款',
            self::ASYNC_REALTIME_PAY => '异步准实时付款',
            self::SYNC_DIRECT_PAY => '同步直接扣款',
            self::NORMAL_ASYNC_PAY => '纯异步付款',
            self::QUOTA_OCCUPYIED_ASYNC_PAY => '异步支付并且预占了先享后付额度',
        };
    }
}
