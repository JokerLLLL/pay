<?php
/**
 * Created by PhpStorm.
 * User: jokerl
 * Date: 2018/12/7
 * Time: 11:29
 */

namespace pay\services_pay;

//支付包demon 引入
define("AOP_SDK_WORK_DIR", __DIR__.'/logs/temp/');
include dirname(__DIR__).'/alipay_demo/AopSdk.php';

class AliPayService extends PayInterface
{
    const GATEWAY_URL = 'https://openapi.alipay.com/gateway.do';
    const API_VERSION = '1.0';
    const SIGN_TYPE = 'RSA2';
    const POST_CHARSET = 'UTF-8';
    const FORMAT = 'json';
    const TEST_ENV_PRICE = 0.01;

    /**
     * @var null|\AopClient
     */
    private $aopClient = null;


    /**
     * 授权
     */
    public function auth()
    {
        $aop = new \AopClient();
        $aop->gatewayUrl = self::GATEWAY_URL;
        $aop->appId = $this->config['app_id'];
        $aop->rsaPrivateKey = $this->config['rsa_private_key'];
        $aop->format =  self::FORMAT;
        $aop->postCharset = self::POST_CHARSET;
        $aop->signType = self::SIGN_TYPE;
        $aop->alipayrsaPublicKey = $this->config['ali_pay_rsa_public_key'];
        $this->aopClient = $aop;

    }

    /** 退款
     * @return bool
     */
    public function refund()
    {
//        $requestObj = new AlipayTradeRefundRequest();
//        $price = $this->bizParams['price'];
//        if ($this->config['test_env'] === true) {
//            $price = self::TEST_ENV_PRICE;
//        }
//        $requestObj->setBizContent(json_encode([
//            'out_trade_no' => $this->bizParams['out_trade_no'],
////            'trade_no' => $this->bizParams['refund_no'],
//            'refund_amount' => $price,
//            'refund_reason' => '正常退款',
//        ]));
//        /** @var mixed $requestObj */
//        $result = $this->aopClient->execute($requestObj);
//
//        return true;
    }

    public function pay()
    {
        $price = $this->bizParams['price'];
        // 测试环境，价钱设置为 0.01
        if ($this->config['test_env'] === true) {
            $price = self::TEST_ENV_PRICE;
        }

        //实例化具体API对应的request类,类名称和接口名称对应,当前调用接口名称：alipay.trade.app.pay
        $request = new \AlipayTradeAppPayRequest();
        //SDK已经封装掉了公共参数，这里只需要传入业务参数
        $request->setNotifyUrl($this->config['notify_url']);
        $request->setBizContent(json_encode([
            'out_trade_no' => $this->bizParams['out_trade_no'],
            'body' => $this->bizParams['body'],
            'subject' => $this->bizParams['subject'],
            'goods_type' => 0,
            'total_amount' => $price,
        ]));
        //这里和普通的接口调用不同，使用的是sdkExecute
        /** @var mixed $request */
        $result = $this->aopClient->sdkExecute($request);
        return $result;
    }

    /**回调验证是否成功
     * @param $requestParams
     * @return mixed
     */
    public function verifySignature($requestParams)
    {
        $result = $this->aopClient->rsaCheckV1($requestParams, null, self::SIGN_TYPE);
        return boolval($result);
    }
}