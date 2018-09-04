<?php
/**
 * 大转盘app model
 * 
 * @author shizq
 */
class Turntable_model extends CI_Model {

	/**
	 * 获取本次转盘转动获得的奖项
	 * 
	 * @param $userId 当前用户编号
	 * @param $appPath 应用路径（名称）
	 * @return array
	 */
	function start($userId, $appPath) {
		info("turntable - begin");
		$rewards = $this->getUserPrize($userId, $appPath);
		if (! empty($rewards)) {
            if (isProd()) {
                throw new Exception("你已经抽过奖了", 1);
            }
		}
		$this->load->model('user_model', 'user');
		$this->load->model('hls_app_model', 'hls_app');
		$user = $this->user->get($userId);
		$appInst = $this->hls_app->getAppInst($user->mchId, $appPath);
		$activity = (object)[
            'mchId'           => $appInst->mchId,
            'name'            => $appInst->name,
            'role'            => 0,
            'subscribeNeeded' => 0
        ];
        $scaninfo = (object)[
            'userId'          => $userId,
            'mchId'           => $appInst->mchId,
            'id'              => -1,
            'instId'          => $appInst->id,
            'over'            => 1,
            'rewardTable'     => NULL,
            'rewardId'        => NULL,
            'openId'          => $user->openid,
            'code'            => 'HLS_APP',
            'batchId'         => -1,
            'activityId'      => -1,
            'geoId'           => 0
        ];
        $appConfig = json_decode($appInst->config);
        if ($appConfig->strategy_type == 3) {
        	$this->load->model('red_packet_model', 'red_packet');
            $result = $this->red_packet->try_mixstrategy($appConfig->strategy_id, $activity, $scaninfo, 1);
        } else {
        	throw new Exception("转盘策略配置不正确", 1);
        }
		info("Turntable - end");
		return $result;
	}

	/**
	 * 获取转盘奖项
	 * 
	 * @param $userId 当前用户编号
	 * @param $appPath 应用路径（名称）
	 * @return array
	 */
	function bonusItem($mchId, $appPath) {
        $this->load->model('mixstrategy_model', 'mixstrategy');
        $this->load->model('hls_app_model', 'hls_app');
        $appInst = $this->hls_app->getAppInst($mchId, $appPath);
        $appConfig = json_decode($appInst->config);
        if (! isset($appConfig->strategy_type) || $appConfig->strategy_type != 3) {
        	throw new Exception("转盘策略配置不正确", 1);
        }
        if (! $appConfig->strategy_id) {
        	error("appInst strategyId does not set, which id is: $appInst->id");
			throw new Exception('没有指定转盘策略', 1);
		}
        $mixStrategySub = $this->mixstrategy->get_detail_by_pid($appConfig->strategy_id, $appInst->mchId);
		if (! $mixStrategySub) {
			error("strategy does not exists, which id is: $appConfig->strategy_id");
			throw new Exception('转盘项策略不存在', 1);
		}
		return $mixStrategySub;
	}

	/**
	 * 奖品发放
	 *
	 * @param $userId 当前用户编号
	 * @param $appPath 应用路径（名称）
	 * @param $profile 用户信息
	 * @return void
	 */
	function saveProfileSendPrize($userId, $appPath, $profile) {
		info("send prize - begin");
		$this->load->model('user_model', 'user');
		$this->load->model('hls_app_model', 'hls_app');
		$user = $this->user->get($userId);
		if (! $user) {
			throw new Exception('用户不存在', 1);
		}
		$this->load->model('app_model', 'app');
		$appInst = $this->hls_app->getAppInst($user->mchId, $appPath);
		$sap = $this->app->send_app_packtets($user, $appInst);
        $sac = $this->app->send_app_cards($user, $appInst);
        $sao = $this->app->send_app_points($user, $appInst);
        if($sap > 0 || $sac > 0 || $sao > 0) {
        } else {
        	throw new Exception('发放失败', 1);
        }
		info("Send prize - end");
	}


	/**
	 * 获取用户的奖品记录
	 * 
	 * @param $userId 当前用户编号
	 * @param $appPath 应用路径（名称）
	 * @return array
	 */
	function getUserPrize($userId, $appPath) {
		info("get user prize start ");
		$this->load->model('user_model', 'user');
		$this->load->model('hls_app_model', 'hls_app');
		$user = $this->user->get($userId);
		if (! $user) {
			debug("user not found which id is: $userId");
			throw new Exception('当前用户不存在', 1);
		}
        $appInst = $this->hls_app->getAppInst($user->mchId, $appPath);
		$this->load->model('app_model', 'app');
        $record1 = $this->app->get_result_rp($userId, $appInst);
        $record2 = $this->app->get_result_card($userId, $appInst);
        $record = [];
        foreach ($record1 as $key => $value) {
            $thisV = $value;
            $thisV->dataType = 0;
            array_push($record, $thisV);
        }
        foreach ($record2 as $key => $value) {
            $thisV = $value;
            $thisV->dataType = 2;
            array_push($record, $thisV);
        }
		info("Get user prize - end");
		return $record;
	}
}