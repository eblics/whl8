<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Userdeal extends MerchantController{
    public function __construct() {
        // 初始化，加载必要的组件
        parent::__construct();
        $this->load->model('user_deal_model');
        $this->mchId = $this->session->userdata('mchId');
    }

    public function index(){
        //如无需使用留空即可
    }

    // 企业级封禁用户列表
    public function mch_forbidden_users(){
        $this->load->view('mch_forbidden_users_list');
    }

    public function get_mch_forbidden_users(){
        $start=$this->input->post('start');
        $length=$this->input->post('length');
        $draw=$this->input->post('draw');
        $search=$this->input->post('search')['value'];
        $list=$this->user_deal_model->get_mch_forbidden_users($this->mchId,$search,$count,$start,$length);
        $data=(object)["draw"=> intval($draw),"recordsTotal"=>$count,'recordsFiltered'=>$count,'data'=>$list];
        $this->output->set_content_type('application/json')->set_output(json_encode($data));
    }

    public function get_forbidden_user_apply($userId){
        $data=null;
        if(!isset($userId)){
            $this->output->set_content_type('application/json')->set_output(json_encode($data));
            return;
        }
        $data=$this->user_deal_model->get_forbidden_user_apply($this->mchId,$userId);
        $this->output->set_content_type('application/json')->set_output(json_encode($data));
    }

    public function deal_forbidden_user_apply(){
        header("Content-type",'application/json;charset=utf-8;');
        $result=(object)['errcode'=>0,'errmsg'=>''];
        $id=$_POST['id'];
        $userId=$_POST['userId'];
        $type=$_POST['type'];
        $value=$_POST['value'];
        if(!isset($id) || !isset($userId) || !isset($type) || !isset($value)){
            $result->errcode=1;
            $result->errmsg='缺少参数';
            echo json_encode($result);
            return;
        }
        switch ($type) {
            case '0':
                //解封
                $update=$this->user_deal_model->deal_forbidden_user_apply_unlock($this->mchId,$id,$userId,$isBlack);
                if($isBlack==1){
                    $result->errcode=1;
                    $result->errmsg='黑名单中的用户不能解封';
                    echo json_encode($result);
                    return;
                }
                break;
            case '1':
                //备注
                $update=$this->user_deal_model->deal_forbidden_user_apply_mark($this->mchId,$id,$userId,$value);
                break;
            case '2':
                //驳回
                $update=$this->user_deal_model->deal_forbidden_user_apply_refuse($this->mchId,$id,$userId,$value);
                break;
            case '3':
                //拉黑
                $update=$this->user_deal_model->deal_forbidden_user_apply_blacklist($this->mchId,$id,$userId,$value);
                break;
            default:
                $update=false;
                break;
        }
        if(! $update){
            $result->errcode=1;
            $result->errmsg='操作失败';
            echo json_encode($result);
            return;
        }
        echo json_encode($result);
        return;
    }

    public function deal_forbidden_user_unlock($userId){
        header("Content-type",'application/json;charset=utf-8;');
        $result=(object)['errcode'=>0,'errmsg'=>''];
        if(!isset($userId)){
            $result->errcode=1;
            $result->errmsg='缺少参数';
            echo json_encode($result);
            return;
        }
        $update=$this->user_deal_model->deal_forbidden_user_unlock($this->mchId,$userId,$isBlack);
        if($isBlack==1){
            $result->errcode=1;
            $result->errmsg='黑名单中的用户不能解封';
            echo json_encode($result);
            return;
        }
        if(! $update){
            $result->errcode=1;
            $result->errmsg='操作失败';
            echo json_encode($result);
            return;
        }
        echo json_encode($result);
        return;
    }
}
