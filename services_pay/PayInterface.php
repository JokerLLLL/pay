<?php
/**
 * Created by PhpStorm.
 * User: jokerl
 * Date: 2018/12/7
 * Time: 10:08
 */

namespace pay\services_pay;


abstract class PayInterface
{
    public $config; //配置参数
    public $bizParams; //业务参数
    public $payNotifyUrl; //回调地址
    public $refundNotifyUrl; //退款回调
    public $payType; //支付类型方式 (相同支付商的不同支付方式)

    /** 设置配置参数
     * @param mixed $config
     * @return PayInterface
     */
    public function setConfig($config)
    {
        $this->config = $config;
        return $this;
    }

    /** 设置业务参数
     * @param mixed $bizParams
     * @return PayInterface
     */
    public function setBizParams($bizParams)
    {
        $this->bizParams = $bizParams;
        return $this;
    }

    /**
     * @param mixed $payNotifyUrl
     * @return PayInterface
     */
    public function setPayNotifyUrl($payNotifyUrl)
    {
        $this->payNotifyUrl = $payNotifyUrl;
        return $this;
    }

    /** 设置 子payType类 如：H5 MINI
     * @param mixed $payType
     * @return PayInterface
     */
    public function setPayType($payType)
    {
        $this->payType = $payType;
        return $this;
    }

    /** 授权
     * @return mixed
     */
    abstract public function auth();

    /** 退款
     * @return mixed
     */
    abstract public function refund();

    /** 生成签名
     * @return mixed
     */
    abstract public function pay();

    /**回调验证是否成功
     * @param $requestParams
     * @return mixed
     */
    abstract public function verifySignature($requestParams);

}