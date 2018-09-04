<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class App_model extends CI_Model {
    public function __construct()
    {
        // Call the CI_Model constructor
        parent::__construct();
        $this->load->model('user_redpacket_model');
    }
    //根据id取一条应用记录
    public function get($id){
        return $this->db->where('id',$id)->where('rowStatus',0)->get('apps')->row();
    }
    
    //根据id取一条应用实例记录
    public function get_inst($id){
        return $this->db->where('id',$id)->where('rowStatus',0)->get('app_inst')->row();
    }

    //根据appid appsecret取一条应用实例记录
    public function get_by_appsecret($appid,$appsecret){
        return $this->db->where('appId',$appid)->where('appSecret',$appsecret)->where('rowStatus',0)->get('app_inst')->row();
    }

    //根据token取一条应用实例记录
    public function get_by_token($token){
        return $this->db->where('token',$token)->where('rowStatus',0)->get('app_inst')->row();
    }
    
    //更新应用实例的token
    public function update_token($id,$token,$time){
        return $this->db->where('id',$id)->update('app_inst',['token'=>$token,'tokenTime'=>$time]);
    }

    //查询用户红包抽奖结果
    public function get_result_rp($userId,$app){
        return $this->db->where('userId',$userId)->where('instId',$app->id)->get('user_redpackets')->result();
    }

    //查询用户乐券抽奖结果
    public function get_result_card($userId,$app){
        $sql="select uc.id id,uc.userId userId,uc.role role,uc.cardId cardId,c.title name from user_cards uc 
        inner join cards c on c.id=uc.cardId 
        where uc.userId=$userId and uc.instId=$app->id and uc.transId=-1";
        return $this->db->query($sql)->result();
    }
    //发放红包奖品
    public function send_app_packtets($user,$app){
        $packets=$this->user_redpacket_model->get_app_pending_packets($user,$app->id);
        $amount=0;
        foreach($packets as $pa){
            $amount+=$pa->amount;
            $this->user_redpacket_model->send_after_subscribe($pa);
        }
        return $amount;
    }
    //发放乐券奖品
    public function send_app_cards($user,$app){
        $cards=$this->user_redpacket_model->get_app_pending_cards($user,$app->id);
        $num=0;
        foreach($cards as $pa){
            $num+=1;
            $this->user_redpacket_model->send_cards_after_subscribe($pa,$user);
        }
        return $num;
    }
    //发放积分奖品
    public function send_app_points($user,$app){
        $points=$this->user_redpacket_model->get_app_pending_points($user,$app->id);
        $num=0;
        foreach($points as $pa){
            $num+=1;
            $this->user_redpacket_model->send_points_after_subscribe($pa,$user);
        }
        return $num;
    }
    
}