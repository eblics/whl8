<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * User
 */
class User extends Mobile_Controller {

    public function __construct() {
        // 初始化，加载必要的组件
        parent::__construct();
        $this->load->library('common/common_lib');
        $this->load->model('user_model');
        $this->load->model('merchant_model');
        $this->load->model('scan_log_model');
        $this->load->library('common/ipwall');
        $this->load->library('common/common_login');
    }

    // --------------------------------------
    // path /user
    // 用户中心界面
    public function index() {
        $mchId = $this->input->get('mch_id');
        if (! isset($mchId)) {
            $this->load->view('error', ['errmsg' => '没有这个商户']);
        }
        $this->getCommonUser();

        $this->load->model('Group_model', 'group');
        $setting = $this->group->get_group_setting($mchId);
        if (isset($setting)) {
            $groupProductName = $setting->productName;
        } else {
            $groupProductName = '好友圈';
        }
        $currentUser = $this->getCurrentUser($mchId);
        if ($currentUser->subscribe === '0' || $currentUser->subscribe === 0) {
            // session_destroy();
            // $this->load->view('error', ['errmsg' => '关注公众号之后才能进入个人中心']);
        }

        $this->load->model('Personalinfo_model', 'personalinfo');
        $personalInfo = $this->personalinfo->getPersonalInfo($currentUser->id);
        if (isset($personalInfo) && $personalInfo->provincename != '') {
            $cityinfo = $personalInfo->provincename . '-' . $personalInfo->cityname;
        } else {
            $currentUser->country = isset($currentUser->country) ? $currentUser->country : '未知';
            $currentUser->province = isset($currentUser->province) ? $currentUser->province : '未知';
            $currentUser->city = isset($currentUser->city) ? $currentUser->city : '未知';
            $cityinfo = $currentUser->country . '-' . $currentUser->province . '-' . $currentUser->city;
        }
        $mallId = $this->personalinfo->mchid_to_mallid($mchId);
        $viewData = [
            'user_id'   => $currentUser->id,
            'image'     => $currentUser->headimgurl,
            'name'      => $currentUser->nickName,
            'cityinfo'  => $cityinfo,
            'mchid'     => $mchId,
            'mallid'    => $mallId,
            'groupProductName' => $groupProductName,
        ];
        $this->load->view('user_index', $viewData);
    }

    // --------------------------------------
    // path /user/profile
    // 用户个人信息界面
    public function profile() {
        $mchId = $this->input->get('mch_id');
         if (! isset($mchId)) {
            $this->load->view('error', ['errmsg' => '没有这个商户']);
        }
        $currentUser = $this->getCurrentUser($mchId);
        $this->load->model('Personalinfo_model', 'personalinfo');
        $personlInfo = $this->personalinfo->getPersonalInfo($currentUser->id);
        
        if ($personlInfo->provincename != '') {
            $cityinfo = $personlInfo->provincename . '-' . $personlInfo->cityname;
        } else {
            $cityinfo = '';
        }
        
        $areas = $this->personalinfo->areas();
        $viewData = [
            'mobile'    => $personlInfo->mobile,
            'birthday'  => $personlInfo->birthday === '0000-00-00' ? '' : $personlInfo->birthday,
            'cityinfo'  => $cityinfo,
            'realname'  => $personlInfo->realname,
            'address'   => $personlInfo->address,
            'provincecode'  => $personlInfo->provincecode,
            'citycode'  => $personlInfo->citycode,
            'areas'     => $areas,
            'mchid'     => $mchId
        ];
        $this->load->view('user_profile', $viewData);
    }

    /**
     * @deprecated 此方法已废弃，请使用/user/red_packet?mch_id={$mchId}
     */
    public function account($mchId = 0) {
        redirect('/user/red_packet?mch_id='. $mchId);
    }

