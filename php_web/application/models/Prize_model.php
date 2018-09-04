<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * 
 * @author shizq
 *
 */
class Prize_model extends CI_Model {
	
	/**
	 * 获取用户账户所有的乐券
	 * 
	 * @param string $openid
	 * @param string $role
	 */
	public function getCards($openid, $role) {
		debug('get cards - begin');
		debug('params: '. json_encode(func_get_args()));
		$sql = "SELECT t1.num, t1.cardId, t2.title, t2.goodsId, t2.allowTransfer, t2.cardType, t2.pointQuantity 
				FROM user_cards_account t1
				JOIN cards              t2 ON t2.id = t1.cardId
				JOIN %s                 t3 ON t3.id = t1.userId
				JOIN cards_group		t4 ON t4.id = t2.parentId	
				WHERE t3.openid = ? AND t1.role = ? AND t2.rowStatus = 0
				ORDER BY t1.num DESC";
		if ($role == ROLE_SALESMAN) {
			$sql = sprintf($sql, 'salesman');
			$cards = $this->db->query($sql, [$openid, ROLE_SALESMAN])->result();
		} else if ($role == ROLE_WAITER) {
			$sql = sprintf($sql, 'waiters');
			$cards = $this->db->query($sql, [$openid, ROLE_WAITER])->result();
		} else if ($role == ROLE_CONSUMER) {
			$sql = sprintf($sql, 'users');
			$cards = $this->db->query($sql, [$openid, ROLE_CONSUMER])->result();
		} else {
			throw new Exception("不支持的角色类型", 1);
		}
		debug('result: '. json_encode($cards));
		if (empty($cards)) {
			$cards = [(object)['cardId' => 0, 'num' => 0, 'title' => 'empty', 'allowTransfer' => 0]];
		}
		return $cards;
	}

	/**
	 * 获取用户的组合乐券
	 * 
	 * @param string $openid
	 * @param int $role
	 * @throws Exception
	 */
	public function getGroupBonusCards($openid, $role) {
		$sql = "SELECT t1.id
				FROM %s t1 WHERE t1.openid = ?";
		if ($role == ROLE_SALESMAN) {
			$sql = sprintf($sql, 'salesman');
			$user = $this->db->query($sql, [$openid])->row();
		} else if ($role == ROLE_WAITER) {
			$sql = sprintf($sql, 'waiters');
			$user = $this->db->query($sql, [$openid])->row();
		} else if ($role == ROLE_CONSUMER) {
			$sql = sprintf($sql, 'users');
			$user = $this->db->query($sql, [$openid])->row();
		} else {
			throw new Exception("不支持的角色类型", 1);
		}
		if (! isset($user)) {
			session_destroy();
			return [];
		}
		$sql = "SELECT ifnull(t2.num, 0) num, t1.id cardId, t3.id parentId, t3.title groupName, 
			t3.bonusQuantity, t1.cardType 
			FROM user_cards_account t2 
			RIGHT JOIN cards 		t1 ON t2.cardId = t1.id AND t2.userId = ? AND t2.role = ?
			JOIN cards_group 		t3 ON t3.id = t1.parentId 
			WHERE t3.hasGroupBonus = 1 AND t1.rowStatus = 0 ORDER BY t2.num DESC";
		$cards = $this->db->query($sql, [$user->id, $role])->result();
		$cardsTempArr = [];
		
		// 去掉parentId重复的子乐券
		foreach ($cards as $card) {
			if (array_key_exists( '_' . $card->parentId . '-', $cardsTempArr)) {
				if ($cardsTempArr['_' . $card->parentId . '-']->num > $card->num) {
					$cardsTempArr['_' . $card->parentId . '-'] = $card;
				}
			} else {
				if ($card->num > 0) { // 如果用户账户中对某一券组中的乐券全部为0，那么不显示此组合乐券
					$cardsTempArr['_' . $card->parentId . '-'] = $card;
				}
			}
		}
		$cards = [];
		
		// 将关联数组转为索引数组
		foreach ($cardsTempArr as $card) {
			$cards[] = $card;
		}
		if (empty($cards)) {
			$cards = [(object)['num' => 0, 'cardId' => 0, 'groupName' => 'empty', 'parentId' => 0]];
		}
		
		function mySort($first, $second) {
			if ($first->num > $second->num) {
				return 0;
			}
			if ($first->num > $second->num) {
				return 1;
			} else {
				return -1;
			}
		}
		uasort($cards, 'mySort');
		return $cards;
	}

