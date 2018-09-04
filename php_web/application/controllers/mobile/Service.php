<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * 
 */
class Service extends Mobile_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('Merchant_model', 'merchant_model');
        $this->load->model('User_model', 'user_model');
        $this->load->model('Service_model','service_model');
        $this->load->library('common/common_lib');
        $this->load->library('common/common_login');
    }

    //重新提交审核
    public function re_lifted(){
        $commonUser = $this->getCommonUser(FALSE);
        $data = (object)array(
            'lreason'=>'',
            'lname'=>'',
            'lphonenum'=>'',
            'img'=>'',
            'isEdit'=>true
        );
        $edit = $this->input->get('edit');
        if(!$edit='ok'){
            return;
        }
        $this->load->view('lifted',['title'=>'申请解封','commonStatus'=>1,'status'=>0,'data'=>$data]);
    }
    public function lifted(){
        $commonUser = $this->getCommonUser(FALSE);
        $is_exists = $this->service_model->is_exists($commonUser->openid);
        if(!empty($is_exists)){
            $this->load->view('lifted',['title'=>'申请解封','commonStatus'=>0,'status'=>1]); 
            return;
        }
        $user=$this->service_model->get_common_user($commonUser->openid);
        $blacklist = $this->service_model->get_blacklist($commonUser->openid);
        if(isset($blacklist)){
            $data = (object)array(
                'lreason'=>'',
                'lname'=>'',
                'lphonenum'=>'',
                'img'=>'',
                'isEdit'=>true
            );
            $this->load->view('lifted_refuse',['title'=>'申请解封','status'=>5,'commonStatus'=>1,'data'=>$data]);
            return;
        }
        $isfb = $this->service_model->get_by_mch($commonUser->openid);
        if($isfb){
        	if($isfb->result==3){
        		$data = (object)array(
	                'refuse'=>$isfb->res->refuse
	                );
	            $this->load->view('lifted_refuse',['title'=>'申请解封-拒绝','status'=>3,'commonStatus'=>1,'data'=>$data]); 
        		return;
        	}
            $data = (object)array(
                'lreason'=>'',
                'lname'=>'',
                'lphonenum'=>'',
                'img'=>'',
                'isEdit'=>true
                );
            $this->load->view('lifted',['title'=>'申请解封','status'=>0,'commonStatus'=>1,'data'=>$data]);
            return;
        }else{
            $this->load->view('lifted',['title'=>'申请解封','status'=>0,'commonStatus'=>0]); 
            return;
        }
        if(!isset($user)){
            $this->load->view('lifted',['title'=>'申请解封','commonStatus'=>0,'user'=>$user]); 
            return;
        }
        $res =  $this->service_model->get_all_data($commonUser->openid);
        $res3 = $this->service_model->out_two($commonUser->openid);
        if(empty($res3)){
            $data = (object)array(
                'lreason'=>'',
                'lname'=>'',
                'lphonenum'=>'',
                'img'=>'',
                'isEdit'=>true
                );
            $this->load->view('lifted',['title'=>'申请解封','status'=>0,'commonStatus'=>1,'data'=>$data]); 
        }else{
            $result = $this->service_model->get_all_data($commonUser->openid);
            foreach ($result as $key => $value) {
                if($value->status == 1){
                    $status = $value->status;
                    $data = (object)array(
                        'lreason'=>$value->reason,
                        'lname'=>$value->name,
                        'lphonenum'=>$value->phoneNum,
                        'img'=>$value->QRimg,
                        'isEdit'=>false,
                        'refuse'=>$value->refuse
                        );
                    $this->load->view('lifted',['title'=>'申请解封','status'=>1,'commonStatus'=>1,'data'=>$data]); 
                }
                if($value->status == 3){
                    $status = $value->status;
                    $data = (object)array(
                        'refuse'=>$value->refuse
                        );
                    $this->load->view('lifted_refuse',['title'=>'申请解封-拒绝','status'=>3,'commonStatus'=>1,'data'=>$data]); 
                }
                if($value->status == 4){
                    $status = $value->status;
                    $data = (object)array(
                        'refuse'=>$value->refuse
                        );
                    $this->load->view('lifted_refuse',['title'=>'申请解封-禁止提交','status'=>5,'commonStatus'=>1,'data'=>$data]); 
                }
            }
        }
    }
    public function get_lifted(){
        $commonUser = $this->getCommonUser(FALSE);
        $this->service_model->appeal($commonUser->openid);
        $lphonenum = $this->input->post('lphonenum');
        $lname = $this->input->post('lname');
        $lreason = $this->input->post('lreason');
        $QRimg = $this->input->post('img');
        $data = array(
            'name'=>$lname,
            'phoneNum'=>$lphonenum,
            'openId'=>$commonUser->openid,
            'reason'=>$lreason,
            'createTime'=>time(),
            'QRimg'=>$QRimg,
            'status'=>1
            );
        //检测是否存在待审核的
        $result = $this->service_model->is_exists($commonUser->openid);
        if(empty($result)){
            $res = $this->service_model->insert_lifted($data,1);
        }
    }
    public function appeal(){
        $commonUser = $this->getCommonUser(FALSE);
        $res = $this->service_model->appeal($commonUser->openid);
        if($res){
            echo json_encode((object)['errocode'=>0,'errmsg'=>'msg']);
        }else{
            echo json_encode((object)['errocode'=>1,'errmsg'=>'msg']);
        }
    }
    /**
     * 二维码图片上传
     */
    public function upload() {
        $commonUser = $this->getCommonUser(FALSE);
        $filepath= '/files/public/qrcode';
        // echo upload_file('gif|jpg|jpeg|png',500,$filepath);
        $file=$_POST['filestr'];
        $base64 = htmlspecialchars($file);
        if (preg_match('/^(data:\s*image\/(\w+);base64,)/', $base64, $result)) {
            $type = $result[2];
            
            $pwd = str_replace('\\', '/', getcwd());
            $mchRelDir = $pwd.$filepath;
            if (! file_exists($mchRelDir) || ! is_dir($mchRelDir)) {
                @mkdir($mchRelDir, 0777, true);
            }
            $new_file = $filepath .'/'.time(). rand(10000,99999) . ".{$type}";
            $relpath = $pwd.$new_file;
            if (file_put_contents($relpath, base64_decode(str_replace($result[1], '', $base64)))) {
                //resize image
                $this->load->library('common/imagick_lib');
                $this->imagick_lib->readImage($relpath);
                $this->imagick_lib->resize(600,600);
                $this->imagick_lib->saveTo($relpath);
                //end resize image
                echo $this->config->item('cdn_m_url').$new_file;
            }
        }
    }
    /*================== 申请解封结束 ==================*/
}
