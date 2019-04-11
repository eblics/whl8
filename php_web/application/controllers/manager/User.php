<?php
defined('BASEPATH') or exit('No direct script access allowed');

class User extends MerchantController {

    public function __construct() {
        parent::__construct();
        $this->load->model('user_model');
        $this->load->model('merchant_model');
        $this->load->helper('cookie');
        $this->load->helper('captcha');
        $this->load->library('validate_code');
        $this->load->library('log_record');

        $this->mchId = $this->session->userdata('mchId');
    }

    // deprecated please use /login
    public function login() {
        redirect('/login');
        exit();
    }

    // 企业注册
    public function reg() {
        $this->load->view('user/reg');
    }

    // 忘记密码
    public function forget() {
        $this->load->view('user/forget');
    }

    /**
     * 接口定义
     * @param $apiName 接口名称
     * {
     *     
     * }
     * @return void
     */
    public function api($apiName = NULL) {
        if (! isset($apiName)) {
            $this->ajaxResponseFail('403 Forbidden', 403, 403);
        } elseif ($apiName === '') {

        } else {
            $this->ajaxResponseFail('404 Not Found', 404, 404);
        }
    }

    /**
     * 过期提醒
     */
    public function remind(){
        $mchid = $this->input->post('mchid');
        $result = $this->merchant_model->get_remind($mchid);
        $nowtime = time();
        $expiredtime = 15*24*60*60;
        if(!empty($result->expired)){
            $time = strtotime($result->expired);
            $difference = $time - $nowtime;
            $remindt = ceil($difference/24/60/60);
            if($remindt < 0){
                $remindt = 0;
            }
            $mes = "服务到期时间只剩".$remindt."天！";
            $data = [
                    'errcode' => 1,
                    'errmsg' => $mes
                ];
            if($difference < $expiredtime){
                echo json_encode ( $data );
                // $this->output->set_content_type('application/json')->set_output(ajax_resp());
            }else{
                $data['errcode'] = 0;
                $data['errmsg'] = null;
                echo json_encode ( $data );
            }
        }else{
                $data['errcode'] = 0;
                $data['errmsg'] = null;
                echo json_encode ( $data );
        }
    }
    /**
     * 忘记密码 检测手机号是否存在
     */
    public function is_num_exists(){
        $sessionId = session_id();
        $phoneNum = $this->input->post('phoneNum');
        $res = $this->merchant_model->get_by_phone($phoneNum);
        if(isset($res)){
            $result = $this->merchant_model->set_session_id($phoneNum,$sessionId);
            if($result){
                $this->output->set_content_type('application/json')->set_output(ajax_resp());
            }else{
                $this->output->set_content_type('application/json')->set_output(ajax_resp(),'查询失败，请稍后再试！',1);
            }

        }else{
            $this->output->set_content_type('application/json')->set_output(ajax_resp([],'该手机号不存在',10021));
        }
    }

    public function mobile_exists() {
        $mobile = $this->input->post('phoneNum');
        $res = $this->merchant_model->get_by_phone($mobile);
        if (isset($res)) {
            $this->ajaxResponseFail('手机号已存在');
        } else {
            $this->ajaxResponseSuccess();
        }
    }


