<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * 
 * @author shizq
 * 
 */
class Group_app extends Mobile_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('merchant_model');
        $this->load->model('user_model');
        $this->load->model('group_model');
        $this->load->model('group_scanpk_model');
        $this->load->library('common/common_login');
        $this->mchId=$this->session->userdata('mchId');
    }

    public function scanpk($pageName = NULL, $groupId = NULL) {
        if ($pageName === 'lists') {
            $this->lists($groupId);
        } else if ($pageName === 'pubpk') {
            $this->pubpk();
        } else if ($pageName === 'detail') {
            $this->detail();
        } else if ($pageName === 'joinpk') {
            $this->joinpk();
        } else {
            show_404();
        }
    }

    private function lists($groupId) {
        if(! isset($groupId)) return false;
        $groupId=intval($groupId);
        $merchant=$this->merchant_model->get($this->mchId);
        if(!$merchant) return;
        $openid=$this->session->userdata('openid_'.$this->mchId);
        if(!$openid) return;
        $user=$this->user_model->get_by_openid($openid);
        if(!$user) return;
        $group=$this->group_model->get_group($groupId);
        if(! $group) return;
        $member=$this->group_model->get_group_member_one($group->id,$user->id);
        if(! $member) return;
        $scanpk=$this->group_scanpk_model->get_scanpk($groupId);
        $myScanpk=$this->group_scanpk_model->get_scanpk_by_user($groupId,$user->id);
        foreach($scanpk as $k=>$v){
            if($v->pkType==0){
                $scanpk[$k]->pkAmount=bcdiv($v->pkAmount,100,2);
            }
            foreach($myScanpk as $k2=>$v2){
                if($v->id==$v2->id){
                    unset($scanpk[$k]);
                }
            }
        }
        foreach($myScanpk as $k2=>$v2){
            if($v2->pkType==0){
                $myScanpk[$k2]->pkAmount=bcdiv($v2->pkAmount,100,2);
            }
        }
        $data=['lists'=>$scanpk,'mylists'=>$myScanpk,'groupId'=>$groupId];
        $this->load->view('group_app/scanpk_lists',$data);
    }

    /**
     * 发起PK
     */
    private function pubpk(){
        header ( "Content-type", 'application/json;charset=utf-8;' );
        $result=(object)['errcode'=>0,'errmsg'=>'','data'=>NULL];

        $groupId = intval($this->input->post('groupId'));
        $pkType = intval($this->input->post('pkType'));
        $pkAmount = intval($this->input->post('pkAmount'));
        $pkTime = $this->input->post('pkTime');
        if($pkAmount<=0){
            $result->errcode=1;
            $result->errmsg='额度必须大于0';
            echo json_encode($result);
            return;
        }
        log_message('debug',$groupId.'_'.$pkType.'_'.$pkAmount.'_'.$pkTime);
        if(trim($groupId)==='' || trim($pkType)==='' || trim($pkAmount)==='' || trim($pkTime)===''){
            $result->errcode=1;
            $result->errmsg='提交的信息不完整';
            $result->data=file_get_contents("php://input");
            echo json_encode($result);
            return;
        }
        $saveData=(object)['groupId'=>$groupId,'pkType'=>$pkType,'pkAmount'=>$pkAmount,'startTime'=>time(),'endTime'=>time()+$pkTime*3600,'createTime'=>time(),'updateTime'=>time(),'status'=>0];
        $mchId=$this->session->userdata('mchId');
        if(! isset($mchId)) {
            $result->errcode=1;
            $result->errmsg='未登录'.$mchId;
            echo json_encode($result);
            return;
        }
        $merchant=$this->merchant_model->get($mchId);
        if(!$merchant) {
            $result->errcode=1;
            $result->errmsg='企业不存在'.$mchId;
            echo json_encode($result);
            return;
        }
        $openid=$this->session->userdata('openid_'.$mchId);
        if(!$openid) {
            $result->errcode=1;
            $result->errmsg='openid不存在'.$openid;
            echo json_encode($result);
            return;
        }
        $user=$this->user_model->get_by_openid($openid);
        if(!$user) {
            $result->errcode=1;
            $result->errmsg='用户不存在'.$openid;
            echo json_encode($result);
            return;
        }
        $saveData->userId=$user->id;
        if($pkType==0){
            $userAmount=$this->user_model->get_amount($user->id,$mchId,0);
            if(!$userAmount) {
                $result->errcode=1;
                $result->errmsg='您的红包余额不足';
                echo json_encode($result);
                return;
            }
            if($userAmount->amount<$pkAmount){
                $result->errcode=1;
                $result->errmsg='您的红包余额不足';
                echo json_encode($result);
                return;
            }
        }
        if($pkType==1){
            $userPoint=$this->user_model->get_point($user->id,$mchId);
            if(!$userPoint) {
                $result->errcode=1;
                $result->errmsg='您的积分余额不足';
                echo json_encode($result);
                return;
            }
            if($userPoint->amount<$pkAmount){
                $result->errcode=1;
                $result->errmsg='您的积分余额不足';
                echo json_encode($result);
                return;
            }
        }
        $save=$this->group_scanpk_model->add_scanpk($saveData,$mchId);
        if(! $save){
            $result->errcode=1;
            $result->errmsg='保存失败';
            echo json_encode($result);
            return;
        }
        $result->data=$save;
        echo json_encode($result);
        return;
    }

    public function detail(){
        if(! $this->common_login->is_login()) return;//common_login login
        header ( "Content-type", 'application/json;charset=utf-8;' );
        $result=(object)['errcode'=>0,'errmsg'=>'','data'=>NULL];

        $id = intval($this->input->post('id'));
        if(trim($id)===''){
            $result->errcode=1;
            $result->errmsg='提交的信息不完整';
            $result->data=file_get_contents("php://input");
            echo json_encode($result);
            return;
        }
        $mchId=$this->session->userdata('mchId');
        if(! isset($mchId)) {
            $result->errcode=1;
            $result->errmsg='未登录'.$mchId;
            echo json_encode($result);
            return;
        }
        $merchant=$this->merchant_model->get($mchId);
        if(!$merchant) {
            $result->errcode=1;
            $result->errmsg='企业不存在'.$mchId;
            echo json_encode($result);
            return;
        }
        $openid=$this->session->userdata('openid_'.$mchId);
        if(!$openid) {
            $result->errcode=1;
            $result->errmsg='openid不存在'.$openid;
            echo json_encode($result);
            return;
        }
        $user=$this->user_model->get_by_openid($openid);
        if(!$user) {
            $result->errcode=1;
            $result->errmsg='用户不存在'.$openid;
            echo json_encode($result);
            return;
        }
        $scanpk=$this->group_scanpk_model->get($id);
        if(! $scanpk){
            $result->errcode=1;
            $result->errmsg='查询失败';
            echo json_encode($result);
            return;
        }
        $member=$this->group_model->get_group_member_one($scanpk->groupId,$user->id);
        if(! $member){
            $result->errcode=1;
            $result->errmsg='无权查看';
            echo json_encode($result);
            return;
        }
        $pkmaster=$this->group_scanpk_model->get_scanpk_users_master($id);
        if(! $pkmaster){
            $result->errcode=1;
            $result->errmsg='查询失败';
            echo json_encode($result);
            return;
        }
        $master=$this->group_model->get_group_member_one($scanpk->groupId,$pkmaster->userId);
        if(! $master){
            $result->errcode=1;
            $result->errmsg='查询失败';
            echo json_encode($result);
            return;
        }
        $pkMasterName=$master->nickName;
        if(mb_strlen($pkMasterName)>8){
            $pkMasterName=mb_substr($pkMasterName,0,8,'utf-8') . '..';
        }
        $scanpk->pkMasterName=$pkMasterName.'<font color=gray>('.$master->userId.')</font>';
        $scanpk->startTimeFormat=date('Y-m-d H:i:s',$scanpk->startTime);
        $scanpk->endTimeFormat=date('Y-m-d H:i:s',$scanpk->endTime);
        if($scanpk->pkType==0){
            $scanpk->pkAmount=bcdiv($scanpk->pkAmount,100,2);
        }
        $pkusers=$this->group_scanpk_model->get_scanpk_users($id,'scanNum','desc');
        $members=$this->group_model->get_group_member($scanpk->groupId);
        $pkusersArr=[];
        foreach($pkusers as $k=>$v){
            foreach($members as $k2=>$v2){
                if($v->userId==$v2->userId){
                    $thisNickName=$v2->nickName;
                    if(mb_strlen($thisNickName)>8){
                        $thisNickName=mb_substr($thisNickName,0,8,'utf-8') . '..';
                    }
                    $v2->nickName=$thisNickName.'('.$v2->userId.')';
                    $v2->scanNum=$v->scanNum;
                    $v2->winner=$v->winner;
                    array_push($pkusersArr,$v2);
                }
            }
        }
        $scanpk->userNum=count($pkusersArr);
        $scanpk->users=$pkusersArr;
        $result->data=$scanpk;
        echo json_encode($result);
        return;
    }

    public function joinpk(){
        header ( "Content-type", 'application/json;charset=utf-8;' );
        $result=(object)['errcode'=>0,'errmsg'=>'','data'=>NULL];

        $id = intval($this->input->post('id'));
        if(trim($id)===''){
            $result->errcode=1;
            $result->errmsg='提交的信息不完整';
            $result->data=file_get_contents("php://input");
            echo json_encode($result);
            return;
        }
        $mchId=$this->session->userdata('mchId');
        if(! isset($mchId)) {
            $result->errcode=1;
            $result->errmsg='未登录'.$mchId;
            echo json_encode($result);
            return;
        }
        $merchant=$this->merchant_model->get($mchId);
        if(!$merchant) {
            $result->errcode=1;
            $result->errmsg='企业不存在'.$mchId;
            echo json_encode($result);
            return;
        }
        $openid=$this->session->userdata('openid_'.$mchId);
        if(!$openid) {
            $result->errcode=1;
            $result->errmsg='openid不存在'.$openid;
            echo json_encode($result);
            return;
        }
        $user=$this->user_model->get_by_openid($openid);
        if(!$user) {
            $result->errcode=1;
            $result->errmsg='用户不存在'.$openid;
            echo json_encode($result);
            return;
        }
        $scanpk=$this->group_scanpk_model->get($id);
        if(! $scanpk){
            $result->errcode=1;
            $result->errmsg='PK活动不存在';
            echo json_encode($result);
            return;
        }
        if($scanpk->endTime>time() && $scanpk->endTime<time()+300){
            $result->errcode=1;
            $result->errmsg='不能加入临近结束的PK';
            echo json_encode($result);
            return;
        }
        if($scanpk->endTime<=time()){
            $result->errcode=1;
            $result->errmsg='PK已结束，不能加入';
            echo json_encode($result);
            return;
        }
        $member=$this->group_model->get_group_member_one($scanpk->groupId,$user->id);
        if(! $member){
            $result->errcode=1;
            $result->errmsg='没有权限加入';
            echo json_encode($result);
            return;
        }
        $pkuser=$this->group_scanpk_model->get_scanpk_users_one($id,$user->id);
        if($pkuser){
            $result->errcode=1;
            $result->errmsg='不能重复加入';
            echo json_encode($result);
            return;
        }
        if($scanpk->pkType==0){
            $userAmount=$this->user_model->get_amount($user->id,$mchId,0);
            if(!$userAmount) {
                $result->errcode=1;
                $result->errmsg='您的红包余额不足';
                echo json_encode($result);
                return;
            }
            if($userAmount->amount<$scanpk->pkAmount){
                $result->errcode=1;
                $result->errmsg='您的红包余额不足';
                echo json_encode($result);
                return;
            }
        }
        if($scanpk->pkType==1){
            $userPoint=$this->user_model->get_point($user->id,$mchId);
            if(!$userPoint) {
                $result->errcode=1;
                $result->errmsg='您的积分余额不足';
                echo json_encode($result);
                return;
            }
            if($userPoint->amount<$scanpk->pkAmount){
                $result->errcode=1;
                $result->errmsg='您的积分余额不足';
                echo json_encode($result);
                return;
            }
        }
        $save=$this->group_scanpk_model->join_scanpk($scanpk,$user);
        if(! $save){
            $result->errcode=1;
            $result->errmsg='加入失败';
            echo json_encode($result);
            return;
        }
        $result->data=$save;
        echo json_encode($result);
        return;
    }

}