	/**
	 * 用户线上兑换乐券
	 * 
	 * @param int $userId
	 * @param int $cardId
	 * @param int $addressId
	 * @deprecated 新增了乐券兑换积分的方式，改线上兑换功能已废弃
	 */
	public function settleCards($userId, $cardId, $addressId) {
		debug("settleCards - begin");
		debug("params:". json_encode(func_get_args()));
		// 1.查询用户的乐券记录
		$sql = "SELECT t1.num, t2.title, t2.goodsId
				FROM user_cards_account t1
				JOIN cards              t2 ON t2.id = t1.cardId
				WHERE t1.userId = ? AND t1.role = 0 AND t1.cardId = ? AND t2.rowStatus = 0";
		$row = $this->db->query($sql, [$userId, $cardId])->row();
		if (! isset($row)) {
			throw new Exception("乐券不存在", 1);
		}

		// 2.查询用户乐券对应的详细
		$sql = "SELECT count(*) num
				FROM user_cards t1
				WHERE t1.userId = ? AND t1.role = 0 AND t1.cardId = ? AND t1.status = 0 AND t1.sended = 1";
		$num = $this->db->query($sql, [$userId, $cardId])->row()->num;
		if ($row->num !== $num) {
			debug("user_cards_account num is: $row->num, user_cards count is: $num");
			throw new Exception("乐券数据异常", 1);
		}
		if ($num == 0) {
			debug("user_cards_account num is: $num");
			throw new Exception("没有可兑换的乐券", 1);
		}
		debug("num is: ". $num);

		// 3.查询礼品对应的第三方虚拟平台，如果不是线上支付，跳转到地址选择界面
		$sql = "SELECT id, viralPlatform, viralAmount, isViral, goodsName, createOrder FROM mall_goods t1 WHERE id = ?";
		$goodsDetail = $this->db->query($sql, [$row->goodsId])->row();
		$user = $this->db->select('mchId, openId, id')->where('id', $userId)->get('users')->row();

		if (! $goodsDetail->isViral || $goodsDetail->createOrder) {
			// 生成兑换订单
			$this->load->model('Mall_model', 'mall');
			$mall = $this->mall->get_mall($user->mchId);
			if ($goodsDetail->isViral && $goodsDetail->viralPlatform == 1 && ($goodsDetail->viralAmount * $num < 100)) {
				throw new Exception('兑换金额不能小于最低限额', 1);
			}
			if (is_null($addressId)) {
				throw new Exception($mall->id, 10110);
			}
			
			try {
				// DB Session - begin
				$this->db->trans_begin();
				// 4.更新用户的乐券状态
				$this->changeUserCardsStatus($userId, $cardId);

				// 5.生成用户兑换订单
				$this->load->model('Mall_mobile_model', 'mall_mobile');
				$goodsObject = [
					'id'            => $goodsDetail->id,
					'goodsName'     => $goodsDetail->goodsName,
					'amount'        => $num,
					'cardId'        => $cardId,
					'cardName'      => $row->title,
					'isViral'       => $goodsDetail->isViral,
					'viralPlatform' => $goodsDetail->viralPlatform,
					'viralAmount'   => $goodsDetail->viralAmount
				];
				$settleResult = $this->mall_mobile->create_order_for_card($mall->id, $userId, $goodsObject, $addressId);

				$settleInfo['platform'] = '欢乐扫礼品商城';
				$settleInfo['amount'] = $goodsObject['amount'];
				$settleInfo['event_time'] = date('Y-m-d H:i:s');
				$settleInfo['card_title'] = $row->title;
				$settleInfo['type'] = '张';
				$settleInfo['online'] = false;
				$settleInfo['mall_id'] = $mall->id;

				// 6.写入兑换记录
				$this->saveTransLog($userId, $cardId, $num);
				
				// DB Session - end
				$this->db->trans_commit();
			} catch (Exception $e) {
				$this->db->trans_rollback();
				throw new Exception($e->getMessage(), $e->getCode());
			}
		} else {
			if (! isset($goodsDetail->viralAmount)) {
				$goodsDetail->viralAmount = 0;
			}
			$params = [
				'mchId'  => $user->mchId,
				'userId' => $user->id,
				'openid' => $user->openId,
				'amount' => $goodsDetail->viralAmount * $num,
				'desc'   => '乐券现金兑换',
				'action' => 2
			];
			try {
				// DB Session - begin
				$this->db->trans_begin();
				if (count(config_item('sender')) <= intval($goodsDetail->viralPlatform)) {
	                debug("goods viralPlatform is: $goodsDetail->viralPlatform");
	                throw new Exception("此乐券无法兑换", 1);
	            }
				$senderName = config_item('sender')[$goodsDetail->viralPlatform];
				$classz = new ReflectionClass($senderName);
				$sender = $classz->newInstance();
				$insertId = $settleResult = $sender->requestThirdPlatform($params);

				$settleInfo['platform'] = $sender->getSettlerName();
				$settleInfo['amount'] = $params['amount'] * 0.01;
				$settleInfo['event_time'] = date('Y-m-d H:i:s');
				$settleInfo['card_title'] = $row->title;
				$settleInfo['type'] = '元';
				$settleInfo['online'] = true;
				$settleInfo['mall_id'] = -1; // 如果是在线兑换，不用生成订单，商城的编号就不需要

				// 5.更新用户的乐券状态
				$this->changeUserCardsStatus($userId, $cardId);

				// 6.写入兑换记录
				$this->saveTransLog($userId, $cardId, $num);
				
				// DB Session - end
				$this->db->trans_commit();
			} catch (Exception $e) {
				$this->db->trans_rollback();
				throw new Exception($e->getMessage(), $e->getCode());
			}
		}
		
		debug("settleCards - end");
		return ['settleResult' => $settleResult, 'settleInfo' => $settleInfo];
	}