    // --------------------------------------
    // path /user/red_packet
    // 用户红包账户界面
    public function red_packet() {
        $commonUser = $this->getCommonUser();
        $mch_id = $this->input->get('mch_id');
        if (! isset($mch_id)) {
            $this->load->view('error', ['errmsg' => '商户不存在']);
        }
        $currentUser = $this->getCurrentUser($mch_id);

        // users_common_sub add
        $sql = "select * from users_common_sub where parentId = ? and mchId = ?";
        $subUser = $this->db->query($sql, [$commonUser->id, $currentUser->mchId])->row();
        if (! isset($subUser)) {
            $sql = "insert into users_common_sub (parentId, userId, openid, mchId, status) values (?, ?, ?, ?, 0)";
            $this->db->query($sql, [$commonUser->id, $currentUser->id, $currentUser->openid, $currentUser->mchId]);
        }

        $this->common_lib->send_pending_packtets($currentUser);
        $normalAmount=$this->user_model->get_amount($currentUser->id,$mch_id,0);
        $groupAmount=$this->user_model->get_amount($currentUser->id,$mch_id,1);
        $merchant = $this->merchant_model->get($mch_id);
        $data=[
            'mchId'=>$mch_id,
            'wxAccountName' => $merchant->wxName,
            'nickname' => $currentUser->nickName,
            'headImg' => $currentUser->headimgurl,
            'wxRpTotalNum'=>($merchant->wxRpTotalNum?$merchant->wxRpTotalNum:3),
            'normalAmount'=>0,
            'groupAmount'=>0,
            'withdrawLimit' => $merchant->withdrawLimit
        ];
        if (mb_strlen($data['nickname']) > 12) {
            $data['nickname'] = mb_substr($data['nickname'], 0, 11, 'utf-8') . '...';
        }
        if($normalAmount){
            $data['normalAmount']=$normalAmount->amount;
        }
        if($groupAmount){
            $data['groupAmount']=$groupAmount->amount;
        }
        $data['wxSendTip']=$merchant->wxSendTip;
        $this->load->view('account',$data);
    }

    // --------------------------------------
    // path /user/cards
    // 用户乐券账户界面
    public function cards() {
        $this->getCommonUser();
        $mch_id = $this->input->get('mch_id');
        if (! isset($mch_id)) {
            $this->load->view('error', ['errmsg' => '商户不存在']);
        }
        $currentUser = $this->getCurrentUser($mch_id);
        $this->load->model('Prize_model', 'prize');
        $cards = $this->prize->getCards($currentUser->openid, ROLE_CONSUMER);
        $groupBonusCards = $this->prize->getGroupBonusCards($currentUser->openid, ROLE_CONSUMER);
        $allowTransferCards = [];
        $allowTransferNum = 0;
        foreach ($cards as $card) {
            if ($card->allowTransfer) {
                $allowTransferNum += 1;
                $allowTransferCards[] = $card;
            }
        }

        $singleCards = [];
        $singleCardsNum = 0;
        foreach ($cards as $card) {
            if ($card->allowTransfer && $card->cardType == 0) {
                continue;
            }
            $singleCardsNum += 1;
            $singleCards[] = $card;
        }
        
        $data['cards'] = $cards;
        $data['singleCards'] = $singleCards;
        $data['singleCardsNum'] = $singleCardsNum;
        $data['allowTransferCards'] = $allowTransferCards;
        $data['groupBonusCards'] = $groupBonusCards;
        $data['allowTransferNum'] = $allowTransferNum;
        $data['role'] = ROLE_CONSUMER;
        $data['role_str']   = 'consumer';
        $data['userinfo'] = $currentUser;
        $data['title'] = '我的乐券';
        $this->load->model('Transfer_model', 'transfer');
        $data['transfers'] = $this->transfer->get_trans_log($currentUser->id, ROLE_CONSUMER);
        $data['mch_id'] = $mch_id;
        $this->load->view('card_account', $data);
    }

    /**
     * @deprecated Please use /user/points
     */
    public function point($mchId = NULL) {
        if (! isset($mchId)) {
            redirect('/user/points');
        } else {
            redirect('/user/points?mch_id=' . $mchId);
        }
    }

