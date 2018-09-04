<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * 
 * @author shizq
 *
 */
class Settle_model extends CI_Model {
	
	const TABLE_USER_CARDS_ACCOUNT 			= 'user_cards_account';
	const TABLE_SALESMAN_STATEMENTS 		= 'salesman_statements';
	const TABLE_SALESMAN_STATEMENTS_OBJS 	= 'salesman_statements_objs';
	const TABLE_USER_CARDS 					= 'user_cards';

	/**
	 * 企业端获取业务员核销乐券申请列表
	 * 
	 * @param $mchId 商户编号
	 * @return array
	 */
	public function listSettles($mchId, $params, $start = 0, $pageSize = 10) {
		$sql = "SELECT t1.*, t3.realName, count(t4.id) cardNum,
			FROM_UNIXTIME(t1.submitTime, '%Y-%m-%d %H:%i:%s') submitTime, t3.rowStatus isDelete
			FROM salesman_statements 		t1
			JOIN mch_salesman 				t3 ON t3.id = t1.smId
			JOIN salesman_statements_objs 	t4 ON t4.statementsId = t1.id
			WHERE t1.mchId = ? ";

		$where = [$mchId];	
		if (! empty($params['smId']) || $params['smId'] === '0') {
			$where[] = $params['smId'];
			$sql .= "AND t3.id = ? ";
		}
		if (! empty($params['state']) || $params['state'] === '0') {
			$where[] = $params['state'];
			$sql .= "AND t1.state = ? ";
		}
		if (! empty($params['search'])) {
			$where[] = '%' . $params['search'] . '%';
			$sql .= "AND t1.statementNo like ? ";
		}

		$sql .= "GROUP BY t1.id
				ORDER BY t1.submitTime DESC LIMIT ?, ?";

		$where[] = intval($start);
		$where[] = intval($pageSize);
		$settles = $this->db->query($sql, $where)->result();
		return $settles;
	}
	
	/**
	 * 通过核销编号获取乐券列表
	 *
	 * @param $mchId 商户编号
	 * @param $statementsId 核销编号
	 * @return array
	 */
	public function listCardsByStatementsId($mchId, $statementsId) {
		$sql = "SELECT sum(num) cards_num, cards.title FROM salesman_statements_objs 
			JOIN cards ON cards.id = salesman_statements_objs.objId 
			WHERE statementsId = ? GROUP BY objId";
		$cards = $this->db->query($sql, [$statementsId])->result();
		return $cards;
	}
	
	/**
	 * 审核核销申请
	 *
	 * @param $mchId 商户编号
	 * @param $statementsId 核销编号
	 * @param $pass 审核码
	 * @param $content 审核说明
	 * @return array
	 */
	public function reviewStatement($mchId, $statementId, $pass, $content) {
		$sql = "UPDATE salesman_statements 
			SET state = 1, settleCode = ?, settleResult = ?, settleTime = ? 
			WHERE mchId = ? AND id = ?";
		$updateOk = $this->db->query($sql, [$pass, $content, time(), $mchId, $statementId]);
		if (! $updateOk) {
			throw new Exception('发生未知错误', 1);
		}
	}

	/**
	 * 获取业务员核销乐券申请的总数量
	 * 
	 * @param $mchId 商户编号
	 * @return integer
	 */
	public function getSettlesNum($mchId, $params) {
		// JOIN salesman 					t2 ON t2.id = t1.smId
		$sql = "SELECT t1.id
			FROM salesman_statements 		t1
			JOIN mch_salesman 				t3 ON t3.id = t1.smId
			JOIN salesman_statements_objs 	t4 ON t4.statementsId = t1.id
			WHERE t1.mchId = ? ";
		$where = [$mchId];
		if (! empty($params['smId']) || $params['smId'] === '0') {
			$where[] = $params['smId'];
			$sql .= "AND t3.id = ? ";
		}
		if (! empty($params['state']) || $params['state'] === '0') {
			$where[] = $params['state'];
			$sql .= "AND t1.state = ? ";
		}
		$sql .= "GROUP BY t1.id";
		$settles = $this->db->query($sql, $where)->result();
		return count($settles);
	}
	
	/**
	 * 获取业务员的所有可核销的卡券
	 * 
	 * @param  string $userid 业务员id
	 * @return array 卡券数组
	 */
	public function listSalesmanCards($userid) {
		$sql = "SELECT user_cards_account.num, cards.title, cards.imgUrl 
			FROM user_cards_account 
			INNER JOIN cards ON cards.id = user_cards_account.cardId
			WHERE userId = ? 
			AND cards.rowStatus = 0
			AND num > 0
			AND role = ?";
		$cards = $this->db->query($sql, [$userid, ROLE_SALESMAN])->result_array();
		return $cards;
	}

