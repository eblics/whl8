<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
 * 微信的第三方应用接口
 */
class Wx3rd extends MY_Controller {
    private $wx3rdAppid;
    private $wx3rdAppsecret;
    private $wx3rdToken;
    private $wx3rdAeskey;
    
    public function __construct() {
        parent::__construct();
        $this->wx3rdAppid=$this->config->item('wx3rd_appid');
        $this->wx3rdAppsecret=$this->config->item('wx3rd_appsecret');
        $this->wx3rdToken=$this->config->item('wx3rd_token');
        $this->wx3rdAeskey=$this->config->item('wx3rd_aeskey');
        $this->load->model('merchant_model');
        $this->load->model('user_model');
        $this->load->model('variables_model');
        $this->load->helper('common/curl_helper');
        $this->load->library('common/wx3rd/wx3rd_crypt',['token'=>$this->wx3rdToken,'encodingAesKey'=>$this->wx3rdAeskey,'appId'=>$this->wx3rdAppid]);
        $this->mchId=$this->session->userdata('mchId');
    }
    
    
    //授权页面
    public function authpage($act=NULL){
        if(!isset($_GET['type']) || ( $_GET['type']!=1 && $_GET['type']!=2 )){
            echo '参数有误';
            return;
        }
        $type=$_GET['type'];//公众号类型 1：消费者 2：供应链
        if(is_null($act)){
            //授权开始页
            $this->authpage_start($type);
        }
        //回调
        if($act=='back'){
            $this->authpage_back($type);
        }
    }
    //授权事件开始
    private function authpage_start($type){
        $redirectUri='http://'.$_SERVER['HTTP_HOST'].'/wx3rd/authpage/back?type='.$type;
        $appId=$this->wx3rdAppid;
        $preAuthCode=$this->wx3rd_lib->get_pre_auth_code((object)["component_appid"=>$appId]);
	debug(var_export($preAuthCode,True));
        if($preAuthCode->errcode!=0){
            $viewData=(object)[
                'errcode'=>1,
                'errmsg'=>'授权失败'
            ];
            $this->load->view('wx3rd_authpage',['data'=>$viewData]);
            return;
        }
        $url='https://mp.weixin.qq.com/cgi-bin/componentloginpage?component_appid='.$appId.'&pre_auth_code='.$preAuthCode->data.'&redirect_uri='.$redirectUri;
        header("Location:$url");
        return;
    }
    //授权事件回调
    private function authpage_back($type){
        if(!isset($_GET['auth_code'])){
            $authName='微信公众号';
            $authUrl='';
            if($type==1){
                $authName='消费者微信公众号';
                $authUrl='/user/wechat';
            }
            if($type==2){
                $authName='供应链微信公众号';
                $authUrl='/user/weixin';
            }
            $viewData=(object)[
                'errcode'=>0,
                'errmsg'=>'授权成功',
                'name'=>$authName,
                'url'=>$authUrl
            ];
            $this->load->view('wx3rd_authpage',['data'=>$viewData]);
            return;
        }
        $httpPost=$this->wx3rd_lib->api_query_auth();
        if(isset($httpPost->errcode) && $httpPost->errcode!=0){
            log_message('error','api_query_auth: '.json_encode($httpPost));
            $viewData=(object)[
                'errcode'=>1,
                'errmsg'=>'授权失败'
            ];
            $this->load->view('wx3rd_authpage',['data'=>$viewData]);
            return;
        }
        $authorizerAppId=$httpPost->authorization_info->authorizer_appid;
        $merchants=$this->merchant_model->get_all();
        foreach ($merchants as $key => $value) {
            if($value->wxAppId==$authorizerAppId || $value->wxAppId_shop==$authorizerAppId){
                if(
                    !($value->id==$this->mchId && $type==1 && $value->wxAppId==$authorizerAppId) && 
                    !($value->id==$this->mchId && $type==2 && $value->wxAppId_shop==$authorizerAppId)
                ){
                    log_message('error','appid重复授权: '.$authorizerAppId);
                    $viewData=(object)[
                        'errcode'=>1,
                        'errmsg'=>'授权失败，本系统中已存在此公众号授权，请换个公众号进行授权',
                        'name'=>$type==1?'消费者微信':'供应链微信',
                        'url'=>$type==1?'/user/wechat':'/user/weixin'
                    ];
                    $this->load->view('wx3rd_authpage',['data'=>$viewData]);
                    return;
                }
            }
        }
        if($type==1){
            $saveData=(object)[
                'wxAppId'=>$httpPost->authorization_info->authorizer_appid,
                'wxAuthStatus'=>1,
                'wxAuthorizerAccessToken'=>$httpPost->authorization_info->authorizer_access_token,
                'wxAuthorizerAccessTokenTime'=>time()+7200,
                'wxAuthorizerRefreshToken'=>$httpPost->authorization_info->authorizer_refresh_token
            ];
        }else if($type==2){
            $saveData=(object)[
                'wxAppId_shop'=>$httpPost->authorization_info->authorizer_appid,
                'wxAuthStatus_shop'=>1,
                'wxAuthorizerAccessToken_shop'=>$httpPost->authorization_info->authorizer_access_token,
                'wxAuthorizerAccessTokenTime_shop'=>time()+7200,
                'wxAuthorizerRefreshToken_shop'=>$httpPost->authorization_info->authorizer_refresh_token
            ];
        }
        $this->merchant_model->update_wxauth_parameter($this->mchId,$saveData);
        //更新授权公众号信息
        if($type==1){
            $this->wx3rd_lib->update_authorizer_info($saveData->wxAppId,$type);
        }else if($type==2){
            $this->wx3rd_lib->update_authorizer_info($saveData->wxAppId_shop,$type);
        }
        //更新授权公众号JSSDK TICKET
        if($type==1){
            $this->wx3rd_lib->refresh_authorizer_jsapi_ticket($saveData->wxAppId,$type);
        }else if($type==2){
            $this->wx3rd_lib->refresh_authorizer_jsapi_ticket($saveData->wxAppId_shop,$type);
        }
        $tourl='http://'.$_SERVER['HTTP_HOST'].'/wx3rd/authpage/back?type='.$type; 
        header("Location:$tourl");
        return;
    }
    
