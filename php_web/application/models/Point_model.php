<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Point_model extends CI_Model {
    var $redis;
    public function __construct(){
        parent::__construct();
        $this->redis=new Redis();
        $this->redis->pconnect($this->config->item('redis')['host'],$this->config->item('redis')['port']);
        if(isset($this->config->item('redis')['password'])){
            $this->redis->auth($this->config->item('redis')['password']);
        }
    }

    function get($id){
        return $this->db->where('id',$id)->where('rowStatus',0)->get('points')->row();
    }
    function get_by_mchid($mchId){
        return $this->db->where('mchId',$mchId)->where('rowStatus',0)->order_by('id','desc')->get('points')->result();
    }
    function get_sub_by_mchid($mchId){
        return $this->db->where('mchId',$mchId)->order_by('id','desc')->get('points_sub')->result();
    }
    function add($data){
        $this->db->insert('points',$data);
        return $this->db->insert_id();
    }
    function update($id,$data){
        return $this->db->where('id',$id)->update('points',$data);
    }
    function del($id){
        return $this->db->where('id',$id)->update('points',['rowStatus'=>1]);
    }
    function get_sub($id){
        return $this->db->where('id',$id)->get('points_sub')->row();
    }
    function add_sub($data){
        $this->db->insert('points_sub',$data);
        return $this->db->insert_id();
    }
    function update_sub($id,$data){
        $point_sub_key="points_sub.remainNum.id.$id";
        $this->redis->zAdd('limit_zone',$data->remainNum,$point_sub_key);
        return $this->db->where('id',$id)->update('points_sub',$data);
    }
    function del_sub($id){
        return $this->db->where('id',$id)->delete('points_sub');
    }

    /**
     * ---------------------------------------
     * 获取用户总共获取的积分
     * @param $mchId 用户所在的企业编号
     * @param $userId 用户编号
     * @return object
     */
    public function get_user_point($mchId, $userId) {
        $scanGet = $this->db->select_sum('amount')
        ->where('mchId', $mchId)
        ->where('userId', $userId)
        ->where('role', 0)
        ->where('sended', 1)
        ->get('user_points')
        ->row();
        $otherGet = $this->db->select_sum('amount')
        ->where('mchId', $mchId)
        ->where('userId', $userId)
        ->where('role', 0)
        ->get('user_points_get')
        ->row();
        return $scanGet->amount + $otherGet->amount;
    }

    /**
     * ---------------------------------------
     * 获取用户已兑换的积分
     * @param $mchId 用户所在的企业编号
     * @param $userId 用户编号
     * @return number
     */
    public function get_user_point_used($mchId, $userId) {
        $user_points_used = $this->get_user_point_used_list($mchId, $userId);
        $usedAmount = 0;
        foreach ($user_points_used as $usedItem) {
            if ($usedItem->wxStatus == 1 || is_null($usedItem->wxStatus)) {
                   $usedAmount += $usedItem->amount;
            }
        }
        return $usedAmount;
    }

    /**
     * ---------------------------------------
     * 获取用户兑换积分的详细记录
     * @param $mchId 用户所在的企业编号
     * @param $userId 用户编号
     * @return array
     */
    public function get_user_point_used_list($mchId, $userId) {
        $sql = "select t1.amount, t2.wxStatus from user_points_used t1 
            left join user_trans t2 on t2.id = t1.doId and t1.doTable = 'user_trans' where t1.mchId = ? and t1.userId = ? and t1.role = 0";
        $user_points_used = $this->db->query($sql , [$mchId, $userId])->result();
        return $user_points_used;
    }



}