	/**
	 * 用户线上兑换单种乐券
	 * 
	 * @param $userId
	 * @param $cardId
	 * @return void
	 */
	public function settleSingleCards($userId, $cardId) {
		debug("settleSingleCards - begin");
		debug("params:". json_encode(func_get_args()));

		// 1.查询用户的乐券记录
		$sql = "SELECT t1.num, t2.title, t2.goodsId FROM user_cards_account t1 JOIN cards t2 ON t2.id = t1.cardId
			WHERE t1.userId = ? AND t1.role = 0 AND t1.cardId = ? AND t2.rowStatus = 0";
		$row = $this->db->query($sql, [$userId, $cardId])->row();
		if (! isset($row)) {
			throw new Exception("用户乐券不存在", 1);
		}

		// 2.查询用户乐券对应的详细
		$sql = "SELECT count(*) num FROM user_cards t1 WHERE t1.userId = ? AND t1.role = 0 AND t1.cardId = ? 
			AND t1.status = 0 AND t1.sended = 1";
		$num = $this->db->query($sql, [$userId, $cardId])->row()->num;
		if ($row->num !== $num) {
			debug("user_cards_account num is: $row->num, user_cards count is: $num");
			throw new Exception("乐券数据异常", 1);
		}
		if ($num == 0) {
			debug("user_cards_account num is: $num");
			throw new Exception("没有可兑换的乐券", 1);
		}
		debug("num is: ". $num);

		// 3.查询乐券可兑换的种类（目前只有积分）
		$sql = "SELECT cardType, pointQuantity, allowTransfer FROM cards WHERE id = ? AND rowStatus = 0";
		$card = $this->db->query($sql, [$cardId])->row();
		if (! isset($card)) {
			throw new Exception("找不到此乐券", 1);
		}
		debug("card:". json_encode($card));

		// DB Session - begin
		$this->db->trans_begin();
		
		// 4.给用户发放兑换的物品（目前只有积分）
		if ($card->cardType === 2 || $card->cardType === '2') {
			if ($card->pointQuantity <= 0) {
				$this->db->trans_rollback();
				throw new Exception("此乐券兑换的积分数量为0，暂不支持0积分兑换", 1);
			}
			$this->handoutPoints($userId, $card->pointQuantity * $num);
		} else {
			$this->db->trans_rollback();
			if ($card->allowTransfer == 1) {
				throw new Exception("此乐券不能线上兑换，请使用转移操作", 1);
			} else {
				throw new Exception("此乐券目前不能兑换", 1);
			}
		}
		
		// 5.更新用户的乐券状态
		$this->changeUserCardsStatus($userId, $cardId);

		// 6.写入乐券的使用记录
		$this->saveTransLog($userId, $cardId, $num);
		
		// DB Session - end
		$this->db->trans_commit();

		return ['settled_num' => $num];
	}

