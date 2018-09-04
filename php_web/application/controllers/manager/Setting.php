<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Setting extends MerchantController
{
    public function __construct() {
        // 初始化，加载必要的组件
        parent::__construct();
        $this->load->model('merchant_model');
        $this->load->model('setting_model');
        $this->mchId=$this->session->userdata('mchId');
        $this->load->library('log_record');
    }
    
    /**
     * CI控制器默认入口
     */
    public function index(){
        //如无需使用留空即可
    }
    
    //扫码频率控制页面
    public function guard(){
        $data=$this->setting_model->get_mch_scan_rule($this->mchId);
        if(!$data){
            $data=(object)[
                'id'=>'',
                'times'=>'',
                'unit'=>'i'
            ];
        }
        $view=(object)[];
        $this->load->view('setting_guard',['data'=>$data,'view'=>$view]);
    }


    public function user_scan() {
        $data = $this->setting_model->get_mch_scan_rule($this->mchId);
        $this->load->view('setting_user_scan', ['mch_id' => $this->mchId, 'times' => $data->scan_other_times]);
    }

    public function save_user_scan() {
        $times = $this->input->post('times');
        if (! is_numeric($times)) {
            $data = ['data' => NULL, 'errmsg' => '请填写正确的限制次数', 'errcode' => 1];
            $this->output->set_content_type('application/json')->set_output(json_encode($data));
            return;
        }
        try {
            $this->setting_model->saveUserScanOtherTimes($this->mchId, $times);
            $data = ['data' => NULL, 'errmsg' => NULL, 'errcode' => 0];
            $this->output->set_content_type('application/json')->set_output(json_encode($data));
        } catch (Exception $e) {
            $data = ['data' => NULL, 'errmsg' => $e->getMessage(), 'errcode' => $e->getCode()];
            $this->output->set_content_type('application/json')->set_output(json_encode($data));
        }
    }

    //扫码频率ajax数据
    public function data_scan_freq(){
        header("Content-type",'application/json;charset=utf-8;');
        $data=$this->setting_model->get_mch_scan_rule($this->mchId);
        if($data){
            $result=[
                'errcode'=>0,
                'errmsg'=>'',
                'data'=>$data
            ];
        }else{
            $result=[
                'errcode'=>1,
                'errmsg'=>'您还没有设置扫码频率，请尽快设置！'
            ];
        }
        echo json_encode($result);
        return;
    }
    //扫码频率ajax保存
    public function save_scan_freq(){
        header("Content-type",'application/json;charset=utf-8;');
        $saveData=(object)[
            'times'=>$this->input->post('times'),
            'unit'=>$this->input->post('unit'),
            'mchId'=>$this->mchId
        ];
        $id=$this->input->post('id');
        if(!empty($id)){
            $saveData->id=$id;
        }
        $data=$this->setting_model->save_mch_scan_rule($saveData);
        if($data){
            $result=[
                'errcode'=>0,
                'errmsg'=>'',
                'data'=>$data
            ];
        }else{
            $result=[
                'errcode'=>1,
                'errmsg'=>'保存失败，请重试！'
            ];
        }
        /*-------记录日志---------add by ccz*/
        if($result['errcode']==0){//-------ccz,日志
            try{
                $logInfo = (array)$saveData;
                if($id!=NULL && !empty($id)){
                    $logInfo ['id'] = $id;
                    $logInfo ['op'] = $this->log_record->Update;
                }else{
                    $logInfo ['id'] = $data;
                    $logInfo ['op'] = $this->log_record->Add;
                }
                $logInfo['type'] = 'scanrate';
                $this->log_record->addLog( $this->mchId,$logInfo,$this->log_record->Setting);
            }catch(Exception $e){
                log_message('error','mch_log_error:'.$e->getMessage());
            }
        }
        /*-------记录日志---------end */
        echo json_encode($result);
        return;
    }
    public function warning() {
        $mchId = $this->session->userdata ( 'mchId' );
        $res = $this->setting_model->get_receive_users($mchId);
        if($res == false){
            $res = array();
        }
        $this->load->view('setting_warning',['data'=>$res]);
    }
    public function del_user(){
        $mchId = $this->session->userdata ( 'mchId' );
        $id = $this->input->post('id');
        $res = $this->setting_model->del_user($id,$mchId);
        if($res){
            $this->output->set_content_type('application/json')->set_output(json_encode(['errmsg'=>'', 'errcode'=>0]));
        }else{
            $this->output->set_content_type('application/json')->set_output(json_encode(['errmsg'=>'删除失败，请稍候重试！', 'errcode'=>1]));
        }
    }
    public function find_user(){
        $mchId = $this->session->userdata ( 'mchId' );
        $nickName = $this->input->post('nickName');
        $res = $this->setting_model->find_user($nickName,$mchId);
        if($res){
            $this->output->set_content_type('application/json')->set_output(json_encode(['errmsg'=>'', 'errcode'=>0,'data'=>$res]));
        }else{
            $this->output->set_content_type('application/json')->set_output(json_encode(['errmsg'=>'没有匹配的查询结果', 'errcode'=>1]));
        }
    }
    public function add_user(){
        $mchId = $this->session->userdata ( 'mchId' );
        $id = $this->input->post('id');
        $res = $this->setting_model->add_user($id,$mchId);
        // $this->output->set_content_type('application/json')->set_output(json_encode(['errmsg'=>'没有匹配的查询结果', 'errcode'=>1,'data'=>$res]));
        
            if($res == 'exists'){
                $this->output->set_content_type('application/json')->set_output(json_encode(['errmsg'=>'该用户已存在！', 'errcode'=>1,'data'=>$res]));
            }
            if($res == 'add success'){
                $this->output->set_content_type('application/json')->set_output(json_encode(['errmsg'=>'', 'errcode'=>0,'data'=>$res]));
            }
            
            if($res == 'add false'){
                $this->output->set_content_type('application/json')->set_output(json_encode(['errmsg'=>'新增失败！', 'errcode'=>1,'data'=>$res]));
            }
        
    }
}
