<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Service extends MerchantController{
    public function __construct() {
        // 初始化，加载必要的组件
        parent::__construct();
        $this->load->model('user_model');
        $this->load->model('merchant_model');
        $this->mchId = $this->session->userdata('mchId');
    }
    
    /**
     * CI控制器默认入口
     */
    public function index(){
        //如无需使用留空即可
    }
    
     /**
     * 必读
     */
    public function help_read(){
        $this->load->view('help_read');
    }
    /**
     * 公众平台
     */
    public function gongzhong(){
        $this->load->view('help_gz');
    }
    /**
     * 认证
     */
    public function renzheng(){
        $this->load->view('help_rz');
    }
    /**
     * 支付
     */
    public function zhifu(){
        $this->load->view('help_zf');
    }
    /**
     * 微信企业支付开通
     */
    public function enable(){
        $this->load->view('help_enable');
    }
    /**
     * 微信红包/企业支付限额修改
     */
    public function limit(){
        $this->load->view('help_limit');
    }
    /**
     * 用户手册
     */
    public function document(){
        $this->load->view('help_doc');
    }
    /**
     * 微信授权
     */
    public function token(){
        $this->load->view('help_token');
    }
    /**
     * Q&A
     */ 
    public function qanda() {
        $this->load->view('help_q_a');
    }
    /**
     * 关于我们
     */
    public function aboutus(){
        $this->load->view('help_aboutus');
    }
    /**
     * 联系我们
     */
    public function contactus(){
        $this->load->view('help_contactus');
    }
}
