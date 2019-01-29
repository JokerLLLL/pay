<?php
/**
 * Created by PhpStorm.
 * User: jokerl
 * Date: 2019/1/26
 * Time: 18:50
 */

namespace pay;


use pay\enum\PayChannelEnums;
use pay\logs\PayLog;



class TestService
{
    /**
     * 微信app支付
     */
    public static function actionWechatApp()
    {
        $bizParams = [
            'out_trade_no' =>'12000001111',
            'price'=>'0.02',
            'body' => '测试用支付',
            'subject' => '测试用商品'
        ];
//        var_dump($bizParams);die;
        $r =PayApiService::setChannel(PayChannelEnums::WechatPay,$bizParams)->payment('APP');
        echo json_encode(['errcode'=>0,'errmsg'=>'success','data'=>$r]);die;
    }

    /**
     *  阿里app支付
     */
    public static function actionAliApp()
    {
        $bizParams = [
            'out_trade_no' =>'12000001111',
            'price'=>'0.02',
            'body' => '测试用支付',
            'subject' => '测试用商品'
        ];
        $r = PayApiService::setChannel(PayChannelEnums::AliPay,$bizParams)->payment('APP');
        echo json_encode(['errcode'=>0,'errmsg'=>'success','data'=>$r]);die;
    }

    /**
     * 阿里回调 成功;
     */
    public static function actionAliNotify()
    {
        //请求参数
        $requestParams = [

        ];
        try {
            PayApiService::setChannel(PayChannelEnums::AliPay)
                ->notify($requestParams,'APP');
        } catch (\Exception $e) {
            PayLog::save($e->getFile() . '/' . $e->getLine() . '/' . $e->getMessage(), PayLog::PAY_BACK);
        }
        die('success');
    }

    /**
     * APP微信回调
     */
    public static function actionWechatNotify()
    {

        //请求参数 xml 数据
        $requestParams = '<xml><appid><![CDATA[wx7febb221a8514ac3]]></appid><bank_type><![CDATA[CFT]]></bank_type><cash_fee><![CDATA[1]]></cash_fee><fee_type><![CDATA[CNY]]></fee_type><is_subscribe><![CDATA[N]]></is_subscribe><mch_id><![CDATA[1524687821]]></mch_id><nonce_str><![CDATA[5c4c4c1cf3372]]></nonce_str><openid><![CDATA[oLMAL6IsrVS7A-AAZFaPU3cJx2GY]]></openid><out_trade_no><![CDATA[19981010A1]]></out_trade_no><result_code><![CDATA[SUCCESS]]></result_code><return_code><![CDATA[SUCCESS]]></return_code><sign><![CDATA[C9BA3525F6DF7C8F2BD844E145CE8FB2]]></sign><time_end><![CDATA[20190126200150]]></time_end><total_fee>1</total_fee><trade_type><![CDATA[APP]]></trade_type><transaction_id><![CDATA[4200000258201901267287496519]]></transaction_id></xml>';

        try {
            PayApiService::setChannel(PayChannelEnums::WechatPay)
                ->notify($requestParams,'APP');
        } catch (\Exception $e) {
            PayLog::save($e->getFile() . '/' . $e->getLine() . '/' . $e->getMessage(), PayLog::PAY_BACK);
           throw $e;
        }
        die('success');
    }


    /**
     * JSAPI微信回调
     */
    public static function actionJSNotify()
    {

        //请求参数 xml 数据
        $requestParams = '<xml><appid><![CDATA[wx7febb221a8514ac3]]></appid><bank_type><![CDATA[CFT]]></bank_type><cash_fee><![CDATA[1]]></cash_fee><fee_type><![CDATA[CNY]]></fee_type><is_subscribe><![CDATA[N]]></is_subscribe><mch_id><![CDATA[1524687821]]></mch_id><nonce_str><![CDATA[5c4c4c1cf3372]]></nonce_str><openid><![CDATA[oLMAL6IsrVS7A-AAZFaPU3cJx2GY]]></openid><out_trade_no><![CDATA[19981010A1]]></out_trade_no><result_code><![CDATA[SUCCESS]]></result_code><return_code><![CDATA[SUCCESS]]></return_code><sign><![CDATA[C9BA3525F6DF7C8F2BD844E145CE8FB2]]></sign><time_end><![CDATA[20190126200150]]></time_end><total_fee>1</total_fee><trade_type><![CDATA[APP]]></trade_type><transaction_id><![CDATA[4200000258201901267287496519]]></transaction_id></xml>';

        try {
            PayApiService::setChannel(PayChannelEnums::WechatPay)
                ->notify($requestParams,'JSAPI');
        } catch (\Exception $e) {
            PayLog::save($e->getFile() . '/' . $e->getLine() . '/' . $e->getMessage(), PayLog::PAY_BACK);
            throw $e;
        }
        die('success');
    }

}