    // -----------------------------------------
    // path /user/points
    // 用户积分界面
    public function points() {
        $this->getCommonUser();
        $mch_id = $this->input->get('mch_id');
        if (! isset($mch_id)) {
            $this->load->view('error', ['errmsg'=>'商户不存在']);
        }
        $currentUser = $this->getCurrentUser($mch_id);
        //检查并发放未发放的积分
        $checkResult = $this->user_model->checkAndSend(3, $currentUser);
        if ($checkResult) {
            echo "<meta http-equiv='refresh' content='0'/>";
            return;
        }
        // 获取积分总数和积分获取使用记录（默认展示6条）
        $logs = $this->user_model->getPointLogs($currentUser->id, 0, $mch_id, 0, 6);
        $points = $this->user_model->getTotalPoints($currentUser->id, 0);
        $data['title'] = '我的积分';
        $data['v']     = time();
        $data['mch_id'] = $mch_id;
        $data['userinfo'] = $currentUser;
        $data['logs'] = $logs;
        $data['points'] = $points;
        $this->load->view('point_account', $data);
    }

    /**
     * 用户提现操作
     */
    public function withdraw($mch_id){
        header('Content-type','application/json;charset=utf-8;');
        $commonUser = $this->getCommonUser();
        $currentUser = $this->getCurrentUser($mch_id);
        if($this->ipwall->is_prevent()){
            $this->common_login->save_user_log(1,'用户提现请求过于频繁');
            return;
        }
        
        $amount=$this->input->post('amount');
        $amount=bcmul($amount,100);
        $moneyType=$this->input->post('moneyType');
        
        $merchant=$this->merchant_model->get($mch_id);

        if(bccomp($amount,$merchant->withdrawLimit)==-1){
            $result=['errorCode'=>1,'errorMsg'=>'满' . $merchant->withdrawLimit * 0.01 . '元才能提取'. $amount];
            echo json_encode($result);
            return;
        }
        $amountLog=$this->user_model->get_amount($currentUser->id,$mch_id,$moneyType);
        if(! $amountLog){
            $result=['errorCode'=>1,'errorMsg'=>'请求失败，请重试'];
            echo json_encode($result);
            return;
        }
        $relAmount=$amountLog->amount;
        if(bccomp($amount,$relAmount)==1){
            $result=['errorCode'=>1,'errorMsg'=>'账户余额不足'];
            log_message('error',"user:$currentUser->id relAmount: $relAmount amount: $amount");
            echo json_encode($result);
            return;
        }
        if(bccomp($amount,20000)==1 && $moneyType==0){
            $result=['errorCode'=>1,'errorMsg'=>'普通红包，单次提现不得超过200元'];
            echo json_encode($result);
            return;
        }
        if(bccomp($amount,bcmul(20000,$merchant->wxRpTotalNum))==1 && $moneyType==1){
            $maxGroup=bcmul(200,$merchant->wxRpTotalNum);
            $result=['errorCode'=>1,'errorMsg'=>'裂变红包，单次提现不得超过'.$maxGroup.'元'];
            echo json_encode($result);
            return;
        }
        if(bccomp($amount,bcmul(100,$merchant->wxRpTotalNum))==-1 && $moneyType==1){
            $result=['errorCode'=>1,'errorMsg'=>'裂变红包，单次提现不得低于'.$merchant->wxRpTotalNum.'元'];
            echo json_encode($result);
            return;
        }
        if($merchant->wxSendType==0 || $moneyType==1){
            $withdraw=$this->user_model->withdraw($currentUser->id,$mch_id,$moneyType,$amount,0,$merchant->payAccountType);
        }else if($merchant->wxSendType==1 && $moneyType==0){
            $withdraw=$this->user_model->withdraw($currentUser->id,$mch_id,$moneyType,$amount,1,$merchant->payAccountType);
        }
        if($withdraw->errcode!=0){
            $result=['errorCode'=>1,'errorMsg'=>$withdraw->errmsg,'wxmsg'=>$withdraw->notes];
            log_message('ERROR','红包详情：'.var_export(['userId'=>$currentUser->id,'mchId'=>$mch_id,'moneyType'=>$moneyType,'amount'=>$amount],TRUE));
            echo json_encode($result);
            return;
        }
        // 提现成功以前不计算报表数据  mod by zht
        //$this->trigger_model->trigger_trans((object)['mchId'=>$currentUser->mchId,'userId'=>$currentUser->id],$amount);
        $result=['errorCode'=>0,'errorMsg'=>'提取成功','payAccountType'=>$merchant->payAccountType,'commonSubscribe'=>''];
        echo json_encode($result);
    }



