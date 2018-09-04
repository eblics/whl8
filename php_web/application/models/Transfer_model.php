<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 *
 * @author shizq
 *
 */
class Transfer_model extends CI_Model {

	const TABLE_TICKETS = 'obj_tickets';
	const TABLE_USER_CARDS_ACCOUNT = 'user_cards_account';
	const TABLE_USER_CARDS = 'user_cards';
	const TABLE_USER_HC_ACCOUNT = 'user_hc_account';
	const TABLE_USER_TRANSFERS = 'user_transfers';

	/**
	 *
	 * @var 票据过期时间，单位：秒
	 */
	const TIME_OUT_VALUE = 60;

	/**
	 * 生成转移票据
	 *
	 * @param int $obj_id     转移对象id
	 * @param int $objType    转移对象type
	 * @return int 确认状态
	 */
	public function generate_ticket($obj_id, $obj_type) {
		$this->db->where('expireTime <', time());
		$this->db->delete(self::TABLE_TICKETS);

		$data = [
			'objId'      => $obj_id,
			'objType'    => $obj_type,
			'expireTime' => time() + self::TIME_OUT_VALUE
		];

		$card = $this->db->where('id', $obj_id)->get('cards')->row();
		if (! isset($card)) {
			throw new Exception("乐券不存在", 1);
		}
		if (! $card->allowTransfer) {
			throw new Exception("该乐券不可转移", 1);
		}

		// 生成转移票据
		$this->db->trans_start();
		$result = $this->db->insert(self::TABLE_TICKETS, $data);
		$result_id = $this->db->insert_id();
		$ticket = sha1($result_id . time());
		$update = ['ticket' => $ticket];
		$this->db->where('id', $result_id);
		$this->db->update(self::TABLE_TICKETS, $update);
		$this->db->trans_complete();
		$data['ticket'] = $ticket;

		return $data;
	}

	/**
	 * 将扫描的二维码标记为已扫描状态
	 *
	 * @param string $ticket 转移票据
	 */
	public function tag_scaned($ticket, $role, $user_id) {
		// 1.查找数据库中的ticket,判断是否在有效期内
		$ticket_info = $this->db->query('SELECT * FROM obj_tickets WHERE ticket = ?
			AND expireTime > ? AND scaned = 0 ORDER BY expireTime DESC',
			[$ticket, time()])->row();
		if (! $ticket_info) {
			throw new Exception('您扫描的二维码不存或已过期');
		}
		if (! isset($user_id)) {
			throw new Exception('数据异常，请重新扫描');
		}
		$this->db->set('scaned', TICKET_SCANED);
		$this->db->set('userId', $user_id);
		$this->db->set('role', $role);
		$this->db->where('ticket', $ticket);
		$this->db->update(self::TABLE_TICKETS);

		$this->session->ticket = $ticket;
		$this->session->role = $role;

		return ['type' => SCAN_RES_TRANSFER];
	}

