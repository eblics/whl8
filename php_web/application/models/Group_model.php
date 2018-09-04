<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * 
 * 好友圈
 *
 */
class Group_model extends CI_Model {
	public function __construct() {
        parent::__construct();
    }
	public function get_group($id) {
		return $this->db->query("select * from groups where id=$id and rowStatus=0")->row();
	}
	
	public function get_group_recommend($mchId,$userId,$num) {
		$userGroup=$this->db->query("select * from groups_members where userId=$userId and rowStatus=0")->result();
		$groupIdSql='';
		if($userGroup){
			foreach($userGroup as $k=>$v){
				$groupIdSql.=' and id!='.$v->groupId;
			}
		}
		$statusSql3='';
		$userAccount=$this->db->query("select * from user_accounts where userId=$userId and moneyType=0")->row();
		if($userAccount){
			if($userAccount->amount>500){
				$statusSql3='or status=3';//有钱人
			}else{
				$statusSql3='or status=4';//穷人
			}
		}else{
			$statusSql3='or status=4';//穷人
		}
		// $userScan=$this->dbhelper->serverow("select count(1) as count from scan_log where userId=$userId");
		// if($userScan->count>50){

		// }
		// log_message('error',"select * from groups where mchId=$mchId $groupIdSql and rowStatus=0 and (status=2 $statusSql3) limit 0,$num");
		return $this->db->query("select * from groups where mchId=$mchId $groupIdSql and rowStatus=0 and (status=2 $statusSql3) limit 0,$num")->result();
	}

	public function get_group_member($id) {
		return $this->db->query("select * from groups_members where groupId=$id and status=0 and rowStatus=0 order by id asc")->result();
	}

	public function get_group_member_one($groupId,$userId) {
		return $this->db->query("select * from groups_members where groupId=$groupId and userId=$userId and status=0 and rowStatus=0")->row();
	}

	public function get_group_master($id) {
		return $this->db->query("select * from groups_members where groupId=$id and role=1 and rowStatus=0")->row();
	}

	public function get_all_group($mchId) {
		return $this->db->query("select * from groups where mchId=$mchId and rowStatus=0 order by id desc")->result();
	}

	public function get_group_page($mchId,$start,$length) {
		return $this->db->query("select * from groups where mchId=$mchId and rowStatus=0 order by id desc limit $start,$length")->result();
	}

	public function get_all_group_count($mchId) {
		return $this->db->query("select count(1) count from groups where mchId=$mchId and rowStatus=0")->row();
	}
	
	public function get_my_group($userId) {
		return $this->db->query("select * from groups_members m 
		inner join groups g on g.id=m.groupId where m.userId=$userId and m.status!=1 and g.status!=1 and m.rowStatus=0 and g.rowStatus=0")->result();
	}

	public function get_group_by_password($password) {
		return $this->db->query("select * from groups where password='$password' and rowStatus=0")->row();
	}

	public function add_group($groupData,$userData){
		$this->db->trans_begin();
        $this->db->insert('groups',$groupData);
        $insertId = $this->db->insert_id();
		$userData['groupId']=$insertId;
		$this->add_group_member($insertId,$userData);
		if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            return FALSE;
        }else{
			$this->db->trans_commit();
		}
		return $insertId;
    }

	public function update_group($data){
        $save=$this->db->where('id',$data['id'])->update('groups',$data);
		if($save){
        	return $data['id'];
		}else{
			return FALSE;
		}
    }
	
	public function del_group($id){
        return $this->db->where('id',$id)->update('groups',['rowStatus'=>1]);
    }

	public function add_group_member($groupId,$data){
		$this->db->trans_begin();
		$key='';
		$val='';
		$index=0;
		foreach($data as $k=>$v){
			if($index==0){
				$key.='`'.$k.'`';
				$val.="'".$v."'";
			}else{
				$key.=','.'`'.$k.'`';
				$val.=','."'".$v."'";
			}
			$index++;
		}
		$this->db->query("insert into groups_members($key) VALUES($val) on DUPLICATE key update status=0");
		log_message('debug',"insert into groups_members($key) VALUES($val) on DUPLICATE key update status=0");
		$insertId=$this->db->insert_id();
		$this->db->query("update groups set memberNum=memberNum+1 where id=$groupId and status!=1 and rowStatus=0");
		if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            return FALSE;
        }else{
			$this->db->trans_commit();
		}
        return $insertId;
    }

	public function update_group_member($id,$data){
        return $this->db->where('id',$id)->where('rowStatus',0)->update('groups_members',$data);
    }

	public function search_group($mchId,$txt) {
		return $this->db->query("select id,groupName from groups where mchId=$mchId and groupName like '%$txt%' and status!=1 and rowStatus=0")->result();
	}

	public function get_group_setting($mchId) {
		return $this->db->query("select * from groups_setting where mchId=$mchId and rowStatus=0")->row();
	}

	public function add_group_setting($data){
        $this->db->insert('groups_setting',$data);
		$insertId=$this->db->insert_id();
        return $insertId;
    }

	public function update_group_setting($data){
        return $this->db->where('mchId',$data['mchId'])->where('rowStatus',0)->update('groups_setting',$data);
    }

	public function delete_group($id){
		return $this->db->where('id',$id)->where('rowStatus',0)->update('groups',['status'=>1]);
    }

	public function delete_group_member($id,$groupId){
		$this->db->trans_begin();
		$this->db->where('id',$id)->where('rowStatus',0)->update('groups_members',['status'=>1]);
		$this->db->query("update groups set memberNum=memberNum-1 where id=$groupId and status!=1 and rowStatus=0");
		if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            return FALSE;
        }else{
			$this->db->trans_commit();
		}
        return TRUE;
    }

}