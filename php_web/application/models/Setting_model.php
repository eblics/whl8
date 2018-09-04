<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Setting_model extends CI_Model {
    public function __construct()
    {
        // Call the CI_Model constructor
        parent::__construct();
    }
    //获取扫码频率设置
    public function get_mch_scan_rule($mchId){
        return $this->db->where('mchId',$mchId)->get('mch_scan_rules')->row();
    }
    //保存扫码频率设置
    public function save_mch_scan_rule($data){
        if(isset($data->id)){
            $this->db->where('id',$data->id)->update('mch_scan_rules',$data);
            return $data->id;
        }else{
            $this->db->insert('mch_scan_rules',$data);
            return $this->db->insert_id();
        }
    }

    public function get($name){
       $row=$this->db->query('select val from settings where name=?',[$name])->row();
       if(!empty($row)){
           return $row->val;
       }
       return NULL;
    }
    //获取微信通知用户
    public function get_receive_users($mchId){
        $ids = $this->db->query('select userId from warning_accounts where mchId=?',[$mchId])->result();
        if(empty($ids)){
            return false;
        }
        $data = [];
        foreach ($ids as $k => $v) {
            array_push($data, $v->userId);
        }
        $string = implode(",", $data);
        $sql = "select id,nickName,headimgurl from users where mchId=$mchId and id in ($string)";
        $res = $this->db->query($sql)->result();
        return $res;
    }
    //
    public function del_user($id,$mchId){
        $res = $this->db->delete('warning_accounts',array('userId'=>$id,'mchId'=>$mchId));
        return $res;
    }
    //
    public function find_user($nickName,$mchId){
        return $this->db->like('nickName',$nickName)->where('mchId',$mchId)->get('users')->result();
    }
    public function add_user($id,$mchId){
        $r = $this->db->where('userId',$id)->where('mchId',$mchId)->get('warning_accounts')->row();
        if(!empty($r)){
            return 'exists';
        }else{
            $data = array(
                'userId' => $id,
                'mchId' => $mchId
            );
            $res = $this->db->insert('warning_accounts', $data);
            if($res){
                return 'add success';
            }else{
                return 'add false';
            } 
        }
    }

    public function saveUserScanOtherTimes($mchId, $times) {
        $updated = $this->db->where('mchId', $mchId)->set('scan_other_times', $times)->update('mch_scan_rules');
        if (! $updated) {
            throw new Exception("发生未知错误", 1);
        }
    }

}