	/**
	 * 确认转移操作
	 *
	 * @param $ticket 生成的转移券信息
	 * @param $num 转移数量
	 * @param $oprator 转出人
	 * @param $role 被转移人的角色类型
	 * @return array
	 */
	public function confirm_trans($ticket, $num, $oprator, $role) {
		$ticket_obj = $this->db->where('ticket', $ticket)->get(self::TABLE_TICKETS)->row_array();

		if (! $ticket_obj or $ticket_obj['expireTime'] < time()) {
			throw new Exception('操作超时，或二维码已过期', 1);
		}

		if ($oprator->id == $ticket_obj['userId'] && $ticket_obj['role'] == $role) {
			throw new Exception('不能向自己兑换', 1);
		}

		if ($ticket_obj['objType'] == OBJ_TYPE_CARD) {
			/**
			 * 删除被转移用户账户中的商品
			 */
			/**
			 * 开始数据库事务
			 */
			$this->db->trans_start();
            //增加for update 以锁定行，避免重复交易
			$sql = 'SELECT num FROM user_cards_account
					WHERE userId = ? AND role = ? AND cardId = ? FOR UPDATE';
			$condition = [$oprator->id, $role, $ticket_obj['objId']];
			$target_left = $this->db->query($sql, $condition)->row();

			if (! $target_left) {
				throw new Exception('找不到对应的兑换内容', 1);
			}
			if ($target_left->num < $num or $num <= 0) {
				throw new Exception('无法兑换当前设置的数量', 1);
			}

			// 将转出者的乐券数量减去指定的数量
			$new_left = $target_left->num - $num;

			$this->db->set('confirmed', TICKET_CONFIRMED);
			$this->db->where('ticket', $ticket);
			$this->db->update(self::TABLE_TICKETS);

			$sql = "UPDATE user_cards_account
				SET num = ? WHERE userId = ? AND role = ? AND cardId = ?";
			$this->db->query($sql, [$new_left, $oprator->id, $role, $ticket_obj['objId']]);

			// 获取user_cards表中用户获得卡券的记录
			$user_cards = $this->db->where('userId', $oprator->id)
				->where('role', $role)
				->where('cardId', $ticket_obj['objId'])
				->where('status', USER_CARDS_STATUS_NORMAL)
				->get(self::TABLE_USER_CARDS)->result_array();
			if (count($user_cards) < $num) {
				$this->db->trans_rollback();
				error('Faild: table user_cards data invalid, where userId ' .
					$oprator->id . ' role ' .
					$role . ' cardId ' . $ticket_obj['objId']);
				throw new Exception('账户数据异常', 1);
			}

			$prepared_card_ids = [];
			$prepared_card_rows = [];
			for ($i = 0; $i < $num; $i++) {
				array_push($prepared_card_ids, $user_cards[$i]['id']);
				array_push($prepared_card_rows, $user_cards[$i]);
			}

			// 更新user_cards表状态
			$update_user_cards_result = $this->db
				->set('status', USER_CARDS_STATUS_TRANSED)
				->where('userId', $oprator->id)
				->where('role', $role)
				->where('cardId', $ticket_obj['objId'])
				->where_in('id', $prepared_card_ids)
				->update(self::TABLE_USER_CARDS);
			if (! $update_user_cards_result) {
				$this->db->trans_rollback();
				throw new Exception('账户数据异常', 1);
			}

			$transfer_id = $this->generate_trans_log($oprator->id, $role, $ticket_obj['userId'],
						$ticket_obj['role'], $ticket_obj['objId'], $ticket_obj['objType'], $num);

			foreach ($prepared_card_rows as &$row) {
				$row['userId'] = $ticket_obj['userId'];
				$row['role'] = $ticket_obj['role'];
				$row['id'] = 0; // 设置为0使其自增长
				$row['transId'] = $transfer_id;
			}

			/**
			 * 为转移人在user_cards表中添加记录
			 *
			 */
			$this->db->insert_batch(self::TABLE_USER_CARDS, $prepared_card_rows);
			if ($this->db->affected_rows() <= 0) {
				$this->db->trans_rollback();
				throw new Exception('转移数据异常');
			}

			/**
			 * 添加转移对象账户中的数据
			 * 首先判断该用户账户是否有该奖品记录，没有则执行insert操作，有则执行update操作
			 */
			$sql = 'SELECT id FROM %s WHERE userId = ? AND role = ? AND cardId = ?';
			$sql = sprintf($sql, SELF::TABLE_USER_CARDS_ACCOUNT);
			$result = $this->db->query($sql, [$ticket_obj['userId'], $ticket_obj['role'], $ticket_obj['objId']])->row();
			if (! $result) {
				$data = [
					'userId' => $ticket_obj['userId'],
					'role' => $ticket_obj['role'],
					'cardId' => $ticket_obj['objId'],
					'mchId' => $oprator->mchId,
					'num' => $num
				];
				$this->db->insert(SELF::TABLE_USER_CARDS_ACCOUNT, $data);
				$this->db->trans_complete();
				return TICKET_CONFIRMED;
			} else {
				$user_cards_account_id = $result->id;
				$sql = 'UPDATE user_cards_account SET num = num + ? WHERE id = ?';
				$this->db->query($sql, [intval($num), $user_cards_account_id]);
				$this->db->trans_complete();
				return TICKET_CONFIRMED;
			}
		} else {
			throw new Exception('暂不支持其他类型物品兑换', 1);
		}
	}

	/**
	 * 获取扫描状态
	 *
	 * @param string $ticket 转移票据
	 */
	public function ticket_status($ticket) {
		$this->db->where('ticket', $ticket);
		$status = $this->db->get(SELF::TABLE_TICKETS)->row();
		if ($status) {
			return $status->scaned;
		}
		return TICKET_NOT_SCANED;
	}

	/**
	 * 获取转移确认状态
	 *
	 * @param string $ticket 转移票据
	 */
	public function ticket_confirmed($ticket) {
		if (! is_string($ticket)) {
			return TICKET_NOT_CONFIRMED;
		}
		$this->db->where('ticket', $ticket);
		$status = $this->db->get(SELF::TABLE_TICKETS)->row();
		if ($status) {
			if ($status->confirmed) {
				$this->db->query('DELETE FROM obj_tickets WHERE ticket = ?', [$ticket]);
			}
			return $status->confirmed;
		}
		return TICKET_NOT_CONFIRMED;
	}

