<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * 
 * @author shizq
 * 
 */
class Appif extends Mobile_Controller {

	public function ranking($apiName = NULL) {
		if (! isset($apiName)) {
			$this->index();
		} else {
			$this->{$apiName}();
		}
	}

	/**
	 * ----------------------------------------------------
	 * 获取排行数据
	 * /ranking
	 *
	 * @param int $user_id 当前用户编号
	 * @param int $mch_id 当前企业编号
	 * @param int $city_code 城市编号
	 * @param int $page 分页
	 * @param int $page_size 分页大小
	 * @param int $range 获取范围
	 * @return json
	 */
	public function index() {
		$user_id   = $this->input->get('user_id');
		$mch_id    = $this->input->get('mch_id');
		$city_code = $this->input->get('city_code');
		$page      = $this->input->get('page');
		$page_size = $this->input->get('page_size');
		$range     = $this->input->get('range');
		switch ($range) {
			case '1': // 获取今天
				$range = date('Y-m-d');
				break;
			case '2': // 本周
				$date = new DateTime();
				$date->modify('this week');
				$range = $date->format('Y-m-d');
				break;
			case '3': // 本月
				$range = date('Y-m') . '-01';
				break;
			case '4': // 今年
				$range = date('Y') . '-01-01';
				break;
			default: // 默认获取所有
				$rage = '1970-01-01';
				break;
		}
		try {
			// ----------------------------------------------------
			// 获取用户自己的排行名次
			/* $this->load->model('Ranking_model', 'ranking');
			$user = new stdClass();
			$user->id = $user_id;
			$user->mchId = $mch_id;
			$this->get_rank_by_user_id($user, 'all'); */
			$sql = "SELECT userId, scanNum, ifnull(nickName, '红码用户') nickname, headimgurl, rank_id FROM 
					(SELECT * FROM 
						(SELECT @rownum1 := @rownum1 + 1 AS rank_id, userId, scanNum FROM 
							(SELECT userId, sum(scanNum) scanNum, @rownum1 := 0
								FROM rpt_user_rank 
								WHERE 1 = 1 AND mchId = %d %s AND theDate >= '$range'
								GROUP BY userId 
								ORDER BY scanNum DESC
							) tmp1
						) tmp2 WHERE userId = %d
					) tmp3 INNER JOIN users ON tmp3.userId = users.id";
			if ($city_code) {
				$sql = sprintf($sql, $mch_id, 'AND cityCode = ' . $city_code, $user_id);
			} else {
				$sql = sprintf($sql, $mch_id, '', $user_id);
			}
			
			$self_data = $this->dbhelper->serve($sql);

			// ----------------------------------------------------
			// 获取排行数据列表
			$sql = "SELECT userId, scanNum, ifnull(nickName, '红码用户') nickname, headimgurl FROM 
						(SELECT userId, sum(scanNum) scanNum
							FROM rpt_user_rank 
							WHERE 1 = 1 AND mchId = %d %s AND theDate >= '$range'
							GROUP BY userId 
							ORDER BY scanNum 
							DESC LIMIT %d, %d
						) tmp1 LEFT JOIN users ON tmp1.userId = users.id";
			if ($city_code) {
				$sql = sprintf($sql, $mch_id, 'AND cityCode = ' . $city_code, ($page - 1) * $page_size, $page_size);
			} else {
				$sql = sprintf($sql, $mch_id, '', ($page - 1) * $page_size, $page_size);
			}
			
			$ranking_data = $this->dbhelper->serve($sql);

			// ----------------------------------------------------
			// 获取参与扫码的总数量
			$sql = "SELECT count(userId) AS total FROM 
						(SELECT userId
							FROM rpt_user_rank 
							WHERE 1 = 1 AND mchId = %d %s AND theDate >= '$range'
							GROUP BY userId
						) tmp1";
			if ($city_code) {
				$sql = sprintf($sql, $mch_id, 'AND cityCode = ' . $city_code);
			} else {
				$sql = sprintf($sql, $mch_id, '');
			}
			
			$total = $this->dbhelper->serve($sql);

			$i = 1;
			if (! isset($ranking_data)) {
				$ranking_data = [];
			}
			foreach ($ranking_data as &$ranking_item) {
				$ranking_item->rank_id = ($page - 1) * $page_size + $i;
				$i++;
			}
			if (! $self_data) {
				$self_data = [
					[]
				];
			}
			if (! isset($total)) {
				$obj = new stdClass();
				$obj->total = 0;
				$total = [$obj];
			}
			$data = [
				'myself' => $self_data[0],
				'ranking_list' => $ranking_data,
				'total_num' => $total[0]->total
			];
			$this->ajaxResponse($data);
		} catch (Exception $e) {
			$this->ajaxResponse([], $e->getMessage(), $e->getCode());
		}
	}

