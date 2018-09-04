<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Login extends OppController {

    public function index() {
        $this->load->view('admin_login');
    }

     /**
     * 图片上传方法
     */
    public function upload() {
        $filepath= '/files/private/upload/'.$this->mchId;
        echo upload_file('gif|jpg|png',500,$filepath);
    }

    /**
     * 附件上传方法
     */
    public function attaupload() {
        $filepath= '/files/private/cert/'.$this->mchId;
        echo upload_file('pem',500,$filepath);
    }

    public function verify_img() {
        $this->load->helper('captcha');
        $result = create_captcha();
	debug(var_export($result,True));
        $this->session->set_userdata('verify_code', $result['word']);
	debug($this->session->userdata('verify_img'));
        echo $result['image'];
    }
}
