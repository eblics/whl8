<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * 
 * 捞红包
 *
 */
class Group_fishing_model extends MY_Model {
	public function __construct() {
        parent::__construct();
    }

	public function get($id) {
		$sql="SELECT * FROM `groups_fishing` WHERE id=$id and rowStatus=0";
		return $this->db->query($sql)->row();
	}

	//随机捞取一条
	public function get_group_rand_fishing($groupId,$userId,$amount) {
		$sql="SELECT t1.* FROM `groups_fishing` AS t1 JOIN (
                    SELECT ROUND(RAND() * ((SELECT MAX(id) FROM `groups_fishing` )-(SELECT MIN(id) FROM `groups_fishing` ))+(SELECT MIN(id) FROM `groups_fishing` )) AS id
                ) AS t2   
                WHERE t1.id >= t2.id and t1.groupId=$groupId and t1.userId!=$userId and t1.amount<=$amount and t1.status=0 and t1.rowStatus=0 
                ORDER BY t1.id LIMIT 1";

				log_message('debug',$sql);
		return $this->db->query($sql)->row();
	}
	
	
	//扔炸弹
	public function add($saveData){
		$this->db->trans_begin();
		$account=$this->db->query("select * from user_accounts where userId=$saveData->userId and amount>=$saveData->amount and moneyType=0")->row();
		if(!$account){
			$this->db->trans_rollback();
            return FALSE;
		}
		$theTime=time();
		$this->db->insert('groups_fishing',$saveData);
		$fishingId=$this->db->insert_id();
		$this->db->query("insert into groups_fishing_log(mchId,groupId,fishingId,userId,fType,amount,createTime) 
							values($saveData->mchId,$saveData->groupId,$fishingId,$saveData->userId,0,$saveData->amount,$theTime)");
		$fishingLogId=$this->db->insert_id();
		$this->db->query("update user_accounts set amount=amount-$saveData->amount where userId=$saveData->userId and moneyType=0");
		$this->db->query("insert into user_accounts_used(userId,mchId,doTable,doId,amount,role,createTime) 
							values($saveData->userId,$saveData->mchId,'groups_fishing_log',$fishingLogId,$saveData->amount,0,$theTime)");
		if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            return FALSE;
        }else{
			$this->db->trans_commit();
		}
		return $fishingId;
    }


	//开箱子成功
	public function open_ok($fishingId,$userId) {
		$this->db->trans_begin();
		$fishing=$this->db->query("SELECT * FROM `groups_fishing` WHERE id=$fishingId and rowStatus=0")->row();
		if($fishing->status!=0){
			$this->db->trans_rollback();
			return FALSE;
		}
		$account=$this->db->query("select * from user_accounts where userId=$userId and amount>=$fishing->amount and moneyType=0")->row();
		if(!$account){
			$this->db->trans_rollback();
            return FALSE;
		}
		$theTime=time();
		$this->db->query("insert into groups_fishing_log(mchId,groupId,fishingId,userId,fType,amount,createTime) 
							values($fishing->mchId,$fishing->groupId,$fishingId,$userId,2,$fishing->amount,$theTime)");
		$fishingLogId=$this->db->insert_id();
		$this->db->query("update user_accounts set amount=amount+$fishing->amount where userId=$userId and moneyType=0");
		$this->db->query("update groups_fishing set status=2 where id=$fishingId");
		if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            return FALSE;
        }else{
			$this->db->trans_commit();

			//发模板消息通知
			$user=$this->db->query("SELECT * FROM `users` WHERE id=$fishing->userId")->row();
			if($user){
				$this->wx3rd_lib->template_send($fishing->mchId,$user->openid,$this->wx3rd_lib->template_format_data($fishing->mchId,'kf_notice',['您有一条“捞红包”消息提醒','消息提醒','已完成','系统','您扔的炸弹红包“'.bcdiv($fishing->amount,100,2).'元”被别人成功拆开，并领走了红包。']));
			}
			//发模板消息通知 end
			
		}
		return TRUE;
	}

	//开箱子失败
	public function open_fail($fishingId,$userId) {
		$this->db->trans_begin();
		$fishing=$this->db->query("SELECT * FROM `groups_fishing` WHERE id=$fishingId and rowStatus=0")->row();
		if($fishing->status!=0){
			$this->db->trans_rollback();
			return FALSE;
		}
		$account=$this->db->query("select * from user_accounts where userId=$userId and amount>=$fishing->amount and moneyType=0")->row();
		if(!$account){
			$this->db->trans_rollback();
            return FALSE;
		}
		$theTime=time();
		$this->db->query("insert into groups_fishing_log(mchId,groupId,fishingId,userId,fType,amount,createTime) 
							values($fishing->mchId,$fishing->groupId,$fishingId,$userId,3,$fishing->amount,$theTime)");
	    $doubleGet=$fishing->amount*2;
		$this->db->query("insert into groups_fishing_log(mchId,groupId,fishingId,userId,fType,amount,createTime) 
							values($fishing->mchId,$fishing->groupId,$fishingId,$fishing->userId,1,$doubleGet,$theTime)");
		$this->db->query("update user_accounts set amount=amount-$fishing->amount where userId=$userId and moneyType=0");
		$this->db->query("update user_accounts set amount=amount+$doubleGet where userId=$fishing->userId and moneyType=0");
		$this->db->query("update groups_fishing set status=1 where id=$fishingId");
		if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            return FALSE;
        }else{
			$this->db->trans_commit();

			//发模板消息通知
			$user=$this->db->query("SELECT * FROM `users` WHERE id=$fishing->userId")->row();
			if($user){
				$this->wx3rd_lib->template_send($fishing->mchId,$user->openid,$this->wx3rd_lib->template_format_data($fishing->mchId,'kf_notice',['您有一条“捞红包”消息提醒','消息提醒','已完成','系统','您扔的炸弹红包“'.bcdiv($fishing->amount,100,2).'元”被别人引爆了，您获得了'.bcdiv($doubleGet,100,2).'元。']));
			}
			//发模板消息通知 end
		}
		return TRUE;
	}

	//获取log
	public function get_log_by_user($groupId,$userId,$page) {
		$limit=10*abs($page-1).',20';
		return $this->db->query("SELECT * FROM `groups_fishing_log` WHERE groupId=$groupId and userId=$userId order by id desc limit $limit")->result();
	}

	//根据Ftype获取log
	public function get_log_by_ftype($fishingId,$fType) {
		return $this->db->query("SELECT * FROM `groups_fishing_log` WHERE fishingId=$fishingId and fType=$fType")->row();
	}

	//获取红包统计
	public function get_count($groupId) {
		$allCount=$this->db->query("SELECT count(*) as allCount FROM `groups_fishing` WHERE groupId=$groupId")->row();
		$remainCount=$this->db->query("SELECT count(*) as remainCount FROM `groups_fishing` WHERE groupId=$groupId and status=0")->row();
		return (object)['allCount'=>$allCount->allCount,'remainCount'=>$remainCount->remainCount];
	}
	

	

	/**
	 * 扔红包炸弹
	 * @param $user 操作的用户
	 * @param $amount 红包金额
	 * @return int
	 */
	public function throwBomb($user, $amount) {
		debug('throw-bomb - begin');
		debug('params:' . json_encode(func_get_args()));
		try {
			$this->beginTransition();
			$sql = "select * from user_accounts where userId = ? and moneyType = 0 for update";
			$userAccount = $this->db->query($sql, [$user->id])->row();
			if (! isset($userAccount)) {
				throw new Exception("您的红包余额不足", 1);
			}
			if ($userAccount->amount < $amount) {
				throw new Exception("您的红包余额不足", 1);
			}
			
			$time = time();
			$bombInfo = [];
			$bombInfo['mchId'] = $user->mchId;
			$bombInfo['userId'] = $user->id;
			$bombInfo['amount'] = $amount;
			$bombInfo['status'] = 0;
			$bombInfo['createTime'] = $time;
			$bombInfo['updateTime'] = $time;

			debug('insert groups_fishing: '. json_encode($bombInfo));
			$this->db->insert('groups_fishing', $bombInfo);
			$fishingId = $this->db->insert_id();

			$logs = [];
			$logs['mchId'] = $user->mchId;
			$logs['fishingId'] = $fishingId;
			$logs['userId'] = $user->id;
			$logs['fType'] = 0;
			$logs['amount'] = $amount;
			$logs['createTime'] = $time;
			debug('insert groups_fishing_log: '.  json_encode($logs));
			$this->db->insert("groups_fishing_log", $logs);
			$logId = $this->db->insert_id();

			// 修改用户账户余额
			debug('update user_accounts old data: '. json_encode($userAccount));
			debug('new data: '. json_encode(['amount' => $userAccount->amount - $amount]));
			$sql = "update user_accounts set amount = amount - ? where userId = ? and moneyType = 0";
			$this->db->query($sql, [$amount, $user->id]);

			// 记录用户金额使用记录
			$userAccountsUsed = [];
			$userAccountsUsed['userId'] = $user->id;
			$userAccountsUsed['mchId'] = $user->mchId;
			$userAccountsUsed['doTable'] = 'groups_fishing_log';
			$userAccountsUsed['doId'] = $logId;
			$userAccountsUsed['amount'] = $amount;
			$userAccountsUsed['role'] = 0;
			$userAccountsUsed['createTime'] = $time;
			debug('insert user_accounts_used: '. json_encode($userAccountsUsed));
			$this->db->insert('user_accounts_used', $userAccountsUsed);

			if (! $this->checkTransitionSuccess()) {
				$this->rollbackTransition();
				throw new Exception("保存失败", 1);
			} 
			$this->commitTransition();
			return $fishingId;
		} catch (Exception $e) {
			debug('throw-bomb - fail: '. $e->getMessage());
			throw $e;
		}  finally {
			debug('throw-bomb - end');
		}
		
	}

	/**
	 * 捞红包炸弹
	 * @param $user 操作的用户
	 * @return int
	 */
	public function extractRedpacket($user) {
		$this->load->model('User_model', 'user');
		$userAccount = $this->user->get_amount($user->id, $user->mchId, 0);
		if (! isset($userAccount)) {
			return NULL;
		}
		$sql = "SELECT t1.* FROM `groups_fishing` AS t1 JOIN (
            SELECT ROUND(RAND() * ((SELECT MAX(id) FROM `groups_fishing` ) - (SELECT MIN(id) FROM `groups_fishing` )) + 
            	(SELECT MIN(id) FROM `groups_fishing` )) AS id
        	) AS t2   
        	WHERE t1.id >= t2.id AND t1.userId != ? AND t1.amount <= ? AND t1.status = 0 AND t1.mchId = ? AND t1.rowStatus = 0 
        	ORDER BY t1.id LIMIT 1";
		$box = $this->db->query($sql, [$user->id, $userAccount->amount, $user->mchId])->row();
		if (! isset($box)) {
			return NULL;
		}
		return $box->id;
	}

	/**
	 * 获取池子中的红包数量
	 * @param $user 
	 * @return array
	 */
	public function countBox($user) {
		$sql = "SELECT count(*) as allCount FROM `groups_fishing` WHERE mchId = ?";
		$allCount = $this->db->query($sql, [$user->mchId])->row();
		$sql = "SELECT count(*) as remainCount FROM `groups_fishing` WHERE status = 0";
		$remainCount = $this->db->query($sql)->row();
		return [
			'allCount' => $allCount->allCount, 
			'remainCount' => $remainCount->remainCount
		];
	}

	/**
	 * 用户打开盒子
	 * @param $user 
	 * @return int
	 */
	public function openBox($user, $boxId) {
		debug('open-box - begin');
		debug('params: '. json_encode(func_get_args()));
		try {
			$this->beginTransition();
			$sql = "select * from groups_fishing where id = ? and status = 0 and rowStatus = 0 for update";
			$box = $this->db->query($sql, [$boxId])->row();
			if (! isset($box)) {
				$this->rollbackTransition();
				throw new Exception("下手太慢，被别人抢了", 1);
			}
			$sql = "select * from user_accounts where userId = ? and moneyType = 0 for update";
			$userAccount = $this->db->query($sql, [$user->id])->row();
			if (! isset($userAccount)) {
				$this->rollbackTransition();
				throw new Exception("这个箱子您开不动", 1);
			}
			if ($userAccount->amount < $box->amount) {
				$this->rollbackTransition();
				throw new Exception("这个箱子您开不动", 1);
			}
			$rand = rand(0, 1);

			if ($rand > 0) {
				// 开出炸弹
				$this->openBoxFail($user, $box, $userAccount);
				$templateMsg = '您扔的炸弹红包“'. bcdiv($box->amount, 100, 2) .'元”被别人引爆了，您获得了'. bcdiv($box->amount, 100, 2) .'元。';
				$result = FALSE;
			} else {
				// 开出红包
				$this->openBoxSuccess($user, $box, $userAccount);
				$templateMsg = '您扔的炸弹红包“'. bcdiv($box->amount, 100, 2). '元”被别人成功拆开，并领走了红包。';
				$result = TRUE;
			}
	        
	        if (! $this->checkTransitionSuccess()) {
	        	$this->rollbackTransition();
	        	throw new Exception("开箱失败了", 1);
	        } 
	        $this->commitTransition();

	        $boxOwner = $this->db->query("SELECT * FROM `users` WHERE id = ?", [$box->userId])->row();
	        if (isset($boxOwner)) {
	        	// 如果用户在数据库中被删除，此处会报错，但实际不存在
	        	// 只有开发或测试阶段才会出现
    	        $msgInfo = [
    		        '您有一条“捞红包”消息提醒',
    		        '消息提醒',
    		        '已完成',
    		        '系统',
    		        $templateMsg
    	        ];
    	        $msg = $this->wx3rd_lib->template_format_data($box->mchId, 'kf_notice', $msgInfo);
    			$this->wx3rd_lib->template_send($box->mchId, $boxOwner->openid, $msg);
	        }

			return ['success' => $result, 'amount' => $box->amount];
		} catch (Exception $e) {
			debug('open-box - fail: '. $e->getMessage());
			throw $e;
		} finally {
			debug('open-box - end');
		}
		
	}

	/**
	 * 捞红包记录
	 * @param $user 
	 * @return int
	 */
	public function logs($user, $page) {
		$page = $page - 1;
		$sql = "SELECT * FROM `groups_fishing_log` WHERE userId = ? ORDER BY id DESC LIMIT ?, ?";
		$logs = $this->db->query($sql, [$user->id, $page * 20, 20])->result();

		$dataArr=[];
		foreach ($logs as $log) {
			$log->createTime = date('Y.m.d',$log->createTime).'<br/>'.date('H:i:s',$log->createTime);
            // $log->title = '';
            // $log->data = '';
            // $log->style = '';
            if ($log->fType == 0) {
                $log->title = '扔炸弹';
                $log->data = '-'.bcdiv($log->amount,100,2).'元';
                $noboomLog = $this->get_log_by_ftype($log->fishingId, 2);
                if($noboomLog){
                    $noboomLog->createTime=date('Y.m.d',$noboomLog->createTime).'<br/>'.date('H:i:s',$noboomLog->createTime);
                    $noboomLog->title=bcdiv($noboomLog->amount,100,2).'元红包被人捞走了';
                    $noboomLog->style='sub0';
                    array_push($dataArr,$noboomLog);
                }
                $boomLog=$this->get_log_by_ftype($log->fishingId,1);
                if($boomLog){
                    $boomLog->createTime=date('Y.m.d',$boomLog->createTime).'<br/>'.date('H:i:s',$boomLog->createTime);
                    $boomLog->title='炸弹成功引爆';
                    $boomLog->data='+'.bcdiv($boomLog->amount,100,2).'元';
                    $boomLog->style='sub1';
                    array_push($dataArr,$boomLog);
                }
                array_push($dataArr,$log);
            }
            if($log->fType==2){
                $log->title='捞到了红包';
                $log->data='+'.bcdiv($log->amount,100,2).'元';
                array_push($dataArr,$log);
            }
            if($log->fType==3){
                $log->title='捞到了炸弹';
                $log->data='-'.bcdiv($log->amount,100,2).'元';
                array_push($dataArr,$log);
            }
		}

		return $dataArr;
	}

	// 开出红包
	private function openBoxSuccess($user, $box, $userAccount) {
		$theTime = time();
		$logs = [];
		$logs['mchId'] = $user->mchId;
		$logs['fishingId'] = $box->id;
		$logs['userId'] = $user->id;
		$logs['fType'] = 2; // 捞到红包
		$logs['amount'] = $box->amount;
		$logs['createTime'] = $theTime;
		debug('insert groups_fishing_log: '. json_encode($logs));
		$this->db->insert('groups_fishing_log', $logs);
		$logId = $this->db->insert_id();

		debug('update user_accounts old data: '. json_encode($userAccount));
		debug('new data: '. json_encode(['amount' => $userAccount->amount + $box->amount]));
		$this->db->query("update user_accounts set amount = amount + ? where userId = ? and moneyType = 0", [$box->amount, $user->id]);
		
		debug('update groups_fishing old data: '. json_encode($box));
		debug('new data: '. json_encode(['status' => 2]));
		$this->db->query("update groups_fishing set status = 2 where id = ?", [$box->id]);
	}

	// 开出炸弹
	private function openBoxFail($user, $box, $userAccount) {
		$theTime = time();
		$logs = [];
		$logs['mchId'] = $user->mchId;
		$logs['fishingId'] = $box->id;
		$logs['userId'] = $user->id;
		$logs['fType'] = 3; // 捞到炸弹
		$logs['amount'] = $box->amount;
		$logs['createTime'] = $theTime;
		debug('insert groups_fishing_log: '. json_encode($logs));
		$this->db->insert('groups_fishing_log', $logs);

		$logs = [];
		$logs['mchId'] = $user->mchId;
		$logs['fishingId'] = $box->id;
		$logs['userId'] = $box->userId;
		$logs['fType'] = 1; // 炸死了捞红包的
		$logs['amount'] = $box->amount * 2;
		$logs['createTime'] = $theTime;
		debug('insert groups_fishing_log: '. json_encode($logs));
		$this->db->insert('groups_fishing_log', $logs);

		debug('update user_accounts old data: '. json_encode($userAccount));
		debug('new data: '. json_encode(['amount' => $userAccount->amount - $box->amount]));
		$this->db->query("update user_accounts set amount = amount - ? where userId = ? and moneyType = 0", [$box->amount, $user->id]);

		debug('update user_accounts old data: '. json_encode($userAccount));
		debug('new data: '. json_encode(['amount' => $userAccount->amount + ($box->amount * 2)]));
		$this->db->query("update user_accounts set amount = amount + ? where userId = ? and moneyType = 0", [$box->amount * 2, $box->userId]);
		$this->db->query("update groups_fishing set status = 1 where id = ?", [$box->id]);
	}

}