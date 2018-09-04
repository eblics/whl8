<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Common_login{
    var $CI;
    public function __construct() {
        $this->CI=&get_instance();
        $this->CI->load->model('common_login_model');
        $this->CI->load->model('merchant_model');
        $this->CI->load->helper('common/hls');
    }

    public function is_login($forbidden=0){
        if($this->CI->session->has_userdata('common_openid_-1')){
            $this->save_user();
            if($forbidden==0 && $this->is_forbidden()){
                $this->show_forbidden();
            }
            return true;
        }
        return false;
    }

    public function login($forbidden=0){
        if($this->is_login($forbidden)){
            if($forbidden==0 && $this->is_forbidden()){
                $this->show_forbidden();
                return;
            }
            $this->save_user();
        }else{
            $commonMerchant=$this->CI->merchant_model->get(-1);
            $this->CI->weixin_rest_api->login($commonMerchant);
        }
    }

    public function save_user(){
        $openid=$this->CI->session->userdata('common_openid_-1');
        if(empty($openid)){
            return false;
        }
        $user=$this->CI->common_login_model->get_user_by_openid($openid);
        if(! $user){
            $user=(object)['openid'=>$openid];
            $user->createTime=time();
            $user->updateTime=$user->createTime;
        }else{
            $user->updateTime=time();
        }
        return $this->CI->common_login_model->save_user($user);
    }
    
    public function is_forbidden($userSubOpenid=null){
        $openid=$this->CI->session->userdata('common_openid_-1');
        if(empty($openid)){
            return false;
        }
        $user=$this->CI->common_login_model->get_user_by_openid($openid);
        if($user && $user->commonStatus==1){
            return true;
        }
        $blacklist=$this->CI->common_login_model->get_blacklist_by_openid($openid);
        debug('blacklist: '. json_encode($blacklist));
        if($blacklist){
            return true;
        }
        if($userSubOpenid){
            $userSub=$this->CI->common_login_model->get_user_sub_by_openid($userSubOpenid);
            debug('userSub: '. json_encode($userSub));
            if($userSub && $userSub->status==1){
                return true;
            }
        }
        return false;
    }

    public function save_user_sub($userSub){
        $openid=$this->CI->session->userdata('common_openid_-1');
        if(empty($openid)){
            return false;
        }
        $user=$this->CI->common_login_model->get_user_by_openid($openid);
        if(! $user){
            return false;
        }
        $subData=(object)['parentId'=>$user->id,'userId'=>$userSub->id,'openid'=>$userSub->openid,'mchId'=>$userSub->mchId];
        return $this->CI->common_login_model->save_user_sub($subData);
    }

    public function forbidden($commonUser, $currentUser = NULL) {
        $whiteList = $this->CI->common_login_model->get_whitelist_by_openid($commonUser->openid);
        if ($whiteList) {
            return;
        }

        if (isset($currentUser)) {
            // 进行企业级封号
            $sql = "select * from users_common_sub where parentId = ? and mchId = ?";
            $subUser = $this->CI->db->query($sql, [$commonUser->id, $currentUser->mchId])->row();
            if (isset($subUser)) {
                $subUser->status = 1;
                $sql = "update users_common_sub set status = 1 where id = ?";
                $this->CI->db->query($sql, [$subUser->id]);
            } else {
                $sql = "insert into users_common_sub (parentId, userId, openid, mchId, status) values (?, ?, ?, ?, 1)";
                $this->CI->db->query($sql, [$commonUser->id, $currentUser->id, $currentUser->openid, $currentUser->mchId]);
            }
        } else {
            // 进行系统级封号
            $sql = "update users_common set commonStatus = 1 where id = ?";
            $this->CI->db->query($sql, [$commonUser->id]);
        }

        $this->show_forbidden();    
    }

    public function show_forbidden(){
        $this->CI->load->view('error_forbidden',['errmsg'=>'您的微信帐号已被锁定<BR>如有疑问，长按识别二维码，申请解锁<BR><font style="font-size:1rem;color:gray;">您本次扫描的商品包装内的二维码<BR>是重要的申诉凭据，请妥善保存</font>']);
    }

    public function openid(){
        return $this->CI->session->userdata('common_openid_-1');
    }
    
    public function save_user_log($logType,$log=null,$lecode=null,$mchId=-1,$mchUserId=-1){
        $openid=$this->CI->session->userdata('common_openid_-1');
        if(empty($openid)) return false;
        $user=$this->CI->common_login_model->get_user_by_openid($openid);
        if(!$user) return false;
        $userIp=get_real_ip();
        $theTime=time();
        $curUrl=$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
        $referer=isset($_SERVER['HTTP_REFERER'])?$_SERVER['HTTP_REFERER']:'';
        $agent=isset($_SERVER['HTTP_USER_AGENT'])?$_SERVER['HTTP_USER_AGENT']:'';
        $logData=(object)['userId'=>$user->id,'logType'=>$logType,'logDesc'=>$log,'logIp'=>$userIp,'logUrl'=>$curUrl,'lecode'=>$lecode,'agent'=>$agent,'referer'=>$referer,'createTime'=>$theTime,'mchId'=>$mchId,'mchUserId'=>$mchUserId];
        $this->CI->common_login_model->save_user_log($logData);
    }
    
}