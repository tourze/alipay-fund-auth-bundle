<?php

namespace AlipayFundAuthBundle;

use Doctrine\Bundle\DoctrineBundle\DoctrineBundle;
use Symfony\Component\HttpKernel\Bundle\Bundle;
use Tourze\BundleDependency\BundleDependencyInterface;
use Tourze\DoctrineIndexedBundle\DoctrineIndexedBundle;
use Tourze\DoctrineTrackBundle\DoctrineTrackBundle;
use Tourze\EasyAdminMenuBundle\EasyAdminMenuBundle;
use Tourze\JsonRPCLockBundle\JsonRPCLockBundle;
use Tourze\Symfony\CronJob\CronJobBundle;

class AlipayFundAuthBundle extends Bundle implements BundleDependencyInterface
{
    public static function getBundleDependencies(): array
    {
        return [
            DoctrineBundle::class => ['all' => true],
            DoctrineTrackBundle::class => ['all' => true],
            DoctrineIndexedBundle::class => ['all' => true],
            CronJobBundle::class => ['all' => true],
            EasyAdminMenuBundle::class => ['all' => true],
            JsonRPCLockBundle::class => ['all' => true],
        ];
    }
}
