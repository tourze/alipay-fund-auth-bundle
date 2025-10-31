# 支付宝模块

[![PHP Version](https://img.shields.io/badge/php-%5E8.1-777BB4.svg?style=flat-square&logo=php)](https://www.php.net)
[![License](https://img.shields.io/badge/license-MIT-blue.svg?style=flat-square)](LICENSE)
[![Build Status](https://img.shields.io/github/actions/workflow/status/tourze/php-monorepo/ci.yml?branch=master&style=flat-square)](https://github.com/tourze/php-monorepo/actions)
[![Code Coverage](https://img.shields.io/codecov/c/github/tourze/php-monorepo?style=flat-square)](https://codecov.io/gh/tourze/php-monorepo)

[English](README.md) | [中文](README.zh-CN.md)

支付宝支付集成模块，提供了支付宝支付相关的功能，包括应用支付、手机网站支付、资金预授权等功能。

## 功能特性

- 支持多种支付方式
  - APP支付 (ALIPAY_AOPAPP)
  - H5支付 (ALIPAY_AOPWAP)
- 资金预授权
  - 支持资金冻结和解冻
  - 支持预授权转支付
- 账单管理
  - 支持多种账单类型下载
  - 自动定时下载账单

## Installation

```bash
composer require tourze/alipay-fund-auth-bundle
```

## 配置

需要配置支付宝账号信息，包括 AppId、RSA私钥、RSA公钥等。

## 使用示例

### APP支付

```php
$data = [
    'outTradeNo' => 'ORDER_123456',  // 商户订单号
    'totalAmount' => '100.00',        // 订单金额
    'subject' => '测试商品',          // 订单标题
];

$result = $aliPayCreateOrderService->createAppOrder($data);
// 返回 orderString，可直接给 APP 客户端使用
```

### H5支付

通过 Controller 提供的路由发起支付：

```php
// 加密支付参数
$payload = json_encode([
    'outTradeNo' => 'ORDER_123456',
    'totalAmount' => '100.00',
    'subject' => '测试商品',
    'callbackUrl' => '支付完成后跳转的页面URL'
]);

return $this->redirectToRoute('alipay-h5-pay-entry', [
    'payload' => $encryptor->encrypt($payload),
]);
```

### 资金预授权

```php
$fundAuthOrder = new FundAuthOrder();
$fundAuthOrder->setOutOrderNo('AUTH_123456');
$fundAuthOrder->setOutRequestNo('REQ_123456');
$fundAuthOrder->setOrderTitle('预授权测试');
$fundAuthOrder->setAmount('100.00');
$fundAuthOrder->setProductCode('PRE_AUTH');

// 保存时会自动调用支付宝接口进行预授权
$entityManager->persist($fundAuthOrder);
$entityManager->flush();
```

### 账单下载

系统会在每天 9:00 和 10:00 自动下载前一天的账单。也可以手动触发下载：

```bash
php bin/console alipay-trade:download-bill
```

## 事件

模块提供了以下事件供订阅：

- `AppPaySuccessNotifyEvent`: APP支付成功通知事件
- `WapPaySuccessNotifyEvent`: H5支付成功异步通知事件
- `WapPaySuccessReturnEvent`: H5支付成功同步跳转事件

## License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.
