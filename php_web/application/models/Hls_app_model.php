<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * @author shizq
 *
 */
class Hls_app_model extends CI_Model {

	// 微信支付统一下单接口
	const WX_PAY_ORDER_GEN_URL = 'https://api.mch.weixin.qq.com/pay/unifiedorder';

	const WX_ORDER_CHECK_URL   = 'https://api.mch.weixin.qq.com/pay/orderquery';

	const PAGE_SIZE = 8;

	/**
	 * 获取所有的APP应用
	 *
	 * @param  $current_page 当前分页页码
	 * @return array
	 */
	function getAllApps($mch_id = NULL, $current_page = 1) {
		$page = $current_page - 1;
		$result = $this->db
			->select('id, name, desc, image, price, unit, path, rowStatus')
			->from('apps')
			->where('rowStatus', 0)
			->limit(self::PAGE_SIZE * $page, self::PAGE_SIZE)
			->get()->result();
		if (isset($mch_id)) {
			$mchApps = $this->db
				->where('mchId', $mch_id)
				->where('payStatus', 1)
				->get('app_inst')
				->result();
		}
		foreach ($result as $app) {
			$app->price = sprintf('%1.2f', $app->price / 100, 2);
			if ($app->path) {
				$app->usefull = 1;
			} else {
				// APP不可用
				$app = new stdClass();
				$app->usefull = 0;
			}
			if (isset($mchApps)) {
				foreach ($mchApps as $mchApp) {
					if (! $app->usefull) {
						continue;
					}
					if ($app->id == $mchApp->pid) {
						if ($mchApp->rowStatus == 1) {
							$app->trash = 1; // 企业已经删除了这个应用
						} else {
							$app->hold = 1; // 企业已经添加了这个应用
						}
					}
				}
			}
		}
		return $result;
	}

	/**
	 * 获取推荐的APP应用
	 *
	 * @param  $mch_id 企业编号
	 * @return array
	 */
	function getRecommed($mch_id) {
		$sql = "SELECT * FROM apps
				WHERE id NOT IN
					(SELECT pid FROM app_inst WHERE mchId = ? AND payStatus = 1)
				LIMIT 4";
		$result = $this->db->query($sql, [$mch_id])->result();
		return $result;
	}

	/**
	 * 获取某个企业的APP应用
	 *
	 * @param  $mch_id 企业ID
	 * @param  $current_page 当前分页页码
	 * @return array
	 */
	function getApps($mch_id, $current_page = 1) {
		$page = $current_page - 1;
		$result = $this->db
			->select('t1.id AS inst_id, t1.config, t1.status, t2.image, t2.path')
			->from('app_inst AS t1')
			->join('apps     As t2', 't1.pid = t2.id')
			->where('t1.mchId', $mch_id)
			->where('t1.payStatus', 1) // 已支付
			->where('t1.rowStatus', 0) 
			->limit(self::PAGE_SIZE * $page, self::PAGE_SIZE)
			->get()->result();
		return $result;
	}

	/**
	 * 获取企业应用的详细信息
	 *
	 * @param  $app_id 应用编号
	 * @return object
	 */
	function getAppById($mch_id, $app_id) {
		$app = $this->db->where('id', $app_id)->get('apps')->row();
		if (! isset($app)) {
			error("App does not exists which id is: $app_id");
			throw new Exception("你要找的应用不存在", 1);
		}
		$mchApp = $this->db
			->where('pid', $app_id)
			->where('mchId', $mch_id)
			->where('payStatus', 1)
			->get('app_inst')
			->row();
		if ($mchApp) {
			if ($mchApp->rowStatus == 1) {
				$app->trash = 1;
			} else {
				$app->hold = 1;
			}
		}
		return $app;
	}

