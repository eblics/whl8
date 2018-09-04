<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Member extends MerchantController{
    public function __construct() {
        // 初始化，加载必要的组件
        parent::__construct();
        $this->load->model('member_model');
        $this->mchId = $this->session->userdata('mchId');
    }

    public function index(){
        //如无需使用留空即可
    }
    /**
     * 会员列表
     */
    public function memberlist(){
        $this->load->view('member_memberlist');
    }
    public function get_memberlist(){

        $start=$this->input->post('start');
        $length=$this->input->post('length');
        $draw=$this->input->post('draw');
        $data=$this->member_model->get_memberlist($this->mchId,$count,$start,$length);
        $data=(object)["draw"=> intval($draw),"recordsTotal"=>$count,'recordsFiltered'=>$count,'data'=>$data];
        $this->output->set_content_type('application/json')->set_output(json_encode($data));
    }
}