    /**
     * 扫码记录ajax数据
     */
    public function scan_data($mch_id,$rows=10) {
        if(! $this->common_login->is_login()) return;//common_login login
        $this->load->model('scan_log_model');
        header('Content-type','application/json;charset=utf-8;');
        if(!isset($mch_id)) exit;
        $merchant=$this->merchant_model->get($mch_id);
        if(!isset($merchant)){
            $result=['errorCode'=>1,'errorMsg'=>'没有这个商户'];
            echo json_encode($result);
            return;
        }
        if(!$this->session->has_userdata('openid_'.$merchant->id)){
            $result=['errorCode'=>1,'errorMsg'=>'未登录'];
            echo json_encode($result);
            return;
        }
        $openid=$this->session->userdata('openid_'.$merchant->id);
        $userInfo=$this->user_model->get_by_openid($openid);
        if(!isset($userInfo)){
            $result=['errorCode'=>1,'errorMsg'=>'用户信息不存在'];
            echo json_encode($result);
            return;
        }
        if($userInfo->mchId!=$merchant->id){
            $result=['errorCode'=>1,'errorMsg'=>'错误的用户信息'];
            echo json_encode($result);
            return;
        }
        $scanData=$this->scan_log_model->get_some_by_user_id($userInfo->id,$mch_id,$rows);
        foreach($scanData as $k=>$v){
            $scanData[$k]->getTime=date('Y-m-d H:i:s',$v->getTime);
        }
        $result=['errorCode'=>0,'errorMsg'=>'','data'=>$scanData];
        echo json_encode($result);
        return;
    }

    /**
     * 用户红包余额ajax数据
     */
    public function rp_data($mch_id) {
        if(! $this->common_login->is_login()) return;//common_login login
        $this->load->model('user_redpacket_model');
        header('Content-type','application/json;charset=utf-8;');
        if(!isset($mch_id)) exit;
        $merchant=$this->merchant_model->get($mch_id);
        if(!isset($merchant)){
            $result=['errorCode'=>1,'errorMsg'=>'没有这个商户'];
            echo json_encode($result);
            return;
        }
        if(!$this->session->has_userdata('openid_'.$merchant->id)){
            $result=['errorCode'=>1,'errorMsg'=>'未登录'];
            echo json_encode($result);
            return;
        }
        $openid=$this->session->userdata('openid_'.$merchant->id);
        $userInfo=$this->user_model->get_by_openid($openid);
        if(!isset($userInfo)){
            $result=['errorCode'=>1,'errorMsg'=>'用户信息不存在'];
            echo json_encode($result);
            return;
        }
        if($userInfo->mchId!=$merchant->id){
            $result=['errorCode'=>1,'errorMsg'=>'错误的用户信息'];
            echo json_encode($result);
            return;
        }
        $allRp=$this->user_redpacket_model->get_history_by_user_id($userInfo->id,$mch_id);
        $remainRpNormal=$this->user_redpacket_model->get_remain_rp($userInfo->id,$mch_id,0);
        $remainRpGroup=$this->user_redpacket_model->get_remain_rp($userInfo->id,$mch_id,1);
        $data=['allRp'=>$allRp?$allRp->amount:0,
        'remainRpNormal'=>$remainRpNormal?$remainRpNormal->amount:0,
        'remainRpGroup'=>$remainRpGroup?$remainRpGroup->amount:0];
        $result=['errorCode'=>0,'errorMsg'=>'','data'=>$data];
        echo json_encode($result);
        return;
    }


    public function red_packet_logs($mch_id) {
        if (!isset($mch_id)) {
            $this->load->view('error', ['errmsg'=>'商户不存在']);
            return;
        }
        $data['title'] = '红包明细';
        $data['v']     = time();
        $data['mch_id'] = $mch_id;
        $this->load->view('red_packet_logs', $data);
    }

