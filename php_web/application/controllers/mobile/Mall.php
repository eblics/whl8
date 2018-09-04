<?php
class Mall extends Mobile_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('Mall_mobile_model','mall');
        $currentUser = $this->getCurrentUser($this->getCurrentMchId());
        $mall = $this->mall->getMallByMchId($currentUser->mchId);
        if (! isset($mall)) {
            $this->output->set_content_type('application/json')
                ->set_output(json_encode(['errcode'=>1,'errmsg'=>'Mall Not Open','data'=>NULL]));
            $this->mallid = NULL;
            $this->userid = $currentUser->id;
        } else {
            $this->mallid = $mall->id;
            $this->userid = $currentUser->id;
        }
    }

    private function send_result($data = null, $errcode = 0, $errmsg = null) {
        $jsonData = ['errcode'=>$errcode,'errmsg'=>$errmsg,'data'=>$data,'mallid'=>$this->mallid];
        $this->output->set_content_type('application/json')
          ->set_output(json_encode($jsonData));
    }

    public function get_mall(){
        $this->send_result($this->mall->get_mall($this->mallid));
    }

    public function get_recommand_goods() {
        $this->send_result($this->mall->get_recommand_goods($this->mallid));
    }

    public function get_categories_list() {
        $this->send_result($this->mall->get_categories_list($this->mallid));
    }

    public function get_goods_list($categoryId=null) {
        $this->send_result($this->mall->get_goods_list($this->mallid,null,$categoryId));
    }

    public function get_goods_list_by_trolley() {
        $ids=explode(',',$this->input->post('ids'));
        $point=$this->mall->get_point($this->mallid,$this->userid);
        $this->send_result(['list'=>$this->mall->get_goods_list($this->mallid,$ids),'point'=>$point]);
    }

    public function get_goods_list_by_submit() {
        $ids=explode(',',$this->input->post('ids'));
        $point=$this->mall->get_point($this->mallid,$this->userid);
        $addressid=$this->input->post('addressid');
        $addressText=null;
        $address=$this->mall->get_address($this->mallid,$this->userid,$addressid);
        if($address!=null){
            $addressText=$address['receiver'].'|'.$address['phoneNum'].'|'.$address['area'].'|'.$address['address'];
            $addressid=$address['id'];
        }
        $this->send_result(['list'=>$this->mall->get_goods_list($this->mallid,$ids),'point'=>$point,'address'=>$addressText,'addressid'=>$addressid]);
    }

    public function get_good($id){
        $this->send_result($this->mall->get_good($this->mallid,$id));
    }

    public function create_order() {
        $list=json_decode($this->input->post('list'),true);
        $addressid=$this->input->post('addressid');
        if (empty($list)) {
            $this->ajaxResponseOver('请选择要兑换的商品');
        }

        $result=$this->mall->create_order($this->mallid,$this->userid,$list,$addressid);
        if($result==null){
            $this->send_result(0);
        }
        else if($result==1){
            $this->send_result(1);
        }
        else{
            $this->send_result(null,1,$result);
        }
    }

    public function cancel_order($orderid) {
        $this->mall->cancel_order($this->mallid,$this->userid,$orderid);
        $this->send_result();
    }

    /*public function delete_order($orderid) {
        $this->mall->delete_order($this->mallid,$this->userid,$orderid);
        $this->send_result();
    }*/

    public function get_point(){
        $this->send_result($this->mall->get_point($this->mallid,$this->userid));
    }

    public function get_orders_list($status){
        $this->send_result($this->mall->get_orders_list($this->mallid,$this->userid,$status));
    }

    public function get_addresses_list(){
        $this->send_result($this->mall->get_addresses_list($this->mallid,$this->userid));
    }

    public function get_address($id=null){
        $areas=$this->mall->get_areacode_info();
        $areasHtml='';
        foreach ($areas as $area) {
            $areasHtml.='<li data-val="'.$area['code'].'">'.$area['name'];
            $areasHtml.='<ul>';
            foreach ($area['children'] as $city) {
                $areasHtml.='<li data-val="'.$city['code'].'">'.$city['name'];
                $areasHtml.='<ul>';
                foreach ($city['children'] as $part) {
                    $areasHtml.='<li data-val="'.$part['code'].'">'.$part['name'];
                    $areasHtml.='</li>';
                }
                $areasHtml.='</ul>';
                $areasHtml.='</li>';
            }
            $areasHtml.='</ul>';
            $areasHtml.='</li>';
        }
        $result=['areahtml'=>$areasHtml];
        if($id!=null){
            $data=$this->mall->get_address($this->mallid,$this->userid,$id);
            $result['address']=$data;
        }
        $this->send_result($result);
    }

    public function update_address($id=null){
        $data=[
            'receiver'=>str_replace('|',' ',$this->input->post('receiver')),
            'areaCode'=>$this->input->post('areacode'),
            'phoneNum'=>$this->input->post('phone'),
            'address'=>str_replace('|',' ',$this->input->post('address'))
        ];
        $this->mall->update_address($this->mallid,$this->userid,$data,$id);
        $this->send_result();
    }

    public function delete_address($id){
        $data=[
            'rowStatus'=>1
        ];
        $this->mall->update_address($this->mallid,$this->userid,$data,$id);
        $this->send_result();
    }

    public function default_address($id=null){
        $this->mall->default_address($this->mallid,$this->userid,$id);
        $this->send_result();
    }
}
