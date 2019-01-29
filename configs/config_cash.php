<?php
/** 提现到银行卡的配置
 * Created by PhpStorm.
 * User: jokerl
 * Date: 2019/1/29
 * Time: 17:04
 */
return [
    //阿里提现配置
    'AliCash' =>[
    ],

    //微信提现配置
    'WechatCash' => [
        'app_id'            => '',
        'secret'            => '',
        'merchant_id'       => '',
        'key'               => '',
        'cert_path'         => dirname(__DIR__).'/cert/1524687821_20190125_cert/apiclient_cert.pem',
        'key_path'          => dirname(__DIR__).'/cert/1524687821_20190125_cert/apiclient_key.pem',
        //通过接口获取
        'public_key'        => dirname(__DIR__).'/cert/1524687821_20190125_cert/public.pem',
    ]
];