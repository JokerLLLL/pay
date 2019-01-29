<?php
/**  支付业务统一层
 * Created by PhpStorm.
 * User: jokerl
 * Date: 2018/12/7
 * Time: 15:18
 */

namespace pay;



use pay\configs\PayConfigInfo;
use pay\enum\PayChannelEnums;
use pay\exceptions\PayException;
use pay\services_pay\PayInterface;

class PayApiService
{
     public static $namespace = 'pay\\services_pay\\';
     public static $channel;   //支付渠道
     public static $bizParams; //业务数据

     /** @var  PayInterface $instance  */
     public static $instance;

     // 各种回调
     public static $payBackCallHandlers = [
         'sku'=>[]
     ];


    /** 设置支付渠道 和 业务参数
     * @param $channel
     * @param $bizParams
     * @return PayApiService
     */
     public static function setChannel($channel, $bizParams = null)
     {
          self::$channel = $channel;
          self::$bizParams = $bizParams;
          self::checkChannel();
          self::getInstance();
          return new self;
     }


    /** 拉起支付
     * @param $payType
     * @return mixed
     */
     public static function payment($payType)
     {
         // 顺序不能调换
         self::$instance->setPayType($payType);
         self::$instance->setConfig(self::getConfigs());
         self::$instance->setBizParams(self::$bizParams);
         //实现的方法
         self::$instance->auth();
         return self::$instance->pay();
     }

    /** 支付回调 业务
     * @param $requestParams
     * @param $payType
     * @return mixed
     */
     public static function notify($requestParams,$payType = 'APP')
     {
         //传递配置和业务参数
         self::$instance->setPayType($payType);
         self::$instance->setConfig(self::getConfigs());
         self::$instance->setBizParams(self::$bizParams);
         //实现的方法
         self::$instance->auth();
         if(self::$instance->verifySignature($requestParams)) {
                //业务业务逻辑
                //throw new \Exception('请实现业务逻辑');
                //todo....
                die('认证通过');
              return true;
          }
          die('失败');
          return false;
     }
    /**
     * @return mixed
     */
    private static function getInstance()
    {
        if(is_null(self::$instance)) {
            $clazz = self::$namespace.self::$channel.'Service';
            self::$instance = new $clazz();
        }
    }


    /** 检查支付渠道
     * @throws PayException
     */
    private static function checkChannel()
    {
        if(!in_array(self::$channel,PayChannelEnums::params())) {
            throw new PayException('支付渠道不存在');
        }
    }


    /** 通过支付渠道类型获取对应配置参数
     * @param null $name
     * @return mixed
     * @throws PayException
     */
    public static function getConfigs($name = null)
    {
        // 获取配置
        $params = PayConfigInfo::init();

        if (!isset($params[self::$channel])) {
            throw new PayException('支付配置信息不存在');
        }
        if($name !== null) {
            return $params[self::$channel][$name];
        }
        return $params[self::$channel];
    }

}