	/**
	 * 获取app实例的详细数据
	 *
	 * @param  $mch_id 企业ID
	 * @param  $app_path 应用名称
	 * @return objet
	 */
	function getAppInst($mch_id, $app_path) {
		if (! isset($mch_id) || ! isset($app_path)) {
			error("Require mch_id and app_path, mch_id is: $mch_id, app_path is: $app_path");
			throw new Exception("缺少必要参数", 10001);
		}
		$sql = "SELECT t1.id AS id, t1.mchId, t1.name, t1.config, t1.status
				FROM app_inst AS t1
				JOIN apps     AS t2 ON t1.pid = t2.id
				WHERE t1.mchId = ? AND t2.path = ? AND payStatus = 1 AND t1.rowStatus = 0";
		$appInst = $this->db->query($sql, [$mch_id, $app_path])->row();
		if (! $appInst) {
			error("App inst not exists which mchId is $mch_id and pid is $app_path");
			throw new Exception("你要找的应用不存在", 1);
		}
		if ($appInst->status == 0) {
			throw new Exception("这个应用没有启用", 1);
		}
		if (! $appInst->config) {
			throw new Exception("这个应用没有初始化配置", 1);
		}
		if ($appInst->status == 2) {
			throw new Exception("这个应用已过期", 1);
		}
		return $appInst;
	}

	/**
	 * 获取app实例的详细数据
	 *
	 * @param  $app_inst_id 实例
	 * @return objet
	 */
	function getAppInstById($app_inst_id) {
		$sql = "SELECT *, apps.path AS config_path
				FROM apps
				JOIN app_inst ON app_inst.pid = apps.id
				WHERE app_inst.id = ?";
		$appInst = $this->db->query($sql, [$app_inst_id])->row();
		if (! isset($appInst)) {
			error("App inst not exists which id is $app_inst_id");
			throw new Exception("你要找的应用不存在", 1);
		}
		return $appInst;
	}

	/**
	 * 获取用户详细信息
	 *
	 * @param  $wxOpenId 微信OpenID
	 * @return objet
	 */
	function getMember($wxOpenId) {
		$user = $this->db->where('openid', $wxOpenId)->get('users')->row();
		if (! $user) {
			throw new Exception("当前微信用户未关注", 1);
		}
		$emptyUser = new stdClass();
		$emptyUser->id = $user->id;
		return $emptyUser;
	}

	/**
	 * 商户修改应用实例信息
	 *
	 * @param  $app_inst 应用实例信息关联数组
	 * @param  $config 配置信息
	 * @return objet
	 */
	function saveInstInfo($app_inst_id, $config) {
		info("======================= SaveInstInfo begin =======================");
		$this->db->set('config', $config);
		$this->db->where('id', $app_inst_id);
		$update_result = $this->db->update('app_inst');
		if (! $update_result) {
			error("Unknow error");
			throw new Exception("发生未知错误", 1);
		}
		info("=======================  SaveInstInfo end  =======================");
	}

	/**
	 * 企业添加应用
	 *
	 * @param  $mch_id 企业ID
	 * @param  $app_id 应用ID
	 * @return void
	 */
	function applyApp($app_id, $mch_id) {
		info("======================= Apply app begin =======================");
		debug("mch_id is $mch_id, app_id is $app_id");
		if (! isset($mch_id) || ! isset($app_id)) {
			error("Params invalid");
			throw new Exception("缺少必要参数", 1);
		}
		$exists = $this->db->where('mchId', $mch_id)->where('pid', $app_id)->get('app_inst')->row();
		if ($exists) {
			if ($exists->payStatus == 1) {
				info("App already exists");
				throw new Exception("您已经添加过此应用", 1);
			} else {
				// 需要支付该应用
				info("Need pay for this app");
				throw new Exception("这个应用需要支付", 10009);
			}
		}
		$app = $this->db->where('id', $app_id)->get('apps')->row();
		$data = [
			'pid'       => $app_id,
			'mchId'     => $mch_id,
			'config'	=> json_encode(['name' => $app->name, 'desc' => $app->desc]),
			'amount'    => 0,
			'payStatus' => $app->price == 0 ? 1 : 0,
			'startTime' => time(),
			'endTime'   => time() + 365 * 24 * 60 * 60,
			'price'     => $app->price
		];
		$result = $this->db->insert('app_inst', $data);
		if (! $result) {
			error("Insert data faild");
			throw new Exception("发生未知错误", 1);
		}
		if ($data['payStatus'] == 0) {
			throw new Exception("这个应用需要支付", 10009);
		}
		info("=======================  Apply app end  =======================");
	}

