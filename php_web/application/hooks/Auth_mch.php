<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * 定义一个在控制器调用之前执行的钩子，用于检测用户是否登录
 */
class Auth {

    private $no_auth_urls = [
        'version',
        'login',
        'user/login',
        'user/check_validate',
        'user/valid_mes',
        'user/up_pass',
        'user/send_mes',
        'user/is_num_exists',
        'user/mobile_exists',
        'user/forget',
        'user/create_img',
        'user/check_verify',
        'user/verifyimg',
        'user/img',
        'user/validcode',
        'user/auth',
        'user/reg',
        'user/reg_user',
        'user/md5',
        'user/auth_getvalid',
        'user/login_old',
        'user/reg_old',
        'user/token_auth',

        'wx3rd/api',
        'wx3rd/api_auth',
        'wx3rd/api',
        'wx3rd/api_auth',
        'wx3rd/test_pub',
        'wx3rd/updateWxToken',

        'wcapi/api',
        'wcapi/test_wcapi',

        'account/update_token',

        'opp/auth',

        'machine/merchants',
        'wxpay/notify',
        'notify/index',
        'notify/yeepay',

        'utils/api/sms.get',

    ];

    private $no_wxpay_auth = [
        'cashier/renew',
        'cashier/scanPay',
        'cashier/is_pay',
        'notify/index',
        'user/remind',
        'user/logout',
        'cashier/sendYeepayOrder',
        'cashier/searchOrder'
    ];

    private $no_wait_urls = [
        'user/login',
        'user/check_validate',
        'user/upgrade_account',
        'user/authorize_account',
        'user/up_pass',
        'user/create_img',
        'user/valid_mes',
        'user/send_mes',
        'user/is_num_exists',
        'user/forget',
        'user/check_verify',
        'user/auth',
        'user/reg',
        'user/verifyimg',
        'user/reg_user',
        'user/md5',
        'user/validcode',
        'user/update_weixin_info',
        'user/reg_user',
        'user/person',
        'user/get_person_info',
        'user/img',
        'user/update_person_info',
        'user/safe',
        'user/update_password',
        'user/company',
        'user/get_company_info',
        'user/get_company_update',
        'user/wachat',
        'user/get_wechat_info',
        'user/update_wechat_info',
        'user/upload',
        'user/reviewing',
        'user/logout',
        'user/weixin',
        'user/wechat',
        'user/attaupload',

        'wcapi/api',
        'wcapi/test_wcapi',
        'account/update_token'
    ];

    private $no_controller = [
        'service',
        'wx3rd'
    ];

    private $no_permission = [
        'user/person',
        'user/up_pass',
        'user/company',
        'user/wechat',
        'user/weixin',
        'user/safe',
        'user/update_person_info',
        'user/update_password',
        'user/get_company_update',
        'user/update_wechat_info'
    ];

    private $pre_account = [
        'batch'
    ];
    // 运营人员权限
    private $normal_account = [
        'batch/add',
        'batch/edit',
        'batch/lists',
        'batch/data',
        'batch/dataenable',
        'batch/data_batch_scannum',
        'batch/del_batch',
        'batch/save',
        'batch/start',
        'batch/stop',
        'batch/download',
        'batch/order_lists',
        'batch/order_out_lists',
        'batch/order_list_data',
        'batch/order_scan_data',
        'batch/order_add',
        'batch/order_out_add',
        'batch/order_detail',
        'batch/order_code_download',
        'batch/order_code_download_log',
        'batch/order_errmsg_download',
        'batch/order_delete',
        'batch/order_exists_orderno',
        'batch/fetch_token',
        'product/catedata',
        'product/prodata'
    ];
    private $CI;

    function  __construct() {
        $this->CI = &get_instance();
    }

    public function check() {
        if (! defined('MCH')) {
            return;
        }
        //add by whl 20160426 1634
        $uriArr=$this->CI->uri->rsegments;
        if (empty($uriArr)) {
            $uriArr = ['', 'index', 'index'];
        }
        $curUri=$uriArr[1].'/'.$uriArr[2];
        if(in_array($curUri,$this->no_auth_urls)){
            return;
        }
        //add by whl 20160426 1634 end
        if(!isset($this->CI->session->userdata['userId'])){
            if(!in_array(uri_string(),$this->no_auth_urls) ){
                redirect(config_item('mch_url').'login');
            }
        }else{
            $url = uri_string();
            $str = explode('/', $url);
            if(in_array($str[0], $this->no_controller)){
                return;
            }
            if($this->CI->session->userdata['part'] == 1 && in_array(uri_string(),$this->no_permission)) {
                redirect(config_item('mch_url').'user/reviewing','auto');
                die();
            }
            //part导致运营端登录企业端错误，先注释掉--栗树亮
            // if(in_array(uri_string(),$this->no_permission)) {
            //     redirect(base_url().'user/reviewing','auto');
            //     return;
            // }
            // if(in_array(uri_string(),$this->no_wait_urls) && $this->CI->session->userdata['part'] == 0 && $this->CI->session->userdata['status'] == 1) {
            //     echo "two";
            //     return;
            // }
            if(!in_array(uri_string(),$this->no_wait_urls)){
                $status = $this->CI->session->userdata['status'];
                if($status == 4 || $status == 0 || $status == 5 || $status == 2 || $status == 6 ){
                    if($status == 5 && in_array($str[0],$this->pre_account) && !isset($this->CI->session->userdata['part'])){
                    }elseif($status == 5 && in_array(uri_string(),$this->normal_account)){
                    }else{
                        redirect(config_item('mch_url').'user/reviewing');
                    }
                    return;
                }
            }

            // elseif(in_array(uri_string(),$this->no_wait_urls)){
            //     if($this->CI->session->userdata['part'] == 1 && $str[0] == 'user'){
            //         redirect(base_url().'user/reviewing');
            //     }
            // }
            // else{
            //     exit('系统状态异常,请清浏览器除缓存后重新登录');
            // }
            //
            //=============================如果试用期到期则显示到期提示页面
            // $this->CI->load->model ('merchant_model');
            // $this->CI->load->model ('batch_model');
            //
            // //查询当前企业的信息
            // $merchantInfo=$this->CI->merchant_model->get_company_info($this->CI->session->userdata['mchId']);
            // $batchNumberTotal=intval($this->CI->batch_model->get_batch_num_total($this->CI->session->userdata['mchId']));
            // //如果还是试用期
            // if($merchantInfo->is_formal==0){
            //     if(!in_array(uri_string(),$this->no_wxpay_auth)){
            //         if($batchNumberTotal==500||$merchantInfo->expired < date('Y-m-d',time())){
            //             redirect(config_item('mch_url').'cashier/renew','auto');
            //             die();
            //         }
            //     }
            // }
            // if($merchantInfo->is_formal==1){
            //     if(!in_array(uri_string(),$this->no_wxpay_auth)){
            //         if($merchantInfo->expired < date('Y-m-d',time())){
            //             redirect(config_item('mch_url').'cashier/renew','auto');
            //             die();
            //         }
            //     }
            // }
        }
    }
}