    /**
     * 生成验证码图片
     */
    public function create_img(){
        unset($_SESSION['valid_session']);
        $img = new Validate_code();
        $img->doimg();
        $_SESSION['valid_session'] = $img->getCode();
    }
    /**
     * 校对图片验证码
     * @ word post过来的验证码
     */
    public function check_validate(){
        $word = trim(strtolower($this->input->post('imgvalid')));
        $session_word = $_SESSION['valid_session'];
        if($word == $session_word){
            $this->output->set_content_type('application/json')->set_output(ajax_resp());
        }else{
            $this->output->set_content_type('application/json')->set_output(ajax_resp([],'图片验证码错误',10025));
        }
    }
    /**
     * 接收用户注册信息
     */
    public function reg_user(){
    	header('Content-type:application/json;charset=utf8;');
        $dayu = $this->config->item('use_alidayu');
        // $result=(object)['errcode'=>0,'errmsg'=>''];
        $getpass =$this->input->post('password');
        $validcode = $this->input->post('validcode');
        $account = $this->input->post('account');
        $dealer = $this->input->post('dealer');
        $salt = mt_rand(100000,999999);
        $password = md5(md5($getpass.$salt).$salt);
        if(!isset($account)||!isset($password)){
            $this->output->set_content_type('application/json')->set_output(ajax_resp([],'账户或密码为空',10001));
            return;
        }
        $user=$this->merchant_model->get_by_phone($account);
        if(!isset($user)){
            if($dayu == 1){
                if($this->merchant_model->validate_validcode($account,$validcode)['success'] == 0 && $_SERVER['CI_ENV'] !== 'development'){
                    $this->output->set_content_type('application/json')->set_output(ajax_resp([],'短信验证码错误',10010));
                    return;
                }
            }else{
                if($this->merchant_model->proof_dayu_vcode($account,$validcode)['success'] == 0 && $_SERVER['CI_ENV'] !== 'development'){
                    $this->output->set_content_type('application/json')->set_output(ajax_resp([],'短信验证码错误',10010));
                    return;
                }
            }

            //分配一个mchId号
            $mchData=array(
                'id'=>null,
                'createTime'=>time(),
                'status'=>0,
                'dealer_code'=>strtoupper($dealer),
                'expired'=>date("Y-m-d",strtotime("+15 day")),// add by cw @2017-07-25 新用户注册15天有效期
                'is_formal'=>0,// add by cw @2017-07-25
            );
            $newmchid = $this->merchant_model->get_new_mchid($mchData);
            //向apps插入数据
            $this_result = $this->merchant_model->reg_apps($newmchid);
            if(!$this_result){
                $this->output->set_content_type('application/json')->set_output(ajax_resp([],'网络错误，写入数据失败',16005));
                return;
            }
        	$userinfo = ['phoneNum'=>$account,'password'=>$password,'role'=>0,'status'=>0,'salt'=>$salt,'createTime'=>time(),'mchId'=>$newmchid];
        	$getres = $this->merchant_model->reg_mchuser($userinfo);
            if(isset($getres)){
                // $this->session->set_userdata(['userId'=>$getres,'part'=>0,'role'=>0,'status'=>0,'mchId'=>$newmchid,'username'=>'']);
                $this->output->set_content_type('application/json')->set_output(ajax_resp([],'OK',0));
            }
        }elseif($user->phoneNum == $account){
        	//验证是否已经存在相同手机号
            $this->output->set_content_type('application/json')->set_output(ajax_resp([],'此账户已存在',10005));
        	return;
        }else{
            $this->output->set_content_type('application/json')->set_output(ajax_resp([],'状态异常',10004));
        	return;
        }
    }
    /**
     * 注册 接收信息获取验证码
     */
    public function validcode() {
        $mobile = $this->input->post('account');
        $smsCode = $this->merchant_model->sendSms($mobile, 'register');
        if (!isProd()) {
            $this->ajaxResponseFail($smsCode, 700);
        } else {
            $this->ajaxResponseSuccess();
        }
    }
    /**
     * 登录 获取密码
     */
    public function auth_getvalid() {
        $this->ajaxResponseFail('Fail: please use /utils/api/sms.get');
    }

    /**
     * 找回密码 获取验证码
     */
    public function send_mes() {
        $mobile = $this->input->post('account');
        $smsCode = $this->merchant_model->sendSms($mobile, 'reset_passwd');
        if (! isProd()) {
            $this->ajaxResponseFail($smsCode, 700);
        } else {
            $this->ajaxResponseSuccess();
        }
    }

    /**
     * 找回密码 校验手机短信
     */
    public function check_mes() {
        $mobile = $this->input->post('account');
        $valid = $this->input->post('value');
        $this->load->library('common/send_sms');
        $res = $this->send_sms->check_validcode($mobile, $valid);
        if ($res['statusCode'] != 0) {
            $this->ajaxResponseFail('短信验证码错误');
        } else {
            $this->ajaxResponseSuccess();
        }
    }

