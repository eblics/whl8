<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * 
 * 好友圈
 *
 */
class Group_scanpk_model extends CI_Model {
	public function __construct() {
        parent::__construct();
        $this->load->model('trigger_model');
    }
	public function get($id) {
		return $this->db->query("select * from groups_scanpk where id=$id and rowStatus=0")->row();
	}
	
	public function get_all_doing() {
		return $this->db->query("select * from groups_scanpk where status=0 and rowStatus=0")->result();
	}

	public function get_all_nopay() {
		$now=time();
		return $this->db->query("select * from groups_scanpk where $now>endTime and status=0 and rowStatus=0")->result();
	}

	public function get_all_paying() {
		$now=time();
		return $this->db->query("select * from groups_scanpk where $now>endTime and status=1 and rowStatus=0")->result();
	}

	public function update_scanpk_scan_num($scanpk,$userId) {
		$scanNum=$this->db->query("select count(id) num from scan_log where userId=$userId and scanTime>=$scanpk->startTime and scanTime<=$scanpk->endTime")->row();
		if($scanNum){
			$sql="update groups_scanpk_users set scanNum=$scanNum->num where scanpkId=$scanpk->id and userId=$userId";
			log_message('debug',$sql);
			return $this->db->query($sql);
		}
		return false;
	}

	public function update_scanpk_status($scanpk,$status) {
		return $this->db->query("update groups_scanpk set status=$status where id=$scanpk->id");
	}

	public function get_scanpk($groupId) {
		return $this->db->query("select * from groups_scanpk where groupId=$groupId and rowStatus=0 order by status asc,id desc")->result();
	}