	/**
	 * 获取某个业务员所有的核销记录
	 * 
	 * @param int $userid 业务员id
	 */
	public function settleNotes($userid, $page = 0) {
		// 查询用户是否锁定或删除
		$sql = "SELECT count(t1.id) data_count, t1.mchSalesmanId FROM salesman t1 
			JOIN mch_salesman t2 ON t2.id = t1.mchSalesmanId AND t2.rowStatus = 0 
			WHERE t1.id = ?";
		$result = $this->db->query($sql, [$userid])->row();
		debug('settleNotes: dataCount is '. json_encode($result));
		if ($result->data_count === 0 || $result->data_count === '0') {
			throw new Exception("请先绑定账号", 1);
		}
		
		$sql = "SELECT t2.id, 
			FROM_UNIXTIME(t2.submitTime, '%Y-%m-%d %H:%i:%s') AS submitTime, 
			t2.state, 
			t2.settleCode,
			SUM(t1.num) AS num, 
			t3.title 
			FROM salesman_statements_objs 	t1
			JOIN salesman_statements 		t2 ON t1.statementsId = t2.id
			JOIN cards               		t3 ON t1.objId = t3.id
			WHERE t2.smId = ? 
			GROUP BY statementsId, t3.id
			ORDER BY submitTime DESC LIMIT ?, 20";
		$settleNotes = $this->db->query($sql, [$result->mchSalesmanId, $page])->result_array();
		return $settleNotes;
	}
	
	/**
	 * 业务员申请核销操作
	 * 
	 * @param object $salesman 业务员对象
	 */
	public function settle($salesmanObj) {
		info("Settle - begin");
		$salesman = $this->db->where('id', $salesmanObj->id)->get('salesman')->row();
		info("Salesman info: " . json_encode($salesman));
		
		$mchSalesman = $this->db->where('id', $salesman->mchSalesmanId)->where('rowStatus', 0)->get('mch_salesman')->row();
		if (! isset($mchSalesman)) {
			throw new Exception("请先绑定账号", 1);
		}
		if ($mchSalesman->status == 2) {
			throw new Exception("账户已锁定，不能执行核销", 1);
		}
		
		// 1.查找业务员账户中所有的卡券，当前只能一次全部核销
		$this->db->trans_start();
		$sql = "SELECT cardId AS objId,
			scanId AS scanId, 1 AS num, 2 AS objType, 0 AS statementsId 
			FROM user_cards WHERE userId = ? AND role = ? AND status = ?";
		$cards = $this->db->query($sql, [$salesman->id, ROLE_SALESMAN, USER_CARDS_STATUS_NORMAL])
			->result_array();
		info("Salesman cards: " . json_encode($cards));
		if (! $cards) {
			$this->db->trans_rollback();
			throw new Exception('没有可用的乐券用于核销');
		}
		
		// 2.向核销表中插入对应数据，并得到插入的行id
		$data = [
			'smId' => $salesman->mchSalesmanId,
			'mchId' => $salesman->mchId,
			'statementNo' => date('YmdHis') . time() . mt_rand(1000, 9999),
			'submitTime' => time(),
			'state' => 0,
			'rowStatus' => 0
		 ];
		$this->db->insert(self::TABLE_SALESMAN_STATEMENTS, $data);
		$statements_id = $this->db->insert_id();
		$time = time();
		foreach ($cards as &$card) {
			$card['statementsId'] = $statements_id;
		}
		
		// 3.向核销详细表中写入乐券的详细信息（多条）
		$this->db->insert_batch(self::TABLE_SALESMAN_STATEMENTS_OBJS, $cards);
		$rows = $this->db->affected_rows();
		if ($rows <= 0) {
			$this->db->trans_rollback();
			debug('Faild: no data inserted into table salesman_statements_objs');
			throw new Exception('核销失败，错误代码100101');
		}
		$updateOk = $this->db->set('status', USER_CARDS_STATUS_SETTLED)
			->where('userId', $salesman->id)
			->where('role', ROLE_SALESMAN)
			->where('status', USER_CARDS_STATUS_NORMAL)
			->update(self::TABLE_USER_CARDS);
		if (! $updateOk) {
			$this->db->trans_rollback();
			debug('Faild: update user_cards status faild');
			throw new Exception('核销失败，错误代码100102');
		}
		$sql = "UPDATE user_cards_account 
				SET num = 0
				WHERE userId = ?
				AND role = ?";
		$this->db->query($sql, [$salesman->id, ROLE_SALESMAN]);
		$rows = $this->db->affected_rows();
		if ($rows <= 0) {
			$this->db->trans_rollback();
			debug('Faild: no data updated in table role_salesman');
			throw new Exception('核销失败，错误代码100103');
		}
		$this->db->trans_complete();
		info("Settle - end");
	}
}
