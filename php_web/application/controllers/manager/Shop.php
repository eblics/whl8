<?php
defined ( 'BASEPATH' ) or exit ( 'No direct script access allowed' );

class Shop extends MerchantController {
    public function __construct() {
        // 初始化，加载必要的组件
        parent::__construct ();
        $this->load->model ( 'shop_model','shop' );
        $this->mchId = $this->session->userdata ( 'mchId' );
    }
    
    public function tag_lists() {
        $this->load->view ( 'shop_tag_lists' );
    }
    
    public function tag_detail($id) {
        $data=$this->shop->get_tag_detail($id,$this->mchId);
        $data['value']=7;
        $shopHtml='';
        foreach ($data['shop'] as $shop){
            $shopHtml.='<option value="'.$shop['shopId'].'" '.(isset($shop['tagId'])?'selected':'').'>'.
                $shop['name'].'</option>';
        }
        $data['shopHtml']=$shopHtml;
        $data['id']=$id;
        $this->load->view ( 'shop_tag_edit' ,$data);
    }
    
    public function get_tag_data() {
        $data=['data'=>$this->shop->get_tag_data($this->mchId)];
        $this->output->set_content_type ( 'application/json' )->set_output ( json_encode ( $data ) );
    }
    
    public function post_tag_data($id) {
        $data = array(
            'name'=>$this->input->post('name'),
        );
        if($id==0){
            $data['mchId']=$this->mchId;
        }
        $shopIds=$this->input->post('shopIds');
        if($shopIds=='')
            $shopIds=[];
        $result=$this->shop->post_tag_data($id,$data,$this->mchId,$shopIds);
        $this->output->set_content_type ( 'application/json' )->set_output ( json_encode ( $result ) );
    }
    
    public function delete_tag_data($id) {
        $this->shop->delete_tag_data($id);
        $result=['errcode'=>0];
        $this->output->set_content_type ( 'application/json' )->set_output ( json_encode ( $result ) );
    }
}