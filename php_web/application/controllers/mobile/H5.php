<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class H5 extends Mobile_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->library('common/code_encoder');
        $this->load->model('merchant_model');
        $this->load->model('user_model');
        $this->load->model('ranking_model');
        $this->load->model('webapp_model');
        $this->load->library('common/ipwall');
        $this->load->model('scan_log_model');
        $this->load->library('common/common_login');
    }

    public function api($apiName = NULL) {
        $currentUser = $this->getCurrentUser($this->getCurrentMchId());
        if (! isset($apiName)) {
            $this->ajaxResponseFail('403 Forbidden.', 403, 403);
        } else if ($apiName === 'profile.save') {
            $mobile = $this->input->post('mobile');
            $valid = $this->input->post('vaild');
            $username = $this->input->post('username');
            $storename = $this->input->post('storename');
            $storeaddress = $this->input->post('storeaddress');
            $province = $this->input->post('province');
            $city = $this->input->post('city');
            $dealername = $this->input->post('dealername');

            $this->load->library('common/send_sms');
            $res = $this->send_sms->check_validcode($mobile, $valid);
            if ($res['statusCode'] != 0) {
                $this->ajaxResponseFail('短信验证码错误');
                return;
            }
            if (empty($mobile) || empty($valid) || 
                empty($username) || empty($storename) || 
                empty($storeaddress) || empty($dealername)) {
                $this->ajaxResponseFail('所有填写项均不能为空');
                return;
            }
            // 保存用户信息
            $addParams['openid'] = $currentUser->openid;
            $addParams['mobile'] = $mobile;
            $addParams['username'] = $username;
            $addParams['storename'] = $storename;
            $addParams['storeaddress'] = $storeaddress;
            $addParams['province'] = $province;
            $addParams['city'] = $city;
            $addParams['dealername'] = $dealername;
            try {
                $this->load->model('Guangming_model', 'guangming');
                $this->guangming->saveGuangmingMember($addParams);
                $this->ajaxResponseSuccess();
            } catch (Exceptin $e) {
                $this->ajaxResponseFail($e->getMessage(), $e->getCode());
            }
        } else if ($apiName === 'sms.send') {
            $phonenum = $this->input->post('mobile');
            $this->load->library('common/send_sms');
            $result = $this->send_sms->get_validcode($phonenum, 75610);
            if ($result['statusCode'] == 0) {
                if (! isProd()) {
                    $smsCode = $this->session->smssender_validcode;
                    $this->ajaxResponseFail('您的验证码是：'. $smsCode, 700);
                } else {
                    $this->ajaxResponseSuccess();
                }
            } else {
                $this->ajaxResponseFail($result['message']);
            }
            
        }
    }

    public function id($h5Name = NULL) {
        if (is_null($h5Name)) {
            exit('404 Not Found.');
        }
        $mchId = $this->getCurrentMchId();
        $currentUser = $this->getCurrentUser($mchId);
        $sql = "SELECT * FROM users WHERE openid = ? AND mchId = ?";
        $dbUser = $this->db->query($sql, [$currentUser->openid, $mchId])->row();
        $currentUser->subscribe = $dbUser->subscribe;
        if (isset($currentUser->subscribe) && ($currentUser->subscribe === '1' || $currentUser->subscribe === 1)) {
            error("h5-id-current-user: ". json_encode($currentUser));
            $normalAmount = $this->user_model->get_amount($currentUser->id, $this->getCurrentMchId(), 0);
            $this->load->model('Guangming_model', 'guangming');
            $gmMember = $this->guangming->getGuangmingMember($currentUser->openid);
            if (isset($gmMember)) {
                $gmMember->nickname = $currentUser->nickName;
                $gmMember->headimgurl = $currentUser->headimgurl;
                $gmMember->mchId = $currentUser->mchId;
            } else {
                $gmMember = new stdClass();
                $gmMember->nickname = $currentUser->nickName;
                $gmMember->headimgurl = $currentUser->headimgurl;
                $gmMember->mchId = $currentUser->mchId;
            }
            if (! isset($normalAmount)) {
                $normalAmount = new stdClass();
                $normalAmount->amount = 0;
            }
            $viewData = [
                'currentUser' => json_encode($gmMember),
                'normalAmount' => $normalAmount->amount,
            ];
            $this->load->view('guangming/index', $viewData);
        } else {
            redirect('/h5/gm-hb/Scan.html');
        }
    }

    public function html($html = NULL) {
        if (is_null($html)) {
            show_404();
        }
        $random = str_replace('.html', '', $html);
        $url = $this->session->userdata('current_h5_path_' . $random);
        if (is_null($url)) {
            show_404();
        }
        print file_get_contents($url);
    }

    public function mchdata() {
        $code = $this->getCurrentScanCode();
        if (empty($code) || ! isset($_SERVER['HTTP_REFERER'])) {
            $this->ajaxResponseOver('403 Forbidden', 403, 403);
        }
        $referer = $_SERVER['HTTP_REFERER'];
        $scaninfo = $this->scan_log_model->get_by_code($code);
        if (! isset($scaninfo)) {
            $this->ajaxResponseOver('找不到扫码记录');
        }
        $code_ret=$this->code_encoder->decode($code);
        if(isset($code_ret->errcode) && $code_ret->errcode!=0){
            $errorMsg='h5/mchdata: ：解码失败';
            log_message('error',$errorMsg);
            $this->common_login->save_user_log(1,$errorMsg,$code);
            $this->ipwall->error_process();
            return;
        }
        $mch_code=$code_ret->result->mch_code;
        $merchant=$this->merchant_model->get_by_code($mch_code);
        if(!isset($merchant)){
            $errorMsg='h5/mchdata: ：企业不存在';
            log_message('error',$errorMsg);
            $this->common_login->save_user_log(1,$errorMsg,$code);
            return;
        }
        $url = explode('/', $referer);
        $path = '%' . $url[4] . '%';
        $sql = "select t1.data from webapp_config t1 join webApps t2 on t2.id = t1.webappId where t2.appPath like ? and t1.mchId = ?";
        $appConfig = $this->db->query($sql, [$path, $merchant->id])->row();
        $config = '';
        if (isset($appConfig)) {
            $config = $appConfig->data;
        }
        //正确扫码处理结果
        $this->ipwall->correct_process();

        $viewData = [
            'mchName' => $merchant->name, 
            'qrCode' => '/h5/get_qrcode/'. urlencode($merchant->wxQrcodeUrl), 
            'h5Config' => $config,
        ];
        $this->ajaxResponseSuccess($viewData);
    }
    
    function get_qrcode($url=null){
        header("Content-Type: image/jpeg;text/html; charset=utf-8");
        ini_set('user_agent','Mozilla/5.0 (Linux; Android 5.1.1; Mi-4c Build/LMY47V) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/37.0.0.0 Mobile MQQBrowser/6.2 TBS/036215 Safari/537.36 MicroMessenger/6.3.16.49_r03ae324.780 NetType/WIFI Language/zh_CN');
        // echo file_get_contents(urldecode($url));
        $width = 300;
        $height = 300;
        $image = new Imagick (urldecode($url));
        $image->resizeimage($width,$height,Imagick::FILTER_LANCZOS,1);
        echo $image->getimageblob();
    }
    
    public function userdata($city=null) {
        // $this->getCommonUser();

        header('Content-type','text/javascript;charset=utf-8;');
		$data=(object)['city'=>$city,'rank'=>null];
        if($this->ipwall->is_prevent()){
            log_message('error','h5/userdata ：扫错误码过于频繁');
            return;
        }
        $mchId=$this->input->get_post('mchid');
        $userId=$this->input->get_post('userid');
        if(!isset($_SERVER['HTTP_REFERER'])){
            log_message('error','h5/userdata ：no HTTP_REFERER');
            return;
        }
        $referer=$_SERVER['HTTP_REFERER'];
        // $url=parse_url($referer);
        // $query=$url['query'];
        // $queryArr=explode('&',$query);

        $code = $this->getCurrentScanCode();
        // foreach ($queryArr as $k => $v) {
        //     if(stripos($v,'code=')!==FALSE){
        //         $code=str_replace('code=','',$v);
        //     }
        // }
        // if(empty($code)){
        //     $errorMsg='h5/userdata: code 为空';
        //     log_message('error',$errorMsg);
        //     $this->common_login->save_user_log(1,$errorMsg,$code,$mchId,$userId);
        //     $this->common_login->forbidden();
        //     $this->ipwall->error_process();
        //     return;
        // }
        // if(strpos($code,'?')>0){
        //     $codeArr=explode("?",$code);
        //     $code=$codeArr[0];
        // }
        $scaninfo=$this->scan_log_model->get_by_code($code);
        if(!isset($scaninfo)){
            $errorMsg='h5/userdata: ：没有扫码记录';
            log_message('error',$errorMsg);
            $this->common_login->save_user_log(1,$errorMsg,$code,$mchId,$userId);
            $this->ipwall->error_process();
            return;
        }
        $code_ret=$this->code_encoder->decode($code);
        if(isset($code_ret->errcode) && $code_ret->errcode!=0){
            $errorMsg='h5/userdata: ：解码失败';
            log_message('error',$errorMsg);
            $this->common_login->save_user_log(1,$errorMsg,$code,$mchId,$userId);
            $this->ipwall->error_process();
            return;
        }
        $mch_code=$code_ret->result->mch_code;
        $merchant=$this->merchant_model->get_by_code($mch_code);
        if(!isset($merchant)){
            $errorMsg='h5/userdata: ：没有企业信息';
            log_message('error',$errorMsg);
            $this->common_login->save_user_log(1,$errorMsg,$code,$mchId,$userId);
            $this->ipwall->error_process();
            return;
        }
        $user=$this->user_model->get($userId);
        if(!$user){
            $errorMsg='h5/userdata: ：查不到用户信息';
            log_message('error',$errorMsg);
            $this->common_login->save_user_log(1,$errorMsg,$code,$mchId,$userId);
            $this->ipwall->error_process();
            return;
        }
        if($user->mchId!=$mchId){
            $errorMsg='h5/userdata: ：用户和企业不匹配';
            log_message('error',$errorMsg);
            $this->common_login->save_user_log(1,$errorMsg,$code,$mchId,$userId);
            $this->ipwall->error_process();
            return;
        }
        $type='all';
        if($city!=null){
            $type='city';
        }
        $user_rank=$this->ranking_model->get_rank_by_user_id($user,$type);
        if(!$user_rank){
            log_message('error','h5/userdata ：查询排名失败');
            return;
        }
        $data->rank=$user_rank->rank;
        $data->city=$city==null?'全国':$city;
        $data->scanNum=$user_rank->scanNum;
        //正确扫码处理结果
        $this->ipwall->correct_process();
        echo $_GET['callback'].'('.json_encode($data).');';
    }
    
}
