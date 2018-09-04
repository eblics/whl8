<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Member_model extends CI_Model {
    public function __construct()
    {
        // Call the CI_Model constructor
        parent::__construct();
    }
    /**
     * 获取会员列表（只显示维护过手机号码的用户）
     */
    public function get_memberlist($mchId,&$count,$start,$length){
        $count=$this->db->query('select count(*) cnt from users where mchId=? and mobile>0',[$mchId])->row()->cnt;
        $sql="select id userId,headimgurl,nickName,realName,sex,mobile,ifnull(address,'未填写') address from users where mchId=? and mobile>0";//,from_unixtime(subscribe_time) subscribe_time,from_unixtime(createTime) createTime
        $data=[$mchId];
        if(isset($start)&&isset($length)){
            $sql.=' limit ?,?';
            $data[]=intval($start);
            $data[]=intval($length);
        }
        $sql=$this->db->compile_binds($sql,$data);
        return $this->dbhelper->serve_array($sql);
    }
}
