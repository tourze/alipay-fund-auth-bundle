# 支付宝资金预授权 Bundle

[![PHP Version](https://img.shields.io/badge/php-%5E8.1-777BB4.svg?style=flat-square&logo=php)](https://www.php.net)
[![License](https://img.shields.io/badge/license-MIT-blue.svg?style=flat-square)](LICENSE)
[![Build Status](https://img.shields.io/github/actions/workflow/status/tourze/php-monorepo/ci.yml?branch=master&style=flat-square)](https://github.com/tourze/php-monorepo/actions)
[![Code Coverage](https://img.shields.io/codecov/c/github/tourze/php-monorepo?style=flat-square)](https://codecov.io/gh/tourze/php-monorepo)

[English](README.md) | [中文](README.zh-CN.md)

支付宝资金预授权 Bundle 是一个基于 Symfony 的包，提供了支付宝资金预授权、转支付、解冻等功能的完整解决方案。

## 功能特性

- ✅ **资金预授权**：支持资金冻结和解冻操作
- ✅ **预授权转支付**：将冻结的资金转为实际支付
- ✅ **交易管理**：完整的交易订单管理功能
- ✅ **账单下载**：支持多种账单类型的自动下载
- ✅ **后台管理**：基于 EasyAdmin 的完整后台管理界面
- ✅ **事件驱动**：支持事件监听和自动化处理
- ✅ **多账户支持**：支持多个支付宝商户账户配置

## 安装

```bash
composer require tourze/alipay-fund-auth-bundle
```

## 配置

### 1. 注册 Bundle

在 `config/bundles.php` 中注册：

```php
return [
    // ...
    AlipayFundAuthBundle\AlipayFundAuthBundle::class => ['all' => true],
];
```

### 2. 配置数据库

运行数据库迁移：

```bash
php bin/console doctrine:migrations:migrate
```

### 3. 配置支付宝账户

在后台管理界面中配置支付宝账户信息：
- AppId：支付宝应用ID
- RSA私钥：应用私钥
- RSA公钥：支付宝公钥

## 使用示例

### 创建预授权订单

```php
use AlipayFundAuthBundle\Entity\FundAuthOrder;
use AlipayFundAuthBundle\Entity\Account;

// 创建预授权订单
$fundAuthOrder = new FundAuthOrder();
$fundAuthOrder->setAccount($account);
$fundAuthOrder->setOutOrderNo('AUTH_' . time());
$fundAuthOrder->setOutRequestNo('REQ_' . time());
$fundAuthOrder->setOrderTitle('测试预授权');
$fundAuthOrder->setAmount('100.00');
$fundAuthOrder->setProductCode('PREAUTH_PAY');

// 保存订单（会自动调用支付宝接口）
$entityManager->persist($fundAuthOrder);
$entityManager->flush();
```

### 预授权转支付

```php
use AlipayFundAuthBundle\Entity\FundAuthPostPayment;

// 创建转支付订单
$postPayment = new FundAuthPostPayment();
$postPayment->setFundAuthOrder($fundAuthOrder);
$postPayment->setOutTradeNo('PAY_' . time());
$postPayment->setOutOrderNo($fundAuthOrder->getOutOrderNo());
$postPayment->setAmount('50.00'); // 转支付金额，不能超过预授权金额

$entityManager->persist($postPayment);
$entityManager->flush();
```

### 解冻资金

```php
use AlipayFundAuthBundle\Entity\FundAuthUnfreezeLog;

// 创建解冻记录
$unfreezeLog = new FundAuthUnfreezeLog();
$unfreezeLog->setFundAuthOrder($fundAuthOrder);
$unfreezeLog->setOutRequestNo('UNFREEZE_' . time());
$unfreezeLog->setAmount('50.00'); // 解冻金额
$unfreezeLog->setRemark('用户取消订单');

$entityManager->persist($unfreezeLog);
$entityManager->flush();
```

### 使用 JSON-RPC 接口

```php
use AlipayFundAuthBundle\Procedure\CreateAlipayPreauthTradeOrder;

// 通过 JSON-RPC 创建预授权订单
$procedure = new CreateAlipayPreauthTradeOrder();
$result = $procedure->call([
    'accountId' => $accountId,
    'outOrderNo' => 'AUTH_' . time(),
    'outRequestNo' => 'REQ_' . time(),
    'orderTitle' => '测试预授权',
    'amount' => '100.00',
    'productCode' => 'PREAUTH_PAY'
]);
```

## 事件系统

Bundle 提供了事件监听机制，可以监听以下事件：

- **预授权订单创建**：自动调用支付宝预授权接口
- **转支付订单创建**：自动执行预授权转支付
- **解冻记录创建**：自动执行资金解冻

## 后台管理

Bundle 集成了 EasyAdmin 后台管理界面，提供以下功能：

- **账户管理**：管理支付宝商户账户
- **预授权订单**：查看和管理预授权订单
- **转支付记录**：查看预授权转支付记录
- **解冻记录**：查看资金解冻记录
- **交易订单**：管理交易订单信息
- **商品详情**：管理交易商品信息

## 实体说明

### 核心实体

- **Account**：支付宝账户配置
- **FundAuthOrder**：预授权订单
- **FundAuthPostPayment**：预授权转支付
- **FundAuthUnfreezeLog**：资金解冻记录
- **TradeOrder**：交易订单
- **TradeGoodsDetail**：交易商品详情

### 枚举类型

- **FundAuthOrderStatus**：预授权订单状态
- **AuthConfirmMode**：授权确认模式
- **AuthTradePayMode**：授权交易付款模式
- **AsyncPaymentMode**：异步支付模式
- **AliPayType**：支付宝支付类型
- **VoucherType**：券类型

## 开发和测试

### 运行测试

```bash
./vendor/bin/phpunit packages/alipay-fund-auth-bundle/tests
```

### 代码质量检查

```bash
php -d memory_limit=2G ./vendor/bin/phpstan analyse packages/alipay-fund-auth-bundle
```

## 许可证

本项目采用 MIT 许可证。详细信息请参见 [LICENSE](LICENSE) 文件。

## 贡献指南

欢迎提交问题和功能请求。请确保在提交代码前运行测试和代码质量检查。

## 相关文档

- [支付宝开放平台文档](https://opendocs.alipay.com/)
- [支付宝预授权产品介绍](https://opendocs.alipay.com/open/20160728150111277227/intro)
- [Symfony Bundle 开发指南](https://symfony.com/doc/current/bundles.html)
