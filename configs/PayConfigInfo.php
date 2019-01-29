<?php

namespace pay\configs;
/**
 * Created by PhpStorm.
 * User: jokerl
 * Date: 2019/1/23
 * Time: 17:03
 */

class PayConfigInfo
{
        /** 支付配置初始化
         * @return mixed
         */
        public static function init()
        {
            return require __DIR__.'/config_pay.php';
        }

        /**
         *  提现配置配置初始化
         */
        public static function cash_init()
        {
            return require __DIR__.'/config_cash.php';
        }
}