	/**
	 * 用户线上兑换组合乐券
	 *
	 * @param $userId
	 * @param $cardGroupId
	 * @return void
	 */
	public function settleGroupCards($userId, $cardGroupId) {
		debug("settleGroupCards - begin.");
		debug("params: ". json_encode(func_get_args()));
		// 1.获取用户该卡组可兑换的数量
		$sql = "SELECT min(ifnull(num, 0)) canSettleNum FROM user_cards_account t1 
			RIGHT JOIN cards t2 ON t2.id = t1.cardId AND t1.userId = ? AND t1.role = 0
			WHERE t2.parentId = ? AND t2.rowStatus = 0";
		$resultRow = $this->db->query($sql, [$userId, $cardGroupId])->row();
		$canSettleNum = $resultRow->canSettleNum;
		debug("canSettleNum is: ". $canSettleNum);
		if ($canSettleNum <= 0) {
			throw new Exception('你还没有集齐此券组，无法兑换', 1);
		}
		
		// 2.用户乐券账户属于此卡组的乐券减去该数量
		$this->load->model('Card_model', 'card');
		$cards = $this->card->getUserGroupCards($cardGroupId, $userId);
		$cardIds = [];
		foreach ($cards as $card) {
			$cardIds[] = $card->id;
			$sql = "SELECT count(*) num FROM user_cards t1 WHERE t1.userId = ? AND t1.role = 0 AND t1.cardId = ?
			AND t1.status = 0 AND t1.sended = 1";
			$num = $this->db->query($sql, [$userId, $card->id])->row()->num;
			if ($num < $canSettleNum) {
				debug('settleGroupCards: count cardId = '. $card->id .' is '. $num);
				throw new Exception('用户乐券数据异常，无法兑换', 1);
			}
		}
		debug("cardIds: ". json_encode($cardIds));
		
		try {
			// DB Session - begin
			$this->db->trans_begin();
			$sql = "UPDATE user_cards_account SET num = num - ? WHERE userId = ? AND num > 0 AND role = 0 AND cardId in (".implode(',', $cardIds).")";
			$success = $this->db->query($sql, [$canSettleNum, $userId]);
			
			// 3.记录用户乐券使用记录
			foreach ($cardIds as $cardId) {
				$sql = "UPDATE user_cards t1 SET t1.status = 2
					WHERE t1.status = 0 AND t1.userId = ? AND t1.role = 0 AND t1.cardId = ? AND t1.sended = 1 LIMIT ?";
				$success = $this->db->query($sql, [$userId, $cardId, intval($canSettleNum)]) && $success;
				if (! $success) {
					$this->db->trans_rollback();
					throw new Exception("发生未知错误", 1);
				}
				$this->saveTransLog($userId, $cardId, $canSettleNum);
			}
			
			// 4.发放对应的奖励
			$cardGroup = $this->card->get_cardgroup($cardGroupId);
			if ($cardGroup->bonusType === 0 || $cardGroup->bonusType === '0') {
				if ($cardGroup->bonusQuantity <= 0) {
					$this->db->trans_rollback();
					throw new Exception("此卡组兑换的积分数量为0，暂不支持0积分兑换", 1);
				}
				$this->handoutPoints($userId, $cardGroup->bonusQuantity * $canSettleNum);
			} else {
				$this->db->trans_rollback();
				throw new Exception("此乐券不能线上兑换，请使用转移操作", 1);
			}
			// DB Session - end
			$this->db->trans_commit();
			return ['settled_num' => $canSettleNum];
		} finally {
			debug("settleGroupCards - end.");
		}
	}

