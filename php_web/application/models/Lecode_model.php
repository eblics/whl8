<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * 
 * @author shizq
 *
 */
class Lecode_model extends MY_Model {
	
	const TABLE_TICKETS = 'obj_tickets';
	
	public function __construct() {
		parent::__construct();
		$this->load->library('common/code_encoder');
		
		$this->load->model('Account_model', 'account');
		$this->load->model('Merchant_model', 'merchant');
		$this->load->model('Activity_model', 'activity');
		$this->load->model('Batch_model', 'batch');
		$this->load->model('Sub_activity_model', 'sub_activity');
		$this->load->model('Setting_model', 'setting');
		$this->load->model('Scan_log_model', 'scan_log');
	}
	

	public function scan($lecode, $waiter) {
        $scanRule = $this->setting->get_mch_scan_rule($waiter->mchId);
        if (! $scanRule) {
            $scanRule = (object)[
                'times'=>12,
                'unit'=>'i'
            ];
        }
        $unit = $scanRule->unit;
        switch ($unit) {
            case 'y':
                $endTime = strtotime("-1 year");
            	break;
            case 'm':
                $endTime = strtotime("-1 month");
            	break;
            case 'd':
                $endTime = strtotime("-1 day");
            	break;
            case 'h':
                $endTime = time() - 3600;
            	break;
            case 'i':
                $endTime = time() - 60;
            	break;
        }
        $scanRecords = $this->scan_log->get_scaninfo_by_scan_rule($waiter->id, $endTime);
        if ($scanRecords) {
            if ($scanRecords->count >= $scanRule->times) {
               throw new Exception('扫码过于频繁，请稍后再试', 1);
            }
        }

		/**
		 * 执行解码操作
		 */
		$decode_result = $this->code_encoder->decode($lecode);
		if ($decode_result->errcode !== 0) {
			throw new Exception($decode_result->errmsg);
		}
		
		$decode_result = $decode_result->result;
		
		$mch_code = $decode_result->mch_code;
		$merchant = $this->merchant->get_by_code($mch_code);
		if (! $merchant) {
			throw new Exception('没有这个商户');
		}
		
		if ($merchant->id !== $waiter->mchId) {
			throw new Exception('此乐码未能识别');
		}
		
		$lecode_val = $decode_result->value;
		$batch = $this->batch->get_by_value($merchant->id, $lecode_val);
		if (! isset($batch)) {
			throw new Exception('此乐码没有申请');
		}
		if ($batch->expireTime < time()) {
			throw new Exception('此乐码已经过期');
		}
		
		$this->db->where('code', $lecode);
		$scanLog = $this->db->get('scan_log_waiters')->row();
		if (! isset($scanLog)) {
			$scanLog['code']     = $lecode;
			$scanLog['userId']   = $waiter->id;
			$scanLog['openid']   = $waiter->openid;
			$scanLog['mchId']    = $merchant->id;
			$scanLog['ip']       = $_SERVER['REMOTE_ADDR'];
			$scanLog['scanTime'] = time();
			$scanLog['batchId']  = $batch->id;
			$scanLog['over']     = 0;
			error("lecode-model-scan - insert scan_log_waiters: ". json_encode($scanLog));
			$this->db->insert('scan_log_waiters', $scanLog);
		} else {
			if ($scanLog->over == BoolEnum::Yes) {
				if ($waiter->id == $scanLog->userId) {
					throw new Exception('您已扫过此码', 1);
				} else {
					throw new Exception('此码已被他人扫过', 1);
				}
			} 
		}
	}
	
	
	/**
	 * 将扫描的二维码标记为已扫描状态
	 * 
	 * @param string $ticket 转移票据
	 */
	public function scanTicket($ticket, $role, $scannerId) {
		// 1.查找数据库中的ticket,判断是否在有效期内
		$this->beginTransition();
		$ticket_info = $this->db->query('SELECT * FROM obj_tickets WHERE ticket = ? 
			AND expireTime > ? AND scaned = 0 ORDER BY expireTime DESC FOR UPDATE', 
			[$ticket, time()])->row();
		if (! $ticket_info) {
			$this->rollbackTransition();
			throw new Exception('您扫描的二维码不存或已过期');
		}
		
		$this->db->set('scaned', BoolEnum::Yes);
		$this->db->set('userId', $scannerId);
		$this->db->set('role', $role);
		$this->db->where('ticket', $ticket);
		$this->db->update(self::TABLE_TICKETS);
		$this->commitTransition();

		$this->session->ticket = $ticket;
		$this->session->role = $role;
		
		return ['type' => ScanActionEnum::Transfer];
	}

	/**
	 * 查询乐码对应的扫码记录
	 * 
	 * @param string $lecode 乐码
	 */
	public function check_scan_log($lecode) {
		$scan_history = $this->db->where('code', $lecode)->get('scan_log')->row();
		return $scan_history;
	}

	/**
	 * 将指定的乐码记录在数据库中至为已扫描
	 * 
	 * @param string $scan_history 扫码记录
	 */
	public function generate_scan_log($scan_history, $openid) {
		if ($scan_history->openId != $openid) {
			throw new Exception("这个乐码已被扫", 1);
		}
		$result = $this->db->set('over', 1)->where('id', $scan_history->id)->update('scan_log');
		if (! $result) {
			throw new Exception("发生未知错误", 1);
		}
	}

	/**
	 * 判断乐码是否合法
	 * 
	 * @return mixed
	 */
	public function if_lecode_invalid($lecode, $mch_id) {
		$this->load->model('Merchant_model', 'merchant');
		$this->load->library('common/code_encoder', 'code_encoder');
		$merchant = $this->merchant->get($mch_id);
		$resp = $this->code_encoder->decode($lecode);
		if ($resp->errcode) {
			return $resp->errmsg;
		}
		if ($merchant->code != $resp->result->mch_code) {
			return '乐码不属于该企业';
		}
		return FALSE;
	}
}