	/**
	 * 企业删除某个APP
	 *
	 * @param  $app_inst_id 要删除的应用实例编号
	 * @return void
	 */
	function unApplyApp($app_inst_id) {
		$appInst = $this->db
			->where('id', $app_inst_id)
			->where('payStatus', 1)
			->where('rowStatus', 0)
			->get('app_inst')->row();
		if (! $appInst) {
			throw new Exception("你要删除的应用不存在", 1);
		}
		$resp = $this->db->set('rowStatus', 1)->where('id', $app_inst_id)->update('app_inst');
		if (! $resp) {
			throw new Exception("发生未知错误", 1);
		}
	}

	/**
	 * 企业停用或启用某个APP
	 *
	 * @param  $app_inst_id 要停用或启用的应用实例编号
	 * @return void
	 */
	function enableAppInst($app_inst_id, $enable) {
		$appInst = $this->db
			->where('id', $app_inst_id)
			->where('payStatus', 1)
			->where('rowStatus', 0)
			->get('app_inst')->row();
		if (! $appInst) {
			throw new Exception("你操作的应用不存在", 1);
		}
		if ($enable) {
			$resp = $this->db->set('status', 1)->where('id', $app_inst_id)->update('app_inst');
		} else {
			$resp = $this->db->set('status', 0)->where('id', $app_inst_id)->update('app_inst');
		}
		if (! $resp) {
			throw new Exception("发生未知错误", 1);
		}
	}

	/**
	 * 企业重新启用APP
	 *
	 * @param  $app_id 要启用的应用编号
	 * @param  $mch_id 企业ID
	 * @return void
	 */
	function reApplyApp($app_id, $mch_id) {
		info("======================= reApplyApp begin =======================");
		debug("mch_id is: $mch_id, app_id is: $app_id");
		$appInst = $this->db->where('pid', $app_id)->where('mchId', $mch_id)->get('app_inst')->row();
		if (! $appInst) {
			error("Merchant does not have this app which mch_id is: $mch_id, app_id is: $app_id");
			throw new Exception("你还没有添加过此应用", 1);
		}
		if ($appInst->payStatus == 0) {
			error("App did not payed which mch_id is: $mch_id, app_id is: $app_id");
			throw new Exception("你还没有购买此应用", 1);
		}
		if ($appInst->rowStatus == 0) {
			error("App already applied which mch_id is: $mch_id, app_id is: $app_id");
			throw new Exception("你已经添加了此应用", 1);
		}
		$resp = $this->db->set('rowStatus', 0)
			->where('pid', $app_id)
			->where('mchId', $mch_id)
			->update('app_inst');
		if (! $resp) {
			error("Update rowStatus faild which mch_id is: $mch_id, app_id is: $app_id");
			throw new Exception("发生未知错误", 1);
		}
		info("=======================  reApplyApp end  =======================");
	}

