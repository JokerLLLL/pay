<?php
/**
 * Created by PhpStorm.
 * User: jokerl
 * Date: 2019/1/29
 * Time: 17:05
 */

namespace pay;


use pay\configs\PayConfigInfo;
use pay\services_cash\WechatCashBankService;

class CashApiService
{

        /** 提现接口
         * @param $attribute
         * @return mixed
         */
        public static function payCashBank($attribute)
        {
            /**
                $attribute = [
                    'price'=>'10.00',
                    'partner_trade_no'=>'19910101010',
                    'bank_code'=>'银行编号',
                    'name'=>'王小李',
                    'bank_card'=>'银行卡号',
                    'desc'=>'提现描述',
                 ];
             */

            return WechatCashBankService::payCashBank($attribute,PayConfigInfo::cash_init());
        }
}