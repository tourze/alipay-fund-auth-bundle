<?php

namespace AlipayFundAuthBundle\Enum;

use Tourze\EnumExtra\Itemable;
use Tourze\EnumExtra\ItemTrait;
use Tourze\EnumExtra\Labelable;
use Tourze\EnumExtra\Selectable;
use Tourze\EnumExtra\SelectTrait;

enum FundAuthOrderStatus: string implements Labelable, Itemable, Selectable
{
    use ItemTrait;
    use SelectTrait;

    case INIT = 'INIT';
    case SUCCESS = 'SUCCESS';
    case CLOSED = 'CLOSED';

    public function getLabel(): string
    {
        return match ($this) {
            self::INIT => '初始',
            self::SUCCESS => '成功',
            self::CLOSED => '关闭',
        };
    }
}
