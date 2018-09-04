<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Wxpay_model extends MY_Model {
    public function __construct()
    {
        parent::__construct();
        $this->mchId=$this->session->userdata('mchId');
    }

    //查余额
    public function get_balance($mchId){
        $sql="select * from mch_balances where mchId=$mchId";
        return $this->db->query($sql)->row();
    }

    //企业所有订单
    public function get_mch_list(){
        $sql="select orderId,amount,from_unixtime(createTime) createTime,status,level from mch_orders where mchId=$this->mchId and rowStatus=0 order by id desc";
        return $this->db->query($sql)->result();
    }

    //查订单
    public function get_order($orderId){
        $sql="select * from mch_orders where orderId='$orderId' and rowStatus=0 and level=0";
        return $this->db->query($sql)->row();
    }

    //查订单
    public function get_mch_order($orderId){
        $sql="select * from mch_orders where mchId=$this->mchId and orderId='$orderId' and rowStatus=0 and level=0";
        return $this->db->query($sql)->row();
    }

    //查未完成的订单
    public function get_mch_order_doing(){
        $sql="select id,mchId,orderId,amount amount,status,from_unixtime(createTime) createTime from mch_orders where mchId=$this->mchId and status=0 and rowStatus=0 and level=0";
        return $this->db->query($sql)->row();
    }

    //生成订单
    public function order_save($data){
        $sql=$this->dbhelper->insert_update_string('mch_orders',$data);
        $this->db->query($sql);
        if(isset($data->id)){
            return $data->id;
        }else{
            return $this->db->insert_id();
        }
    }

    /**
     * 更新订单状态并更新企业账户余额
     * @param $orderId       
     * @param $transaction_id
     * @return void
     */
    public function deal_order($orderId, $transaction_id) {
        error('wxpay-model-deal-order - begin');
        error('wxpay-model-deal-order - params: '. json_encode(func_get_args()));

        try {
            $this->beginTransition();
            $sql = "select * from mch_orders where orderId = ? and status = 0 and rowStatus = 0 and level = 0 for update";
            $order = $this->db->query($sql, [$orderId])->row();
            if (! isset($order)) {
                throw new Exception("订单不存在", 100404);
            }
            if ($order->amount >= 100) {
                $order->amount = $order->amount * 0.99;
            }
            error('wxpay-model-deal-order - update mch_orders old: '. json_encode($order));
            error('wxpay-model-deal-order - update mch_orders new: '. json_encode([$transaction_id, $orderId]));
            $sql = "update mch_orders set status = 1, wxNum = ? where orderId = ?";
            $this->db->query($sql, [$transaction_id, $orderId]);
            if ($this->db->affected_rows() !== 1) {
                throw new Exception("Update mch_orders fail", 1);
            }
            $balance = $this->db->query("select * from mch_balances where mchId=$order->mchId")->row();
            if (! isset($balance)) {
                error('wxpay-model-deal-order - insert mch_balances: '. $order->amount);
                $this->db->query("insert into mch_balances(mchId,amount) values($order->mchId,$order->amount)");
            } else {
                error('wxpay-model-deal-order - update mch_balances old: '. json_encode($balance));
                error('wxpay-model-deal-order - update mch_balances new: '. ($order->amount + $balance->amount));
                $this->db->query("update mch_balances set amount=amount+$order->amount where mchId=$order->mchId");
            }
            if (! $this->checkTransitionSuccess()) {
                throw new Exception("发生未知错误", 1);
            }
            $this->commitTransition();
            return TRUE;
        } catch (Exception $e) {
            $this->rollbackTransition();
            error('wxpay-model-deal-order - fail: '. $e->getMessage());
            throw $e;
        } finally {
            error('wxpay-model-deal-order - end');
        }
    }
}