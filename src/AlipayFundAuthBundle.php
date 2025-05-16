<?php

namespace AlipayFundAuthBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use Tourze\BundleDependency\BundleDependencyInterface;
use Tourze\EasyAdmin\Attribute\Permission\AsPermission;

#[AsPermission(title: '支付宝预授权支付')]
class AlipayFundAuthBundle extends Bundle implements BundleDependencyInterface
{
    public static function getBundleDependencies(): array
    {
        return [
            \Tourze\DoctrineTrackBundle\DoctrineTrackBundle::class => ['all' => true],
            \Tourze\DoctrineIndexedBundle\DoctrineIndexedBundle::class => ['all' => true],
            \Tourze\Symfony\CronJob\CronJobBundle::class => ['all' => true],
        ];
    }
}
