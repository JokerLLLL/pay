<?php
/**
 * Created by PhpStorm.
 * User: jokerl
 * Date: 2019/1/23
 * Time: 17:04
 */
return [
    //阿里支付配置
    'AliPay' =>[
        //是否测试金额
        'test_env' => false,

        //appid
        'app_id' => '',

        // 应用公钥  上传到支付包的公钥 (可填写用来备份)
        'public_key' => 'xxx',

        // 应用私钥  本地用来加密
        'rsa_private_key' => 'xxx',

        //阿里的公钥(上传公钥后 下载的) 用于解密
        'ali_pay_rsa_public_key' => 'xxcx',       // 阿里的公钥 正式环境

        //回调地址
        'notify_url' => 'http://xxx/pay/pay-api/ali-back-call',
    ],

    //微信支付配置
    'WechatPay' => [
        //是否测试金额
        'test_env'          => true,

        // 开放平台
        'app_id'            => '',
        'secret'            => '',
        'merchant_id'       => '',
        'key'               => '',
        'cert_path'         => dirname(__DIR__).'/cert/1524687821_20190125_cert/apiclient_cert.pem',
        'key_path'          => dirname(__DIR__).'/cert/1524687821_20190125_cert/apiclient_key.pem',
        'notify_url'        => 'http://xxx/pay/pay-api/we-chat-back-call',

        // 小程序
        'mini_app_id'       => '',
        'mini_secret'       => '',
        'mini_merchant_id'  => '',
        'mini_merchant_key' => '',
        'mini_cert_path'    => '',
        'mini_key_path'     => '',
        'mini_notify_url'   => '',

        // 公众号
        'js_app_id'         => '',
        'js_secret'         => '',
        'js_merchant_id'    => '',
        'js_merchant_key'   => '',
        'js_cert_path'      => '',
        'js_key_path'       => '',
        'js_notify_url'     => '',


    ]

];