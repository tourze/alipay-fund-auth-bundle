<?php

namespace AlipayFundAuthBundle\Enum;

use Tourze\EnumExtra\Itemable;
use Tourze\EnumExtra\ItemTrait;
use Tourze\EnumExtra\Labelable;
use Tourze\EnumExtra\Selectable;
use Tourze\EnumExtra\SelectTrait;

enum AuthConfirmMode: string implements Labelable, Itemable, Selectable
{
    use ItemTrait;
    use SelectTrait;

    case NOT_COMPLETE = 'NOT_COMPLETE';
    case COMPLETE = 'COMPLETE';

    public function getLabel(): string
    {
        return match ($this) {
            self::NOT_COMPLETE => '转交易完成后不解冻剩余冻结金额',
            self::COMPLETE => '转交易完成后解冻剩余冻结金额',
        };
    }
}
