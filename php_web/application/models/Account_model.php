<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * 
 * @author shizq
 *
 */
class Account_model extends CI_Model {
	
	const TABLE_SALESMAN     = 'salesman';
	const TABLE_WAITER       = 'waiters';
	
	/**
	 * 根据openid获取一个服务员数据
	 * 
	 * @param string $openid
	 * @param string $role 服务员或业务员
	 * @return mixed
	 */
	public function get($openid, $role) {
		$this->db->where('openid', $openid);
		if ($role == ROLE_SALESMAN) {
			$account = $this->db->get(SELF::TABLE_SALESMAN)->row();
		} else if ($role == ROLE_WAITER) {
			$account = $this->db->get(SELF::TABLE_WAITER)->row();
		} else {
			return NULL;
		}
		
		return $account;
	}

	public function getSalesman($salesmanId) {
		return $this->db->select('*')->from('salesman as t1')
		->join('mch_salesman as t2', 't2.id = t1.mchSalesmanId and t2.rowStatus = 0', 'left')
		->where('t1.id', $salesmanId)
		->get(self::TABLE_SALESMAN)->row();
	}
	
	/**
	 * 保存用户账户信息
	 * 
	 * @param string $salesmanObj
	 * @param others 用户信息
	 * @return boolean 是否有数据更新
	 */
	public function save($salesmanObj, $realname, $mobile, $id_card_no, $smsCode) {
		info('update salesman - begin');
		info('params: '. json_encode(func_get_args()));
		$mchSalesman = $this->db
			->where('mobile', $mobile)
			->where('idCardNo', $id_card_no)
			->where('rowStatus', 0)
			->get('mch_salesman')->row();
		if (! isset($mchSalesman)) {
			throw new Exception("业务员不存在", 1);
		}
		if ($mchSalesman->realName !== $realname) {
			throw new Exception("姓名验证失败", 1);
		}

		$salesman = $this->db->where('id', $salesmanObj->id)->get('salesman')->row();
		// 已经绑定到此微信账户
		if ($salesman->mchSalesmanId === $mchSalesman->id) {
			return 1;
		}
		$this->load->library('sms_vcode');
		if (! isset($smsCode)) {
			$template_id = 'SMS_7895086';
			$code = mt_rand(1000, 9999);
			$signame = '红码';
			info("send sms_code - begin");
			$sendOk = $this->sms_vcode->send_sms_vcode($mobile, $code, $template_id, $signame);
			if ($sendOk) {
			    throw new Exception("sms_code_required", 100123);
			} else {
			    throw new Exception("发送短信验证码失败，请重试", 1);
			}
			info("send sms_code - end");
		}
		$passObj = $this->sms_vcode->proof_vcode($mobile, $smsCode);
        if ($passObj['statusCode']) {
        	throw new Exception("短信验证码不正确", 1);
        } 
		$this->db->set('mchSalesmanId', $mchSalesman->id);
		$this->db->where('id', $salesman->id);
		$this->db->update(self::TABLE_SALESMAN);
		$affected_rows = $this->db->affected_rows();
		info('update salesman - end');
		return $affected_rows;
	}
	
	/**
	 * 添加一个新的服务员或管理员账户到数据库
	 * 
	 * @param array $account
	 */
	public function add($account, $role) {
		info("Add a account - begin");
		info('Role is ' . $role);
		debug('Account info is ' . json_encode($account));
		if ($role == ROLE_SALESMAN) {
			$this->db->insert(SELF::TABLE_SALESMAN, $account);
		} else if ($role == ROLE_WAITER) {
			$this->db->insert(SELF::TABLE_WAITER, $account);
		}
		info("Add a account - end");
	}
	
	/**
	 * 获取当前session中的微信用户信息，用户进入微信页面后，信息只是暂时保存在session中
	 * 并没有保存在数据库，因为系统不知道该用户是服务员还是业务员，也许两者都是，只有在
	 * 用户扫描二维码的入口才能判断该微信用户是以什么身份登录的
	 * 
	 * @param $mchId 企业id，此id需要从外部传入
	 * @return array $account 服务员或业务员的信息数组
	 */
	public function getCurrentSessionUser($mchId) {
		$account['mchId']      = $mchId;
		$account['openid']     = $this->session->openid;
		$account['nickName']   = $this->session->userinfo->nickname;
		$account['headimgurl'] = $this->session->userinfo->headimgurl;
		$account['sex']        = $this->session->userinfo->sex == NULL ? 0 : $this->session->userinfo->sex;
		$account['city']       = $this->session->userinfo->city;
		$account['province']   = $this->session->userinfo->province;
		$account['country']    = $this->session->userinfo->country;
		$account['subscribe']  = 0;
		$account['subscribe_time'] = time();
		$account['createTime'] = time();
		$account['updateTime'] = time();
		
		return $account;
	}
	
