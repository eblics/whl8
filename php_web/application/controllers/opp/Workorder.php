<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Workorder extends OppController {
    public function __construct() {
        parent::__construct();
        $this->load->model('Owork_model', 'work');

    }
	public function index() {
        $title = "工单管理";
		$this->load->view('work_lists',['title'=>$title]);
	}
    /**
     * 工单处理开始
     */
    public function lists(){
        $title = "工单管理";
        $this->load->view('work_lists',['title'=>$title]);
    }
    //所有工单
    public function listsdata(){
        $data = $this->work->get_all_works();
        $data = ['data'=>$data];
        $this->output->set_content_type ( 'application/json' )->set_output ( json_encode ( $data ) );
    }
    //新建工单 未处理状态
    public function untreatedlists(){

    }
    public function treat_work($id = null){
        $title = '工单处理';
        $result = $this->work->get_one_work($id);
        if($id == null || !isset($result)){
            show_404();
            exit;
        }
        // var_dump($result);
        // exit;
        switch ($result) {
            case '1':
                $result->type = '投诉';
                break;
            case '2':
                $result->type = '建议';
                break;
            case '3':
                $result->type = '使用';
                break;
        }
        $this->load->view('work_treat',['title'=>$title,'data'=>$result]);
    }
    /**
     * 工单处理结束
     */
    /**
     * 工单设置开始
     */
    //角色管理
    public function w_setting(){
        $title = '工单配置';
        $this->load->view('work_setting',['title'=>$title]);
    }
    //模块管理
    public function w_module(){
        $title = '模块管理';
        $res = $this->work->get_modules();
        $data = [];
        // foreach ($res as $key => $value) {
        //     array_push($data,$value->name);
        // }
        $this->load->view('work_module',['title'=>$title,'data'=>$res]);
    }
    //角色管理
    public function w_role(){
        $title = '角色管理';
        $this->load->view('work_role',['title'=>$title]);
    }
    //角色编辑
    public function wr_add(){
        $title = '角色添加';
        $data = [
                'id'=>null,
                'code'=>null,
                'name'=>null,
                'role'=>1,
                'mail'=>null,
                'phoneNum'=>null
                ];
        $this->load->view('work_role_edit',['title'=>$title,'data'=>$data]);
    }
    public function wr_edit($id = null){
        if($id == null){
            show_404();
            exit;
        }
        $res = $this->work->get_role_by_id($id);
        $data['name'] = $res->name;
        $data['code'] = $res->pCode;
        $data['id'] = $res->id;
        $data['phoneNum'] = $res->phoneNum;
        $data['mail'] = $res->mail;
        switch ($res->sRole) {
            case '客服':
                $data['role'] = 1;
                break;
            case '技术':
                $data['role'] = 2;
                break;
            default:
                $data['role'] = 1;
                break;
        }
        $title = '角色编辑';
        $this->load->view('work_role_edit',['title'=>$title,'data'=>$data]);
    }
    //角色保存
    public function save_role(){
        $data['sRole'] = $this->input->post('role');
        $data['id'] = $this->input->post('id');
        $data['pCode'] = $this->input->post('code');
        $data['name'] = $this->input->post('name');
        $data['phoneNum'] = $this->input->post('phoneNum');
        $data['mail'] = $this->input->post('mail');
        $res = $this->work->save_role($data);
        if(isset($res)){
            $this->ajaxResponse([],'success',0);
        }else{
            $this->ajaxResponse([],'',1);
        }

    }
    //角色删除
    public function del_role(){
        $id = $this->input->post('id');
        $res = $this->work->del_r($id);
        if($res){
            $this->ajaxResponse([],'success',0);
        }else{
            $this->ajaxResponse([],'',1);
        }
    }
    //角色锁定
    public function lock_role(){
        $id = $this->input->post('id');
        $res = $this->work->lock($id);
        if($res){
            $this->ajaxResponse([],'success',0);
        }else{
            $this->ajaxResponse([],'',1);
        }
    }
    //角色解锁
    public function unlock_role(){
        $id = $this->input->post('id');
        $res = $this->work->unlock($id);
        if($res){
            $this->ajaxResponse([],'success',0);
        }else{
            $this->ajaxResponse([],'',1);
        }
    }
    //角色数据
    public function roledata(){
        $data = $this->work->all_role();
        foreach ($data as $key => $value) {
            if($data[$key]->sRole == 1){
                $data[$key]->sRole = '客服';
            }
            if($data[$key]->sRole == 2){
                $data[$key]->sRole = '技术';
            }
        }
        $data = ['data'=>$data];
        $this->output->set_content_type ( 'application/json' )->set_output ( json_encode ( $data ) );
    }
    /**
     * 工单设置结束
     */
    //增加模块
    public function add_littbar(){
        $val = $this->input->post('val');
        $res = $this->work->littbar($val);
        // 返回结果说明：1 存在相同值(失败)，2 插入成功，3 插入失败
        switch ($res['ecode']) {
            case 1:
                $this->ajaxResponse([],'存在相同名称',1);
                break;
            case 2:
                $this->ajaxResponse(['res'=>$res['eres']],'success',0);
                break;
            case 3:
                $this->ajaxResponse([],'插入失败',1);
                break;
        }

    }
    //删除模块 ajax
    public function del_littbar(){
        $id = $this->input->post('id');
        if(isset($id)){
            $res = $this->work->del_bar($id);
            if($res){
                $this->ajaxResponse([],'',0);
            }else{
                $this->ajaxResponse([],'删除失败',1);
            }
        }else{
            $this->ajaxResponse([],'参数错误',1);
        }
    }	
}