	/**
	 * 生成微信支付订单
	 *
	 * @param  $app_id 要购买的应用ID
	 * @param  $mch_id 企业ID
	 * @return void
	 */
	function generateWxPayOrder($app_id, $mch_id) {
		info("======================= generateWxPayOrder begin =======================");

		$resp = $this->db->where('mchId', $mch_id)->where('pid', $app_id)->get('app_inst')->row();
		if (! $resp) {
			error("Order does not exists which app_id is: $app_id and mch_id is: $mch_id");
			throw new Exception("微信下单失败", 1);
		}
		if ($resp->codeUrlExpireTime > time()) {
			return ['order_id' => $resp->id];
		}

		$price = $this->getAppPrice($app_id, $mch_id);
		$this->load->helper('common/hls');

		$appid            = 'wxd08b6f01d21d28aa';
		$attach           = '欢乐扫应用';
		$body             = '欢乐扫应用购买';
		$mch_id_          = '1305639101';
		$nonce_str        = createNonceStr();
		$notify_url       = 'http://dev.www.lsa0.cn/app/wx_pay_notify'; // 可以不填
		$out_trade_no     = strval(date('Ymdhis')) . strval(mt_rand(1000, 9999));
		$spbill_create_ip = $_SERVER["REMOTE_ADDR"];
		$total_fee        = $price;
		$trade_type       = 'NATIVE';

		$sign_str = "appid=%s&attach=%s&body=%s&mch_id=%s&nonce_str=%s&notify_url=%s&out_trade_no=%s&spbill_create_ip=%s&total_fee=%d&trade_type=%s";
		$sign_str = $sign_str . '&key=Id68a23LdMX7092DNAnmi8nY0R3XeHec';
		$sign_str = sprintf($sign_str, $appid, $attach, $body, $mch_id_, $nonce_str,
			$notify_url, $out_trade_no, $spbill_create_ip, $total_fee, $trade_type);
		debug("Sigh_str is: $sign_str");
		$sign = strtoupper(md5($sign_str));
		debug("Sign is: $sign");
		$params = "";
		$params .= "<xml>";
		$params .=   "<appid>%s</appid>";
		$params .=   "<attach>%s</attach>";
		$params .=   "<body>%s</body>";
		$params .=   "<mch_id>%s</mch_id>";
		$params .=   "<nonce_str>%s</nonce_str>";
		$params .=   "<notify_url>%s</notify_url>";
		$params .=   "<out_trade_no>%s</out_trade_no>";
		$params .=   "<spbill_create_ip>%s</spbill_create_ip>";
		$params .=   "<total_fee>%d</total_fee>";
		$params .=   "<trade_type>%s</trade_type>";
		$params .=   "<sign>%s</sign>";
		$params .= "</xml>";
		$params = sprintf($params, $appid, $attach, $body, $mch_id_, $nonce_str,
			$notify_url, $out_trade_no, $spbill_create_ip, $total_fee, $trade_type, $sign);
		debug("Request xml data is: $params");

		$resp = curl_post_text(self::WX_PAY_ORDER_GEN_URL, $params);
		$resp = new SimpleXMLElement($resp);
		foreach ($resp->children() as $child) {
			$data[$child->getName()] = (string)$child;
		}
		if ($data['return_code'] != 'SUCCESS') {
			error("微信统一下单接口调用出错");
			throw new Exception($data['return_msg'], 1);
		}
		if ($data['result_code'] != 'SUCCESS') {
			error("微信统一下单接口调用出错，错误代码：" . $data['err_code']);
			throw new Exception($data['err_code_des'], 1);
		}
		$sql = "UPDATE app_inst
				SET codeUrl = ?, orderNumber = ?, codeUrlExpireTime = ?, prepayId = ?
				WHERE mchId = ? AND pid = ?";
		$resp = $this->db->query($sql, [$data['code_url'], $out_trade_no, time() + 7000, $data['prepay_id'], $mch_id, $app_id]);
		if (! $resp) {
			error("update order info faild, sql is: $sql");
			throw new Exception("发生未知错误", 1);
		}
		$app_inst_order = $this->db->where('mchId', $mch_id)->where('pid', $app_id)->get('app_inst')->row();
		if (! $app_inst_order) {
			error("app order does not exists can not get order_id, mch_id is: $mch_id, pid is: $app_id");
			throw new Exception("发生未知错误", 1);
		}
		info("=======================  generateWxPayOrder end  =======================");
		return ['order_id' => $app_inst_order->id];
	}

	/**
	 * 获取应用实例的订单信息
	 *
	 * @param  $order_id 订单ID
	 * @param  $mch_id 企业ID
	 * @return array
	 */
	function getOrder($order_id, $mch_id) {
		if (! $order_id) {
			error("App order does not exists which id is: $order_id");
			throw new Exception("没有找到购买订单", 1);
		}
		$app_inst = $this->db->where('id', $order_id)->where('mchId', $mch_id)->get('app_inst')->row();
		if (! $app_inst) {
			error("App order does not exists which id is: $order_id");
			throw new Exception("订单不存在", 1);
		}
		$params = [
			'qrcode'       => $app_inst->codeUrl,
			'order_number' => $app_inst->orderNumber,
			'order_type'   => '微信支付',
			'price'        => $app_inst->price
		];
		return $params;
	}