    // -----------------------------------------
    // Added by shizq
    // 获取红包的领取或体现记录
    public function fetch_red_packet_logs($mch_id) {
        $this->getCommonUser();
        // copy from account function - begin
        $merchant=$this->merchant_model->get($mch_id);
        if(!isset($merchant)){
            $this->load->view('error',['errmsg'=>'没有这个商户']);
            return;
        }
        $userInfo = $this->getCurrentUser($mch_id);
        // copy from account function - end

        $page = $this->input->get('page');
        $pageSize = $this->input->get('page_size');
        if (!isset($page)) {
            $page = 0;
        }
        if (!isset($pageSize)) {
            $pageSize = 10;
        }

        $resp = $this->user_model->getRedpacketLogs($userInfo->id, intval($page), intval($pageSize));
        $this->output->set_content_type('application/json')->set_output(ajax_resp($resp));
    }

    // -----------------------------------------
    // Added by shizq
    // 用户积分详细列表界面
    public function point_logs($mch_id) {
        if (!isset($mch_id)) {
            $this->load->view('error', ['errmsg'=>'商户不存在']);
            return;
        }
        $data['title'] = '积分明细';
        $data['v']     = time();
        $data['mch_id'] = $mch_id;
        $this->load->view('point_logs', $data);
    }

    // -----------------------------------------
    // Added by shizq
    // 获取积分获取和使用记录
    public function fetch_point_logs() {
        $currentUser = $this->getCurrentUser($this->getCurrentMchId());
        $page = $this->input->get('page');
        $pageSize = $this->input->get('page_size');
        if (!isset($page)) {
            $page = 0;
        }
        if (!isset($pageSize)) {
            $pageSize = 10;
        }

        $logs = $this->user_model->getPointLogs($currentUser->id, 0, $currentUser->mchId, intval($page), intval($pageSize));
        $this->output->set_content_type('application/json')->set_output(ajax_resp($logs));
    }

    // -----------------------------------------
    // 用户兑换积分为现金
    public function exchange_point($mch_id) {
        if ($mch_id == 0 || $mch_id == 173) {
            $currentUser = $this->getCurrentUser($mch_id);
            try {
                $this->user_model->exchangePoint($currentUser);
                $this->output->set_content_type('application/json')->set_output(ajax_resp([]));
            } catch (Exception $e) {
                $this->output->set_content_type('application/json')->set_output(ajax_resp([], $e->getMessage(), $e->getCode()));
            }
        } else {
            $this->output->set_content_type('application/json')->set_output(ajax_resp([], '拒绝访问', 1));
        }
    }

    // -----------------------------------------
    // 获取某个活动的总扫码次数，当前扫码用户的扫码次数
    // path /user/scan_times
    // method get
    public function scan_times($mchId = NULL) {
        if (! isset($mchId)) {
            $mchId = $this->getCurrentMchId();
        }
        if (! isset($mchId)) {
            $this->output->set_content_type('application/json')->set_output(ajax_resp([], '参数错误', 1));
            return;
        }
        $currentUser = $this->getCurrentUser($mch_id);
        $wxOpenId = $currentUser->openid;
        try {
            $respData = $this->user_model->getScanTimes($wxOpenId);
            $this->output->set_content_type('application/json')->set_output(ajax_resp($respData, NULL, 0));
        } catch (Exception $e) {
            $this->output->set_content_type('application/json')->set_output(ajax_resp(NULL, $e->getMessage(), $e->getCode()));
        }
    }

