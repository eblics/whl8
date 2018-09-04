<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Shop extends OppController {
    
    public function __construct() {
        // 初始化，加载必要的组件
        parent::__construct();
        $this->load->model('OShop_model', 'shop');
    }
    
    public function index() {
        $this->load->view('shop', ['title'=>'门店列表','value' => 9]);
    }
    
    public function examine() {
        $this->load->view('shop_examine', ['title'=>'门店审批','value' => 9]);
    }
    
    public function examine_detail($id) {
        $data=$this->shop->get_examine_detail($id);
        $data['title']='门店查看';
        if($data['state']==1){
            $data['title']='门店审批';
        }
        $data['value']=9;
        
        $data['id']=$id;
        $this->load->view('shop_examine_detail', $data);
    }
    
    public function examine_agree($id) {
        $this->shop->agree_examine($id);
    }
    
    public function examine_reject($id) {
        $this->shop->reject_examine($id);
    }
    
    public function get_shop_data() {
        $data=['data'=>$this->shop->get_shop_data()];
        $this->output->set_content_type ( 'application/json' )->set_output ( json_encode ( $data ) );
    }
    
    public function get_examine_data() {
        $data=['data'=>$this->shop->get_examine_data()];
        $this->output->set_content_type ( 'application/json' )->set_output ( json_encode ( $data ) );
    }
    
    public function shop_detail($id=0) {
        $data=$this->shop->get_shop_detail($id);
        if(count($data['shop'])==0)
            $data['shop']=['name'=>'','address'=>'','ownerName'=>'','ownerPhoneNum'=>''];
        $data['value']=9;
        $deviceHtml='';
        foreach ($data['device'] as $device){
            $deviceHtml.='<option value="'.$device['id'].'" '.(isset($device['shopId'])?'selected':'').'>'.
            $device['deviceId'].' ('.$device['comment'].')</option>';
        }
        $data['deviceHtml']=$deviceHtml;
        $data['id']=$id;
        $this->load->view('shop_edit', $data);
    }
    
    public function post_shop_data($id=0) {
        $data = array(
            'name'=>$this->input->post('name'),
            'address'=>$this->input->post('address'),
            'ownerName'=>$this->input->post('ownerName'),
            'ownerPhoneNum'=>$this->input->post('ownerPhoneNum')
        );
        $deviceIds=$this->input->post('deviceIds');
        if($deviceIds=='')
            $deviceIds=[];
        $this->shop->post_shop_data($id,$data,$deviceIds);
        $result=['errcode'=>0];
        $this->output->set_content_type ( 'application/json' )->set_output ( json_encode ( $result ) );
    }
    
    public function delete_shop_data($id) {
        $this->shop->delete_shop_data($id);
        $result=['errcode'=>0];
        $this->output->set_content_type ( 'application/json' )->set_output ( json_encode ( $result ) );
    }
    
    public function device() {
        $this->load->view('shop_device', ['title'=>'设备列表','value' => 9]);
    }
    
    public function get_device_data() {
        $data=['data'=>$this->shop->get_device_data()];
        $this->output->set_content_type ( 'application/json' )->set_output ( json_encode ( $data ) );
    }
    
    public function permission() {
        $this->load->view('shop_permission', ['title'=>'授权列表','value' => 9]);
    }
    
    public function get_permission_data() {
        $data=['data'=>$this->shop->get_permission_data()];
        $this->output->set_content_type ( 'application/json' )->set_output ( json_encode ( $data ) );
    }
    
    public function permission_detail($id) {
        $data=$this->shop->get_permission_detail($id);
        $data['value']=9;
        $shopHtml='';
        foreach ($data['shop'] as $shop){
            $shopHtml.='<option value="'.$shop['id'].'" '.(isset($shop['shopId'])?'selected':'').'>'.
                $shop['name'].'</option>';
        }
        $data['shopHtml']=$shopHtml;
        $data['id']=$id;
        $this->load->view('shop_permission_edit', $data);
    }
    
    public function post_permission_data($id) {
        $shopIds=$this->input->post('shopIds');
        if($shopIds=='')
            $shopIds=[];
        $this->shop->post_permission_data($id,$shopIds);
        $result=['errcode'=>0];
        $this->output->set_content_type ( 'application/json' )->set_output ( json_encode ( $result ) );
    }
}