	/*
	 * 发放积分给用户
	 * 
	 * @param $userId 用户编号
	 * @param $amount 积分数量
	 * @return void
	 */
	private function handoutPoints($userId, $amount) {
		$user = $this->db->select('mchId, openId, id')->where('id', $userId)->get('users')->row();
		$userPoint = [
            'userId'	=> $userId,
            'mchId'		=> $user->mchId,
            'pointsId'	=> -1,
            'amount'	=> $amount,
            'getTime'	=> time(),
            'scanId'	=> -2,
            'code'		=> 'from card trans',
            'instId'	=> -1,
            'sended'	=>  1
        ];
        $success = $this->db->insert('user_points', $userPoint);
        $sql = "INSERT INTO user_points_accounts (userId, mchId, amount, role)
            VALUES ($userId, $user->mchId, IFNULL(amount, 0) + $amount, 0)
            ON DUPLICATE KEY UPDATE amount = IFNULL(amount, 0) + $amount";
        $success = $this->db->query($sql) && $success;
        if (! $success) {
        	$this->db->trans_rollback();
        	throw new Exception("发生未知错误", 1);
        }
	}


	/*
	 * 生成乐券兑换记录
	 * 
	 * @param int $userId
	 * @param int $cardId
	 */
	private function saveTransLog($userId, $cardId, $num) {
		$this->load->model('Transfer_model', 'transfer');
		$logId = $this->transfer->generate_trans_log($userId, 0, -1, -1, $cardId, 2, $num);
		if (! $logId) {
			$this->db->trans_rollback();
			debug("generate_trans_log fail");
			throw new Exception("兑换失败", 1);
		}
	}

	/*
	 * 更新用户乐券状态
	 * 
	 * @param int $userId
	 * @param int $cardId
	 */
	private function changeUserCardsStatus($userId, $cardId) {
		debug("change user cards status - begin");
		debug("params:". json_encode(func_get_args()));
		
		// 更新user_cards_account
		$sql = "UPDATE user_cards_account t1 SET t1.num = 0 
			WHERE t1.role = 0 AND t1.userId = ? AND t1.cardId = ?";
		$success = $this->db->query($sql, [$userId, $cardId]);
		
		// 更新user_cards
		$sql = "UPDATE user_cards t1 SET t1.status = 2 
			WHERE t1.status = 0 AND t1.userId = ? AND t1.role = 0 AND t1.cardId = ? AND t1.sended = 1";
		$success = $this->db->query($sql, [$userId, $cardId]) && $success;
		if (! $success) {
			$this->db->trans_rollback();
			debug("update user_cards fail");
			throw new Exception("兑换失败", 1);
		}	
		debug("change user cards status - end");
	}
}