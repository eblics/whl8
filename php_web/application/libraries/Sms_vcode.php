<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * 短信验证码
 */
class Sms_vcode {
	function __construct()
	{
		$this->ci = & get_instance ();
	}
	function send_sms_vcode($mobile, $code, $template_id, $signame,$product='红码平台') 
	{	
		return $this->sendSms($mobile, $template_id, $signame, $product, $code);
	}
	/**
	 * 校对短信验证码
	 */ 
	function proof_vcode($phonenum,$validcode){
		if($this->ci->session->smssender_phonenum!=$phonenum ||
            $this->ci->session->smssender_validcode!=$validcode ||
            time()-$this->ci->session->smssender_time>50*10){
            return ['statusCode'=>1,'message'=>'手机对应的验证码不正确'];
        }
        return ['statusCode'=>0];
	}

	public function sendSms($mobile, $templateId, $signame = '红码', $product = '红码平台', $code = NULL) {
		if (isset($code)) {
			$smsCode = $code;
		} else {
			$smsCode = mt_rand(100000, 999999);
		}
		if (isProd()) {
			$c = $this->getClient();
			$req = new AlibabaAliqinFcSmsNumSendRequest;
			$req->setExtend("123456");
			$req->setSmsType("normal");
			$req->setSmsFreeSignName($signame);
			$req->setSmsParam("{\"code\": \"$smsCode\", \"product\": \"$product\"}");
			$req->setRecNum($mobile);
			$req->setSmsTemplateCode($templateId);
			$resp = $c->execute($req);
			//debug('sms:'.var_export($resp,True));
			if (isset($resp->result)) {
				if (! $resp->result->err_code) {
					$this->setSession($smsCode, $mobile);
					return $smsCode;
				} else {
					throw new Exception(json_encode($resp->result), 1);
				}
			} else {
				throw new Exception(json_encode($resp), 1);
			}
		} else {
			$this->setSession($smsCode, $mobile);
			return $smsCode;
		}
	}

	private function setSession($smsCode, $mobile) {
		$this->ci->session->smssender_validcode = $smsCode;
		$this->ci->session->smssender_phonenum = $mobile;
		$this->ci->session->smssender_time = time();
	}

	private function getClient() {
		require '../application/third_party/alidayu/TopSdk.php';
		$c = new TopClient;
		$c->appkey = '23348970';
		$c->secretKey = 'a5a5141980e84cb9e84c82e96630afdb';
		$c->format = 'json';
		return $c;
	}
    
}