    /**
     * 校对短信密码
     */
    public function valid_mes(){
        $dayu       = $this->config->item('use_alidayu');
        $account    = $this->input->post('account');
        $value      = $this->input->post('value');
        $change     = null !== $this->input->post('change') ? 1 : null;
        if (isset($change)) {
            $this->merchant_model->up_cstatus($account,1);
        }
        if($dayu == 1) {
            $res = $this->merchant_model->validate_validcode($account,$value);
        } else {
            $res = $this->merchant_model->proof_dayu_vcode($account,$value);
        }

        // 免验证码登录
        $mchAccount = $this->merchant_model->get_by_phone($account);
        if (isset($mchAccount) && isset($mchAccount->noSms)) {
            if ($mchAccount->noSms === '1') {
                $res['success'] = 1;
            }
        }
        // if (! isProd()) {
        //     $this->output->set_content_type('application/json')->set_output(ajax_resp([]));
        //     return;
        // }
        if ($res['success'] == 0){
            $this->output->set_content_type('application/json')->set_output(ajax_resp([], '短信验证码错误', 10022));
            return;
        }
        if($res['success'] == 1){
            $adopt = ['account'=>$account,'adopt'=>1];
            $this->session->set_userdata($adopt);

            // -----------------------------------
            // Added by shizq - begin
            // 1.判断该账户是普通账户还是企业账户
            $accountType = $this->merchant_model->getAccountType($account);
            if ($accountType == AccountTypeEnum::Normal) {
                $this->output->set_content_type('application/json')->set_output(ajax_resp([]));
            } else if ($accountType == AccountTypeEnum::Merchant) {
                $merchants = $this->merchant_model->getMerchantList($account);
                $this->output->set_content_type('application/json')->set_output(ajax_resp($merchants));
            } else if ($accountType == AccountTypeEnum::Isnone) {
                $merchants = $this->merchant_model->getMerchantList($account);
                $this->output->set_content_type('application/json')->set_output(ajax_resp($merchants));
            } else {
                $this->output->set_content_type('application/json')->set_output(ajax_resp([], '系统错误', 10023));
            }
            // Added by shizq - end
        }
    }
    /**
     * 接收用户登录信息
     */
    public function auth(){
        $account    = $this->input->post('account');
        $password   = $this->input->post('password');
        $keep_state = $this->input->post('keep_state');
        $mch_id     = $this->input->post('mch_id');
        $phoneNum   = $this->input->post('account');

        if(!isset($account)||!isset($password)){
            $this->output->set_content_type('application/json')->set_output(ajax_resp([],'缺少账户或密码',10001));
            return;
        }
        $user=$this->merchant_model->get_by_phone($account);

        if(!isset($user) || $user->status == 3){
            $this->output->set_content_type('application/json')->set_output(ajax_resp([],'没有这个账户',10002));
            return;
        }
        if($user->password!=md5(md5($password.$user->salt).$user->salt)){
            $this->output->set_content_type('application/json')->set_output(ajax_resp([],'密码不正确',10003));
            return;
        }
        if ($user->status == 2) {
            $this->output->set_content_type('application/json')->set_output(ajax_resp([],'账户已锁定',10007));
            return;
        }

        $accountType = $this->merchant_model->getAccountType($account);
        if ($accountType == AccountTypeEnum::Normal) {
            if($user->role == 0) {
                $m_status = $this->merchant_model->get_company_info($user->mchId);
                if($m_status->status == 3){
                    $this->output->set_content_type('application/json')->set_output(ajax_resp([],'企业已被冻结，请联系我司销售',10008));
                    return;
                }
            }
            $mid = $user->mchId;

            $modules = $this->merchant_model->getUserPermissionModules($user->role);
            $this->session->set_userdata('permission_modules', $modules);

            $company = $this->merchant_model->get_company_info($mid);
            $this->merchant_model->up_cstatus($phoneNum,0);
            // added by George == start == 2017年10月20日 ==判断是否为试用，兼容view/left.php判断。
            if($company->is_formal == 1){
                $expired = null;
            } else {
                $expired = new stdClass();
            }
            // added by George == end
            $merchant = $this->merchant_model->get($user->mchId);
            $this->session->set_userdata('current_merchant', $merchant);
            $this->session->set_userdata([
                'userId'=>$user->id,
                'status'=>$company->status,
                'mchId'=>$user->mchId,
                'username'=>$user->userName,
                'part'=>0,
                'role' => $user->role,
                'is_formal' => $company->is_formal,//add by cw @2017-07-26
                'expired' => $expired,//add by cw @2017-07-26
                'mail'=>$user->mail,//add by fengyanjun @2018-01-18
            ]);
            $this->output->set_content_type('application/json')->set_output(ajax_resp(['account_type' => '普通账户']));
            /*------------------记录日志start----------*/
            if(isset($company)){
                try{
                    $logInfo = (array)$user;
                    $logInfo['info'] = '登陆';
                    $logInfo['id'] = $mid;
                    $logInfo['op'] = $this->log_record->Login;
                    $this->log_record->addLog( $mid,$logInfo,$this->log_record->User);
                }catch(Exception $e){
                    log_message('error','mch_log_error:'.$e->getMessage());
                }
            }
            /*------------------记录日志end----------*/
        } else if ($accountType == AccountTypeEnum::Merchant) {
            if ($mch_id === "") {
                $this->output->set_content_type('application/json')->set_output(ajax_resp([], '请选择要登录的商户', 10008));
                return;
            }
            $merchantUser = $this->merchant_model->getMerchantUser($user->id, $mch_id);
            if (! isset($merchantUser)) {
                $this->output->set_content_type('application/json')->set_output(ajax_resp([], '企业不存在', 10002));
                return;
            }
            if($merchantUser->role == 0) {
                $m_status = $this->merchant_model->get_company_info($mch_id);
                if($m_status->status == 3){
                    $this->output->set_content_type('application/json')->set_output(ajax_resp([],'企业已被冻结，请联系我司销售',10008));
                    return;
                }
            }

            $modules = $this->merchant_model->getUserPermissionModules($user->role);
            $this->session->set_userdata('permission_modules', $modules);

            $company = $this->merchant_model->get_company_info($mch_id);
            // added by George == start == 2017年10月20日 ==判断是否为试用，兼容view/left.php判断。
            if($company->is_formal == 1){
                $expired = null;
            } else {
                $expired = new stdClass();
            }
            // added by George == end
            $this->merchant_model->up_cstatus($phoneNum,0);
            $merchant = $this->merchant_model->get($mch_id);
            $this->session->set_userdata('current_merchant', $merchant);
            $this->session->set_userdata([
                'userId'   => $user->id,
                'status'   => $company->status,
                'mchId'    => $mch_id,
                'username' => $user->userName,
                'part'     => 0,
                'role'     => $merchantUser->role,
                'is_formal' => $company->is_formal,//add by cw @2017-07-26
                'expired' => $expired,//add by cw @2017-07-26 modified by George 2017/10/20
                'mail'=>$user->mail,//add by fengyanjun @2018-01-18
            ]);
	    debug('manager-user:'.var_export($this->session,True));
            $this->output->set_content_type('application/json')->set_output(ajax_resp(['account_type' => '企业号']));
            /*------------------记录日志start----------*/
            if(isset($company)){
                try{
                    $logInfo = (array)$user;
                    $logInfo['info'] = '登陆';
                    $logInfo['id'] = $mch_id;
                    $logInfo['op'] = $this->log_record->Login;
                    $this->log_record->addLog($mch_id,$logInfo,$this->log_record->User);
                }catch(Exception $e){
                    log_message('error','mch_log_error:'.$e->getMessage());
                }
            }
            /*------------------记录日志end----------*/

        } else {
            $this->output->set_content_type('application/json')->set_output(ajax_resp(['account_type' => 'invalid'], '系统错误', 10023));
        }
    }
    /**
     * 跳转到帐号信息
     */
    public function person(){
        $res = $this->merchant_model->get_person_info($this->session->userId);
        $data = array(
            'res' => $res
        );
        $this->load->model('Account_model', 'account');
        $accountType = $this->account->check4AccountType($this->session->userId);
        $data['account_type'] = $accountType;
        $this->load->view('person',$data);
    }
    /**
     * 获取帐号信息
     */
    public function get_person_info(){
        $res = $this->merchant_model->get_person_info($this->session->userId);
        $this->output->set_content_type('application/json')->set_output(ajax_resp([$res]));
    }
    /**
     * 更新帐号信息
     */
    public function update_person_info(){
        $data['userName'] = $this->input->post('userName');
        $data['realName'] = $this->input->post('realName');
        $data['mail'] = $this->input->post('mail');
        $data['idCardNum'] = $this->input->post('idCardNum');
        $data['idCardImgUrl'] = $this->input->post('idCardImgUrl');
        $data['updateTime'] = time();
        $res = $this->merchant_model->update_person_info($this->session->userId,$data);
        if($res){
            /*--------update -日志信息--------by ccz*/
            try{
                $logInfo = (array)$data;
                $logInfo['info'] = '修改个人账号信息';
                $logInfo['id'] = $this->session->userId;
                $logInfo['objInfo'] = '0';//修改账户信息
                $logInfo['op'] = $this->log_record->Update;
                $this->log_record->addLog( $this->mchId,$logInfo,$this->log_record->User);
            }catch(Exception $e){
                log_message('error','mch_log_error:'.$e->getMessage());
            }
            /*-------记录日志---------end */
            $this->output->set_content_type('application/json')->set_output(ajax_resp([]));
        }
    }
    /**
     * 跳转到企业信息
     */
    public function safe(){
        $this->load->view('safe');
    }
    /**
     * 找回密码模块 修改密码
     */
    function up_pass(){
        $sessionId = session_id();
        $password = $this->input->post('password');
        $phoneNum = $this->input->post('phoneNum');
        if($phoneNum == '17700000000'){
            $this->output->set_content_type('application/json')->set_output(ajax_resp([],'禁止修改密码！',1));
            return;
        }
        //获取状态
        $row = $this->merchant_model->get_status($phoneNum);
        //解决绕过前台验证导致修改非实际号码密码的问题
        // if($this->session->userdata['adopt'] != 1){
        //     $this->output->set_content_type('application/json')->set_output(ajax_resp([],'非法操作1',10023));
        //     return;
        // }
        // if($this->session->userdata['account'] != $phoneNum){
        //     $this->output->set_content_type('application/json')->set_output(ajax_resp([],'非法操作2',10023));
        //     return;
        // } 
        // 
        //加强验证
        $resSessionId = $row->sessionId;
        $time = time();
        $minus = $time - $row->cExpired;
        $cStatus = $row->cStatus;
        if($resSessionId != $sessionId){
            $this->output->set_content_type('application/json')->set_output(ajax_resp([],'非法操作3',10023));
            return;
        }
        if($minus>300){
            $this->output->set_content_type('application/json')->set_output(ajax_resp([],'短信验证码已经过期，请刷新页面重新操作！',10023));
            return;
        }
        // if($cStatus == 0 || $cStatus == null){
        //     $this->output->set_content_type('application/json')->set_output(ajax_resp([],'非法操作1111',10023));
        //     return;
        // }
        $array_items = array('account', 'adopt');
        $salt = mt_rand(100000,999999);
        $pass = md5(md5($password.$salt).$salt);
        $data = ['password'=>$pass,'salt'=>$salt];
        $res = $this->merchant_model->update_pass($phoneNum,$data);
        if(isset($res)){
            $this->session->unset_userdata($array_items);
            $this->merchant_model->up_cstatus($phoneNum,0);
            $this->output->set_content_type('application/json')->set_output(ajax_resp([]));
        }else{
            $this->session->unset_userdata($array_items);
            $this->merchant_model->up_cstatus($phoneNum,0);
            $this->output->set_content_type('application/json')->set_output(ajax_resp([],'操作异常',1));

        }
        /*--------update -日志信息--------by ccz*/
        if(isset($res)){
            try{
                $mchinfo = $this->merchant_model->get_by_phone($phoneNum);
                $logInfo = (array)$mchinfo;
                $logInfo['info'] = '找回密码';
                $logInfo['objInfo'] = '0';//针对用户信息修改
                $logInfo['op'] = $this->log_record->Repassword;
                $this->log_record->addLog( $this->mchId,$logInfo,$this->log_record->User);
            }catch(Exception $e){
                log_message('error','mch_log_error:'.$e->getMessage());
            }
        }
        /*-------记录日志---------end */

    }
    /**
     * 更新登录密码
     */
    public function update_password(){
        $oldpass = $this->input->post('oldpass');
        $newpass = $this->input->post('newpass');
        $renewpass = $this->input->post('renewpass');
        $userid = $this->session->userId;
        $res = $this->merchant_model->get_person_info($userid);
        $salt = $res->salt;
        $pass = $res->password;
        //md5下oldpass
        $checkpass = md5(md5($oldpass.$salt).$salt);
        $updatepass = md5(md5($newpass.$salt).$salt);
        if($pass != $checkpass){
            $this->output->set_content_type('application/json')->set_output(ajax_resp([],'原始密码错误',16010));
            return;
        }
        if($renewpass !=$newpass){
            $this->output->set_content_type('application/json')->set_output(ajax_resp([],'确认密码和新密码不一致',16012));
            return;
        }
        if($updatepass == $pass){
            $this->output->set_content_type('application/json')->set_output(ajax_resp([],'新密码和原始密码不能一致',16011));
            return;
        }
        $data = array('password'=>$updatepass);
        $getres = $this->merchant_model->update_person_info($userid,$data);
        if(!$getres){
            $this->output->set_content_type('application/json')->set_output(ajax_resp([],'保存失败',1));
        }
        /*--------update -日志信息--------by ccz*/
        if($getres){
            try{
                $logInfo['info'] = '修改了登陆密码';
                $logInfo['id'] = $userid;
                $logInfo['objInfo'] = '0';//更改用户本身
                $logInfo['op'] = $this->log_record->Update;
                $this->log_record->addLog( $this->mchId,$logInfo,$this->log_record->User);
            }catch(Exception $e){
                log_message('error','mch_log_error:'.$e->getMessage());
            }
        }
        /*-------记录日志---------end */
        $this->output->set_content_type('application/json')->set_output(ajax_resp([]));
    }
    /**
     * 跳转到企业信息
     */
    public function company(){
        $data = array("username"=>$_SESSION['username'],"mchId"=>$_SESSION['mchId'],"userId"=>$_SESSION['userId']);
        $type = $this->uri->segment(2);
        $res = $this->merchant_model->get_company_info($_SESSION['mchId']);
        $data = array(
            'res' => $res,
            'router' => $type
        );
        $this->load->view('company',$data);
    }
    /**
     * 获取企业信息
     */
    public function get_company_info(){
    	// $result=(object)['errcode'=>0,'errmsg'=>''];
    	$res = $this->merchant_model->get_company_info($_SESSION['mchId']);
    	$this->output->set_output(json_encode($res));
    }
    /**
     * 接收企业更新信息
     */
    public function get_company_update(){
    	$name = $this->input->post('name');
    	$address = $this->input->post('addetail');
    	$contact = $this->input->post('contact');
    	$mail = $this->input->post('mail');
    	$phoneNum = $this->input->post('phoneNum');
    	$licenseNo = $this->input->post('licenseNo');
    	$licenseImgUrl = $this->input->post('licenseImgUrl');
    	$idCardImgUrl = $this->input->post('idCardImgUrl');
    	$idCardNum = $this->input->post('idCardNum');
    	$desc = $this->input->post('desc');
        //查询企业是否验证通过
        $res = $this->merchant_model->get_company_info($this->session->mchId);
        $status = $res->status;
        if($status == 1){
            $data =array(
                'address'=>$address,
                'mail'=>$mail,
                'phoneNum'=>$phoneNum,
                'desc'=>$desc,
                'updateTime'=>time()
                );
        }else{
            $data = array(
                'name' => $name,
                'address'  => $address,
                'contact'  => $contact,
                'mail' => $mail,
                'phoneNum' =>$phoneNum,
                'licenseNo' => $licenseNo,
                'licenseImgUrl' =>$licenseImgUrl,
                'idCardImgUrl' => $idCardImgUrl,
                'idCardNum' => $idCardNum,
                'desc' => $desc,
                'updateTime' =>time()
            );
            if(isset($res->wxName) && isset($res->wxName_shop) && isset($res->name)){
                $data['status'] = 4;
                $this->session->set_userdata(['status'=>4]);
            }else{
                $data['status'] = 0;
            }
        }
        $getres = $this->merchant_model->update_merchant($this->session->mchId,$data);
        /*--------update -日志信息--------by ccz*/
        if($getres){
            $this->output->set_content_type('application/json')->set_output(ajax_resp([]));
            try{
                $logInfo = (array)$data;
                $logInfo['info'] = '修改了企业信息';
                $logInfo['objInfo'] = '3';//更新企业信息
                $logInfo['id'] = $this->session->mchId;
                $logInfo['op'] = $this->log_record->Update;
                $this->log_record->addLog( $this->mchId,$logInfo,$this->log_record->User);
            }catch(Exception $e){
                log_message('error','mch_log_error:'.$e->getMessage());
            }
        }else{
            $this->output->set_content_type('application/json')->set_output(ajax_resp([],'企业信息保存失败',16006));
        }
        /*-------记录日志---------end */
        // $this->output
            // ->set_output(json_encode($result));

    }
    /**
     * weixin请求
     */
    public function wechat(){
        $res = $this->merchant_model->get_company_info($this->session->mchId);
        $data = (object)array();
        if($res->status == 1 && $res->wxAuthStatus == 1){
            $data->status = 0;
        }
        if($res->status == 1 && $res->wxAuthStatus == 0){
            $data->status = 1;
        }
        if($res->status == 0){
            $data->status = 0;
        }
        if($res){
            // $data = (object)array();
            $data->wxAuthStatus=$res->wxAuthStatus;
            $data->wxName=$res->wxName;
            $this->load->view('wechat',['res'=>$res,'data'=>$data]);
        }else{
            $this->load->view('wechat');
        }
    }
    /**
     * 获取weixin信息
     */
    public function get_wechat_info(){
        $res = $this->merchant_model->get_company_info($_SESSION['mchId']);
        $this->output->set_content_type('application/json')->set_output(ajax_resp([$res]));
    }
    /**
     * 更新消费者微信信息
     */
    public function update_wechat_info(){
        $postData=$this->input->post();
        $id = $this->session->mchId;
        //查询企业是否验证通过
        $res = $this->merchant_model->get_by_id($id);
        $wxRpTotalNum = $postData['wxRpTotalNum'];
        $pattern = "/^[3-9]$|^1[0-9]{1}$|^20$/";
        if (!preg_match($pattern,$wxRpTotalNum)){
            exit("非法操作");
        }
        if($res->status == 1){
            $data = [
                'subscribeMsg'=>$postData['subscribeMsg'],
                'subscribeImgUrl'=>$postData['subscribeImgUrl'],
                'wxSendName'=>$postData['wxSendName'],
                'wxActName'=>$postData['wxActName'],
                'wxRpTotalNum'=>$wxRpTotalNum,
                'wxWishing'=>$postData['wxWishing'],
                'wxRemark'=>$postData['wxRemark'],
                'updateTime'=>time(),
                'wxSendType'=>$postData['wxSendType'],
                'wxSendTip'=>$postData['wxSendTip'],
                'withCaptcha'=>$postData['withCaptcha'],
                'geoLocation'=>$postData['geoLocation']
            ];
        }else{
            $data = [
                'wxMchId'=>$postData['wxMchId'],
                'wxPayKey'=>$postData['wxPayKey'],
                'certPath'=>$postData['certPath'],
                'keyPath'=>$postData['keyPath'],
                'caPath'=>$postData['caPath'],
                'subscribeMsg'=>$postData['subscribeMsg'],
                'subscribeImgUrl'=>$postData['subscribeImgUrl'],
                'wxSendName'=>$postData['wxSendName'],
                'wxActName'=>$postData['wxActName'],
                'wxRpTotalNum'=>$wxRpTotalNum,
                'wxWishing'=>$postData['wxWishing'],
                'wxRemark'=>$postData['wxRemark'],
                'updateTime'=>time(),
                'wxSendType'=>$postData['wxSendType'],
                'wxSendTip'=>$postData['wxSendTip'],
                'withCaptcha'=>$postData['withCaptcha'],
                'geoLocation'=>$postData['geoLocation']
            ];
        }
        $res = $this->merchant_model->update_merchant($id,$data);
        if(!$res){
            $this->output->set_content_type('application/json')->set_output(ajax_resp([],'保存失败',16014));
        }else{
            /*--------update -日志信息--------by ccz*/
            try{
                $logInfo = (array)$data;
                $logInfo['info'] = '修改了企业消费者微信授权';
                $logInfo['objInfo'] = '1';//更新消费者信心
                $logInfo['id'] = $id;
                $logInfo['op'] = $this->log_record->Update;
                $this->log_record->addLog( $this->mchId,$logInfo,$this->log_record->User);
            }catch(Exception $e){
                log_message('error','mch_log_error:'.$e->getMessage());
            }
            /*-------记录日志---------end */
            // $this->output
            // ->set_output(json_encode($result));
            $this->output->set_content_type('application/json')->set_output(ajax_resp([]));
        }
    }
    /**
     * 更新供应链微信信息
     */
    public function update_weixin_info(){
        $result=(object)['errcode'=>0,'errmsg'=>''];
        $postData=$this->input->post();
        $id = $this->session->mchId;
        //查询企业是否验证通过
        $res = $this->merchant_model->get_by_id($id);
        $wxRpTotalNum_shop = $postData['wxRpTotalNum_shop'];
        $pattern = "/^[3-9]$|^1[0-9]{1}$|^20$/";
        if (!preg_match($pattern,$wxRpTotalNum_shop)){
            exit("非法操作");
        }
        if($res->status == 1){
            $data = [
                'subscribeMsg_shop'=>$postData['subscribeMsg_shop'],
                'subscribeImgUrl_shop'=>$postData['subscribeImgUrl_shop'],
                'wxSendName_shop'=>$postData['wxSendName_shop'],
                'wxActName_shop'=>$postData['wxActName_shop'],
                'wxRpTotalNum_shop'=>$wxRpTotalNum_shop,
                'wxWishing_shop'=>$postData['wxWishing_shop'],
                'wxRemark_shop'=>$postData['wxRemark_shop'],
                'updateTime'=>time()
            ];
        }else{
            $data = [
                'wxMchId_shop'=>$postData['wxMchId_shop'],
                'wxPayKey_shop'=>$postData['wxPayKey_shop'],
                'certPath_shop'=>$postData['certPath_shop'],
                'keyPath_shop'=>$postData['keyPath_shop'],
                'caPath_shop'=>$postData['caPath_shop'],
                'subscribeMsg_shop'=>$postData['subscribeMsg_shop'],
                'subscribeImgUrl_shop'=>$postData['subscribeImgUrl_shop'],
                'wxSendName_shop'=>$postData['wxSendName_shop'],
                'wxActName_shop'=>$postData['wxActName_shop'],
                'wxRpTotalNum_shop'=>$wxRpTotalNum_shop,
                'wxWishing_shop'=>$postData['wxWishing_shop'],
                'wxRemark_shop'=>$postData['wxRemark_shop'],
                'updateTime'=>time()
            ];
            if(isset($res->wxName) && isset($res->wxName_shop) && isset($res->name)){
                $data['status'] = 4;
                $this->session->set_userdata(['status'=>4]);
            }else{
                $data['status'] = 0;
            }
        }
        $res = $this->merchant_model->update_merchant($id,$data);
        if(!$res){
            $this->output->set_content_type('application/json')->set_output(ajax_resp([],'保存失败',16014));
        }else{
            /*--------update -日志信息--------by ccz*/
            try{
                $logInfo = (array)$data;
                $logInfo['info'] = '修改了企业供应链微信授权';
                $logInfo['objInfo'] = '2'; //更新供应链端信息
                $logInfo['id'] = $id;
                $logInfo['op'] = $this->log_record->Update;
                $this->log_record->addLog( $this->mchId,$logInfo,$this->log_record->User);
            }catch(Exception $e){
                log_message('error','mch_log_error:'.$e->getMessage());
            }
            /*-------记录日志---------end */
            $this->output->set_content_type('application/json')->set_output(ajax_resp([]));
        }
    }
    /**
     * 获取供应链微信菜单
     */
    public function weixin(){
        $mchId = $this->session->mchId;
        $res = $this->merchant_model->get_company_info($mchId);
        $this->load->view('weixin',['res'=>$res]);
    }
    /**
     * tts接口
     */
    public function tts() {
        $this->load->view('tts_apps');
    }
    /**
     * 请求tts appid appsecret
     */
    public function get_tts() {
        $mchId = $this->mchId;
        $res = $this->merchant_model->get_apps($mchId);
        if(isset($res)){
            $this->output->set_content_type('application/json')->set_output(ajax_resp(['appId'=>$res->appId,'appSecret'=>$res->appSecret]));
        }else{
            $this->output->set_content_type('application/json')->set_output(ajax_resp([],'请求失败',16000));
        }
    }
    /**
     * 腾讯云接口
     */
    public function tcloud() {
        $mchId = $this->session->mchId;
        $res = $this->merchant_model->get_tcloud($mchId);
        if(!isset($res)){
            $data = array(
                'secretId' => '',
                'secretKey' => '',
                'appId' => '',
                'isUse'=>0,
                'validLevel'=>0,
                'ignoreLevel'=>2,
                'mchId' => $mchId
            );
            $res = $this->db->insert('tencent_cloud',$data);
        }else{
            $data = $res;
        }
        $this->load->view('tcloud',['data'=>$data]);
    }
    /**
     * 请求腾讯云秘钥
     */
    public function get_tcloud() {
        $mchId = $this->mchId;
        $res = $this->merchant_model->get_tcloud($mchId);
        if(isset($res)){
            $mch = $this->merchant_model->get($mchId);
            $this->load->library('common/tencent_api',array('wxAppId'=>$mch->wxAppId));
            $result = $this->tencent_api->checkInterface($res);
            
            $this->output->set_content_type('application/json')->set_output(ajax_resp(['secretId'=>$res->secretId,'secretKey'=>$res->secretKey,'status'=>$result['status'],'message'=>$result['message']]));
        }else{
            $this->output->set_content_type('application/json')->set_output(ajax_resp([],'请求失败',16000));
        }
    }
    
