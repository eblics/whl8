<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Merchant_model extends CI_Model {
    public function __construct()
    {
        // Call the CI_Model constructor
        parent::__construct();
    }

    function get($id){
        $merchant = $this->db->where('id',$id)->get('merchants')->row();
        $merchantExt = $this->db->where('mchId', $id)->get('merchants_ext')->row();
        if (isset($merchant)) {
            if (isset($merchantExt)) {
                $merchant->withdrawLimit = $merchantExt->withdrawLimit;
            } else {
                $merchant->withdrawLimit = 100;
            }
        }
        return $merchant;
    }

    function get_all() {
        return $this->db->get('merchants')->result();
    }

    function get_by_wxid($wxid){
        return $this->db->where('wxYsId',$wxid)->get('merchants')->row();
    }

    function get_by_code($mch_code){
        return $this->db->where('code',$mch_code)->get('merchants')->row();
    }

    function get_activity($corpCode,$serialCode){
        return (object)['errcode'=>0,'errmsg'=>'ok'];
    }

    /**
     * 通过appid获取商户信息
     * @appId 商户appid
     */
    function get_by_appid($appId){
        return $this->db->where('wxAppId',$appId)->get('merchants')->row();
    }
    /**
     * 通过appid获取商户信息
     * @appId 商户appid
     */
    function get_by_appid_shop($appId){
        return $this->db->where('wxAppId_shop',$appId)->get('merchants')->row();
    }

    /**
     * 更新商户jsapiTicket
     * @mchId 商户appid
     * @jsapiTicket jsapiTicket值
     * @time jsapiTicket过期时间
     */
    public function update_jsapi_ticket($appid,$jsapiTicket,$time){
        $data=[
            'jsapiTicket'=>$jsapiTicket,
            'jsapiTicketTime'=>$time
        ];
        $where="wxAppId=".$appid;
        return $this->db->where('wxAppId',$appid)->update('merchants',$data);
    }
    /**
     * 账号服务时间过期提醒
     */
    public function get_remind($mchid){
        return $this->db->where('id',$mchid)->get('merchants')->row();
    }
    /**
     * 安全验证块插入session id
     */
    public function set_session_id($phoneNum,$sessionId){
        $cExpired = time();
        return $this->db->where('phoneNum',$phoneNum)->update('mch_accounts',['sessionId'=>$sessionId,'cExpired'=>$cExpired]);
    }
    /**
     * 找回密码获取验证码
     */
    public function send_mes($phonenum,$tid){
        $res = $this->db->where('phoneNum',$phonenum)->get('mch_accounts')->row();
        if(isset($res)){
            $this->load->library('common/send_sms');
            $result=$this->send_sms->get_validcode($phonenum,$tid);
            if($result['statusCode']==0){
                return ['success'=>1];
            }
            return ['success'=>0,'message'=>$result['message']];
        }
        return ['success'=>0,'message'=>'该手机号不存在'];
    }
    /**
     * 获取验证码
     * @phonenum 手机号码
     * @
     * @
     */
    public function get_validcode($phonenum,$tid){
        $ct = $this->db->where('phoneNum',$phonenum)->get('mch_accounts')->row();
        if(!isset($ct)){
            $this->load->library('common/send_sms');
            $result=$this->send_sms->get_validcode($phonenum,$tid);
            if($result['statusCode']==0){
                return ['success'=>1];
            }
            return ['success'=>0,'message'=>$result['message']];
        }
        return ['success'=>0,'message'=>'该手机号已经被注册'];
    }
    /**
     * 阿里大鱼获取验证码
     */
    public function send_sms_vcode($phone, $code, $template_id,$signame){
        $ct = $this->db->where('phoneNum',$phone)->get('mch_accounts')->row();
        if(!isset($ct)){
            $this->load->library('sms_vcode');
            $res = $this->sms_vcode->send_sms_vcode($phone, $code, $template_id,$signame);
            if($res){
                return ['success'=>1];
            }
            return ['success'=>0,'message'=>'短信发送失败'];
        }
        return ['success'=>0,'message'=>'该手机号已经被注册'];
    }
    /**
     * 阿里大鱼找回密码/登录获取短信验证码
     */
    public function find_pass($phone, $code, $template_id,$signame){
        $ct = $this->db->where('phoneNum',$phone)->get('mch_accounts')->row();
        if(isset($ct)){
            $this->load->library('sms_vcode');
            $res = $this->sms_vcode->send_sms_vcode($phone, $code, $template_id,$signame);
            if($res){
                return ['success'=>1];
            }
            return ['success'=>0,'message'=>'短信发送失败'];
        }
        return ['success'=>0,'message'=>'该手机号不存在'];
    }
    /**
     * 校对验证码
     */
    public function validate_validcode($phonenum,$validcode){
        $row = $this->db->where('phoneNum',$phonenum)->get('mch_accounts')->row();
        $cExpired = time();
        if($row->noSms == 1 && $row->mchId == 0){
            $this->db->where('phoneNum',$phonenum)->update('mch_accounts',['cStatus'=>1,'cExpired'=>$cExpired]);
            return ['success'=>1];
        }else{
            $this->load->library('common/send_sms');
            if($this->send_sms->check_validcode($phonenum,$validcode)['statusCode']!=0){
                return ['success'=>0,'message'=>'手机对应的验证码不正确'];
            }else{
                $this->db->where('phoneNum',$phonenum)->update('mch_accounts',['cStatus'=>1,'cExpired'=>$cExpired]);
                return ['success'=>1];
            }
        }
    }
    /**
     * 短信 阿里大鱼 校对验证码proof_dayu_sms_verification
     */
    public function proof_dayu_vcode($phonenum,$validcode){
        $row = $this->db->where('phoneNum',$phonenum)->get('mch_accounts')->row();
        $cExpired = time();
        //免密登录
        if(!empty($row)&&$row->noSms == 1 && $row->mchId == 0){
            $this->db->where('phoneNum',$phonenum)->update('mch_accounts',['cStatus'=>1,'cExpired'=>$cExpired]);
            return ['success'=>1];
        }else{
            $this->load->library('sms_vcode');
            $res = $this->sms_vcode->proof_vcode($phonenum,$validcode);
            if($res['statusCode'] != 0){
                return ['success'=>0,'message'=>'手机对应的验证码不正确'];
            }else{
                $this->db->where('phoneNum',$phonenum)->update('mch_accounts',['cStatus'=>1,'cExpired'=>$cExpired]);
                return ['success'=>1];
            }
        }
    }
    /**
     * 更新cStatus状态 （找回密码专用）
     */
    public function up_cstatus($phoneNum,$status){
        $this->db->where('phoneNum',$phoneNum)->update('mch_accounts',['cStatus'=>$status]);
    }
    /**
     * 
     */
    public function get_status($phoneNum){
        return $row = $this->db->where('phoneNum',$phoneNum)->get('mch_accounts')->row();
    }
    /**
     * 更新商户baseToken
     * @mchId 商户appid
     * @baseToken baseToken值
     * @time baseToken过期时间
     */
    public function update_base_token($appid,$baseToken,$time){
        $data=[
            'baseToken'=>$baseToken,
            'baseTokenTime'=>$time
        ];
        return $this->db->where('wxAppId',$appid)->update('merchants',$data);
    }
    /**
     * 更新商户 供应链baseToken
     * @mchId 商户appid
     * @baseToken baseToken值
     * @time baseToken过期时间
     */
    public function update_base_token_shop($appid,$baseToken,$time){
        $data=[
            'baseToken_shop'=>$baseToken,
            'baseTokenTime_shop'=>$time
        ];
        return $this->db->where('wxAppId_shop',$appid)->update('merchants',$data);
    }
    /**
     * 校对用户的图片验证码
     * @word 获得的验证字符
     * @ip_address 用户ip
     * @expiration 到期时间
     */
    public function verify_verify($word,$ip_address,$expiration){
        $array = array('word' => $word,'ip_address' => $ip_address,'expiration' => $expiration);
        $sql = 'SELECT COUNT(*) AS count FROM captcha WHERE word = ? AND ip_address = ? AND captcha_time > ?';
        $binds = array($word, $ip_address, $expiration);
        $query = $this->db->query($sql, $binds);
        return $query->row();
    }
    //用户注册
    function reg_mchuser($userinfo){
        $this->db->where('phoneNum', $userinfo['phoneNum'])->where('status', 3)->delete('mch_accounts');
        $this->db->insert('mch_accounts',$userinfo);
        return $this->db->insert_id();
    }
    //验证企业用户手机登录
    function get_by_phone($phone){
        return $this->db->where('phoneNum',$phone)->where('status !=', 3)->get('mch_accounts')->row();
    }
    //获取帐号信息
    function get_person_info($userid){
        return $this->db->where('id',$userid)->get('mch_accounts')->row();
    }
    //更新帐号信息
    function update_person_info($id,$data){
        return $this->db->where('id',$id)->update('mch_accounts',$data);
    }
    //通过手机号更新帐号信息
    function update_pass($phoneNum,$data){
        return $this->db->where('phoneNum',$phoneNum)->update('mch_accounts',$data);
    }
    //获取企业信息
    function get_company_info($mchid){
        return $this->db->where('id',$mchid)->get('merchants')->row();
    }
    //查找企业用户
    function getuser_by_id($id){
        return $this->db->where('id',$id)->get('mch_accounts')->row();
    }
    //查找企业信息
    function get_by_id($id){
        return $this->db->where('id',$id)->get('merchants')->row();
    }
    //企业信息插入
    function insert_merchant($info){
        $this->db->insert('merchants', $info);
        return $this->db->insert_id();
    }
    //企业信息更新
    function update_merchant($mchId,$data){
        return $this->db->where('id',$mchId)->update('merchants',$data);
    }
    //获取企业mchId
    function get_mchid(){
        // return $this->db->where('id',$id)->get('')
    }
    //更新mch_accounts mchId字段(更新企业mchId)
    function update_mchid($id,$mchid){
        $mch =array('mchId' => $mchid);
        return $this->db->where('id',$id)->update('mch_accounts',$mch);
    }

    //新用户注册时分配一个mchId
    function get_new_mchid($data){
        $this->db->insert('merchants',$data);
        return $this->db->insert_id();
    }
    /**
     * 查找appid是否存在重复的
     */
    function find_sameid($appid){
        $res = $this->db->where('appId',$appid)->get('tts_apps')->row();
        return $res;
    }
    /**
     * 注册apps
     */
    function reg_apps($mchid){
        if(!$mchid){
            return false;
        }
        //获取appid
        $appid = random_apps(1);
        $ccc = $this->find_sameid($appid);

        //判断是否重复
        while(isset($ccc)) {
            $appid = random_apps(1);
            $ccc = $this->find_sameid($appid);
        }
        //获取appsecret
        $appsecret = random_apps(2);
        if($appid != false && $appsecret != false){
            $data = array(
                'appId' => $appid,
                'appSecret' => $appsecret,
                'mchId' => $mchid
                );
            $res = $this->db->insert('tts_apps',$data);
        }else{
            return false;
        }
        return $this->db->insert_id();
    }
    /**
     * 请求appsid和appsecret
     */
    function get_apps($mchId) {
        return $this->db->where('mchId',$mchId)->get('tts_apps')->row();
    }
    /**
     * 请求secretid和secretKey
     */
    function get_tcloud($mchId) {
        return $this->db->where('mchId',$mchId)->get('tencent_cloud')->row();
    }
    /**
     * 更新secretid和secretKey
     */
    function update_tcloud($mchId,$data) {
        return $this->db->where('mchId',$mchId)->update('tencent_cloud',$data);
    }
    /**
     * 更新商户授权参数
     */
    public function update_wxauth_parameter($mchId,$data){
        return $this->db->where('id',$mchId)->update('merchants',$data);
    }
    /**
     * 更新商户授权公众号信息
     */
    public function update_authorizer_info($wxAppId,$data){
        return $this->db->where('wxAppId',$wxAppId)->update('merchants',$data);
    }
    /**
     * 更新商户授权公众号信息shop
     */
    public function update_authorizer_info_shop($wxAppId,$data){
        return $this->db->where('wxAppId_shop',$wxAppId)->update('merchants',$data);
    }
    /**
     * 更新商户授权令牌
     */
    public function update_authorizer_access_token($wxAppId,$data){
        return $this->db->where('wxAppId',$wxAppId)->update('merchants',$data);
    }
    /**
     * 更新商户授权令牌shop
     */
    public function update_authorizer_access_token_shop($wxAppId,$data){
        return $this->db->where('wxAppId_shop',$wxAppId)->update('merchants',$data);
    }
    /**
     * 更新商户授权jssdk ticket
     */
    public function update_authorizer_jsapi_ticket($wxAppId,$ticket,$time){
        $newData=[
            'wxAuthorizerJsapiTicket'=>$ticket,
            'wxAuthorizerJsapiTicketTime'=>$time
        ];
        return $this->db->where('wxAppId',$wxAppId)->update('merchants',$newData);
    }
    /**
     * 更新商户授权jssdk ticket shop
     */
    public function update_authorizer_jsapi_ticket_shop($wxAppId,$ticket,$time){
        $newData=[
            'wxAuthorizerJsapiTicket_shop'=>$ticket,
            'wxAuthorizerJsapiTicketTime_shop'=>$time
        ];
        return $this->db->where('wxAppId_shop',$wxAppId)->update('merchants',$newData);
    }


    /**
     * 获取乐码对应的企业信息（Added by shizq）
     * 
     * @param   $lecode 乐码
     * @return  object
     */
    function getFormScanHistory($lecode) {
        $scan_history = $this->db->where('code', $lecode)->get('scan_log')->row();
        if (! isset($scan_history)) {
            error("Can not find scan history where code is: $lecode");
            throw new Exception("找不到扫码记录", 1);
        }
        $merchant = $this->get($scan_history->mchId);
        if (! isset($merchant)) {
            error("Merchant does not exists which id is: $scan_history->mchId");
            throw new Exception("找不到乐码所属企业", 1);     
        }
        return $merchant;
    }

    /**
     * 获取企业账户的种类
     * 
     * @param   $account 账号（手机号）
     * @return  int
     */
    function getAccountType($account) {
        $mchAccount = $this->get_by_phone($account);
        if (! isset($mchAccount)) {
            // throw new Exception("账户不存在", 1);
            return AccountTypeEnum::Isnone;
        }
        $mchNum = $this->db->where('accountId', $mchAccount->id)->get('mch_accounts_ext')->result();
        if (count($mchNum) > 0) {
            return AccountTypeEnum::Merchant;
        }
        return AccountTypeEnum::Normal;
    }

    /**
     * 获取企业号下所有的商户列表
     * 
     * @param   $account 账号（手机号）
     * @return  array
     */
    function getMerchantList($account) {
        $merchantIds = $this->db->select('t2.mchId')
            ->from('mch_accounts     t1')
            ->join('mch_accounts_ext t2', 't2.accountId = t1.id')
            ->where('t1.phoneNum', $account)
            ->get()->result();
        $ids = [];
        foreach ($merchantIds as $value) {
            $ids[] = $value->mchId;
        }
        if (empty($ids)) {
            return [];
        }
        $merchants = $this->db->select('id, name')->where_in('id', $ids)->get('merchants')->result();
        return $merchants;
    }

    /**
     * 获取企业号在商户中的角色类型
     * 
     * @param   $userId
     * @param   $mchId
     * @return  object    
     */
    function getMerchantUser($userId, $mchId) {
        return $this->db->where('accountId', $userId)->where('mchId', $mchId)->get('mch_accounts_ext')->row();
    }

    /**
     * 获取角色拥有的权限模块
     * 
     * @param $roleId 
     * @return array
     */
    function getUserPermissionModules($roleId) {
        $rolePermissions = $this->db->select('t1.module')
            ->from('mch_permissions as t1')
            ->join('mch_role_permissions as t2', 't2.permissionKey = t1.key')
            ->where('t2.roleId', $roleId)
            ->get()->result();
        $rolePermissionsModules = [];
        foreach ($rolePermissions as $key => $value) {
            $rolePermissionsModules[] = $value->module;
        }
        return $rolePermissionsModules;
    }

    /**
     * 获取扫码记录中的商户信息
     *
     * @param $scaninfo 扫码日志
     */
    function getMerchantByScanLog($scanLog) {
        $this->checkMerchantExists($scanLog->mchId);
        return $this->get($scanLog->mchId);
    }

    /**
     * 检查商户编号是否存在
     *
     * @param $mchId 商户编号
     */
    function checkMerchantExists($mchId) {
        $merchant = $this->get($mchId);
        if (! isset($merchant)) {
            throw new Exception("商户不存在", 110105);
        }
    }

    function getMerchantByMchCode($mchCode) {
        $merchant = $this->merchant->get_by_code($mchCode);
        if (! isset($merchant)) {
            throw new Exception("商户不存在", 110106);
        }
        return $merchant;
    }

    /**
     * 发送短信验证码
     * 
     * @param $mobile 手机号码
     * @param $templateId 模板编号
     * @param $for 发送目的
     * @return void
     */
    public function sendSms($mobile, $for) {
        if ($for == 'login') {
            $mchAccount = $this->db->where('phoneNum', $mobile)->get('mch_accounts')->row();
            if (! isset($mchAccount)) {
                throw new Exception("此账户不存在", 1);
            }
            $templateId = 'SMS_7895084';
        }
        $templateId = 'SMS_7895084';
        $this->load->library('sms_vcode');
        $smsCode = $this->sms_vcode->sendSms($mobile, $templateId);

        // $this->load->library('common/send_sms');
        // $smsCode = $this->send_sms->get_validcode($mobile, $templateId);
        return $smsCode;
    }

}
