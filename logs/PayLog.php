<?php
namespace pay\logs;
/**
 * Created by PhpStorm.
 * User: jokerl
 * Date: 2019/1/23
 * Time: 15:52
 */

class PayLog
{
        CONST PAY = 'PAY';
        CONST PAY_BACK = 'PAY_BACK';
        CONST PAY_DEVELOP = 'PAY_DEVELOP';

        CONST REFUND = 'REFUND';

        CONST CASH = 'CASH';

        /**
         * @param $info
         * @param $type
         */
        public static function save($info, $type){
            //\Yii::error($info,$type);
        }
}