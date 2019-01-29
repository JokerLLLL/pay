<?php

namespace pay\services_cash;
use pay\configs\PayConfigInfo;
use pay\logs\PayLog;

/** 微信提现到银行银行卡
 * Created by PhpStorm.
 * User: jokerl
 * Date: 2018/12/17
 * Time: 16:36
 */


class WechatCashBankService
{
    // 获取公钥文件
    const PUB_RUL = 'https://fraud.mch.weixin.qq.com/risk/getpublickey';
    //提现接口
    const URL  = 'https://api.mch.weixin.qq.com/mmpaysptrans/pay_bank';


    /**
     * 获取提现的公共钥匙
     */
    public static function getPublicKey()
    {
        $params = PayConfigInfo::cash_init();
        $conf = $params['WechatCash'];
        $request_data =[
            'mch_id'=>$conf['merchant_id'],
            'sign_type'=>'MD5',
            'nonce_str' => self::randStr(16),
        ];
        //md5签名
        $request_data['sign'] =                             //商务号 key
            self::signMd5(self::serializeData($request_data),$conf['merchant_key']);

        $request_data = self::arrayToXml($request_data);
        $response = self::sendRequest(
            $request_data,
            self::PUB_RUL,
            $conf['cert_path'],
            $conf['key_path']);
        self::p($response); die;
    }

    private static function p($vars = "")
    {
        if (is_bool($vars)) {
            var_dump($vars);
        } else if (is_null($vars)) {
            var_dump(NULL);
        } else {
            echo "<pre style='position:relative;z-index:1000;padding:10px;border-radius:5px;background:#F5F5F5;border:1px solid #aaa;font-size:14px;line-height:18px;opacity:0.9;'>" . print_r($vars, true) . "</pre>";
        }
    }


    /** 企业付款到银行
     * @param $attributes
     * @param $conf
     * @return mixed
     */
    public static function payCashBank($attributes, $conf)
    {
        $money = $attributes['price'];
        $partner_trade_no = $attributes['partner_trade_no'];
        $bank_code = $attributes['bank_code'];
        $true_name = $attributes['name'];
        $bank_no = $attributes['bank_card'];
        $desc = $attributes['desc'];

        //组装请求数据
        $request_data = [
            'mch_id'=>$conf['merchant_id'],
            'partner_trade_no' => $partner_trade_no,
            'nonce_str'=>self::randStr(16),
            'amount' => $money*100,
            'bank_code' => $bank_code,
            'desc' => $desc
        ];
        //加密公钥 账号 姓名
        $public_key = file_get_contents($conf['public_key']);
        $pu_key = openssl_pkey_get_public($public_key);
        openssl_public_encrypt($bank_no,$enc_bank_no,$pu_key,OPENSSL_PKCS1_OAEP_PADDING);
        openssl_public_encrypt($true_name,$enc_true_name,$pu_key,OPENSSL_PKCS1_OAEP_PADDING);

        $request_data['enc_bank_no'] = base64_encode($enc_bank_no);
        $request_data['enc_true_name'] = base64_encode($enc_true_name);

        //md5签名
        $request_data['sign'] =                             //商务号 key
            self::signMd5(self::serializeData($request_data),$conf['key']);

        $request_data = self::arrayToXml($request_data);
        //ssh请求
        $response = self::sendRequest(
            $request_data,
            self::URL,
            $conf['cert_path'],
            $conf['key_path']
        );
        return self::responseData($response);
    }


    /** 请求分析
     * @param $response
     * @return bool
     */
    public static function responseData($response)
    {
        PayLog::save($response,PayLog::CASH);
        if($response['return_code'] == 'SUCCESS' && $response['result_code'] == 'SUCCESS') {
            return true;
        }
        return false;
    }

    /**
     * 发送请求
     * @param $request_data
     * @param $url
     * @param $cert_path
     * @param $key_path
     * @return bool|mixed
     */
    protected static function sendRequest($request_data,$url,$cert_path,$key_path)
    {
        $ch = curl_init();
        curl_setopt($ch,CURLOPT_URL, $url);
        curl_setopt($ch,CURLOPT_TIMEOUT, 30);

        curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,FALSE);
        curl_setopt($ch,CURLOPT_SSL_VERIFYHOST,FALSE);
        //默认格式为PEM，可以注释
        curl_setopt($ch,CURLOPT_SSLCERTTYPE,'PEM');
        curl_setopt($ch,CURLOPT_SSLCERT,$cert_path);
        //默认格式为PEM，可以注释
        curl_setopt($ch,CURLOPT_SSLKEYTYPE,'PEM');
        curl_setopt($ch,CURLOPT_SSLKEY,$key_path);

        curl_setopt($ch,CURLOPT_HEADER,FALSE);
        curl_setopt($ch,CURLOPT_RETURNTRANSFER,TRUE);
        curl_setopt($ch,CURLOPT_POST, true);
        curl_setopt($ch,CURLOPT_POSTFIELDS, $request_data);
        $ret = curl_exec($ch);
        if($ret){
            curl_close($ch);
            return self::xmlToArray($ret);
        } else {
            \Yii::error('error','cash');
            return false;
        }
    }


    /**
     * 格式化数据
     * @param $request_data
     * @return string
     */
    private  static function serializeData($request_data)
    {
        ksort($request_data);
        $sign_data = '';
        $i = 0;
        foreach ($request_data as $k => $v) {
            if (!empty($v) && "@" != substr($v, 0, 1)) {
                if ($i == 0) {
                    $sign_data .= "$k" . "=" . "$v";
                } else {
                    $sign_data .= "&" . "$k" . "=" . "$v";
                }
                $i++;
            }
        }

        unset ($k, $v);
        return $sign_data;
    }

    protected static function signMd5($sign_data,$key)
    {
        return mb_strtoupper(md5($sign_data.'&key='.$key));
    }


    /** arrayToXml
     * @param $arr
     * @return string
     */
    private static  function arrayToXml($arr)
    {
        $xml = "<xml>";
        foreach ($arr as $key=>$val)
        {
            $xml.="<".$key.">".$val."</".$key.">";
        }
        $xml.="</xml>";
        return $xml;
    }

    /** xmlToArray
     * @param $xml
     * @return mixed
     */
    private static  function xmlToArray($xml) {
        $arr = json_decode(json_encode(simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA)), true);
        return $arr;
    }


    /** 随机16字符串
     * @param int $length
     * @return string
     */
    protected static function randStr($length = 16)
    {
            $string = '';
            $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789abcdefghijklmnopqrstuvwxyz';
            $max = strlen($chars) - 1;
            if (version_compare(PHP_VERSION, '4.2.0') >= 0) {
                for ($i = 0; $i < $length; $i++) {
                    $p = rand(0, $max);
                    $string .= $chars[$p];
                }
            } else {
                mt_srand((double)microtime(true) * 1000000);
                for ($i = 0; $i < $length; $i++) {
                    $p = mt_rand(0, $max);
                    $string .= $chars[$p];
                }
            }
            return $string;
    }

}
