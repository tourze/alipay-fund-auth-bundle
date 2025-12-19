<?php

declare(strict_types=1);

namespace AlipayFundAuthBundle\Param;

use Tourze\JsonRPC\Core\Contracts\RpcParamInterface;

/**
 * CreateAlipayPreauthTradeOrder Procedure 的参数对象
 *
 * 用于创建预付款交易订单的请求参数
 */
readonly class CreateAlipayPreauthTradeOrderParam implements RpcParamInterface
{
    public function __construct()
    {
    }
}
