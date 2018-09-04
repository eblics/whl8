<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Common_lib{
    var $CI;
    public function __construct() {
        $this->CI=&get_instance();
        $this->CI->load->model('user_redpacket_model');
    }

    public function send_pending_packtets($user){
        $packets=$this->CI->user_redpacket_model->get_pending_packets($user);
        $amount=0;
        foreach($packets as $pa){
            $amount+=$pa->amount;
            $this->CI->user_redpacket_model->send_after_subscribe($pa);
        }
        return $amount;
    }
    
    public function send_pending_cards($user){
        $cards=$this->CI->user_redpacket_model->get_pending_cards($user);
        $num=0;
        foreach($cards as $pa){
            $num+=1;
            $this->CI->user_redpacket_model->send_cards_after_subscribe($pa,$user);
        }
        return $num;
    }
    

    /*
     * 根据客户端要求，返回协议要求的结果
     */
    public function encode_output($data,$type = 'json'){
        if($type=='json') {
            header('Content-Type: application/json;charset=utf-8;');
            return json_encode($data);
        }
        if($type=='jsonp'){
            header('Content-Type: application/javascript;charset=utf-8;');
            return $_GET['callback'].'('.json_encode($data).');';
        }
        return $data;
    }
}


