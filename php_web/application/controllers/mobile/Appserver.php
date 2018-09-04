<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Api
 */
class Appserver extends Mobile_Controller {
   public function __construct() {
        // 初始化，加载必要的组件
    parent::__construct();
        $this->load->model('app_model');
        $this->load->model('user_model');
    }
    
    /**
     * CI控制器默认入口
     */
    public function index(){
        //如无需使用留空即可
    }

    //完善用户信息（转盘）
    public function supplement_userinfo(){
        $result=(object)['errcode'=>0,'errmsg'=>'ok'];
        $token=$this->input->get_post('token');
        $role=$this->input->get_post('role');
        $openid=$this->input->get_post('openid');
        $mobile=$this->input->get_post('mobile');
        $areaCode=$this->input->get_post('area_code');
        $birthday=$this->input->get_post('birthday');
        if(!isset($role)){
            $result->errcode=1;
            $result->errmsg='缺少角色参数';
            echo json_encode($result);
            return;
        }
        $isWaiter=FALSE;
        if(!empty($role) && intval($role)===1){
            $isWaiter=TRUE;
        }
        $app=$this->app_model->get_by_token($token);
        if(!$app){
            $result->errcode=1;
            $result->errmsg='token有误';
            echo json_encode($result);
            return;
        }
        if($app->status!=1){
            $result->errcode=1;
            $result->errmsg='应用状态异常';
            echo json_encode($result);
            return;
        }
        if($app->payStatus!=1){
            $result->errcode=1;
            $result->errmsg='应用未支付';
            echo json_encode($result);
            return;
        }
        if($app->startTime>time() && $app->endTime<time()){
            $result->errcode=1;
            $result->errmsg='应用服务已到期';
            echo json_encode($result);
            return;
        }
        if($app->tokenTime<time()){
            $result->errcode=1;
            $result->errmsg='token已过期';
            echo json_encode($result);
            return;
        }
        if($isWaiter){
            $user=$this->user_model->get_by_openid_waiter($openid);
        }else{
            $user=$this->user_model->get_by_openid($openid);
        }
        if(!$user){
            $result->errcode=1;
            $result->errmsg='无效的openid';
            echo json_encode($result);
            return;
        }
        if($app->mchId!=$user->mchId){
            $result->errcode=1;
            $result->errmsg='此用户不是这个企业的';
            echo json_encode($result);
            return;
        }
        if(!isset($app->strategyType) || empty($app->strategyId)){
            $result->errcode=1;
            $result->errmsg='未绑定策略';
            echo json_encode($result);
            return;
        }
        if(empty($mobile) || empty($areaCode) || empty($birthday)){
            $result->errcode=1;
            $result->errmsg='信息填写不完整';
            echo json_encode($result);
            return;
        }
        $user->mobile=trim($mobile);
        $user->areaCode=trim($areaCode);
        $user->birthday=trim($birthday);
        $save=NULL;
        if($isWaiter){
            $save=$this->user_model->save_waiter($user);
        }else{
            $save=$this->user_model->save($user);
        }
        if(!$save){
            $result->errcode=1;
            $result->errmsg='保存失败';
            echo json_encode($result);
            return;
        }
        $result->errcode=0;
        $result->errmsg='保存成功';
        echo json_encode($result);
        return;
    }

}
