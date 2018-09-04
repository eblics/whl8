<?php 
	defined('BASEPATH') or exit('No direct script access allowed');
	if (! function_exists('send_sms')) {
		require_once '../application/third_party/alidayu/TopSdk.php';
		function send_sms($mobile, $template_id, $title, $con2 = null, $con3 = null, $type) {
			//$type短信发送的类型,1为验证码,2为单条通知,3为多条
			//单条通知 SMS_8966308
			$var1 = 'SMS_8966308';
			$var2 = 'SMS_9180035';
			$var3 = 'SMS_9315001';
			//多条通知 
			$content2 = $con2;
			$content3 = $con3;
			$c = new TopClient;
			$c->appkey = '23348970';
			$c->secretKey = 'a5a5141980e84cb9e84c82e96630afdb';
			$c->format = 'json';
			$req = new AlibabaAliqinFcSmsNumSendRequest;
			$req->setExtend("123456");
			$req->setSmsType("normal");
			$req->setSmsFreeSignName("欢乐扫");
			if($type == 3){
				$req->setSmsParam("{\"title\": \"$title\",\"content\": \"$content2\",\"detail\": \"$content3\"}");
			}
			if($type == 2){
				$req->setSmsParam("{\"title\": \"$title\",\"content\": \"$content2\"}");
			}
			if($type == 1){
				$req->setSmsParam("{\"title\": \"$title\"}");
			}
			$req->setRecNum($mobile);
			if($type == 1){
				$req->setSmsTemplateCode($var1);
			}
			if($type == 2){
				$req->setSmsTemplateCode($var2);
			}
			if($type == 3){
				$req->setSmsTemplateCode($var3);
			}
			$resp = $c->execute($req);
			if ($resp->result->success == true) {
				info("Send a msm to $mobile, code is $title template_id is $template_id");
				return TRUE;
			} else {
				error("Send mobile sms faild: " . json_encode($resp->result));
				return FALSE;
			}
		}
	}
	if( !function_exists('notice_sms')) {
		require_once '../application/third_party/alidayu/TopSdk.php';
		function notice_sms($mobile, $template_id) {
			$c = new TopClient;
			$c->appkey = '23348970';
			$c->secretKey = 'a5a5141980e84cb9e84c82e96630afdb';
			$c->format = 'json';
			$req = new AlibabaAliqinFcSmsNumSendRequest;
			$req->setExtend("123456");
			$req->setSmsType("normal");
			$req->setSmsFreeSignName("欢乐扫");
			$req->setRecNum($mobile->phoneNum);
			$req->setSmsTemplateCode($template_id);
			$resp = $c->execute($req);
			if ($resp->result->success == true) {
				return TRUE;
			} else {
				error("Send mobile sms faild: " . json_encode($resp->result));
				return FALSE;
			}
		}
	}
?>