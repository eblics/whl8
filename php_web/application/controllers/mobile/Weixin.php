<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * 微信功能入口
 */
class Weixin extends Mobile_Controller {
    /**
     * 初始化
     */
    public function __construct() {
        // 初始化，加载必要的组件
        parent::__construct();
        $this->load->model('merchant_model','merchant');
        $this->load->library('common/code_encoder');
        $this->load->library('common/common_lib');
        $this->load->model('scan_log_model');
        $this->load->library('common/ipwall');
        $this->load->library('common/common_login');
    }

    /**
     * 基础支持-全局access_token-oauth2授权获取微信用户code
     */
    public function code($mchId, $from){
        $mchInfo = $this->merchant->get($mchId);
        if (isset($_GET['code'])) {
            $code = $_GET['code'];
            $result = $this->weixin_rest_api->code($code, $mchInfo, $from);
            if ($result->errcode != 0) {
                $this->load->view('error_code_scan', ['errmsg' => $result->errmsg]);
            }
        } else {
            exit('获取微信Code失败');
        }
    }

    /**
     * 创建公众号菜单 临时方法
     */
    public function create_menu($mchId=NULL){
        if(!isset($mchId)) exit('缺少mchid参数');
        if($this->input->get_post('create')!=NULL){
            $re=$this->weixin_rest_api->create_menu($mchId,$this->input->get_post('menu_data'));
            var_dump($re);
            return;
        }
        $data=$this->weixin_rest_api->get_menu($mchId);
        $newdata=str_replace('{"menu":','',$data);
        $newdata=substr($newdata,0,strlen($newdata)-1);
        $this->load->view('tmp_weixin',['menu_data'=>$newdata]);
    }

    /**
     * 异步获取微信jssdk签名
     *
     * @param string $url 当前请求页面的URL地址
     * @return json
     */
    public function jssignature() {
        // $this->getCommonUser();
        $url    = $this->input->get_post('url');
        $code   = $this->getCurrentScanCode();
        $mchId  = $this->getCurrentMchId();
        $currentUser = $this->getCurrentUser($mchId);
        $result = (object)['errcode' => 0, 'errmsg' => NULL];

        $jsticket = ['appId' => '', 'timestamp' => '', 'nonceStr' => '', 'signature' => ''];
        $merchant = $this->merchant->get($mchId);
        $params = ['appId' => $merchant->wxAppId, 'appSecret' => $merchant->wxAppSecret];
        $this->load->library('weixin_jssdk', $params);
        $data = $this->weixin_jssdk->get_url_SignPackage($url);
        $data['code'] = $code;
        $data['debug'] = false;
        // $data['member'] = $currentUser;
        $result->options = $data;
        header('Content-Type: application/json;charset=utf-8;');
        echo $this->common_lib->encode_output($result, 'json');
    }
}
