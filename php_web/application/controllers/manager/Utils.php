<?php
/**
 * 
 * 工具类控制器 Created at 2018-01-11
 *
 * @author shizq
 * 
 */
class Utils extends MerchantController {


    private $secretKey = 'aichuang_scrm';

    /**
     * 接口定义
     * @param $apiName 接口名称
     * {
     *     sms.get: 获取短信验证码;
     * }
     * @return void
     */
    public function api($apiName = NULL) {
        if (! isset($apiName)) {
            $this->ajaxResponseFail('403 Forbidden', 403, 403);
        } elseif ($apiName == 'sms.get') {
        	$this->getSms();
        } elseif ($apiName == 'sms.check') {
        	$this->checkSms();
        } else if ($apiName == 'sso.login') {
            $user = $this->session->userdata;
            $userMail = $user['mail'];
            $this->actionLogin($userMail,$this->secretKey);
        } else {
            $this->ajaxResponseFail('404 Not Found', 404, 404);
        }
    }

    private function getSms() {
	    debug('getSms');
    	$mobile = $this->input->get('account');
    	$for = $this->input->get('for');
        if (! isset($for)) {
            $this->ajaxResponseOver("参数错误，缺少发送目的");
        }
        $this->load->model('Merchant_model', 'merchant');
        $smsCode = $this->merchant->sendSms($mobile, $for);
        if (!isProd()) {
            $this->ajaxResponseFail('您的验证码是：'. $smsCode, 700);
        } else {
            $this->ajaxResponseSuccess();
        }
    }

    private function checkSms() {
    	$mobile = $this->input->post('account');
    	$value = $this->input->post('value');
    }

    /**
     * 至趣scrm 提供的登录方法
     * @auther fengyanjun
     * @dateTime 2018-01-18 17:38
     * @param string $userMail 用户登录邮箱
     * @param string $yzk 秘钥
     */
    private function actionLogin($userMail,$yzk){
        $user = $this->authcode($userMail,"ENCODE",$yzk,24*3600);
        $user = urlencode($user);
        //需要将地址输入到页面中，通过页面a连接点击(菜单栏的连接)
        //$url = "http://system3.cpd.test.social-touch.com/User/Index/aclogin?user=".$user;
        $this->output->set_content_type('application/json')->set_output(ajax_resp($user));
    }

    /**
     * 至趣scrm 提供的加密算法
     * @auther fengyanjun
     * @dateTime 2018-01-18 17:34
     * @param string $string  用户登录邮箱
     * @param string $operation 操作方式 默认解码
     * @param string $key 秘钥
     * @param int $expiry 超时时间
     * @return bool|string
     */
    private function authcode($string, $operation = 'DECODE', $key = '', $expiry = 0) {
        $ckey_length = 4;
        $key = md5($key ? $key : $GLOBALS['discuz_auth_key']);
        $keya = md5(substr($key, 0, 16));
        $keyb = md5(substr($key, 16, 16));
        $keyc = $ckey_length ? ($operation == 'DECODE' ? substr($string, 0, $ckey_length):
            substr(md5(microtime()), -$ckey_length)) : '';
        $cryptkey = $keya.md5($keya.$keyc);
        $key_length = strlen($cryptkey);
        $string = $operation == 'DECODE' ? base64_decode(substr($string, $ckey_length)) :
            sprintf('%010d', $expiry ? $expiry + time() : 0).substr(md5($string.$keyb), 0, 16).$string;
        $string_length = strlen($string);
        $result = '';
        $box = range(0, 255);
        $rndkey = array();
        for($i = 0; $i <= 255; $i++) {
            $rndkey[$i] = ord($cryptkey[$i % $key_length]);
        }
        for($j = $i = 0; $i < 256; $i++) {
            $j = ($j + $box[$i] + $rndkey[$i]) % 256;
            $tmp = $box[$i];
            $box[$i] = $box[$j];
            $box[$j] = $tmp;
        }
        for($a = $j = $i = 0; $i < $string_length; $i++) {
            $a = ($a + 1) % 256;
            $j = ($j + $box[$a]) % 256;
            $tmp = $box[$a];
            $box[$a] = $box[$j];
            $box[$j] = $tmp;
            $result .= chr(ord($string[$i]) ^ ($box[($box[$a] + $box[$j]) % 256]));
        }
        if($operation == 'DECODE') {
            if((substr($result, 0, 10) == 0 || substr($result, 0, 10) - time() > 0) &&
                substr($result, 10, 16) == substr(md5(substr($result, 26).$keyb), 0, 16)) {
                return substr($result, 26);
            } else {
                return '';
            }
        } else {
            return $keyc.str_replace('=', '', base64_encode($result));
        }
    }
}
