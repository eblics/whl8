<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class User_deal_model extends CI_Model {
    public function __construct()
    {
        parent::__construct();
    }

    //获取企业级封号用户列表
    function get_mch_forbidden_users($mchId,$search,&$count,$start,$length){
        $mchId=intval($mchId);
        $start=intval($start);
        $length=intval($length);
        $countSql='select count(1) c 
            from users_common_sub s 
            inner join users_common cu on cu.id=s.parentId 
            inner join users u on u.id=s.userId 
            where s.mchId=? and u.mchId=? and s.status=1 ';
        $data=[$mchId,$mchId];

        if(isset($search)&&$search!==''&&$search!==NULL){
            $countSql.='and concat(u.id,u.nickName,u.openid) like ?';
            $data[]='%'.$search.'%';
        }
        $countSql=$this->db->compile_binds($countSql,$data);
        $count=$this->db->query($countSql)->row()->c;
        // --------------------------------------------------------
        // 
        $sql='select u.id userId,u.subscribe,u.openid,u.nickName,u.headimgurl,s.parentId,s.status,from_unixtime(g.createTime) logTime,g.logDesc,g.lecode,a.status applyStatus,from_unixtime(a.createTime) applyTime,a.mark 
            from users_common_sub s 
            inner join users_common cu on cu.id=s.parentId 
            inner join users u on u.id=s.userId 
            left join users_common_log g on g.mchUserId=u.id and g.mchId=? and g.logType=1 and g.id=(select max(id) from users_common_log where mchId=? and mchUserId=u.id and logType=1) 
            left join service_appeal a on a.openId=cu.openid and a.id=(select max(id) from service_appeal where openId=a.openId) where s.mchId=? and u.mchId=? and s.status=1 ';

        $data1=[$mchId,$mchId,$mchId,$mchId];

        if(isset($search)&&$search!==''&&$search!==NULL){
            $sql.='and concat(u.id,ifnull(u.nickName,"欢乐扫用户"),u.openid) like ? ';
            $data1[]='%'.$search.'%';
        }
        $sql.='order by logTime desc ';
        $sql.='limit ?,?';
        $data1[]=$start;
        $data1[]=$length;

        $sql=$this->db->compile_binds($sql,$data1);
        log_message('debug',$sql);
        return $this->db->query($sql)->result();
    }

    //查看用户申诉详情
    function get_forbidden_user_apply($mchId,$userId){
        $sql='select a.*,u.nickName,u.openid,u.id userId,a.mark,a.refuse,b.remark
            from users_common_sub s 
            inner join users_common cu on cu.id=s.parentId 
            inner join users u on u.id=s.userId 
            inner join service_appeal a on a.openId=cu.openid 
            left join users_common_blacklist b on b.openid=cu.openid
            where s.mchId=? and s.userId=? order by a.createTime desc limit 0,1';
        $sql=$this->db->compile_binds($sql,[$mchId,$userId]);
        log_message('debug',$sql);
        return $this->db->query($sql)->row();
    }

    //直接解封
    function deal_forbidden_user_unlock($mchId,$userId,&$isBlack){
        $black=$this->db->query("select * from users_common_blacklist b inner join users_common_sub s on s.parentId=b.userId where s.mchId=$mchId and s.userId=$userId")->row();
        $isBlack=0;
        if($black){
            $isBlack=1;
            return false;
        }
        $this->db->query("update service_appeal a 
            inner join users_common u on u.openid=a.openId 
            inner join users_common_sub s on s.parentId=u.id 
            set a.status=2 
            where s.mchId=$mchId and s.userId=$userId and a.status<2");
        $sql='update users_common_sub s 
            inner join users_common u on u.id=s.parentId 
            set s.status=0 
            where s.mchId=? and s.userId=?';
        $sql=$this->db->compile_binds($sql,[$mchId,$userId]);
        log_message('debug',$sql);
        return $this->db->query($sql);
    }

    //解封申诉请求
    function deal_forbidden_user_apply_unlock($mchId,$id,$userId,&$isBlack){
        $black=$this->db->query("select * from users_common_blacklist b inner join users_common_sub s on s.parentId=b.userId where s.mchId=$mchId and s.userId=$userId")->row();
        $isBlack=0;
        if($black){
            $isBlack=1;
            return false;
        }
        $sql='update service_appeal a 
            inner join users_common u on u.openid=a.openId 
            inner join users_common_sub s on s.parentId=u.id 
            set a.status=2,s.status=0 
            where s.mchId=? and a.id=? and s.userId=?';
        $sql=$this->db->compile_binds($sql,[$mchId,$id,$userId]);
        log_message('debug',$sql);
        return $this->db->query($sql);
    }

    //备注申诉请求
    function deal_forbidden_user_apply_mark($mchId,$id,$userId,$value){
        $sql='update service_appeal a 
            inner join users_common u on u.openid=a.openId 
            inner join users_common_sub s on s.parentId=u.id 
            set a.mark=? 
            where s.mchId=? and a.id=? and s.userId=?';
        $sql=$this->db->compile_binds($sql,[$value,$mchId,$id,$userId]);
        log_message('debug',$sql);
        return $this->db->query($sql);
    }

    //驳回申诉请求
    function deal_forbidden_user_apply_refuse($mchId,$id,$userId,$value){
        $sql='update service_appeal a 
            inner join users_common u on u.openid=a.openId 
            inner join users_common_sub s on s.parentId=u.id 
            set a.refuse=?,a.status=3
            where s.mchId=? and a.id=? and s.userId=?';
        $sql=$this->db->compile_binds($sql,[$value,$mchId,$id,$userId]);
        log_message('debug',$sql);
        return $this->db->query($sql);
    }

    //拉黑申诉请求
    function deal_forbidden_user_apply_blacklist($mchId,$id,$userId,$value){
        $sql='update service_appeal a 
            inner join users_common u on u.openid=a.openId 
            inner join users_common_sub s on s.parentId=u.id 
            set a.status=3
            where s.mchId=? and a.id=? and s.userId=?';
        $sql=$this->db->compile_binds($sql,[$mchId,$id,$userId]);
        $this->db->query($sql);
        $sql2='insert ignore into users_common_blacklist(userId,openid,remark,createTime) 
            (select u.id userId,u.openid,? remark,? createTime from users_common u 
            inner join users_common_sub s on s.parentId=u.id 
            where s.mchId=? and s.userId=?) on duplicate key update remark=?,createTime=?';
        $sql2=$this->db->compile_binds($sql2,[$value,time(),$mchId,$userId,$value,time()]);
        log_message('debug',$sql2);
        return $this->db->query($sql2);
    }

    

    //获取平台级封号用户列表
    function get_common_forbidden_users($mchId,&$count,$start,$length){
        $mchId=intval($mchId);
        $start=intval($start);
        $length=intval($length);
        $sql='select u.id commonUserId,u.openid commonOpenid,u.headimgurl,u.nickName,s.openid,u.commonStatus,s.status from users_common u 
            inner join users_common_sub s on u.id=s.parentId where s.mchId=? and u.commonStatus=1  order by u.id asc limit ?,?';
        $sql=$this->db->compile_binds($sql,[$mchId,$start,$length]);
        return $this->db->query($sql)->result();
    }
    

}