	public function get_scanpk_by_user($groupId,$userId) {
		return $this->db->query("select s.* from groups_scanpk s inner join groups_scanpk_users u 
			on u.scanpkId=s.id and u.groupId=$groupId and u.userId=$userId and u.rowStatus=0 and s.rowStatus=0 order by s.status asc,s.id desc")->result();
	}

	public function get_scanpk_users($scanpkId,$orderBy='id',$orderRule='asc') {
		return $this->db->query("select * from groups_scanpk_users where scanpkId=$scanpkId and rowStatus=0 order by $orderBy $orderRule")->result();
	}

	public function get_scanpk_users_one($scanpkId,$userId) {
		return $this->db->query("select * from groups_scanpk_users where scanpkId=$scanpkId and userId=$userId and rowStatus=0")->row();
	}

	public function get_scanpk_users_master($scanpkId) {
		return $this->db->query("select * from groups_scanpk_users where scanpkId=$scanpkId and role=1 and rowStatus=0")->row();
	}

	public function add_scanpk($saveData,$mchId){
		$this->db->trans_begin();
		// $account=$this->db->query("select * from user_accounts where userId=$saveData->userId and amount>=$saveData->pkAmount and moneyType=0")->row();
		// if(!$account){
  //           debug('group_scanpk/add_scan/account not exists');
		// 	$this->db->trans_rollback();
  //           return FALSE;
		// }
		$theTime=time();
		$this->db->insert('groups_scanpk',$saveData);
		$scanpkId=$this->db->insert_id();
		$this->db->query("insert into groups_scanpk_users(groupId,scanpkId,userId,role,createTime,updateTime) 
							values($saveData->groupId,$scanpkId,$saveData->userId,1,$theTime,$theTime)");
		$scanpkUserId=$this->db->insert_id();
		if($saveData->pkType===0){
			$this->db->query("update user_accounts set amount=amount-$saveData->pkAmount where userId=$saveData->userId and moneyType=0");
			$this->db->query("insert into user_accounts_used(userId,mchId,doTable,doId,amount,role,createTime) 
							values($saveData->userId,$mchId,'groups_scanpk_users',$scanpkUserId,$saveData->pkAmount,0,$theTime)");
		}
		if($saveData->pkType===1){
			$this->db->query("update user_points_accounts set amount=amount-$saveData->pkAmount where userId=$saveData->userId and mchId=$mchId");
			$this->db->query("insert into user_points_used(userId,mchId,doTable,doId,amount,role,createTime) 
							values($saveData->userId,$mchId,'groups_scanpk_users',$scanpkUserId,$saveData->pkAmount,0,$theTime)");
			// 积分使用触发器 add by cw
			debug("CW-exchange point - begin");
			$data=[
				'mchId'=>$mchId,
				'userId'=>$saveData->userId,
				'amount'=>$saveData->pkAmount
			];
	        $this->trigger_model->trigger_point_used((object)$data);
	        debug(json_encode($data));
	        debug("CW-exchange point - end");
	        // 积分使用触发器 end
		}
		if ($this->db->trans_status() === FALSE) {
            debug('group_scanpk/add_scan/trans false');
            $this->db->trans_rollback();
            return FALSE;
        }else{
			$this->db->trans_commit();
		}
		return $scanpkId;
    }

	public function join_scanpk($scanpk,$user){
		$this->db->trans_start();
		// $account=$this->db->query("select * from user_accounts where userId=$user->id and amount>=$scanpk->pkAmount and moneyType=0")->row();
		// if(!$account){
  //           return FALSE;
		// }
		$theTime=time();
		$this->db->query("insert into groups_scanpk_users(groupId,scanpkId,userId,role,createTime,updateTime) 
							values($scanpk->groupId,$scanpk->id,$user->id,0,$theTime,$theTime)");
		$scanpkUserId=$this->db->insert_id();
		if($scanpk->pkType==0){
			$this->db->query("update user_accounts set amount=amount-$scanpk->pkAmount where userId=$user->id and moneyType=0");
			$this->db->query("insert into user_accounts_used(userId,mchId,doTable,doId,amount,role,createTime) 
							values($user->id,$user->mchId,'groups_scanpk_users',$scanpkUserId,$scanpk->pkAmount,0,$theTime)");
		}
		if($scanpk->pkType==1){
			$this->db->query("update user_points_accounts set amount=amount-$scanpk->pkAmount where userId=$user->id and mchId=$user->mchId");
			$this->db->query("insert into user_points_used(userId,mchId,doTable,doId,amount,role,createTime) 
							values($user->id,$user->mchId,'groups_scanpk_users',$scanpkUserId,$scanpk->pkAmount,0,$theTime)");
			// 积分使用触发器 add by cw
			debug("CW-exchange point - begin");
			$data=[
				'mchId'=>$user->mchId,
				'userId'=>$user->id,
				'amount'=>$scanpk->pkAmount
			];
	        $this->trigger_model->trigger_point_used((object)$data);
	        debug(json_encode($data));
	        debug("CW-exchange point - end");
	        // 积分使用触发器 end
		}
		$this->db->trans_complete();
		return $scanpk->id;
    }

	public function pay_scanpk($scanpkPaying){
		foreach ($scanpkPaying as $k => $v) {
			$this->db->trans_begin();
			$scanpkConfirm=$this->db->query("select * from groups_scanpk where id=$v->id for update")->row();
			if(! $scanpkConfirm) continue;
			if($scanpkConfirm->status!=1)  continue;
            $thisScanpkUsers=$this->get_scanpk_users($v->id,'scanNum','desc');
            $amount=$v->pkAmount;
            $topNum=$thisScanpkUsers[0]->scanNum;
            $winUser=[];
            if($topNum==0){
                array_push($winUser,$v->userId);
            }else{
                foreach ($thisScanpkUsers as $k2 => $v2) {
                    if($v2->scanNum==$topNum){
                        array_push($winUser,$v2->userId);
                    }
                }
            }
            $pkUserNum=count($thisScanpkUsers);
            $winUserNum=count($winUser);
            $perWinnerGet=bcdiv($pkUserNum*$amount,$winUserNum,2);
            if($v->pkType==1){
                $perWinnerGet=bcdiv($pkUserNum*$amount,$winUserNum);
            }
            foreach($winUser as $k3=>$v3){
                $this->pay_scanpk_winner($v,$v3,$perWinnerGet);
            }
            $this->update_scanpk_status($v,2);
			if ($this->db->trans_status() === FALSE) {
				$this->db->trans_rollback();
				return FALSE;
			}else{
				$this->db->trans_commit();
			}
        }
    }

	public function pay_scanpk_winner($scanpk,$userId,$perWinnerGet){
		$theTime=time();
		$user=$this->db->query("select * from users where id=$userId")->row();
		if(! $user){
            return FALSE;
		}
		if($scanpk->pkType==0){
			$sql="INSERT INTO user_accounts(userId,mchId,moneyType,amount,role)
                VALUES($user->id,$user->mchId,0,IFNULL(amount,0)+$perWinnerGet,0) ON DUPLICATE KEY UPDATE amount=IFNULL(amount,0)+$perWinnerGet";
            $this->db->query($sql);
			$this->db->query("INSERT INTO user_redpackets_get(userId,mchId,amount,doTable,doId,getTime,role)
                VALUES($user->id,$user->mchId,$perWinnerGet,'groups_scanpk',$scanpk->id,$theTime,0)");
		}
		if($scanpk->pkType==1){
			$sql="INSERT INTO user_points_accounts(userId,mchId,amount,role)
                VALUES($user->id,$user->mchId,IFNULL(amount,0)+$perWinnerGet,0) ON DUPLICATE KEY UPDATE amount=IFNULL(amount,0)+$perWinnerGet";
            $this->db->query($sql);
			$this->db->query("INSERT INTO user_points_get(userId,mchId,amount,doTable,doId,getTime,role)
                VALUES($user->id,$user->mchId,$perWinnerGet,'groups_scanpk',$scanpk->id,$theTime,0)");
		}
		$upStatus=$this->db->query("update groups_scanpk_users set status=1,winner=1 where scanpkId=$scanpk->id and userId=$user->id");
		return TRUE;
    }

	


}