	public function turntable($apiName = NULL) {
		if (! isset($apiName)) {
			$this->ajaxResponseFail('403 Forbidden', 403, 403);
		} else {
			$this->{$apiName}();
		}
	}

	public function areas() {
		try {
			$this->load->model('Ranking_model', 'ranking');
			$provinces = $this->ranking->areas();
			$this->ajaxResponse($provinces);
		} catch (Exception $e) {
			$this->ajaxResponse([], $e->getMessage(), $e->getCode());
		}
	}

	public function area_code() {
		$cityName = $this->input->get('city_name');
		try {
			$this->load->model('Ranking_model', 'ranking');
			$code = $this->ranking->address($cityName);
			$this->ajaxResponse($code);
		} catch (Exception $e) {
			$this->ajaxResponse([], $e->getMessage(), $e->getCode());
		}
	}

	// --------------------------------
	// 获取大转盘每项内容
	public function bonus_item() {
		$appPath = $this->input->get('app_path');
		$mchId   = $this->input->get('mch_id');
		try {
			$this->load->model('turntable_model', 'turntable');
			$items = $this->turntable->bonusItem($mchId, $appPath);
			$this->ajaxResponse(['bonus_item' => $items]);
		} catch (Exception $e) {
			$this->ajaxResponse([], $e->getMessage(), $e->getCode());
		}
	}

	// --------------------------------
	// 获取用户的奖品
	public function user_prize() {
		$userId  = $this->input->get('user_id');
        $appPath = $this->input->get('app_path');
		$this->load->model('Turntable_model', 'turntable');
		try {
			$prize = $this->turntable->getUserPrize($userId, $appPath);
			$this->ajaxResponse($prize);
		} catch (Exception $e) {
			$this->ajaxResponse([], $e->getMessage(), $e->getCode());
		}
	}

	// --------------------------------
    // 发放抽到的奖品
    public function send_prize() {
        $userId   = $this->input->post('user_id');
        $appPath  = $this->input->post('app_path');
        $phone    = $this->input->post('phone');
        $birthday = $this->input->post('birthday');
        $areaCode = $this->input->post('areaCode');
        $profile  = [
            'phone'     => $phone,
            'birthday'  => $birthday,
            'area_code' => $areaCode
        ];
        $this->load->model('Turntable_model', 'turntable');
        try {
            $this->turntable->saveProfileSendPrize($userId, $appPath, $profile);
            $this->ajaxResponse();
        } catch (Exception $e) {
            $this->ajaxResponse([], $e->getMessage(), $e->getCode());
        }
    }

	// --------------------------------
	// 启动转盘
	public function start() {
        $userId  = $this->input->get('user_id');
        $appPath = $this->input->get('app_path');
		$this->load->model('Turntable_model', 'turntable');
		try {
			$result = $this->turntable->start($userId, $appPath);
			$this->ajaxResponse($result);
		} catch (Exception $e) {
			$this->ajaxResponse([], $e->getMessage(), $e->getCode());
		}
	}

}