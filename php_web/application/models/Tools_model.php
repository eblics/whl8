<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Tools_model extends CI_Model {
	public function __construct()
    {
        // Call the CI_Model constructor
        parent::__construct();
        $this->mchId = $this->session->userdata('mchId');
        $this->load->library('common/code_encoder');
    }
    public function get_code_info($code){
      $sql="select g.name mname,g.id mid,f.name aname,f.id aid,FROM_UNIXTIME(f.endTime) aendTime,FROM_UNIXTIME(a.scanTime) scanTime,b.nickName,b.openid,ifnull(c.fullName,d.address) scanAddress,ifnull(round(e.amount/100,2),0) amount,a.over from scan_log a
left join users b on a.userId=b.id
left join areas c on a.areaCode=c.code
left join geo_gps d on a.geoId=d.id
left join user_redpackets e on a.code=e.code
left join sub_activities f on a.activityId=f.id
left join merchants g on a.mchId=g.id
where a.code=?";
        return $this->db->query($sql,[$code])->row();
    }
    public function get_userscan_info($userId){
        $sql="select a.mchId,a.id userId,a.nickName,a.headimgurl,a.openid,a.province,a.city,a.country,c.commonStatus,d.logDesc,d.logUrl from users a 
left join users_common_sub b on a.id=b.userId
left join users_common c on b.parentId=c.id
left join users_common_log d on d.userId=c.id
where a.id=? order by d.createTime desc";

        $result=$this->db->query($sql,[$userId])->row_array();
        $result['scanList']=$this->db->query("select id scanId,mchId,userId,openid,FROM_UNIXTIME(scanTime) scanTime from scan_log where userId=?",[$userId])->result_array();
        return $result;
    }

    public function get_code_noscan($code_ret){
        $mcode=$code_ret->result->mch_code;
        $val=$code_ret->result->value;
        $sql="SELECT b.id batchId,b.state batchState,b.batchNo batchNo,m.name mchName,m.id mchId 
        from batchs b INNER JOIN merchants m on m.id=b.mchId 
        where m.code='$mcode' and b.start<=$val and b.end>=$val";
        return $this->db->query($sql)->row();
    }

    public function is_mch_code($code){
        if($this->mchId==0) return true;
        $code_ret=$this->code_encoder->decode($code);
        $mcode=$code_ret->result->mch_code;
        $merchant=$this->db->query("select * from merchants where code='$mcode'")->row();
        if(!$merchant || $merchant->id!=$this->mchId){
            return false;
        }
        return true;
    }

    public function getInfo_from_openid($openid){
        $sql="select m.*,m.redAmount-m.transMoney r_tMoney from (
select t1.userId,t1.openid,t1.mchId,t4.commonStatus,count(t1.id) scanNum,count(t2.id) redNum,
(select round(sum(amount)/100,2) money from user_redpackets where userId=(select id from users where openid=?)) redAmount,
(select round(amount/100,2) money from user_accounts where userId=(select id from users where openid=?) and moneyType=0) accMoney,
(select round(sum(amount)/100,2) money from user_trans where userId=(select id from users where openid=?)) transMoney
from scan_log t1
left join user_redpackets t2 on t1.code=t2.code
left join users_common_sub t3 on t3.openid=t1.openid
left join users_common t4 on t3.parentId=t4.id
left join user_accounts t5 on t5.userId=t1.userId
where t1.openid=? and t5.moneyType=0 group by t1.openid)m;";
        return $this->db->query($sql,[$openid,$openid,$openid,$openid])->row();
    }

    public function get_userscan_info_Bak($userId){
        $sql="select a.mchId,a.id userId,a.nickName,a.headimgurl,a.openid,a.province,a.city,a.country,c.commonStatus,d.* from users a 
left join users_common_sub b on a.id=b.userId
left join users_common c on b.parentId=c.id
left join users_common_log d on d.userId=c.id
where a.id=? order by d.createTime desc";

        $result=$this->db->query($sql,[$userId])->row_array();
        $result['scanList']=$this->db->query("select a.id scanId,a.mchId,a.userId,a.openid,FROM_UNIXTIME(a.scanTime) scanTime,a.code,b.batchNo from scan_log a left join batchs b on a.batchId=b.id where a.userId=?",[$userId])->result_array();
        return $result;
    }
    //码追踪
    public function get_trace($type,$term){
        $sql="select ifnull(b.nickName,'欢乐扫用户') nickname,a.mchUserId userId,a.lecode code,FROM_UNIXTIME(a.createTime) theDate,logDesc from users_common_log a
left join users b on a.mchUserId=b.id
where ";
        if($type==1){
            $sql.="a.lecode=? ";
        }
        if($type==2){
            $sql.="a.mchUserId=(select id from users where openid=?) ";
        }

        if($type==3){
            $sql.="a.mchUserId=? ";
        }
        
        $sql.="order by theDate;";
         return $this->db->query($sql,[$term])->result();
    }
    //查询用户是否是当前企业
    public function get_user($type,$term){
        if($type==2){
            return $this->db->query("select * from users where openid=?",[$term])->row();
        }
        if($type==3){
            return $this->db->query("select * from users where id=?",[$term])->row();
        }
    }
}