    // -----------------------------------------
    // 获取短信验证码
    // path /user/get_sms_code
    // method get
    // params mobile string
    public function get_sms_code() {
        $mobile = $this->input->get('mobile');
        $mchId = $this->input->get('mch_id');
        info("get_sms_code mch_id is: ". $mchId);
        $wxOpenId = $this->session->userdata('openid_' . $mchId);
        if (! isset($wxOpenId)) {
            $this->output->set_status_header(401)->set_content_type('application/json')->set_output(ajax_resp([], '用户身份验证失败，拒绝访问', 2));
            return;
        }
        if (! isset($mobile)) {
            $this->output->set_content_type('application/json')->set_output(ajax_resp(NULL, '请输入手机号', 1));
            return;
        }
        if (! preg_match("/^1[34578]{1}\d{9}$/", $mobile)) {
            $this->output->set_content_type('application/json')->set_output(ajax_resp(NULL, '请输入有效的手机号', 1));
            return;
        }
        $template_id = 'SMS_7895086';
        $code = mt_rand(1000, 9999);
        $signame = '红码';
        $this->load->library('sms_vcode');
        info("send sms_code - begin");
        info("params: ". json_encode([$mobile, $code, $template_id, $signame]));
        $sendOk = $this->sms_vcode->send_sms_vcode($mobile, $code, $template_id, $signame);
        if ($sendOk) {
            $this->output->set_content_type('application/json')->set_output(ajax_resp(NULL, NULL, 0));
        } else {
            $this->output->set_content_type('application/json')->set_output(ajax_resp(NULL, '验证码发送失败，请重试', 1));
        }
        info("send sms_code - end");
    }

    // -----------------------------------------
    // 校验短信验证码是否正确
    // path /user/validate_sms_code
    // method post
    // params sms_code string
    public function validate_sms_code() {
        $mobile = $this->input->post('mobile');
        $smsCode = $this->input->post('sms_code');
        $mchId = $this->input->post('mch_id');
        info("validate_sms_code params: ". json_encode([$mobile, $smsCode, $mchId]));
        $wxOpenId = $this->session->userdata('openid_' . $mchId);
        if (! isset($wxOpenId)) {
            $this->output->set_status_header(401)->set_content_type('application/json')->set_output(ajax_resp([], '用户身份验证失败，拒绝访问', 200200));
            return;
        }
        if (empty($smsCode) || empty($mobile)) {
            $this->output->set_content_type('application/json')->set_output(ajax_resp(NULL, '短信验证码不正确', 1));
            return;
        }
        $this->load->library('sms_vcode');
        $passObj = $this->sms_vcode->proof_vcode($mobile, $smsCode);
        if ($passObj['statusCode']) {
            $this->output->set_content_type('application/json')->set_output(ajax_resp(NULL, '短信验证码不正确', 2));
        } else {
            $this->output->set_content_type('application/json')->set_output(ajax_resp(['status' => 0], NULL, 0));
        }
    }

    public function api($apiName = NULL) {
        if (! isset($apiName)) {
            $this->ajaxResponseOver('403 Forbbiden.', 403, 403);
        } else if ($apiName === 'user.current') {
            $currentUser = $this->getCurrentUser($this->getCurrentMchId());
            $this->load->model('Merchant_model', 'merchant');
            $merchant = $this->merchant->get($currentUser->mchId);
            $respData = [
                'nickname' => $currentUser->nickName, 
                'mch_id' => $currentUser->mchId,
                'qrcode_url' => $merchant->wxQrcodeUrl,
            ];
            $this->ajaxResponseSuccess($respData);
        } else if ($apiName === 'user.update') {
            $this->updateMemberProfile();
        } else {
            $this->ajaxResponseOver('404 Not Found.', 404, 404);
        }
    }
    
    /**
     * 更新用户个人信息
     * /user/api/user.update
     * @return [type] [description]
     */
    private function updateMemberProfile() {
        $this->getCommonUser();
        $currentUser = $this->getCurrentUser($this->getCurrentMchId());
        $mch_id     = $this->input->get_post('mchid');
        $mobile     = $this->input->get_post('mobile');
        $birthday   = $this->input->get_post('birthday');
        $city       = $this->input->get_post('city');
        $realname   = $this->input->get_post('realname');
        $address    = $this->input->get_post('address');
        
        $data = [];
        if($mobile!=null)
            $data['mobile']=$mobile;
        if($birthday!=null)
            $data['birthday']=$birthday;
        if($city!=null)
            $data['areaCode']=$city;
        if($realname!=null)
            $data['realname']=$realname;
        if($address!=null)
            $data['address']=$address;

        $this->load->model('Personalinfo_model', 'personalinfo');
        $this->personalinfo->updateinfo($mch_id, $currentUser->openid, $data);
        $this->load->library('common/Common_lib');
        $result=(object)['errcode'=>0,'errmsg'=>''];
        $this->ajaxResponseSuccess();
    }

}