    // 授权事件接收URL，微信开放平台每10分钟请求一次核心方法
    // http://domain/wx3rd/api_auth
    public function api_auth() {
        $postdata = file_get_contents('php://input');
        debug("wx3rd-api-auth - begin");
        $timeStamp = $this->input->get('timestamp');
        $nonce = $this->input->get('nonce');
        $msgSignature = $this->input->get('msg_signature');
        $msg = '';
        $errCode = $this->wx3rd_crypt->decryptMsg($msgSignature, $timeStamp, $nonce, $postdata, $msg);
        if ($errCode != 0) {
            error("wx3rd-api-auth - fail: ". $errCode);
            exit();
        }
        $msgobj = simplexml_load_string($msg, 'SimpleXMLElement', LIBXML_NOCDATA);
        //消息类型：component_verify_ticket
        if(! empty($msgobj->ComponentVerifyTicket)){
            $this->variables_model->set('component_verify_ticket', (string)$msgobj->ComponentVerifyTicket);
            if (isset($msgobj->InfoType) && $msgobj->InfoType == 'component_verify_ticket') {
                $this->wx3rd_lib->refresh_component_access_token($msgobj);//刷新component_access_token
            }
        }
        $this->updateWxToken();
    }

    public function updateWxToken() {
        print 'wx3rd-update-wx-token - begin';
        print PHP_EOL;
        //批量更新商户接口调用凭据、授权公众号信息
        $merchants = $this->merchant_model->get_all();
        if (empty($merchants)) {
            print '消息：没有任何商户。';
            print PHP_EOL;
            exit();
        }
        foreach ($merchants as $merchant) {
            if ($merchant->wxAuthStatus == BoolEnum::Yes && ! empty($merchant->wxAppId)){
                //刷新平台TOKEN
                $refresh = $this->wx3rd_lib->refresh_authorizer_access_token($merchant->wxAppId, 1);
                if ($refresh->errcode != 0) {
                    error('refresh_authorizer_access_token fail: mchId=> '. $merchant->id .' '. $refresh->errmsg);
                }
                //更新授权公众号信息
                $authorizerInfo = $this->wx3rd_lib->update_authorizer_info($merchant->wxAppId, 1);
                if ($authorizerInfo->errcode != 0) {
                    error('update_authorizer_info fail: mchId=>'. $merchant->id .' '. $authorizerInfo->errmsg);
                }
                //更新授权公众号JSSDK TICKET
                $authorizerJsapiTicket = $this->wx3rd_lib->refresh_authorizer_jsapi_ticket($merchant->wxAppId, 1);
                if ($authorizerJsapiTicket->errcode != 0) {
                    error('refresh_authorizer_jsapi_ticket fail: mchId=>'. $merchant->id .' '. $authorizerJsapiTicket->errmsg);
                }
            } 
            if ($merchant->wxAuthStatus_shop == BoolEnum::Yes && ! empty($merchant->wxAppId_shop)) {
                //刷新平台TOKEN shop
                $refresh_shop = $this->wx3rd_lib->refresh_authorizer_access_token($merchant->wxAppId_shop, 2);
                if ($refresh_shop->errcode != 0) {
                    error('refresh_authorizer_access_token shop fail: mchId=> '. $merchant->id .' '. $refresh_shop->errmsg);
                }
                //更新授权公众号信息 shop
                $authorizerInfo_shop = $this->wx3rd_lib->update_authorizer_info($merchant->wxAppId_shop, 2);
                if ($authorizerInfo_shop->errcode != 0) {
                    error('update_authorizer_info shop fail: mchId=>'. $merchant->id .' '. $authorizerInfo_shop->errmsg);
                }
                //更新授权公众号JSSDK TICKET shop
                $authorizerJsapiTicket_shop = $this->wx3rd_lib->refresh_authorizer_jsapi_ticket($merchant->wxAppId_shop, 2);
                if ($authorizerJsapiTicket_shop->errcode != 0) {
                    error('refresh_authorizer_jsapi_ticket shop fail: mchId=>'. $merchant->id .' '. $authorizerJsapiTicket_shop->errmsg);
                }
            } 
        }
        //end 批量更新商户接口调用凭据
        print 'update wx token end.';
    }