	function updateWxUser($user) {
		$this->db
			->set('nickName', $user->nickname)
			->set('headimgurl', $user->headimgurl)
			->set('subscribe', $user->subscribe)
			->set('updateTime', time())
			->where('openid', $user->openid)
			->update(SELF::TABLE_SALESMAN);
		$this->db
			->set('nickName', $user->nickname)
			->set('headimgurl', $user->headimgurl)
			->set('subscribe', $user->subscribe)
			->set('updateTime', time())
			->where('openid', $user->openid)
			->update(SELF::TABLE_WAITER);
	}

	function check4AccountType($accountId) {
		$mchAccount = $this->db->where('id', $accountId)->get('mch_accounts')->row();
		$exists = $this->db->where('accountId', $accountId)->where('mchId', $mchAccount->mchId)->get('mch_accounts_ext')->row();
		if (isset($exists)) {
			return AccountTypeEnum::Merchant;
		}
		return AccountTypeEnum::Normal;
	}

	/**
	 * 账户升级为企业号
	 * 
	 * @param   $accountId
	 * @param   $mchId
	 * @return void
	 */
	function upgradeAccount($accountId, $mchId) {
		$this->load->model('Merchant_model', 'merchant');
		$merchant = $this->merchant->get($mchId);
		if ($merchant->status == 0) {
			throw new Exception("企业未审核，审核后才能操作", 1);
		}
		$mchAccount = $this->db->where('id', $accountId)->get('mch_accounts')->row();
		$exists = $this->db->where('accountId', $accountId)->where('mchId', $mchId)->get('mch_accounts_ext')->row();
		if (isset($exists)) {
			throw new Exception("已经是企业号，无需操作", 1);
		}
		$params = [
			'accountId' => $accountId,
			'mchId'		=> $mchId,
			'role'		=> $mchAccount->role,
			'createTime'	=> time()
		];
		$this->db->insert('mch_accounts_ext', $params);
	}

	/**
	 * 账户授权给企业号
	 * 
	 * @param   $mobile
	 * @return void
	 */
	function authorizeAccount($mobile, $mchId, $role, $smsCode = NULL) {
		$mchAccount = $this->db->where('phoneNum', $mobile)->where('status !=', 3)->get('mch_accounts')->row();
		if (! isset($mchAccount)) {
			throw new Exception("你要授权给的企业号不存在", 1);
		}
		$params = [
			'accountId' => $mchAccount->id,
			'mchId'		=> $mchId,
			'role'		=> $role,
			'createTime'	=> time()
		];
		$mchNum = $this->db->where('accountId', $mchAccount->id)->get('mch_accounts_ext')->result();
		if (count($mchNum) == 0) {
			throw new Exception("该账户不是企业号，请先升级", 1);
		}
		$exists = $this->db->where('accountId', $mchAccount->id)->where('mchId', $mchId)->get('mch_accounts_ext')->row();
		if ($exists) {
			throw new Exception("已经授权给该企业号，无需操作", 1);
		}

		$this->load->library('sms_vcode');
		if (is_null($smsCode)) {
			// 发送短信验证码到$mobile
			$template_id = 'SMS_7895084';
            $code = mt_rand(100000, 999999);
            $signame = '红码';
            $res = $this->sms_vcode->send_sms_vcode($mobile, $code, $template_id, $signame);
            if ($res['success']) {
            	throw new Exception("短信验证码发送失败", 1);
            }
			throw new Exception("请求输入手机短信验证码", 10123);
		}

		$sendedCode = $this->session->userdata('auth_mobile_sms_code');
		$proofResp = $this->sms_vcode->proof_vcode($mobile, $smsCode);
		if ($proofResp['statusCode'] != 0) {
			throw new Exception($proofResp['message'], 1);
		}

		$success = $this->db->insert('mch_accounts_ext', $params);
		if (! $success) {
			throw new Exception("发送未知错误", 1);
		}
	}

}