	/**
	 * 获取用户的转移记录数据
	 *
	 * @param $userid 用户编号
	 * @param $role   用户角色
	 * @param $isList 是否是获取列表
	 * @param $queryType 查询类型：all 所有，prize 中奖，trans_in 转入，trans_out 转出
	 */
	public function get_trans_log($userid, $role, $isList = false, $queryType = 'all', $page = 0, $pageSize = 10) {
		if ($isList) {
			$start = $page * $pageSize;
			$end = $pageSize;
		} else {
			$start = 0;
			$end = 3;
		}

		debug('get_trans_log: '. json_encode(func_get_args()));

		if ($queryType === 'all') {
			$sql = "SELECT t1.num, t1.transferTime, t2.title, ifnull(CASE t1.toRole
						WHEN 0 THEN t3.nickName
						WHEN 1 THEN t4.nickName
						WHEN 2 THEN t5.nickName
						ELSE '未知用户' END, '未知用户') AS nickName, '发送' AS type
					FROM user_transfers t1
					JOIN cards          t2 ON t2.id = t1.objId
					LEFT JOIN users     t3 ON t3.id = t1.toId AND t1.toRole = 0
					LEFT JOIN waiters   t4 ON t4.id = t1.toId AND t1.toRole = 1
					LEFT JOIN salesman  t5 ON t5.id = t1.toId AND t1.toRole = 2
					WHERE t1.fromId = ? AND t1.fromRole = ? AND t1.toId != -1
					UNION ALL
					SELECT t1.num, t1.transferTime, t2.title, '在线兑付' AS nickName, '兑换' AS type
					FROM user_transfers t1
					JOIN cards          t2 ON t2.id = t1.objId
					WHERE t1.fromId = ? AND t1.fromRole = ? AND t1.toId = -1 AND t1.toRole = -1
					UNION ALL
					SELECT t1.num, t1.transferTime, t2.title, ifnull(CASE t1.fromRole
						WHEN 0 THEN t3.nickName
						WHEN 1 THEN t4.nickName
						WHEN 2 THEN t5.nickName
						ELSE '未知用户' END, '未知用户') AS nickName, '接收' AS type
					FROM user_transfers t1
					JOIN cards          t2 ON t2.id = t1.objId
					LEFT JOIN users     t3 ON t3.id = t1.fromId AND t1.fromRole = 0
					LEFT JOIN waiters   t4 ON t4.id = t1.fromId AND t1.fromRole = 1
					LEFT JOIN salesman  t5 ON t5.id = t1.fromId AND t1.fromRole = 2
					WHERE t1.toId = ? AND t1.toRole = ?
					UNION ALL
					SELECT 1 AS num, t1.getTime AS transferTime, t2.title, CASE t1.role
						WHEN 0 THEN t3.nickName
						WHEN 1 THEN t4.nickName
						ELSE '未知用户' END AS nickName, '中得' AS type
					FROM user_cards    t1
					JOIN cards         t2 ON t2.id = t1.cardId
					LEFT JOIN users    t3 ON t3.id = t1.userId AND t1.role = 0
					LEFT JOIN waiters  t4 ON t4.id = t1.userId AND t1.role = 1
					LEFT JOIN salesman t5 ON t5.id = t1.userId AND t1.role = 2
					WHERE t1.userId = ? AND t1.role = ? AND t1.transId = -1 AND t1.sended = 1
					ORDER BY transferTime DESC LIMIT ?, ?";
			$query = [$userid, $role, $userid, $role, $userid, $role, $userid, $role, $start, $end];
			$transfers = $this->db->query($sql, $query)->result_array();
			return $transfers;
		}
		if ($queryType === 'prize') {
			$sql = "SELECT 1 AS num, t1.getTime AS transferTime, t2.title, CASE t1.role
						WHEN 0 THEN t3.nickName
						WHEN 1 THEN t4.nickName
						ELSE '未知用户' END AS nickName, '中得' AS type
					FROM user_cards    t1
					JOIN cards         t2 ON t2.id = t1.cardId
					LEFT JOIN users    t3 ON t3.id = t1.userId AND t1.role = 0
					LEFT JOIN waiters  t4 ON t4.id = t1.userId AND t1.role = 1
					LEFT JOIN salesman t5 ON t5.id = t1.userId AND t1.role = 2
					WHERE t1.userId = ? AND t1.role = ? AND t1.transId = -1 AND t1.sended = 1
					ORDER BY transferTime DESC LIMIT ?, ?";
			$query = [intval($userid), $role, $start, $end];
			$transfers = $this->db->query($sql, $query)->result_array();
			return $transfers;
		}
		if ($queryType === 'trans_in') {
			$sql = "SELECT t1.*, t2.title, ifnull(CASE t1.fromRole
						WHEN 0 THEN t3.nickName
						WHEN 1 THEN t4.nickName
						WHEN 2 THEN t5.nickName
						ELSE '未知角色' END, '未知用户') AS nickName, '接收' AS type
					FROM user_transfers t1
					JOIN cards          t2 ON t2.id = t1.objId
					LEFT JOIN users     t3 ON t3.id = t1.fromId AND t1.fromRole = 0
					LEFT JOIN waiters   t4 ON t4.id = t1.fromId AND t1.fromRole = 1
					LEFT JOIN salesman  t5 ON t5.id = t1.fromId AND t1.fromRole = 2
					WHERE t1.toId = ? AND t1.toRole = ?
					ORDER BY transferTime DESC LIMIT ?, ?";
			$query = [$userid, $role, $start, $end];
			$transfers = $this->db->query($sql, $query)->result_array();
			return $transfers;
		}
		if ($queryType === 'trans_out') {
			$sql = "SELECT t1.*, t2.title, ifnull(CASE t1.toRole
						WHEN 0 THEN t3.nickName
						WHEN 1 THEN t4.nickName
						WHEN 2 THEN t5.nickName
						ELSE '未知角色' END, '未知用户') AS nickName, '发送' AS type
					FROM user_transfers t1
					JOIN cards          t2 ON t2.id = t1.objId
					LEFT JOIN users     t3 ON t3.id = t1.toId AND t1.toRole = 0
					LEFT JOIN waiters   t4 ON t4.id = t1.toId AND t1.toRole = 1
					LEFT JOIN salesman  t5 ON t5.id = t1.toId AND t1.toRole = 2
					WHERE t1.fromId = ? AND t1.fromRole = ? AND t1.toId != -1
					UNION ALL
					SELECT t1.*, t2.title, '在线兑付' AS nickName, '兑换' AS type
					FROM user_transfers t1
					JOIN cards          t2 ON t2.id = t1.objId
					WHERE t1.fromId = ? AND t1.fromRole = ? AND t1.toId = -1 AND t1.toRole = -1
					ORDER BY transferTime DESC LIMIT ?, ?";
			$query = [$userid, $role, $userid, $role, $start, $end];
			$transfers = $this->db->query($sql, $query)->result_array();
			return $transfers;
		}

	}

	/**
	 * 获取用户要兑换的卡片的详细信息
	 *
	 * @param integer $obj_id 要兑换的卡券或欢乐币id
	 * @param integer $user_id 用户id
	 * @param integer $role 用户角色
	 * @return object 商品信息
	 */
	public function get_card_info($obj_id, $user_id, $role) {
		$prize = $this->db->query('SELECT * FROM user_cards_account WHERE cardId
			= ? AND userId = ? AND role = ?', [$obj_id, $user_id, $role])->row();
		if (! isset($prize)) {
			throw new Exception('兑换的商品不存在');
		}

		$card_id = $prize->cardId;
		$card_result = $this->db->query('SELECT title FROM cards WHERE id = ?',
			[$card_id])->row();
		$card_title = $card_result->title;
		$num = $prize->num;

		return ['type' => SCAN_RES_TRANSFER, 'title' => $card_title, 'num' => $num];
	}

	/**
	 * 生成转移记录
	 *
	 * @param int $from_id
	 * @param int $from_role
	 * @param int $to_id
	 * @param int $to_role
	 * @param int $obj_id
	 * @param int $obj_type
	 * @param int $num
	 */
	public function generate_trans_log($from_id, $from_role, $to_id, $to_role, $obj_id, $obj_type, $num) {
		$data = [
			'fromId'       => $from_id,
			'fromRole'     => $from_role,
			'toId'         => $to_id,
			'toRole'       => $to_role,
			'objId'        => $obj_id,
			'objType'      => $obj_type,
			'num'          => $num,
			'transferTime' => time()
		];
		info("generate_trans_log - begin");
		info("log data: " . json_encode($data));
		$success = $this->db->insert(self::TABLE_USER_TRANSFERS, $data);
		info("generate_trans_log - end");
		if ($success) {
			return $this->db->insert_id();
		} else {
			return FALSE;
		}
	}
}
