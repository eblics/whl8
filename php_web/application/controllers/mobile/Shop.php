<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Shop extends Mobile_Controller {

    public function __construct() {
        // 初始化，加载必要的组件
        parent::__construct();
        $this->load->model ( 'shop_model','shop' );
        
        /*$merchant=(object)['id'=>-1];
        if (! $this->session->has_userdata('common_openid_' . $merchant->id)) {
            $this->weixin_rest_api->login($merchant);
            exit();
        } else {
            $this->openid= $this->session->userdata('common_openid_' . $merchant->id);
        }
        
        echo $this->openid;*/
        
        $this->openid = $this->getCommonUser()->openid;
    }
    
    public function shop_detail($id=0){
        $data=['name'=>'','ownerName'=>'','ownerPhoneNum'=>'','city'=>'','areaCode'=>'','lat'=>'','lng'=>'','address'=>'','areaLen'=>50,'id'=>0];
        if($id!=0){
            $data=$this->shop->get_shop_detail($id);
        }
        $this->load->view('shop_detail',$data);
    }
    
    public function shop_list(){
        $shops=$this->shop->get_shop_list($this->openid);
        $data=['shops'=>$shops];
        $this->load->view('shop_list',$data);
    }
    
    public function shop_activate($id=0){
        if($this->shop->validate_shop($id)==0){
            echo '<!DOCTYPE html><html><head><meta charset="UTF-8"><meta name="viewport" content="initial-scale=1.0,user-scalable=no" /></head>
<body><h3>此门店已被激活或无此门店</h3></body></html>';
            exit;
        }
        $this->load->view('shop_activate',['id'=>$id]);
    }
    
    public function validate_owner($id){
        $data=[];
        $data['ownerName']=$this->input->post('ownerName');
        $data['ownerPhoneNum']=$this->input->post('ownerPhoneNum');
        $data['id']=$id;
        $result=$this->shop->validate_owner($data);
        if($result==1){
            $this->load->library('sms_vcode');
            $template_id = 'SMS_7895086';
            $code = mt_rand(100000,999999);
            $signame = '红码';
            $product='门店';
            $this->sms_vcode->send_sms_vcode($data['ownerPhoneNum'], $code, $template_id, $signame,$product);
        }
        echo $result;
    }
    
    public function activate_owner($id){
        $this->load->library('sms_vcode');
        $ownerPhoneNum=$this->input->post('ownerPhoneNum');
        $validCode=$this->input->post('validCode');
        $state=$this->sms_vcode->proof_vcode($ownerPhoneNum, $validCode);
        if($state['statusCode']==1){
            echo 0;
            exit;
        }
        $data=[];
        $gps=(object)['lat'=>$this->input->post('lat'),'lng'=>$this->input->post('lng')];
        $this->load->library('geolocation');
        $geo=$this->geolocation->get_geo_area($gps);
        $data=[];
        $data['lat']=$this->input->post('lat');
        $data['lng']=$this->input->post('lng');
        $data['openid']=$this->openid;
        $data['areaCode']=$geo->areaCode;
        $this->shop->activate_bluetooth_shop($id,$data);
        echo 1;
    }
    
    private function post_shop_data($id,$state) {
        $data=[];
        $data['name']=$this->input->post('name');
        $data['address']=$this->input->post('address');
        $data['ownerName']=$this->input->post('ownerName');
        $data['ownerPhoneNum']=$this->input->post('ownerPhoneNum');
        if($this->input->post('areaCode')!=null)
            $data['areaCode']=$this->input->post('areaCode');
        if($this->input->post('lat')!=null)
            $data['lat']=$this->input->post('lat');
        if($this->input->post('lng')!=null)
            $data['lng']=$this->input->post('lng');
        if($this->input->post('areaLen')!=null)
            $data['areaLen']=$this->input->post('areaLen');
        $this->shop->post_gps_shop_data($id,$data,$this->openid,$state);
    }
    
    public function save_shop_data($id){
        $this->post_shop_data($id,0);
    }
    
    public function submit_shop_data($id){
        $this->post_shop_data($id,1);
    }
    
    public function revoke($id){
        $this->shop->revoke_shop($id);
    }
    
    public function delete($id){
        $this->shop->delete_shop($id);
    }
    
    public function get_address_from_gps($id=null) {
        $data=[];
        $gps=(object)['lat'=>$this->input->post('lat'),'lng'=>$this->input->post('lng')];
        $this->load->library('common/geolocation');
        $geo=$this->geolocation->get_geo_area($gps);
        $city=$this->shop->get_address_from_areacode($geo->areaCode);
        $this->output->set_content_type('application/json')->set_output (json_encode(
            ['areaCode'=>$geo->areaCode,'address'=>$geo->address,'city'=>$city]
        ));
    }
}