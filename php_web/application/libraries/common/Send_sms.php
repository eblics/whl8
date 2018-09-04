<?php
class Send_sms {

    private $ci;
    private $rest;
    
    public function __construct() {
        $accountSid     = 'aaf98f89519fcd0d0151a390f44507fa';
        $accountToken   = '6e45b13aae6944d9afcb4e325ce01a64';
        $appId          = '8a48b55152a56fc20152ed346ed05102';
        $serverIP       = 'app.cloopen.com';
        $serverPort     = '8883';
        $softVersion    = '2013-12-26';
        
        $this->ci = &get_instance();

        $params = [
            'ServerIP'      => $serverIP, 
            'ServerPort'    => $serverPort, 
            'SoftVersion'   => $softVersion,
        ];

        $this->ci->load->library('common/ccprest_sdk', $params);
        $this->rest = $this->ci->ccprest_sdk;
        
        $this->rest->setAccount($accountSid, $accountToken);
        $this->rest->setAppId($appId);
    }

    private function sendTemplateSMS($to, $datas, $tempId) {
        error('send-msm-send-template-sms - begin');
        error('send-msm-send-template-sms - params: '. json_encode(func_get_args()));
        $result = $this->rest->sendTemplateSMS($to,$datas,$tempId);
        error('send-msm-send-template-sms - result: '. json_encode($result));
        if (empty($result)) {
            throw new Exception("短信验证码发送失败，未知错误", 1);
        }
        if ($result->statusCode != 0) {
            throw new Exception($result->statusMsg, 1);
        }
    }

    public function get_validcode($phonenum, $tid) {
        $smsCode = mt_rand(100000, 999999);
        $this->ci->session->smssender_phonenum = $phonenum;
        $this->ci->session->smssender_validcode = $smsCode;
        $this->ci->session->smssender_time = time();
        if (! isProd()) {
            return $smsCode;
        } else {
            $this->sendTemplateSMS($phonenum, [$smsCode, 10], $tid);
            return $smsCode;
        }
    }
    
    public function check_validcode($phonenum,$validcode) {
        $sendTime = $this->ci->session->smssender_time;
        if ($this->ci->session->smssender_phonenum != $phonenum ||
            $this->ci->session->smssender_validcode != $validcode ||
            (time()-$sendTime) > 600) {
            return ['statusCode' => 1, 'message' => '手机对应的验证码不正确'];
        }
        return ['statusCode'=>0];
    }
} 