    /**
     * 保存腾讯云秘钥
     */
    public function update_tcloud() {
        $mchId = $this->session->mchId;
        $data['secretId'] = $this->input->post('secretId');
        $data['secretKey'] = $this->input->post('secretKey');
        $data['validLevel'] = $this->input->post('validLevel');
        $data['ignoreLevel'] = $this->input->post('ignoreLevel');
        $data['isUse'] = $this->input->post('isUse');
        $data['updateTime'] = time();
        $res = $this->merchant_model->update_tcloud($mchId,$data);
        if($res){
            /*--------update -日志信息--------by ccz*/
            try{
                $logInfo = (array)$data;
                $logInfo['info'] = '修改腾讯云账号信息';
                $logInfo['id'] = $this->session->userId;
                $logInfo['objInfo'] = '0';//修改账户信息
                $logInfo['op'] = $this->log_record->Update;
                $this->log_record->addLog( $mchId,$logInfo,$this->log_record->User);
            }catch(Exception $e){
                log_message('error','mch_log_error:'.$e->getMessage());
            }
            /*-------记录日志---------end */
            $this->output->set_content_type('application/json')->set_output(ajax_resp([]));
        }
    }
    /**
     * 获取供应链微信菜单
     */

    /**
     * 更新消费者微信菜单
     */
    public function update_weixin(){

    }


