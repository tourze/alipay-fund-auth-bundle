<?php

namespace AlipayFundAuthBundle\Service;

use AlipayFundAuthBundle\Entity\TradeOrder;
use AlipayFundAuthBundle\Enum\AliPayType;
use AlipayFundAuthBundle\Repository\AccountRepository;
use AlipayFundAuthBundle\Repository\TradeOrderRepository;
use Carbon\Carbon;
use Doctrine\ORM\EntityManagerInterface;
use Omnipay\Alipay\AopAppGateway;
use Omnipay\Alipay\Responses\AopTradeAppPayResponse;
use Omnipay\Omnipay;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class AliPayCreateOrderService
{
    public function __construct(
        private readonly TradeOrderRepository $tradeOrderRepository,
        private readonly AccountRepository $accountRepository,
        private readonly UrlGeneratorInterface $urlGenerator,
        private readonly LoggerInterface $logger,
        private readonly EntityManagerInterface $entityManager,
    ) {
    }

    public function createAppOrder(array $data)
    {
        $outTradeNo = $data['outTradeNo'];
        $totalAmount = $data['totalAmount'];
        $subject = $data['subject'];

        $tradeOrder = $this->tradeOrderRepository->findOneBy(['outTradeNo' => $outTradeNo]);
        if (!$tradeOrder) {
            $account = $this->accountRepository->findOneBy(['valid' => true]);
            if (!$account) {
                throw new NotFoundHttpException('找不到任何可用的Account');
            }
            // 自动创建订单
            $tradeOrder = new TradeOrder();
            $tradeOrder->setTradeStatus('NO_PAY');
            $tradeOrder->setPayType(AliPayType::ALIPAY_AOPAPP);
            $tradeOrder->setAccount($account);
            $tradeOrder->setOutTradeNo($outTradeNo);
            $tradeOrder->setTotalAmount($totalAmount);
            $tradeOrder->setSubject($subject);
            $tradeOrder->setGmtPayment(Carbon::now());
            $this->entityManager->persist($tradeOrder);
            $this->entityManager->flush();
        }

        $gateway = $this->getAopAppGateway($tradeOrder);
        $notifyUrl = $this->urlGenerator->generate('alipay-app-pay-notify', [], UrlGeneratorInterface::ABSOLUTE_URL);
        if ('dev' == $_ENV['APP_ENV']) {
            $notifyUrl = str_replace('https', 'http', $notifyUrl);
        }
        //        $notifyUrl .= "?tradeOrderId={$tradeOrder->getId()}";
        $gateway->setNotifyUrl($notifyUrl);

        /**
         * @var AopTradeAppPayResponse $response
         */
        $response = $gateway->purchase()->setBizContent([
            'subject' => $tradeOrder->getSubject(),
            'out_trade_no' => $tradeOrder->getOutTradeNo(),
            'total_amount' => $tradeOrder->getTotalAmount(),
            'product_code' => 'QUICK_MSECURITY_PAY',
        ])->send();
        $orderString = $response->getOrderString();

        return [
            'orderString' => $orderString,
        ];
    }

    /**
     * 查询支付宝订单状态
     */
    public function getTradeOrderStatus(string $outTradeNo): string
    {
        $tradeOrder = $this->tradeOrderRepository->findOneBy(['outTradeNo' => $outTradeNo]);
        if (!$tradeOrder) {
            return '';
        }
        $gateway = $this->getAopAppGateway($tradeOrder);
        // 查询订单状态
        $tradeStatus = '';
        try {
            $request = $gateway->query();
            // 设置查询参数
            $request->setBizContent([
                'out_trade_no' => $outTradeNo, // 商户订单号
            ]);
            $response = $request->send();
            if ($response->isSuccessful()) {
                // 查询成功
                $data = $response->getData();
                $this->logger->debug('查询支付宝订单状态', $data);
                // 获取交易状态
                $tradeStatus = $data['alipay_fund_auth_trade_query_response']['trade_status'] ?? '';
                // 根据交易状态进行处理
                switch ($tradeStatus) {
                    case 'WAIT_BUYER_PAY':
                        // '交易创建，等待买家付款';
                        break;
                    case 'TRADE_CLOSED':
                        // '未付款交易超时关闭，或支付完成后全额退款';
                        break;
                    case 'TRADE_SUCCESS':
                        // '交易支付成功';
                        break;
                    case 'TRADE_FINISHED':
                        // '交易结束，不可退款';
                        break;
                    default:
                        // '未知状态';
                        break;
                }
            } else {
                // 查询失败
                echo '查询失败: ' . $response->getMessage();
            }
        } catch (\Exception $e) {
            $this->logger->debug('查询支付宝订单状态错误', [
                'outTradeNo' => $outTradeNo,
                'error' => $e,
            ]);
        }

        return $tradeStatus;
    }

    private function getAopAppGateway(TradeOrder $tradeOrder)
    {
        /**
         * @var AopAppGateway $gateway
         */
        $gateway = Omnipay::create($tradeOrder->getPayType()->value);
        $gateway->setSignType('RSA2'); // RSA/RSA2/MD5. Use certificate mode must set RSA2
        $gateway->setAppId($tradeOrder->getAccount()->getAppId());
        $gateway->setPrivateKey($tradeOrder->getAccount()->getRsaPrivateKey());
        $gateway->setAlipayPublicKey($tradeOrder->getAccount()->getRsaPublicKey()); // Need not set this when used certificate mode

        return $gateway;
    }
}
