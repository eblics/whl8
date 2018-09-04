<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Cashier_model extends CI_Model {
    public function __construct()
    {
        parent::__construct();
    }
    //查询出回调
    public function get_notify_by_wxnum($wxnum){
        return $this->db->where('wxnum',$wxnum)->get('notify')->row();
    }
    //新增回调
    public function add_notify($data){
        $this->db->insert('notify',$data);
        return $this->db->insert_id();
    }
    //新增订单
    public function add_order($data){
        $this->db->insert('mch_orders',$data);
        return $this->db->insert_id();
    }
    //企业账户余额扣款操作
    public function payment($mchId,$amount){
        $this->db->trans_begin();
        $this->db->query("update mch_balances set amount=amount-$amount where mchId=$mchId");
        if ($this->db->trans_status() === FALSE){
            $this->db->trans_rollback();
            return false;
        }else{
            $this->db->trans_commit();
            return true;
        }
    }
}
