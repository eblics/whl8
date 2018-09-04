<?php 
class MY_Model extends CI_Model {

	private static $redis;

	protected function beginTransition() {
		$this->db->trans_begin();
	}

	protected function rollbackTransition() {
		$this->db->trans_rollback();
	}

	protected function commitTransition() {
		$this->db->trans_commit();
	}

	protected function checkTransitionSuccess() {
		return $this->db->trans_status() !== FALSE;
	}

	protected function getRedisClient() {
		if (isset(self::$redis)) {
			return self::$redis;
		}
		self::$redis = new Redis();
		self::$redis->pconnect(config_item('redis')['host'], config_item('redis')['port']);
		self::$redis->auth(config_item('redis')['password']);
		return self::$redis;
	}


	protected function getEnvMchId() {
		if (! isProd()) {
			return 0;
		}
		return static::MCH_ID; 
	}

	protected function getEnvGmMchId() {
		return config_item('gm_mch_id');
	}
}

require 'sender/Settler.php';
require 'sender/MchPay.php';
require 'sender/RedpacketSender.php';
