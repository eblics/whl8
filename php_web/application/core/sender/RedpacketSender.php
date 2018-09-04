<?php
/**
 * 微信发红包实现类
 *
 * @author shizq <shizqiang@foxmail.com>
 */
class RedpacketSender extends MY_Model implements Settler {

	const SETTLER_NAME = '微信红包';

	public function requestThirdPlatform($params) {

		$user_trans = [
			'userId' => $params['userId'],
			'role' => 0,
			'amount' => $params['amount'],
			'theTime' => time(),
			'mchId' => $params['mchId'],
			'isAuto' => 0,
			'action' => 1,
			'payType' => 0
		];
		if (isset($params['action'])) {
			$user_trans['action'] = $params['action'];
		}

		info('insert into user_trans - begin');
		info('data is: '. json_encode($user_trans));
		$success = $this->db->insert('user_trans', $user_trans);
		if (! $success) {
			throw new Exception("发生未知错误", 1);
		} 
		info('insert into user_trans - end');

		// 不用实时发放
		return $this->db->insert_id();
		
		/**
		 * 调用微信红包提现（已改为异步发放）
		$this->load->model('Merchant_model', 'merchant');
		$merchant = $this->merchant->get($params['mchId']);

		$params = array_merge($params, [
			'redpackType'  	=> 0,
			'mch_id' 		=> $merchant->wxMchId,
			'wxappid'  		=> $merchant->wxAppId,
			'send_name'		=> $merchant->wxSendName,
			'act_name'		=> $params['desc'],
			'remark' 		=> $params['desc'],
			'wishing' 		=> $merchant->wxWishing,
			'total_amount' 	=> $params['amount'],
			'total_num'		=> $merchant->wxRpTotalNum,
			're_openid'		=> $params['openid'],
			'key'			=> $merchant->wxPayKey,
			'certPath'		=> $merchant->certPath,
	        'keyPath'		=> $merchant->keyPath,
	        'caPath'		=> $merchant->caPath
		]);

	    debug('send wx redpacket - begin');
		debug('params: '. json_encode($params));
	    $respObj = $this->weixin_rest_api->send_redpack((object)$params);
	    debug('send wx redpacket - end');
		debug('send wx redpacket result: '. json_encode($respObj));
		debug("errcode: ". $respObj->err_code);
		if (isset($respObj->err_code) && $respObj->err_code != "SUCCESS") {
			throw new Exception($respObj->err_code_des, 1);
		}
		return $respObj;
		*/
	}

	public function getSettlerName() {
		return self::SETTLER_NAME;
	}
}