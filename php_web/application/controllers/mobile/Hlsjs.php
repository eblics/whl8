<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Api
 */
class Hlsjs extends Mobile_Controller {

    // public function v1($code = NULL){
    //     header('Content-Type: application/javascript;charset=utf-8;');
    //     $jsticket=['appId'=>'','timestamp'=> 0,'nonceStr'=>'','signature'=>''];
    //     if (! isset($code)) {
    //         $code = $this->getCurrentLecode();
    //     }
    //     if(!isset($code)){
    //         $this->load->view('hlsjs',$jsticket);
    //         return;
    //     }
    //     $code_ret=$this->code_encoder->decode($code);
    //     if($code_ret->errcode!=0){
    //         $this->load->view('hlsjs',$jsticket);
    //         return;
    //     }
    //     $mch_code=$code_ret->result->mch_code;
    //     //$user=$this->user->get_by_openid($this->session->userdata('openid_'.$mch_id));
    //     $merchant=$this->merchant->get_by_code($mch_code);
    //     if(!isset($merchant)){
    //         $this->load->view('hlsjs',$jsticket);
    //         return;
    //     }
    //     $this->load->library('weixin_jssdk',['appId'=>$merchant->wxAppId,'appSecret'=>$merchant->wxAppSecret]);
    //     $url=$_SERVER['HTTP_REFERER'];
    //     //str_replace($code,'',$url);
    //     $data=$this->weixin_jssdk->get_url_SignPackage($url);
    //     //$data=$this->weixin_jssdk->getSignPackage();
    //     $data['code']=$code;
    //     $this->load->view('hlsjs',$data);
    // }
    /*
     * js获取用户信息，这个建立在已经取到用户openid的前提下
     */

    /**
     * @deprecated please use /hlsjs/get_current_user
     * @return ajax
     */
    public function get_user_info() {
        $this->get_current_user();
    }

    public function get_current_user() {
        $currentUser = $this->getCurrentUser($this->getCurrentMchId());
        $this->ajaxResponseSuccess($currentUser);
    }

}
