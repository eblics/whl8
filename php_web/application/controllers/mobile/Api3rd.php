<?php
/**
 * 欢乐扫平台外部接口请求处理控制器
 * 
 * @author shizq
 */
class Api3rd extends Mobile_Controller {

	/**
	 * 定义可被访问的IP地址
	 */
	const ALLOW_IP_ADDRESS = ['127.0.0.1', '114.55.109.50', '116.226.186.56'];

	public function __construct() {
		parent::__construct();
		$ipAddress = $_SERVER['REMOTE_ADDR'];
		if (! in_array($ipAddress, self::ALLOW_IP_ADDRESS) && isProd()) {
			$this->ajaxResponseOver('403 Forbidden: '. $_SERVER['REMOTE_ADDR'], 403, 403);
		}
	}

	/**
	 * 积分回传接口
	 * @return void
	 */
	public function backpoint() {
		$openid = $this->input->post('openid');
		$code = $this->input->post('code');
		$point = $this->input->post('point');
		$getTime = $this->input->post('getTime');
		$location = $this->input->post('location');

		if (empty($openid) || empty($code) || empty($point) || empty($getTime) || empty($location)) {
			$this->ajaxResponseFail('参数不能为空');
			return;
		}
		if (preg_match("/^\d+$/", $point) === 0) {
			$this->ajaxResponseFail('积分必须为正整数类型');
			return;
		}
		if (preg_match("/^\d+$/", $getTime) === 0) {
			$this->ajaxResponseFail('getTime参数错误');
			return;
		}
		if (preg_match("/\d,\d/", $location) === 0) {
			$this->ajaxResponseFail('location参数错误');
			return;
		}
		$updateParams['openid'] = $openid;
		$updateParams['code'] = $code;
		$updateParams['point'] = $point;
		$updateParams['getTime'] = $getTime;
		$updateParams['location'] = $location;
		try {
			$this->load->model('Guangming_model', 'guangming');
			$this->guangming->updateBackpoint($updateParams);
			$this->ajaxResponseSuccess();
		} catch (Exception $e) {
			$this->ajaxResponseFail($e->getMessage(), $e->getCode());
		}
		
	}

	/**
	 * 获取乐码信息接口
	 * @return void
	 */
	public function getcodeinfo() {
		$lecode = $this->input->get('code');
		if (empty($lecode)) {
			$this->ajaxResponseFail('乐码不能为空');
			return;
		}
		
		try {
			$this->load->model('Scan_model', 'scan');
			$lecodeInfoObj = $this->scan->deLecode($lecode);
			$this->load->model('Merchant_model', 'merchant');
			$merchant = $this->merchant->getMerchantByMchCode($lecodeInfoObj->mch_code);
			$this->load->model('Batch_model', 'batch');
			$params = new stdClass();
			$params->mchId = $merchant->id;
			$this->load->model('Guangming_model', 'guangming');
			$this->guangming->checkCodeOwner($merchant->id);
        	$batch = $this->batch->getBatchByValueWithoutException($params, $lecodeInfoObj->value);
        	if ($batch->state == 0) {
	            $state = 0;
	        } else if ($batch->expireTime < time()) {
	        	$state = 2;
	        } else if ($batch->rowStatus == 1 || $batch->state == 2) {
	        	$state = 3;
	        } else {
	        	$state = 1;
	        }
        	$resp = [
        		'code' => $lecode,
        		'batchNo' => $batch->batchNo,
        		'state' => $state,
        	];
        	$this->ajaxResponseSuccess($resp);
		} catch (Exception $e) {
			$this->ajaxResponseFail($e->getMessage(), $e->getCode());
		}
		
	}

	/**
	 * 用户信息回传接口
	 * @return string
	 */
	public function backuser() {
		$data['subscribe'] = $this->input->post('subscribe');
		$data['openid'] = $this->input->post('openid');
		$data['nickName'] = $this->input->post('nickname');
		$data['sex'] = $this->input->post('sex');
		$data['language'] = $this->input->post('language');
		$data['city'] = $this->input->post('city');
		$data['province'] = $this->input->post('province');
		$data['country'] = $this->input->post('country');
		$data['headimgurl'] = $this->input->post('headimgurl');
		$data['subscribe_time'] = $this->input->post('subscribe_time');
		$data['mobile'] = $this->input->post('mobile');
		if($data['subscribe'] != 0 && $data['subscribe'] !=1){
			$this->ajaxResponseFail('subscribe参数错误');
			return;
		}
		if(empty($data['openid'])){
			$this->ajaxResponseFail('openid参数不能为空');
			return;
		}
		if(!is_numeric($data['subscribe_time'])){
			$this->ajaxResponseFail('时间格式错误');
			return;
		}
		if(empty($data['headimgurl'])){
			$this->ajaxResponseFail('headimgurl参数错误');
			return;
		}
		try {
			$this->load->model('Guangming_model', 'guangming');
			$this->guangming->updateBackuser($data);
			$this->ajaxResponseSuccess();
		} catch (Exception $e) {
			$this->ajaxResponseFail($e->getMessage(), $e->getCode());
		}
	}

}