    // 消息与事件接收URL(微信开放平台设置)
    public function api($appid = NULL) {
        if (! isset($appid)) {
            error("wx3rd-api- fail: appid is empty");
            exit();
        }
        $postdata   = file_get_contents('php://input');
        $timeStamp  = $this->input->get('timestamp');
        $nonce      = $this->input->get('nonce');
        $signature  = $this->input->get('msg_signature');
        $msg = '';

        // 消息解密处理
        $errCode = $this->wx3rd_crypt->decryptMsg($signature, $timeStamp, $nonce, $postdata, $msg);
        if ($errCode != 0) {
            error("wx3rd-api - fail: ". $errCode);
            exit();
        }
        $message = simplexml_load_string($msg, 'SimpleXMLElement', LIBXML_NOCDATA);
        echo $this->responseMsg($message);
    }
    
    // 公众号响应消息
    private function responseMsg($message) {
        $msgType = trim($message->MsgType);

        // 调用指定函数处理事件
        $result     = $this->wx3rd_lib->{$this->wx3rd_lib->msg_handlers[$msgType]}($message);
        $timeStamp  = $this->input->get('timestamp');
        $nonce      = $this->input->get('nonce');
        $encryptMsg = '';

        // 消息加密处理
        $errCode    = $this->wx3rd_crypt->encryptMsg($result, $timeStamp, $nonce, $encryptMsg);
        if ($errCode != 0) {
            error("wx3rd-api - fail: responseMsg ". $errCode);
            exit();
        }
        return $encryptMsg;
    }
    
    //测试发布
    public function test_pub(){
        $query_auth_code=trim(file_get_contents("query_auth_code.txt"));
        $arr=explode('%%%%%%',$query_auth_code);
        log_message('debug',var_export($arr,TRUE));
        $object=json_decode($arr[0]);
        $authinfo=json_decode($arr[2]);
        $object->token=$authinfo->authorization_info->authorizer_access_token;
        $time=intval($arr[3]);
        $this->wx3rd_lib->send_kf_msg($object,$arr[1].'_from_api');
        echo 'ok';
    }
    
}