	/**
	 * 查询微信订单状态是否已支付
	 *
	 * @param  $order_id 订单ID
	 * @return array
	 */
	function wxOrderquery($order_id) {
		$this->load->helper('common/hls');

		$app_inst = $this->db->where('id', $order_id)->get('app_inst')->row();
		if (! $app_inst) {
			error("App order does not exists which id is: $order_id");
			throw new Exception("订单不存在", 1);
		}
		info("======================= wxOrderquery begin =======================");
		$appid            = 'wxd08b6f01d21d28aa';
		$mch_id_          = '1305639101';
		$out_trade_no     = $app_inst->orderNumber;
		$nonce_str        = createNonceStr();
		$sign_str = "appid=%s&mch_id=%s&nonce_str=%s&out_trade_no=%s";
		$sign_str = $sign_str . '&key=Id68a23LdMX7092DNAnmi8nY0R3XeHec';
		$sign_str = sprintf($sign_str, $appid, $mch_id_, $nonce_str, $out_trade_no);
		debug("Sigh_str is: $sign_str");
		$sign = strtoupper(md5($sign_str));
		debug("Sign is: $sign");
		$params = "";
		$params .= "<xml>";
		$params .=   "<appid>%s</appid>";
		$params .=   "<mch_id>%s</mch_id>";
		$params .=   "<nonce_str>%s</nonce_str>";
		$params .=   "<out_trade_no>%s</out_trade_no>";
		$params .=   "<sign>%s</sign>";
		$params .= "</xml>";
		$params = sprintf($params, $appid, $mch_id_, $nonce_str, $out_trade_no, $sign);
		debug("Request xml data is: $params");

		$resp = curl_post_text(self::WX_ORDER_CHECK_URL, $params);
		$resp = new SimpleXMLElement($resp);
		foreach ($resp->children() as $child) {
			$data[$child->getName()] = (string)$child;
		}
		if ($data['return_code'] != 'SUCCESS') {
			error("查询微信订单状态出错");
			throw new Exception($data['return_msg'], 1);
		}
		if ($data['result_code'] != 'SUCCESS') {
			error("查询微信订单状态出错，错误代码：" . $data['err_code']);
			throw new Exception($data['err_code_des'], 1);
		}
		if ($data['trade_state'] == 'SUCCESS' && $data['total_fee'] == $app_inst->price) {
			// 支付成功，可以发货咯
			$resp = $this->db->set('payStatus', 1)
				->where('id', $order_id)
				->update('app_inst');
			if (! $resp) {
				throw new Exception("购买失败", 1);
			}
		} else {
			throw new Exception($data['trade_state'], 10006);
		}
		info("=======================  wxOrderquery end  =======================");
	}

	/**
	 * 根据订单获取app实例信息
	 * 
	 * @param  int $order_id 订单ID(app_inst id)
	 * @return stdObject
	 */
	function getAppinstByOrderId($order_id) {
		return $this->db->where('id', $order_id)->get('app_inst')->row();
	}

	/**
	 * 获取要购买的app的价格
	 *
	 * @param  $app_id 要购买的应用ID
	 * @return int 价格（单位：分）
	 */
	private function getAppPrice($app_id, $mch_id) {
		$app = $this->db->where('id', $app_id)->get('apps')->row();
		if (! $app) {
			error("App does not exists which id is: $app_id");
			throw new Exception("购买的应用不存在", 1);
		}
		if ($app->price == 0) {
			error("App is free which id is: $app_id");
			throw new Exception("这个应用是免费的，无需购买", 1);
		}
		$app_inst = $this->db
			->where('pid', $app_id)
			->where('mchId', $mch_id)
			->get('app_inst')->row();
		if ($app_inst->payStatus == 1) {
			error("Already pay finished for app: $app_id");
			throw new Exception("你已经支付过了该应用", 1);
		}
		return $app->price;
	}

}
