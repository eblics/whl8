<?php
/**
* 微信功能类
*/
/* Demo section */

define('URL', 'csec.api.qcloud.com/v2/index.php');
class Tencent_api {

    var $CI;
    private $appId;
    private $secretId;
    private $secretKey;
    function __construct($data){
        $this->CI=& get_instance();
        $this->CI->load->model('merchant_model');
        $this->wxAppId = $data['wxAppId'];
        $this->secretId = '';
        $this->secretKey = '';
    }
    /**
    * 微信用户登录
    * @param object $mchInfo 商户信息
    */
    public function checkInterface($tcloud){
        if(empty($tcloud) ){
            return array('status'=>1,'message'=>'系统错误');
        }
        if(empty($tcloud->secretId) || empty($tcloud->secretKey)){
            return array('status'=>3,'message'=>'请完善secretId和secretKey');
        }
        
        $this->secretId = $tcloud->secretId;
        $this->secretKey = $tcloud->secretKey;
        $params = array(
            'accountType'          => '0',
            'uid'                  => '10000000',
            'userIp'               => '127.0.0.1',
            'postTime'             => time(),
        );
        debug(json_encode($params));
        $result = $this->ActivityAntiRush($params);
        //var_dump($result);
        if(isset($result['code']) && $result['code'] == 0){
            return array('status'=>$result['code'],'message'=>'已开通');
        }
        return array('status'=>$result['code'],'message'=>$result['message']);
    }
    public function checkCsec($userInfo,$code){
        $tcloud = $this->CI->merchant_model->get_tcloud($userInfo->mchId);
        if(empty($tcloud) || $tcloud->isUse != 1 || empty($tcloud->secretId) || empty($tcloud->secretKey)){
            //debug('mchId='.$userInfo->mchId.',cannot use tencent cloud');
            return 0;
        }
        $this->secretId = $tcloud->secretId;
        $this->secretKey = $tcloud->secretKey;
        if (isset($_SERVER['HTTP_X_FORWARDED_FOR'] ) && $_SERVER['HTTP_X_FORWARDED_FOR'] && preg_match('/^([0-9]{1,3}\.){3}[0-9]{1,3}$/',$_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $onlineip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } elseif  (isset($_SERVER['HTTP_CLIENT_IP'] ) && $_SERVER['HTTP_CLIENT_IP']  && preg_match('/^([0-9]{1,3}\.){3}[0-9]{1,3}$/',$_SERVER['HTTP_CLIENT_IP'])) {
            $onlineip = $_SERVER['HTTP_CLIENT_IP'];
        }else{
            $onlineip = null;
        }
        //$mchInfo = $this->CI->merchant_model->get($userInfo->mchId);
        $params = array(
            /* 账号信息 */
            'accountType'          => '2',
            'uid'                  => $userInfo->openid,
            'associateAccount'     => $userInfo->id,
            'nickName'             => $userInfo->nickName,
            //'phoneNumber'          => '086+15166666666',
            //'emailAddress'         => 'hellword@qq.com',
            'registerTime'         => $userInfo->createTime,
            //'registerIp'           => '121.14.96.121',
            //'passwordHash'         => 'f158abb2a762f7919846ee9bf8445c7f22a244c5',
            'appId'                => $this->wxAppId,
            
            /* 行为信息 */
            'userIp'               => $_SERVER['REMOTE_ADDR'],
            'postTime'             => time(),
            'loginSource'          => '2',
            //'loginType'            => '3',
            'rootId'               => $code,
            //'referer'              => $_SERVER['HTTP_REFERER'],
            //'jumpUrl'              => 'https://ui.ptlogin2.qq.com/cgi-bin/hello',
            'cookieHash'           => md5(md5( json_encode($_COOKIE) )),
            'userAgent'            => $_SERVER['HTTP_USER_AGENT'],
            //'xForwardedFor'        => $onlineip,
            //'mouseClickCount'      => '10',
            //'keyboardClickCount'   => '34',
            
            /* 设备信息 */
            //'macAddress'           => '00-05-9A-3C-7A-00',
            //'vendorId'             => 'tencent.com',
            //'imei'                 => '54654654646',
            //'appVersion'           => '10.0.1',
            
            /* 其他信息 */
            'businessId'           => '1',
        );
        if(!empty($userInfo->mobile)){
            $params['phoneNumber'] = $userInfo->mobile;
        }
        if(!empty($userInfo->email)){
            $params['emailAddress'] = $userInfo->email;
        }
        if(!empty($onlineip)){
            $params['xForwardedFor'] = $onlineip;
        }
        if(!isset($_SERVER['HTTP_REFERER']) && !empty($_SERVER['HTTP_REFERER'])){
            $params['referer'] = $_SERVER['HTTP_REFERER'];
        }
        debug(json_encode($params));
        $result = $this->ActivityAntiRush($params);
        //var_dump($result);
        if(isset($result['code']) && $result['code'] == 0){
            $level = $result['level'];
    
            if($level >= $tcloud->ignoreLevel){
                throw new Exception("禁止访问，您的账号存在风险行为！", 5);
            }
            return $level;
        }
        return 0;
//         if($result == 2){
//             throw new Exception("账号异常", 5);
//             //$this->ajaxResponseFail('您的账号异常，无法参加活动');
//         }
//         if($result == 1){
//             throw new Exception("没有中奖", 4);
//         }
        
        
    }
    private function sendRequest($url, $method = 'POST')
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        
        if (false !== strpos($url, "https")) {
            // 证书
            // curl_setopt($ch,CURLOPT_CAINFO,"ca.crt");
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        }
        $resultStr = curl_exec($ch);
        debug($resultStr);
        $result = json_decode($resultStr, true);
        //var_dump($result);
        return $result;
    }
    
    /* Generates an available URL */
    private function makeURL($method, $action, $region, $secretId, $secretKey, $args)
    {
        /* Add common parameters */
        $args['Nonce'] = (string)rand(0, 0x7fffffff);
        $args['Action'] = $action;
        $args['Region'] = $region;
        $args['SecretId'] = $secretId;
        $args['Timestamp'] = (string)time();
        
        /* Sort by key (ASCII order, ascending), then calculate signature using HMAC-SHA1 algorithm */
        ksort($args);
        $args['Signature'] = base64_encode(
            hash_hmac(
                'sha1', $method . URL . '?' . $this->makeQueryString($args, false),
                $secretKey, true
                )
            );
        
        /* Assemble final request URL */
        //var_dump($args);
        return 'https://' . URL . '?' . $this->makeQueryString($args, true);
    }
    
    /* Construct query string from array */
    private function makeQueryString($args, $isURLEncoded)
    {
        $arr = array();
        foreach ($args as $key => $value) {
            if (!$isURLEncoded) {
                $arr[] = "$key=$value";
            } else {
                $arr[] = $key . '=' . urlencode($value);
            }
        }
        return implode('&', $arr);
    }
    

    

    
    private function ActivityAntiRush($params, $region='gz')
    {
        /*
         * 补充用户、行为信息数据,方便我们做更准确的数据模型
         * 协议参考 https://www.qcloud.com/doc/api/254/2910
         */
        $url = $this->makeURL('GET', 'ActivityAntiRush', $region, $this->secretId, $this->secretKey, $params);
        $result = $this->sendRequest($url);
        return $result;
    }

    
}