    // 等待审核状态
    public function reviewing(){

        $this->load->view('review');
    }
     /**
     * 验证密码
     */
    public function verpass(){

    }
    public function help_read(){
        $this->load->view('help_read');
    }
    public function gongzhong(){
        $this->load->view('help_gz');
    }
    public function renzheng(){
        $this->load->view('help_rz');
    }
    public function zhifu(){
        $this->load->view('help_zf');
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
    /**
     * 退出
     */
    public function logout(){
        // session_destroy();
        $this->session->sess_destroy();
        redirect('/user/login');
    }
    function md5(){
        $str=$this->input->get_post('str');
        $salt=$this->input->get_post('salt');
        echo md5($str.$salt);
    }


    /**
     * ----------------------------------------------------------
     * 使用授权的access_token登录系统报表后台(POST)
     *
     * @param string access_token
     * @return void
     */
    public function token_auth($access_token = NULL) {
        try {
            if (! isset($access_token)) {
                throw new Exception("缺少参数 access_token", 1);
            }
            $this->load->model('Auth_model', 'auth');
            $merchant = $this->auth->getMerchant($access_token);
            $session_data = [
                'userId'   => -1, // 报表查看人员在mch_accounts表中没有记录，为虚拟用户
                'status'   => 1, // 企业状态标识为已审核
                'mchId'    => $merchant->id,
                'username' => '报表查看人员',
                'role'     => -1,
                'part'     => 0 // 未知含义
            ];
            $this->session->set_userdata($session_data);
            redirect('/charts/scan');
        } catch (Exception $e) {
            redirect('/user/login');
        }
    }

    /**
     * ----------------------------------------------------------
     * 企业账户升级为企业号
     *
     * @return json
     */
    public function upgrade_account() {
        $this->load->model('Account_model', 'account');
        $accountId = $this->session->userdata('userId');
        try {
            $this->account->upgradeAccount($accountId, $this->mchId);
            $this->output->set_content_type('application/json')->set_output(ajax_resp([]));
        } catch (Exception $e) {
            $this->output->set_content_type('application/json')->set_output(ajax_resp([], $e->getMessage(), $e->getCode()));
        }
    }

    /**
     * ----------------------------------------------------------
     * 商户授权给企业号
     *
     * @return json
     */
    public function authorize_account() {
        $this->load->model('Account_model', 'account');
        $mobile     = $this->input->post('mobile');
        $mchId      = $this->mchId;
        $role       = $this->session->userdata('role');
        $smsCode    = $this->input->post('sms_code');
        try {
            $this->account->authorizeAccount($mobile, $mchId, $role, $smsCode);
            $this->output->set_content_type('application/json')->set_output(ajax_resp([]));
        } catch (Exception $e) {
            $this->output->set_content_type('application/json')->set_output(ajax_resp([], $e->getMessage(), $e->getCode()));
        }
    }
}
