<?php
/**
 * 微信支付业务逻辑类
 * User: 季俊潇
 * Date: 2018/11/20
 * Time: 11:35
 */

namespace pay\services_pay;

use pay\logs\PayLog;
use EasyWeChat\Foundation\Application;
use EasyWeChat\Payment\Order;


class WechatPayService extends PayInterface
{
    // 支付类型
    const TRADE_TYPE = 'JSAPI';
    // 支付成功返回字符
    const SUCCESS_RETURN_CODE = 'SUCCESS';
    // 测试环境支付金额
    const TEST_ENV_PRICE = 1;

    //是否签名验证通过
    public static $is_verify = false;

    /**
     * EasyWeChat 的 Application 类的实例
     * @var null|\EasyWeChat\Foundation\Application
     */
    private $application = null;

    /**
     * 授权 (回调或拉取支付都要调用)
     * @return void
     */
    public function auth()
    {
        // 如果是 APP 支付，采用开放平台配置
        if (is_null($this->payType) || $this->payType === 'APP') {
            $appId = $this->config['app_id'];
            $secret = $this->config['secret'];
            $merchantId = $this->config['merchant_id'];
            $merchantKey = $this->config['key'];
            $certPath = $this->config['cert_path'];
            $keyPath = $this->config['key_path'];
            $this->setPayNotifyUrl($this->config['notify_url']);
        }
        // 如果是非 APP 支付, 采用微信公众号配置
        if ($this->payType === 'JSAPI') {
            $appId = $this->config['js_app_id'];
            $secret = $this->config['js_secret'];
            $merchantId = $this->config['js_merchant_id'];
            $merchantKey = $this->config['key'];
            $certPath = $this->config['js_cert_path'];
            $keyPath = $this->config['js_key_path'];
            $this->setPayNotifyUrl($this->config['js_notify_url']);
        }
        // 如果是小程序, 采用小程序公众号配置
        if ($this->payType === 'MINI') {
            $appId = $this->config['mini_app_id'];
            $secret = $this->config['mini_secret'];
            $merchantId = $this->config['mini_merchant_id'];
            $merchantKey = $this->config['mini_merchant_key'];
            $certPath = $this->config['mini_cert_path'];
            $keyPath = $this->config['mini_key_path'];
            $this->setPayNotifyUrl($this->config['mini_notify_url']);
        }

        $config = [
            'app_id' => $appId,
            'secret' => $secret,        // 微信公众号开发者密钥
            'payment' => [
                'merchant_id' => $merchantId,      // 商户号
                'key' => $merchantKey,      // 微信支付开发者密钥
                'cert_path' => $certPath,      // 证书绝对路径
                'key_path' => $keyPath,        // 证书 Key 绝对路径
                'notify_url' => $this->payNotifyUrl,    // 支付回调地址, 可能会在支付的方法被覆盖
            ]
        ];
        $this->application = new Application($config);
    }


    public function pay()
    {
        $order = self::createOrder();
        $result = $this->application->payment->prepare($order);
        if ($result->return_code == self::SUCCESS_RETURN_CODE && $result->result_code == self::SUCCESS_RETURN_CODE) {
            if ($this->payType === 'APP') {
                // APP 格式返回的配置信息
                $result = $this->application->payment->configForAppPayment($result->prepay_id);
            } else {
                // 非 APP 格式返回的配置信息
                $result = $this->application->payment->configForPayment($result->prepay_id);
            }
            return $result;
        } else {
            var_dump($result);
            die();
        }
    }

    /**
     * 创建支付订单
     * @return Order
     */
    protected function createOrder()
    {
        $price = $this->bizParams['price'] * 100;
        // 测试环境下支付金额为1分钱
        if ($this->config['test_env'] === true) {
            $price = self::TEST_ENV_PRICE;
        }
        // 设置订单参数
        $orderParams = [
            'body' => $this->bizParams['body'],
            'detail' => $this->bizParams['subject'],
            'out_trade_no' => $this->bizParams['out_trade_no'],
            'total_fee' => $price,
            'notify_url' => $this->payNotifyUrl,
        ];
        PayLog::save($orderParams, PayLog::PAY);
        // 设置支付类型
        $orderParams['trade_type'] = 'APP';
        if ($this->payType !== 'APP') {
            $orderParams['trade_type'] = $this->payType;
            $orderParams['openid'] = $this->bizParams['openId'];
        }
        // 小程序特殊处理
        if ($this->payType === 'MINI') {
            $orderParams['trade_type'] = 'JSAPI';
        }

        return new Order($orderParams);
    }

    /**
     * 支付验签
     * @param mixed $requestParams
     * @return mixed
     * @throws \EasyWeChat\Core\Exceptions\FaultException
     */
    public function verifySignature($requestParams)
    {
        self::$is_verify = false;
        $response = $this->application->payment->handleNotify(function ($notify, $successful) use ($requestParams) {
            self::$is_verify = true;
        });
        return self::$is_verify;
    }

    /**
     * 统一退款
     * @return mixed
     */
    public function refund()
    {
        $price = $this->bizParams['price'] * 100;
        $sub_total = $this->bizParams['sub_total'] * 100;
        // 测试环境下支付金额为1分钱
        if ($this->config['test_env'] === true) {
            $price = self::TEST_ENV_PRICE;
            $sub_total = self::TEST_ENV_PRICE;
        }
        PayLog::save([$this->bizParams['out_trade_no'], $this->bizParams['refund_no']], PayLog::PAY);

        $result = $this->application->payment->refund($this->bizParams['out_trade_no'], $this->bizParams['refund_no'], 1,
            1);

        return $result;
    }

}
