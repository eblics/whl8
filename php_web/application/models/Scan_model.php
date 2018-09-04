<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Scan_model extends MY_Model {

	const LIMIT_EXPIRE_TIME = 60;

	/**
	 * 检查企业每秒钟最多扫码数量
	 * @param $merchant 
	 * @return void
	 */
	public function checkScanLimit($merchant) {
		$redis = parent::getRedisClient();
		$scanNumKey = 'scan_nums_' . $merchant->id . '_' . time();
        $currentScanNum = $redis->incr($scanNumKey, 1);
        $redis->expire($scanNumKey, self::LIMIT_EXPIRE_TIME);
		if ($currentScanNum > $merchant->concurrencyNum) {
			throw new Exception("扫码人数过多，请重试", 100065);
		}
	}

	/**
     * 校验验证码
     * 
     * @param $needle 是否需要校验验证码
     * @return array
     */
    public function checkValidate($needle) {
        if ($needle == 1) {
            if (isset($_POST['captcha'])) {
                $postStr = trim($_POST['captcha']);
                $this->load->library('common/captcha/cool_captcha');
                $validate = $this->cool_captcha->validate($postStr);
                if (! $validate) {
                    return ['pass' => FALSE, 'msg' => '验证码不正确'];
                }
            } else {
                return ['pass' => FALSE, 'msg' => '需要提供验证码'];
            }
        }
        return ['pass' => TRUE, 'msg' => NULL];
    }

    /**
     * 检测是否超出扫码次数限制
     * 
     * @return void
     */
    public function checkScanDenyTimes($mchId, $userId) {
        $this->load->model('setting_model');
        $scanRule = $this->setting_model->get_mch_scan_rule($mchId);
        if (! isset($scanRule)) {
            $scanRule = new stdClass();
            $scanRule->times = 12;
            $scanRule->unit = 'i';
        }

        $us_re_key = "user_scan_limit_" . $userId . "_";
        switch ($scanRule->unit) {
            case 'y':
                $us_re_key .= date('Y');
                $endTime = strtotime(date('Y-1-1 00:00:00'));
                $scanError = '扫码超出了每年数量限制，请稍后再试！';
                break;
            case 'm':
                $us_re_key .= date('Y_m');
                $endTime = strtotime(date('Y-m-1 00:00:00'));
                $scanError = '扫码超出了每月数量限制，请稍后再试！';
                break;
            case 'd':
                $us_re_key .= date('Y_m_d');
                $endTime = strtotime(date('Y-m-d 00:00:00'));
                $scanError = '扫码超出每天数量限制，请明天再扫！';
                break;
            case 'h':
                $us_re_key .= date('Y_m_d_H');
                $endTime = strtotime(date('Y-m-d H:00:00'));
                $scanError = '扫码超出每小时数量限制，请稍后再扫！';
                break;
            case 'i':
                $us_re_key .= date('Y_m_d_H_i');
                $endTime = strtotime(date('Y-m-d H:i:00'));
                $scanError = '扫码超出每分钟数量限制，请稍后再扫！';
                break;
            default:
            	$scanError = '扫码过于频繁，请稍后再试！';
            	break;
        }

        $redis = parent::getRedisClient();
        if (! $redis->get($us_re_key)) {
        	$this->load->model('Scan_log_model','scan_log');
            $scanCount = 0;
            $scanRecords = $this->scan_log->get_scaninfo_by_scan_rule($userId, $endTime);
            if (isset($scanRecords)) {
            	$scanCount += $scanRecords->count;
            }
            $logRecords = $this->scan_log->get_commonlog_by_scan_rule($userId, $endTime);
            if (isset($logRecords)) {
            	$scanCount += $logRecords->count;
            }
            $redis->set($us_re_key, $scanCount);
            $redis->expire($us_re_key, 60 * 60 * 24);
        }
        $count = $redis->get($us_re_key);
        if ($count >= $scanRule->times) {
            $msgExt = '<br/><br/><font style="color:#999">注：扫描别人的码，也会记入扫码次数。</font>';
            throw new Exception($scanError . $msgExt, 1);
        }
        $redis->incr($us_re_key);
    }

    /**
     * 校验当前用户扫描他人的乐码的次数
     *
     * @param $userId 当前企业编号
     * @param $userId 当前扫码用户编号
     * @return void
     */
    public function checkScanOtherTimes($mchId, $userId) {
        $redis = parent::getRedisClient();
        $scanTimes = $redis->get('scan_other_times_' . $userId);
        if ($scanTimes) {
            $scanTimes += 1;
            $redis->incr('scan_other_times_' . $userId);
        } else {
            $result = $this->db->where('logType', 6)->where('mchUserId', $userId)
            	->get('users_common_log')->result();
            $scanTimes = count($result);
            $scanTimes += 1;
            $redis->set('scan_other_times_' . $userId, $scanTimes);
        }
        $redis->expire('scan_other_times_' . $userId, 3600 * 2);
        $configRow = $this->db->where('mchId', $mchId)->get('mch_scan_rules')->row();
        if (! $configRow) {
            return FALSE;
        }
        if ($configRow->scan_other_times === 0 || $configRow->scan_other_times === '0') {
            return FALSE;
        }
        if (! isProd()) {
            error("check-scan-other-times -> this times is: ". $scanTimes . ", limit times is: ". $configRow->scan_other_times);
        }
        if ($scanTimes > $configRow->scan_other_times) {
            throw new Exception("扫描他人的码已超过企业次数限制，已做封号处理", 110133);
        }
        return FALSE;
    }

    /**
     * 解析乐码
     * 
     * @param $lecode 乐码
     * @return object
     */
    public function deLecode($lecode) {
    	$this->load->library('common/code_encoder');
        $code_ret = $this->code_encoder->decode($lecode);
        if ($code_ret->errcode != 0) {
            throw new Exception($code_ret->errmsg, 110109);
        }
        return $code_ret->result;
    }

}