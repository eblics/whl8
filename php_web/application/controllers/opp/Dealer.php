<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Dealer extends OppController {

    public function __construct() {
        parent::__construct();
        $this->load->model('ODealer_model', 'dealer');
    }
	public function index() {
        $title = '代理列表';
		$this->load->view('dealer_list',['title'=>$title,'value'=>50]);
	}
    public function lists() {
        $title = '代理列表';
        $this->load->view('dealer_list',['title'=>$title,'value'=>50]);
    }
    public function get_dealer_data(){
        $result = $this->dealer->get_data();
        // $data = [];
        $this->ajaxResponse($result);
    }
	public function add() { 
        $title = '添加代理商';
        //代理商代码生成
        $str1 = ['A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z'];
        $str2 = ['1','2','3','4','5','6','7','8','9','0'];
        // print_r(array_rand($str1,1));
        $countA = sizeof($str1)-1;
        $countB = sizeof($str2);
        $rand1 = mt_rand(0,$countA);
        $a = $str1[$rand1];
        $rand2 = mt_rand(0,$countA);
        $b = $str1[$rand2];
        $c = (string)mt_rand(0,99);
        $c = sprintf("%02d", $c);
        $code = $a.$b.$c;
        //查找数据库存在的代理商代码，并存到数组里
        $result = $this->dealer->get_code();
        $array = [];
        do{
            $c = sprintf("%02d", $c);
            $code = $a.$b.$c;
            foreach ($result as $key => $value) {
                array_push($array, $value->code);  
            }
        }while(in_array($code, $array));
        $dealer['name'] = null;
        $dealer['address'] = null;
        $dealer['ownerName'] = null;
        $dealer['phone'] = null;
        $dealer['code'] = $code;
        $dealer['id'] = null;
        $dealer['mail'] = null;
		$this->load->view('dealer_edit',['title'=>$title,'dealer'=>$dealer,'value'=>50]);
	}

	public function edit($id) {
        $title = '编辑代理商';
		if (! isset($id)) {
			show_404();
		} else {
            try {
                $result = $this->dealer->get_dealer($id);
                $data['name'] = $result->name;
                $data['address'] = $result->address;
                $data['code'] = $result->code;
                $data['id'] = $result->id;
                $data['ownerName'] = $result->contact;
                $data['phone'] = $result->phoneNum;
                $data['mail'] = $result->mail;
                $this->load->view('dealer_edit',['dealer'=>$data,'title'=>$title,'value'=>50]);
            } catch (Exception $e) {
                show_404();
            }
		}
	}
    public function save_dealer(){
        $name =$this->input->post('name');
        $address = $this->input->post('address');
        $contact = $this->input->post('ownerName');
        $phoneNum = $this->input->post('phone');
        $mail = $this->input->post('mail');
        $code = $this->input->post('code');
        $id = $this->input->post('id');
        $dealer = [
                'code'=>$code,
                'name'=>$name,
                'contact'=>$contact,
                'phoneNum'=>$phoneNum,
                'mail'=>$mail,
                'address'=>$address,
                'createTime'=>time()
            ];
        if(empty($id)){
            try {
                $result = $this->dealer->add($dealer);
                $this->saveDynamic('新增了代理商', $name, DynamicTypeEnum::Admin);
                $this->ajaxResponse();
            } catch (Exception $e) {
                $this->ajaxResponse([], $e->getMessage(), $e->getCode());
            }
        }else{
            $dealer['id'] = $id;
            unset($dealer['code']);
            try {  
                $this->dealer->update_dealer($id,$dealer);
                $this->saveDynamic('修改了代理商', $name, DynamicTypeEnum::Admin);
                $this->ajaxResponse();
            } catch (Exception $e) {
                $this->ajaxResponse([], $e->getMessage(), $e->getCode());
            }
        }

    }
    public function lock_dealer(){
        $id = $this->input->post('id');
        if(!empty($id)){   
            try {
                $res = $this->dealer->lock_dealer($id);
                $this->saveDynamic('锁定了代理商', $id, DynamicTypeEnum::Admin);
                $this->ajaxResponse();  
            } catch (Exception $e) {
                $this->ajaxResponse([], $e->getMessage(), $e->getCode());
            }
        }
    }



	public function dynamic() {
    	$time = $this->input->get('time');
    	if (! isset($time)) {
    		$time = 'today';
    	}
    	$data = [];
        $this->load->view('admin_dynamic', $data);
    }

  
  
    
}