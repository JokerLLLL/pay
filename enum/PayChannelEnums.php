<?php

namespace pay\enum;
/**
 * Created by PhpStorm.
 * User: jokerl
 * Date: 2019/1/23
 * Time: 16:22
 */

class PayChannelEnums
{
    CONST AliPay = 'AliPay';
    CONST WechatPay = 'WechatPay';

    /** 获取参数
     * @return array
     */
    public static function params()
    {
        $reflection = new \ReflectionClass(self::class);
        return array_values($reflection->getConstants());
    }
}