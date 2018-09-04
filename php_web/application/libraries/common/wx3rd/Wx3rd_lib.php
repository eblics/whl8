<?php
/*
 * 微信的第三方应用接口lib
 */
class Wx3rd_lib {
    var $CI;

    private $wx3rdAppid;
    private $wx3rdAppsecret;
    private $wx3rdToken;
    private $wx3rdAeskey;

    public function __construct() {
        $this->CI=& get_instance();
        $this->wx3rdAppid=$this->CI->config->item('wx3rd_appid');
        $this->wx3rdAppsecret=$this->CI->config->item('wx3rd_appsecret');
        $this->wx3rdToken=$this->CI->config->item('wx3rd_token');
        $this->wx3rdAeskey=$this->CI->config->item('wx3rd_aeskey');
        $this->CI->load->model('merchant_model');
        $this->CI->load->model('user_model');
        $this->CI->load->model('variables_model');
        $this->CI->load->model('common_login_model');
        $this->CI->load->helper('common/curl_helper');
    }

    //获取预授权码
    public function get_pre_auth_code($data){
        $result=(object)['errcode'=>0,'errmsg'=>'获取成功'];
        $component_access_token=$this->CI->variables_model->get('component_access_token');
        if(!$component_access_token){
            $result->errcode=1;
            $result->errmsg='component_access_token 无效';
            return $result;
        }
        $url='https://api.weixin.qq.com/cgi-bin/component/api_create_preauthcode?component_access_token='.$component_access_token->val;
        $postData=(object)[
            "component_appid"=>$data->component_appid
        ];
        $postData=json_encode($postData);
        $httpPost=curl_post($url,$postData,TRUE);
        $httpPost=json_decode($httpPost);
        if(isset($httpPost->errcode)){
            log_message('error','get_pre_auth_code: '.json_encode($httpPost));
            $result->errcode=1;
            $result->errmsg='curl_post api_create_preauthcode 失败';
            return $result;
        }
        $result->data=$httpPost->pre_auth_code;
        return $result;
    }

    public function api_query_auth(){
        $component_access_token=$this->CI->variables_model->get('component_access_token');
        if(!$component_access_token){
            $viewData=(object)[
                'errcode'=>1,
                'errmsg'=>'授权失败'
            ];
            $this->CI->load->view('wx3rd_authpage',['data'=>$viewData]);
            return NULL;
        }
        $url='https://api.weixin.qq.com/cgi-bin/component/api_query_auth?component_access_token='.$component_access_token->val;
        $postData=(object)[
            "component_appid"=>$this->wx3rdAppid,
            "authorization_code"=>$_GET['auth_code']
        ];
        $postData=json_encode($postData);
        $httpPost=curl_post($url,$postData,TRUE);
        $httpPost=json_decode($httpPost);
        return $httpPost;
    }

    //刷新第三方平台component_access_token
    public function refresh_component_access_token(){
        $component_access_token=$this->CI->variables_model->get('component_access_token');
        if($component_access_token){
            $cat_expire_time=$component_access_token->theTime;
        }else{
            $cat_expire_time=0;
        }
        if($cat_expire_time-1200<time()){
            $cvt=$this->CI->variables_model->get('component_verify_ticket');
            if(! $cvt){
                return FALSE;
            }
            $data=(object)[
                "component_appid"=>$this->wx3rdAppid,
                "component_appsecret"=>$this->wx3rdAppsecret,
                "component_verify_ticket"=> $cvt->val
            ];
            $data=json_encode($data);
            $result=curl_post('https://api.weixin.qq.com/cgi-bin/component/api_component_token',$data,TRUE);
            // log_message('debug','refresh_component_access_token: '.$result);
            $result=json_decode($result);
            if(isset($result->errcode)){
                log_message('error','api_component_token: '.$result);
                return;
            }
            $this->CI->variables_model->set('component_access_token',(string)$result->component_access_token,time()+intval($result->expires_in));
        }
    }

    //获取（刷新）授权公众号的接口调用凭据（令牌）
    public function refresh_authorizer_access_token($wxAppId,$type){
        $result=(object)['errcode'=>0,'errmsg'=>'刷新成功'];
        if($type==1){
            $merchant=$this->CI->merchant_model->get_by_appid($wxAppId);
        }else if($type==2){
            $merchant=$this->CI->merchant_model->get_by_appid_shop($wxAppId);
        }
        if(!$merchant){
            $result->errcode=1;
            $result->errmsg='商户信息不存在';
            return $result;
        }
        error("wx3rd-lib-refresh-auth-access-token - merchant: " . $merchant->name);
        if($type==1){
            $wxAuthorizerAccessTokenTime=$merchant->wxAuthorizerAccessTokenTime;
        }else if($type==2){
            $wxAuthorizerAccessTokenTime=$merchant->wxAuthorizerAccessTokenTime_shop;
        }
        if(empty($wxAuthorizerAccessTokenTime)){
            $wxAuthorizerAccessTokenTime=0;
        }
        if($wxAuthorizerAccessTokenTime-600>time()){
            error("wx3rd-lib-refresh-auth-access-token - break: 不需要刷新");
            return $result;
        }
        $component_access_token=$this->CI->variables_model->get('component_access_token');
        if(!$component_access_token){
            $result->errcode=1;
            $result->errmsg='component_access_token不存在';
            return $result;
        }
        if($component_access_token->theTime-500<time()){
            return $result;
        }
        $url='https://api.weixin.qq.com/cgi-bin/component/api_authorizer_token?component_access_token='.$component_access_token->val;
        $componentAppid=$this->wx3rdAppid;
        if($type==1){
            $wxAuthorizerRefreshToken=$merchant->wxAuthorizerRefreshToken;
        }else if($type==2){
            $wxAuthorizerRefreshToken=$merchant->wxAuthorizerRefreshToken_shop;
        }
        $postData=(object)[
            "component_appid"=>$componentAppid,
            "authorizer_appid"=>$wxAppId,
            "authorizer_refresh_token"=>$wxAuthorizerRefreshToken
        ];
        $postData=json_encode($postData);
        $httpPost=curl_post($url,$postData,TRUE);
        error("wx3rd-lib-refresh-auth-access-token - result: ". json_encode($httpPost));
        $httpPost=json_decode($httpPost);
        if(! $httpPost){
            $result->errcode=1;
            $result->errmsg='curl_post api_authorizer_token 失败';
            return $result;
        }
        if(isset($httpPost->errcode)){
            log_message('error','api_authorizer_token: '.json_encode($httpPost));
            $result->errcode=1;
            $result->errmsg='curl_post api_authorizer_token 失败';
            return $result;
        }
        if($type==1){
            $saveData=[
                'wxAuthorizerAccessToken'=>$httpPost->authorizer_access_token,
                'wxAuthorizerAccessTokenTime'=>7200+time(),
                'wxAuthorizerRefreshToken'=>$httpPost->authorizer_refresh_token
            ];
            $save=$this->CI->merchant_model->update_authorizer_access_token($wxAppId,$saveData);
        }else if($type==2){
            $saveData=[
                'wxAuthorizerAccessToken_shop'=>$httpPost->authorizer_access_token,
                'wxAuthorizerAccessTokenTime_shop'=>7200+time(),
                'wxAuthorizerRefreshToken_shop'=>$httpPost->authorizer_refresh_token
            ];
            $save=$this->CI->merchant_model->update_authorizer_access_token_shop($wxAppId,$saveData);
        }
        if(!$save){
            $result->errcode=1;
            $result->errmsg='刷新失败';
            return $result;
        }
        $result->errcode=0;
        $result->errmsg='刷新成功';
        return $result;
    }
    //获取(更新)授权方的公众号帐号基本信息
    public function update_authorizer_info($wxAppId,$type){
        $result=(object)['errcode'=>0,'errmsg'=>'获取成功'];
        $component_access_token=$this->CI->variables_model->get('component_access_token');
        if(!$component_access_token){
            $result->errcode=1;
            $result->errmsg='component_access_token不存在';
            return $result;
        }
        if($component_access_token->theTime<time()){
            $result->errcode=1;
            $result->errmsg='component_access_token 已过期';
            log_message('error','wxAppId:'.$wxAppId.' function get_authorizer_info component_access_token 已过期');
            return $result;
        }
        $url='https://api.weixin.qq.com/cgi-bin/component/api_get_authorizer_info?component_access_token='.$component_access_token->val;
        $componentAppid=$this->wx3rdAppid;
        $postData=(object)[
            "component_appid"=>$componentAppid,
            "authorizer_appid"=>$wxAppId
        ];
        $postData=json_encode($postData);
        $httpPost=curl_post($url,$postData,TRUE);
        $httpPost=json_decode($httpPost);
        if(isset($httpPost->errcode)){
            log_message('error','api_get_authorizer_info: '.json_encode($httpPost));
            $result->errcode=1;
            $result->errmsg='curl_post api_get_authorizer_info 失败';
            return $result;
        }
        $result->errcode=0;
        $result->errmsg='获取成功';
        $dataObj=$httpPost;
        if($type==1){
            $saveData=[
                'wxName'=>$dataObj->authorizer_info->nick_name,
                'wxYsId'=>$dataObj->authorizer_info->user_name,
                'wxQrcodeUrl'=>$dataObj->authorizer_info->qrcode_url,
                'wxAuthorizerInfo'=>json_encode($httpPost)
            ];
            $save=$this->CI->merchant_model->update_authorizer_info($wxAppId,$saveData);
        }else if($type==2){
            $saveData=[
                'wxName_shop'=>$dataObj->authorizer_info->nick_name,
                'wxYsId_shop'=>$dataObj->authorizer_info->user_name,
                'wxQrcodeUrl_shop'=>$dataObj->authorizer_info->qrcode_url,
                'wxAuthorizerInfo_shop'=>json_encode($httpPost)
            ];
            $save=$this->CI->merchant_model->update_authorizer_info_shop($wxAppId,$saveData);
        }
        if(! $save){
            $result->errcode=1;
            $result->errmsg='获取失败';
        }
        return $result;
    }
    //刷新授权公众号jssdk ticket
    public function refresh_authorizer_jsapi_ticket($wxAppId,$type){
        $result=(object)['errcode'=>0,'errmsg'=>'刷新成功'];
        if($type==1){
            $merchant=$this->CI->merchant_model->get_by_appid($wxAppId);
        }else if($type==2){
            $merchant=$this->CI->merchant_model->get_by_appid_shop($wxAppId);
        }
        if(!$merchant){
            $result->errcode=1;
            $result->errmsg='商户信息不存在';
            return $result;
        }
        if($type==1){
            $wxAuthorizerJsapiTicketTime=$merchant->wxAuthorizerJsapiTicketTime;
            $wxAuthorizerAccessToken=$merchant->wxAuthorizerAccessToken;
        }else if($type==2){
            $wxAuthorizerJsapiTicketTime=$merchant->wxAuthorizerJsapiTicketTime_shop;
            $wxAuthorizerAccessToken=$merchant->wxAuthorizerAccessToken_shop;
        }
        if(empty($wxAuthorizerJsapiTicketTime)){
            $wxAuthorizerJsapiTicketTime=0;
        }
        if($wxAuthorizerJsapiTicketTime-600>time()){
            return $result;
        }
        $url='https://api.weixin.qq.com/cgi-bin/ticket/getticket?type=jsapi&access_token='.$wxAuthorizerAccessToken;
        $res=file_get_contents($url);
        if(!$res){
            $result->errcode=1;
            $result->errmsg='微信jssdk getticket接口请求失败';
            return $result;
        }
        $res = json_decode($res,true);
        $ticket = isset($res['ticket'])?$res['ticket']:NULL;
        if(!$ticket) {
            $result->errcode=1;
            $result->errmsg='微信jssdk getticket获取失败';
            return $result;
        }
        if($type==1){
            $save=$this->CI->merchant_model->update_authorizer_jsapi_ticket($wxAppId,$ticket,time()+7000);
        }else if($type==2){
            $save=$this->CI->merchant_model->update_authorizer_jsapi_ticket_shop($wxAppId,$ticket,time()+7000);
        }
        if(!$save){
            $result->errcode=1;
            $result->errmsg='刷新失败';
            return $result;
        }
        $result->errcode=0;
        $result->errmsg='刷新成功';
        return $result;
    }

    //消息处理器
    var $msg_handlers=[
        'event'=>'receiveEvent',
        'text'=>'receiveText',
        'image'=>'receiveImage',
        'location'=>'receiveLocation',
        'voice'=>'receiveVoice',
        'video'=>'receiveVideo',
        'link'=>'receiveLink'
    ];
    //事件处理器
    var $event_handlers=[
        'subscribe'=>'receive_evt_subscribe',       //用户订阅事件
        'unsubscribe'=>'receive_evt_unsubscribe',   //取消订阅事件
        'SCAN'=>'receive_evt_scan',                 //用户已关注时的事件推送
        'CLICK'=>'receive_evt_click',               //点击菜单拉取消息时的事件
        'LOCATION'=>'receive_evt_location',         //上报地理位置事件
        'VIEW'=>'receive_evt_view',                 //点击菜单跳转链接时的事件
        'MASSSENDJOBFINISH'=>'receive_evt_mjf',     //事件推送群发结果事件
        'TEMPLATESENDJOBFINISH'=>'receive_evt_tplsjf',//模板消息推送结果反馈事件
        'scancode_push'=>'receive_evt_scancode_push',//扫码推送事件
        'scancode_waitmsg'=>'receive_evt_scancode_waitmsg',//扫码推送事件，且弹出“消息接收中”提示框
        'user_get_card'=>'receive_evt_user_get_card',//用户领取卡券
        'ShakearoundUserShake'=>'receive_evt_user_shake'//摇一摇周边用户摇一摇事件
    ];

    //接收事件消息
    public function receiveEvent($object){
        if($object->ToUserName=='gh_3c884a361561'){
            $result = $this->transmitText($object, $object->Event.'from_callback');
            return $result;
        }
        $evt = trim($object->Event);
        if(! array_key_exists($evt, $this->event_handlers)){
            log_message('error','receive new Event: '.var_export($object,true));
            return;
        }
        return $this->{$this->event_handlers[$evt]}($object);
    }

    //接收文本消息
    public function receiveText($object){
        if($object->ToUserName=='gh_3c884a361561'){
            if($object->Content=='TESTCOMPONENT_MSG_TYPE_TEXT'){
                $content = 'TESTCOMPONENT_MSG_TYPE_TEXT_callback';
                $result = $this->transmitText($object, $content);
                return $result;
            }
            if(strpos($object->Content,'QUERY_AUTH_CODE')!==FALSE){
                $query_auth_code=trim(str_replace('QUERY_AUTH_CODE:','',$object->Content));
                $component_access_token=$this->CI->variables_model->get('component_access_token');
                if(!$component_access_token){
                    return;
                }
                $url='https://api.weixin.qq.com/cgi-bin/component/api_query_auth?component_access_token='.$component_access_token->val;
                $postData=(object)[
                    "component_appid"=>$this->wx3rdAppid,
                    "authorization_code"=>$query_auth_code
                ];
                $postData=json_encode($postData);
                $httpPost=curl_post($url,$postData,TRUE);
                // $httpPost=json_decode($httpPost);
                $max_size = 10000;
                $log_filename = "query_auth_code.txt";
                if(file_exists($log_filename) and (abs(filesize($log_filename)) > $max_size)){unlink($log_filename);}
                file_put_contents($log_filename, json_encode($object).'%%%%%%'.$query_auth_code.'%%%%%%'.$httpPost.'%%%%%%'.time());
                return;
            }
        }else{
            return $this->transmitService($object);
        }
    }

    //接收图片消息
    public function receiveImage($object){
        // $content = array("MediaId"=>$object->MediaId);
        // $result = $this->transmitImage($object, $content);
        // return $result;
        return $this->transmitService($object);
    }
    //接收位置消息
    public function receiveLocation($object){
        // $content = "你发送的是位置，纬度为：".$object->Location_X."；经度为：".$object->Location_Y."；缩放级别为：".$object->Scale."；位置为：".$object->Label;
        // $result = $this->transmitText($object, $content);
        // return $result;
    }
    //接收语音消息
    public function receiveVoice($object){
        // if (isset($object->Recognition) && !empty($object->Recognition)){
        //     $content = "您刚才说的是：".$object->Recognition;
        //     $result = $this->transmitText($object, $content);
        // }else{
        //     $content = array("MediaId"=>$object->MediaId);
        //     $result = $this->transmitVoice($object, $content);
        // }
        // return $result;
        return $this->transmitService($object);
    }
    //接收视频消息
    public function receiveVideo($object){
        // $content = array("MediaId"=>$object->MediaId, "ThumbMediaId"=>$object->ThumbMediaId, "Title"=>"", "Description"=>"");
        // $result = $this->transmitVideo($object, $content);
        // return $result;
        return $this->transmitService($object);
    }
    //接收链接消息
    public function receiveLink($object){
        // $content = "你发送的是链接，标题为：".$object->Title."；内容为：".$object->Description."；链接地址为：".$object->Url;
        // $result = $this->transmitText($object, $content);
        // return $result;
        return $this->transmitService($object);
    }
    //点击菜单跳转链接时的事件处理函数
    function receive_evt_view($object){
        //发放未发放的奖项
        // $this->send_reward($object);
        //更新用户信息
        $this->update_userinfo($object);
    }

    //用户订阅事件处理函数
    public function receive_evt_subscribe($object){
        //发放未发放的奖项
        try {
            $this->send_reward($object);
        } catch (Exception $e) {
            error('wx3rd-lib-receive-evt-subscribe - fail: '. $e->getMessage());
        }
        //更新用户信息
        $this->update_userinfo($object);
        //发送欢迎词
        $sendWelcome=$this->send_welcome($object);
        if(empty($sendWelcome)){
            return '';
            // return $this->transmitEventSubscribe($object);
        }
        return $sendWelcome;
    }
    //取消订阅事件处理函数
    function receive_evt_unsubscribe($object){
        //更新用户信息
        $this->update_userinfo_unsubscribe($object);
    }
    //上报地理位置事件处理函数
    function receive_evt_location($object){
        //更新用户信息
        // $this->update_userinfo($object);
    }
    //事件推送群发结果处理函数
    function receive_evt_mjf($object){
    }
    //模板消息推送发结果处理函数
    function receive_evt_tplsjf($object){
        if($object->Status!='success'){
            log_message('error','receive_evt_tplsjf :'.var_export($object,TRUE));
        }else{
            log_message('debug','receive_evt_tplsjf :'.var_export($object,TRUE));
        }
    }
    function receive_evt_scan($object){
        log_message('debug','receive_evt_scan :'.var_export($object,TRUE));
    }
    //扫码推送事件
    function receive_evt_scancode_push($object){
        log_message('debug','receive_evt_scancode_push :'.var_export($object,TRUE));
    }
    //扫码推送事件，且弹出“消息接收中”提示框
    function receive_evt_scancode_waitmsg($object){
        // redirect($object->ScanCodeInfo->scanResult);exit;
        if($object->EventKey=='scancode_waitmsg_waiter_sys'){

        }
        if($object->EventKey=='scancode_waitmsg_waiter_sq'){

        }
        if($object->EventKey=='scancode_waitmsg_salesman_sys'){
        }
        if($object->EventKey=='scancode_waitmsg_salesman_sq'){

        }
        log_message('debug','receive_evt_scancode_waitmsg :'.var_export($object,TRUE));
    }
    //用户领取卡券
    function receive_evt_user_get_card($object){
        log_message('debug','receive_evt_user_get_card :'.var_export($object,TRUE));
    }
    //点击菜单拉取消息时的事件
    function receive_evt_click($object){
        log_message('debug','receive_evt_click :'.var_export($object,TRUE));
    }

    //摇一摇周边用户摇一摇事件
    function receive_evt_user_shake($object){
        log_message('error','receive_evt_user_shake debug:'.var_export($object,TRUE));
    }



    //回复文本消息
    private function transmitText($object, $content){
        $xmlTpl = "<xml>
        <ToUserName><![CDATA[%s]]></ToUserName>
        <FromUserName><![CDATA[%s]]></FromUserName>
        <CreateTime>%s</CreateTime>
        <MsgType><![CDATA[text]]></MsgType>
        <Content><![CDATA[%s]]></Content>
        </xml>";
        $result = sprintf($xmlTpl, $object->FromUserName, $object->ToUserName, time(), $content);
        return $result;
    }
    //回复图片消息
    private function transmitImage($object, $imageArray){
        $itemTpl = "<Image>
        <MediaId><![CDATA[%s]]></MediaId>
        </Image>";
        $item_str = sprintf($itemTpl, $imageArray['MediaId']);
        $xmlTpl = "<xml>
        <ToUserName><![CDATA[%s]]></ToUserName>
        <FromUserName><![CDATA[%s]]></FromUserName>
        <CreateTime>%s</CreateTime>
        <MsgType><![CDATA[image]]></MsgType>
        $item_str
        </xml>";
        $result = sprintf($xmlTpl, $object->FromUserName, $object->ToUserName, time());
        return $result;
    }
    //回复语音消息
    private function transmitVoice($object, $voiceArray){
        $itemTpl = "<Voice>
        <MediaId><![CDATA[%s]]></MediaId>
        </Voice>";
        $item_str = sprintf($itemTpl, $voiceArray['MediaId']);
        $xmlTpl = "<xml>
        <ToUserName><![CDATA[%s]]></ToUserName>
        <FromUserName><![CDATA[%s]]></FromUserName>
        <CreateTime>%s</CreateTime>
        <MsgType><![CDATA[voice]]></MsgType>
        $item_str
        </xml>";
        $result = sprintf($xmlTpl, $object->FromUserName, $object->ToUserName, time());
        return $result;
    }
    //回复视频消息
    private function transmitVideo($object, $videoArray){
        $itemTpl = "<Video>
        <MediaId><![CDATA[%s]]></MediaId>
        <ThumbMediaId><![CDATA[%s]]></ThumbMediaId>
        <Title><![CDATA[%s]]></Title>
        <Description><![CDATA[%s]]></Description>
        </Video>";
        $item_str = sprintf($itemTpl, $videoArray['MediaId'], $videoArray['ThumbMediaId'], $videoArray['Title'], $videoArray['Description']);
        $xmlTpl = "<xml>
        <ToUserName><![CDATA[%s]]></ToUserName>
        <FromUserName><![CDATA[%s]]></FromUserName>
        <CreateTime>%s</CreateTime>
        <MsgType><![CDATA[video]]></MsgType>
        $item_str
        </xml>";
        $result = sprintf($xmlTpl, $object->FromUserName, $object->ToUserName, time());
        return $result;
    }
    //回复图文消息
    private function transmitNews($object, $newsArray){
        if(!is_array($newsArray)){
            return;
        }
        $itemTpl = "<item>
        <Title><![CDATA[%s]]></Title>
        <Description><![CDATA[%s]]></Description>
        <PicUrl><![CDATA[%s]]></PicUrl>
        <Url><![CDATA[%s]]></Url>
        </item>
        ";
        $item_str = "";
        foreach ($newsArray as $item){
            $item_str .= sprintf($itemTpl, $item['Title'], $item['Description'], $item['PicUrl'], $item['Url']);
        }
        $xmlTpl = "<xml>
        <ToUserName><![CDATA[%s]]></ToUserName>
        <FromUserName><![CDATA[%s]]></FromUserName>
        <CreateTime>%s</CreateTime>
        <MsgType><![CDATA[news]]></MsgType>
        <ArticleCount>%s</ArticleCount>
        <Articles>$item_str</Articles>
        </xml>";
        $result = sprintf($xmlTpl, $object->FromUserName, $object->ToUserName, time(), count($newsArray));
        return $result;
    }
    //回复音乐消息
    private function transmitMusic($object, $musicArray){
        $itemTpl = "<Music>
        <Title><![CDATA[%s]]></Title>
        <Description><![CDATA[%s]]></Description>
        <MusicUrl><![CDATA[%s]]></MusicUrl>
        <HQMusicUrl><![CDATA[%s]]></HQMusicUrl>
        </Music>";
        $item_str = sprintf($itemTpl, $musicArray['Title'], $musicArray['Description'], $musicArray['MusicUrl'], $musicArray['HQMusicUrl']);
        $xmlTpl = "<xml>
        <ToUserName><![CDATA[%s]]></ToUserName>
        <FromUserName><![CDATA[%s]]></FromUserName>
        <CreateTime>%s</CreateTime>
        <MsgType><![CDATA[music]]></MsgType>
        $item_str
        </xml>";
        $result = sprintf($xmlTpl, $object->FromUserName, $object->ToUserName, time());
        return $result;
    }
    //回复多客服消息
    private function transmitService($object){
        $xmlTpl = "<xml>
        <ToUserName><![CDATA[%s]]></ToUserName>
        <FromUserName><![CDATA[%s]]></FromUserName>
        <CreateTime>%s</CreateTime>
        <MsgType><![CDATA[transfer_customer_service]]></MsgType>
        </xml>";
        $result = sprintf($xmlTpl, $object->FromUserName, $object->ToUserName, time());
        return $result;
    }
    //响应关注事件
    private function transmitEventSubscribe($object){
        log_message('debug','响应关注事件：'.var_export($object,true));
        $xmlTpl = "<xml>
        <ToUserName><![CDATA[%s]]></ToUserName>
        <FromUserName><![CDATA[%s]]></FromUserName>
        <CreateTime>%s</CreateTime>
        <MsgType><![CDATA[event]]></MsgType>
        <Event><![CDATA[subscribe]]></Event>
        </xml>";
        $result = sprintf($xmlTpl, $object->FromUserName, $object->ToUserName, time());
        return $result;
    }

    //公众号消息与事件接收及处理=======================================end================================================


    //平台业务处理函数======================================start===============================================
    //更新用户信息
    public function update_userinfo($object){
        $openid=(string)$object->FromUserName;
        $record=$this->CI->db->query("select * from user_update where wxYsId='$object->ToUserName' and openid='$openid' and createTime>".(time()-20)." limit 0,1")->row();
        if(!$record){
            $this->CI->db->query("insert into user_update(wxYsId,openid,status,createTime) values('$object->ToUserName','$openid',0,".time().")");
        }
    }
    //更新用户信息(取消关注)
    public function update_userinfo_unsubscribe($object){
        $openid=(string)$object->FromUserName;
        $theTime=time();
        //更新users_common 关注状态
        if($object->ToUserName=='gh_c2a5ae6512e7'){
            $this->CI->db->query("update users_common set subscribe=0,updateTime=$theTime where openid='$openid'");
        }else{
            $this->CI->db->query("update users set subscribe=0,updateTime=$theTime where openid='$openid'");
        }
    }
    //更新发放未发放奖品
    public function send_reward($object) {
        debug("wx3rd-lib-send-reward - begin");
        $merchant = $this->CI->merchant_model->get_by_wxid($object->ToUserName);
        if ($merchant->id == -1) return;
        if (! isset($merchant)) {
            throw new Exception("Merchant Not Found", 1);
        }
        $openid = (string)$object->FromUserName;
        $user = $this->CI->user_model->get_by_openId($openid);
        if (! isset($user)) {
            $user = new stdClass();
            $user->openid = $openid;
            $user->mchId = $merchant->id;
            $user->subscribe = 1;
            $this->CI->db->insert('users', $user);
            $user->id = $this->CI->db->insert_id();
        }
        if ($user->mchId != $merchant->id) {
            throw new Exception("User and Merchant invalid", 1);
        }
        $sql = "select * from user_redpackets where userId = ? and mchId = ? and sended = ?";
        $redpackets = $this->CI->db->query($sql, [$user->id, $merchant->id, 0])->result();
        if (empty($redpackets)) {
            debug('wx3rd-lib-send-reward - no redpacket');
        }
        foreach ($redpackets as $redpacket) {
            $this->send_after_subscribe($redpacket, $openid);
        }
        $cards = $this->CI->db->query("select * from user_cards where userId=$user->id and sended=0")->result();
        if (empty($cards)) {
            debug('wx3rd-lib-send-reward - no card');
        }
        foreach ($cards as $card){
            $this->send_cards_after_subscribe($card, $user);
        }
        $points = $this->CI->db->query("select * from user_points where userId=$user->id and sended=0")->result();
        if (empty($points)) {
            debug('wx3rd-lib-send-reward - no point');
        }
        foreach ($points as $point){
            $this->send_points_after_subscribe($point, $user);
        }
    }
    //关注后发放红包
    public function send_after_subscribe($user_redpacket, $openid){
        error('wx3rd-lib-send-redpacket - begin');
        error('wx3rd-lib-send-redpacket - params: '. json_encode($user_redpacket));
        $this->CI->load->model('red_packet_model');
        $red_packet=$this->CI->db->where('id',$user_redpacket->rpId)->get('red_packets')->row();
        if(!$red_packet) return;
        $this->CI->db->trans_start();
        $sql = "select sended from user_redpackets where id = ?";
        $confirmRedpacket = $this->CI->db->query($sql, [$user_redpacket->id])->row();
        if (! $confirmRedpacket || $confirmRedpacket->sended == 1){
            return;
        }
        $this->CI->load->model('scan_log_model');
        $scaninfo=NULL;
        if(!empty($user_redpacket->code)){
            $scaninfo=$this->CI->scan_log_model->get_by_code($user_redpacket->code);
        }else if($user_redpacket->scanId>0){
            $scaninfo=$this->CI->scan_log_model->get($user_redpacket->scanId);
        }else{
            return;
        }
        if(!$scaninfo) return;
        //要发放的金额
        $amount=$user_redpacket->amount;
        error('wx3rd-lib-send-redpacket - red_packet: '. json_encode($red_packet));
        error('wx3rd-lib-send-redpacket - amount: '. $amount);
        $sendResult=$this->CI->red_packet_model->send_redpacket($red_packet,$scaninfo,$amount,$user_redpacket->role);
        if($sendResult->errcode!=0){
            return (object)['errcode'=>8,'errmsg'=>'关注公众号红包发放失败'];
        }
        //sended置为1
        $this->CI->db->query("update user_redpackets set sended=1 where id=$user_redpacket->id");
        $this->CI->db->trans_complete();
        if ($this->CI->db->trans_status() === FALSE){
            return (object)['errcode'=>9,'errmsg'=>'关注公众号红包发放失败'];
        }
        return (object)['errcode'=>0,'errmsg'=>'ok'];
    }
    //关注后发放乐券
    public function send_cards_after_subscribe($user_card,$user){
        $this->CI->db->trans_start();
        $confirmCard=$this->CI->db->query("select sended from user_cards where id=$user_card->id for update")->row();
        if(!$confirmCard || $confirmCard->sended==1){
            return;
        }
        try{
            $user_cards_account = (object)[
                'userId'=>$user_card->userId,
                'role'=>$user_card->role,
                'mchId'=>$user->mchId,
                'cardId'=>$user_card->cardId
            ];
            $sql = "INSERT INTO user_cards_account(userId,role,mchId,cardId,num)
                    VALUES (?, ?, ?, ?, IFNULL(num,0)+1) ON DUPLICATE KEY UPDATE num = IFNULL(num, 0) + 1";
            $updateData = [
                $user_cards_account->userId,
                $user_cards_account->role,
                $user_cards_account->mchId,
                $user_cards_account->cardId
            ];
            $this->CI->db->query($sql, $updateData);
            $this->CI->db->set('sended',1)->where('id',$user_card->id)->update('user_cards');
            $this->CI->db->trans_complete();
            return (object)['errcode'=>0,'errmsg'=>'ok'];
        } catch (ErrorException $e) {
            log_message('ERROR',$e);
            log_message('ERROR',var_export($user_card,TRUE));
        }
    }
    //关注后发放积分
    public function send_points_after_subscribe($user_point,$user){
        $this->CI->db->trans_start();
        $confirmPoint=$this->CI->db->query("select sended from user_points where id=$user_point->id for update")->row();
        if(!$confirmPoint || $confirmPoint->sended==1){
            return;
        }
        try{
            $user_points_account=(object)['userId'=>$user_point->userId,'role'=>$user_point->role,'mchId'=>$user->mchId,'amount'=>$user_point->amount];
            $sql="INSERT INTO user_points_accounts(userId,role,mchId,amount) VALUES($user_points_account->userId,$user_points_account->role,$user_points_account->mchId,$user_points_account->amount)
                ON DUPLICATE KEY UPDATE amount=IFNULL(amount,0)+$user_points_account->amount";
            log_message('debug','send_points_after_subscribe: '.var_export($sql,TRUE));
            $this->CI->db->query($sql);
            $this->CI->db->set('sended',1)->where('id',$user_point->id)->update('user_points');
            $this->CI->db->trans_complete();
            return (object)['errcode'=>0,'errmsg'=>'ok'];
        }catch(ErrorException $e){
            log_message('ERROR',$e);
            log_message('ERROR',var_export($user_point,TRUE));
        }
    }
    //发送欢迎词
    private function send_welcome($object){
        $merchant=$this->CI->merchant_model->get_by_wxid($object->ToUserName);
        if(!$merchant) return;
        if($merchant->id==92) return;//RIO不弹出关注欢迎推送
        if(trim($merchant->subscribeMsg)=='' || trim($merchant->subscribeImgUrl)==''){
            return;
        }
        $openid=(string)$object->FromUserName;
        $content[] = [
                "Title"=>$merchant->subscribeMsg,
                "Description"=>'',
                "PicUrl"=>config_item('mobile_url').$merchant->subscribeImgUrl,
                "Url" =>config_item('mobile_url').'/user?mch_id='.$merchant->id
        ];
        $userInfo=$this->CI->user_model->get_by_openId($openid);
        if($userInfo){
            $content[] = [
                    "Title"=>'快来领取您的红包吧',
                    "Description"=>"请从“菜单 - 我的帐户 - 我的红包”进入领取红包",
                    "PicUrl"=>config_item('mobile_url')."static/images/icon-hongbao.png",
                    "Url" =>config_item('mobile_url').'/user?mch_id='.$merchant->id
            ];
        }
        return $this->transmitNews($object,$content);
    }
    //获取所有客服账号
    public function get_kf_list($object){
        $merchant=$this->CI->merchant_model->get_by_wxid($object->ToUserName);
        if(!$merchant) return;
        $url='https://api.weixin.qq.com/cgi-bin/customservice/getkflist?access_token='.$merchant->wxAuthorizerAccessToken;
        $result=curl_post($url,NULL,TRUE);
        if(! $result) return;
        $result=json_decode($result);
        if(isset($result->errcode) && $result->errcode!=0){
            log_message('error','get_kf_list: '.var_export($result,TRUE));
            return;
        }
        return $result;
    }
    //添加客服账号
    public function add_kf_account($object,$postData){
        $merchant=$this->CI->merchant_model->get_by_wxid($object->ToUserName);
        if(!$merchant) return;
        $url='https://api.weixin.qq.com/customservice/kfaccount/add?access_token='.$merchant->wxAuthorizerAccessToken;
        // $postData='{
        //     "kf_account" : "test1@test",
        //     "nickname" : "客服1",
        //     "password" : "pswmd5",
        // }';
        $result=curl_post($url,$postData,TRUE);
        if(! $result) return;
        $result=json_decode($result);
        if(isset($result->errcode) && $result->errcode!=0){
            log_message('error','add_kf_account: '.var_export($result,TRUE));
            return;
        }
        return $result;
    }
    //发送客服消息
    public function send_kf_msg($object,$content){
        log_message('debug','send_kf_msg para: '.var_export($object,TRUE).' , '.var_export($content,TRUE));
        if(property_exists($object,'token')){
            $token=$object->token;
        }else{
            $merchant=$this->CI->merchant_model->get_by_wxid($object->ToUserName);
            if(!$merchant) return;
            $token=$merchant->wxAuthorizerAccessToken;
        }
        $url='https://api.weixin.qq.com/cgi-bin/message/custom/send?access_token='.$token;
        $postData=(object)[
            "touser"=>$object->FromUserName,
            "msgtype"=>"text",
            "text"=>(object)[
                "content"=>$content
            ]
        ];
        $postData=json_encode($postData);
        $result=curl_post($url,$postData,TRUE);
        log_message('debug','send_kf_msg result: '.var_export($result,TRUE));
        if(! $result) return;
        $result=json_decode($result);
        if(isset($result->errcode) && $result->errcode!=0){
            log_message('error','add_kf_account: '.var_export($result,TRUE));
            return;
        }
        return $result;
    }

    //模板消息-设置行业
    public function template_api_set_industry($mchId){
        $merchant=$this->CI->merchant_model->get($mchId);
        if(!$merchant) return NULL;
        if($merchant->wxAuthStatus!=1) return NULL;
        $url='https://api.weixin.qq.com/cgi-bin/template/api_set_industry?access_token='.$merchant->wxAuthorizerAccessToken;
        //1互联网电子商务 31消费品
        $postData='{
            "industry_id1":"1",
            "industry_id2":"31"
        }';
        $result=curl_post($url,$postData,TRUE);
        if(! $result) return NULL;
        $result=json_decode($result);
        if(isset($result->errcode) && $result->errcode!=0){
            log_message('error','template_api_set_industry: '.var_export($result,TRUE));
            return NULL;
        }
        log_message('debug','template_api_set_industry result: '.var_export($result,TRUE));
        return $result;
    }
    //模板消息-获取行业
    public function template_get_industry($mchId){
        $merchant=$this->CI->merchant_model->get($mchId);
        if(!$merchant) {
            log_message('error','template_get_industry merchant 查询失败:');
            return NULL;
        }
        if($merchant->wxAuthStatus!=1) {
            log_message('error','template_get_industry wxAuthStatus 公众号未授权');
            return NULL;
        }
        $url='https://api.weixin.qq.com/cgi-bin/template/get_industry?access_token='.$merchant->wxAuthorizerAccessToken;
        //1互联网电子商务 31消费品
        $postData=NULL;
        $result=curl_post($url,$postData,TRUE);
        if(! $result) {
            log_message('error','template_get_industry 微信接口：'.var_export($result,TRUE));
            return NULL;
        }
        $result=json_decode($result);
        if(isset($result->errcode) && $result->errcode!=0){
            log_message('error','template_get_industry errcode: '.var_export($result,TRUE));
            return NULL;
        }
        return $result;
    }
    //模板消息-获得模板ID
    public function template_api_add_template($mchId,$templateShortId){
        $merchant=$this->CI->merchant_model->get($mchId);
        if(!$merchant) return NULL;
        if($merchant->wxAuthStatus!=1) return NULL;
        $url='https://api.weixin.qq.com/cgi-bin/template/api_add_template?access_token='.$merchant->wxAuthorizerAccessToken;
        $postData='{
           "template_id_short":"'.$templateShortId.'"
        }';
        $result=curl_post($url,$postData,TRUE);
        if(! $result) return NULL;
        $result=json_decode($result);
        if(isset($result->errcode) && $result->errcode!=0){
            log_message('error','template_api_add_template: '.var_export($result,TRUE));
            return NULL;
        }
        log_message('debug','template_api_add_template result: '.var_export($result,TRUE));
        return $result;
    }
    //模板消息-获取模板列表
    public function template_get_all_private_template($mchId){
        $merchant=$this->CI->merchant_model->get($mchId);
        if(!$merchant) return NULL;
        if($merchant->wxAuthStatus!=1) return NULL;
        $url='https://api.weixin.qq.com/cgi-bin/template/get_all_private_template?access_token='.$merchant->wxAuthorizerAccessToken;
        $postData=NULL;
        $result=curl_post($url,$postData,TRUE);
        if(! $result) return NULL;
        $result=json_decode($result);
        if(isset($result->errcode) && $result->errcode!=0){
            log_message('error','template_get_all_private_template: '.var_export($result,TRUE));
            return NULL;
        }
        log_message('debug','template_get_all_private_template result: '.var_export($result,TRUE));
        return $result;
    }

    //模板消息生成格式数据$mchId int 企业ID， $tplName string 模板名称 , $msgData array 消息数据
    public function template_format_data($mchId,$tplName,$msgData){
        //消息模板名称
        $template_tpl_title=[
            //获得红包
            'get_redpacket'=>'扫码结果通知',
            'get_card'=>'扫码结果通知',
            'get_card_youzan'=>'扫码结果通知',
            'get_point'=>'扫码结果通知',
            'kf_notice'=>'服务进度提醒'
        ];
        //消息数据模板
        $template_tpl_data=[
            //获得红包：first（消息标题），keynote1（扫码结果），keynote2（活动名称），keynote3（活动时间），keynote4（活动内容），remark（消息备注）
            //$msgData格式为： ['恭喜您中奖啦！','XXX活动名称','1.54元','请到微信菜单：『我的帐户』-『我的红包』界面进行提现操作。']
            'get_redpacket'=>'{
                "first": {"value":"%%#%%","color":"#173177"},
                "keynote1": {"value":"%%#%%","color":"#173177"},
                "keynote2": {"value":"%%#%%","color":"#173177"},
                "keynote3": {"value":"%%#%%","color":"#173177"},
                "keynote4": {"value":"%%#%%","color":"#173177"},
                "remark":{"value":"%%#%%","color":"#173177"}
            }',
            'get_card'=>'{
                "first": {"value":"%%#%%","color":"#173177"},
                "keynote1": {"value":"%%#%%","color":"#173177"},
                "keynote2": {"value":"%%#%%","color":"#173177"},
                "keynote3": {"value":"%%#%%","color":"#173177"},
                "keynote4": {"value":"%%#%%","color":"#173177"},
                "remark":{"value":"%%#%%","color":"#173177"}
            }',
            'get_card_youzan'=>'{
                "first": {"value":"%%#%%","color":"#173177"},
                "keynote1": {"value":"%%#%%","color":"#173177"},
                "keynote2": {"value":"%%#%%","color":"#173177"},
                "keynote3": {"value":"%%#%%","color":"#173177"},
                "keynote4": {"value":"%%#%%","color":"#173177"},
                "remark":{"value":"%%#%%","color":"#173177"}
            }',
            'get_point'=>'{
                "first": {"value":"%%#%%","color":"#173177"},
                "keynote1": {"value":"%%#%%","color":"#173177"},
                "keynote2": {"value":"%%#%%","color":"#173177"},
                "keynote3": {"value":"%%#%%","color":"#173177"},
                "keynote4": {"value":"%%#%%","color":"#173177"},
                "remark":{"value":"%%#%%","color":"#173177"}
            }',
            'kf_notice'=>'{
                "first": {"value":"%%#%%","color":"#173177"},
                "keyword1": {"value":"%%#%%","color":"#173177"},
                "keyword2": {"value":"%%#%%","color":"#173177"},
                "keyword3": {"value":"%%#%%","color":"#173177"},
                "remark":{"value":"%%#%%","color":"#173177"}
            }',
        ];
        //消息模板短ID
        $template_tpl_sid=[
            //获得红包
            'get_redpacket'=>'TM206875804',
            'get_card'=>'TM206875804',
            'get_card_youzan'=>'TM206875804',
            'get_point'=>'TM206875804',
            'kf_notice'=>'OPENTM401306909'
        ];
        //消息跳转URL
        $template_tpl_url=[
            //获得红包
            'get_redpacket'=>config_item('mobile_url').'user?mch_id='.$mchId,
            'get_card'=>config_item('mobile_url').'user?mch_id='.$mchId,
            'get_card_youzan'=>config_item('mobile_url').'user?mch_id='.$mchId,
            'get_point'=>config_item('mobile_url').'user?mch_id='.$mchId,
            'kf_notice'=>'',
        ];
        $format=$template_tpl_data[$tplName];
        $formatArr=explode('%%#%%',$format);
        $dataStr='';
        $length=count($formatArr)-1;
        for($i=0;$i<$length;$i++){
            $dataStr.=$formatArr[$i].$msgData[$i];
        }
        $dataStr.=$formatArr[$length];
        $result=(object)[
            'title'=>$template_tpl_title[$tplName],
            'shortId'=>$template_tpl_sid[$tplName],
            'url'=>$template_tpl_url[$tplName],
            'message'=>$dataStr
        ];
        return $result;
    }

    //模板消息-检查模板消息
    public function template_check($mchId){
        //所有需要用到的消息模板名称
        $template_title_arr=['扫码结果通知','服务进度提醒'];
        $industry=$this->template_get_industry($mchId);
        if(!$industry) {
            log_message('error','mchid '.$mchId.' industry 获取失败');
            return NULL;
        }
        if(! ($industry->primary_industry->first_class=='IT科技' && $industry->primary_industry->second_class=='互联网|电子商务' &&
             $industry->secondary_industry->first_class=='消费品' && $industry->secondary_industry->second_class=='消费品') ){
            $setIndustry=$this->template_api_set_industry($mchId);
            if(! $setIndustry) {
                log_message('error','mchid '.$mchId.' setIndustry 获取失败');
                return NULL;
            }
        }
        $tplList=$this->template_get_all_private_template($mchId);
        if(!$tplList) {
            log_message('error','mchid '.$mchId.' tplList 不存在');
            return NULL;
        }
        foreach ($tplList->template_list as $k => $v) {
            if(in_array($v->title,$template_title_arr)){
                $sql="insert into mch_templates(mchId,template_id,title) values($mchId,'$v->template_id','$v->title') on duplicate key update template_id='$v->template_id'";
                $this->CI->db->query($sql);
            }
        }
    }

    //模板消息-发送模板消息
    public function template_send($mchId,$openid,$templateFormatData){
        $merchant=$this->CI->merchant_model->get($mchId);
        if(!$merchant) {
            log_message('error','mchid '.$mchId.' 企业信息获取失败');
            return NULL;
        }
        if($merchant->wxAuthStatus!=1) {
            log_message('error','mchid '.$mchId.' 公众号未授权');
            return NULL;
        }
        $tplList=$this->CI->db->query("select * from mch_templates where mchId=$mchId")->result();
        if(!$tplList) {
            log_message('error','mchid '.$mchId.' tplList 不存在');
        }
        $templatePrivateId=NULL;
        foreach ($tplList as $key => $value) {
            if($value->title==$templateFormatData->title){
                $templatePrivateId=$value->template_id;
            }
        }
        if($templatePrivateId===NULL){
            $tplInfo=$this->template_api_add_template($mchId,$templateFormatData->shortId);
            if(!$tplInfo) {
                log_message('error','mchid '.$mchId.' tplInfo 获取失败');
                return NULL;
            }
            if(gettype($tplInfo)!='object'){
                $tplInfo=json_decode($tplInfo);
            }
            $templatePrivateId=$tplInfo->template_id;
            $this->template_check($mchId);
        }
        $url='https://api.weixin.qq.com/cgi-bin/message/template/send?access_token='.$merchant->wxAuthorizerAccessToken;
        $postData= '{
            "touser":"'.$openid.'",
            "template_id":"'.$templatePrivateId.'",
            "url":"'.$templateFormatData->url.'",
            "data":'.$templateFormatData->message.'
        }';
        log_message('debug','template_send postData: '.var_export($postData,TRUE));
        // $postData=json_encode($postData);
        $result=curl_post($url,$postData,TRUE);
        if(! $result) {
            log_message('error','template_send result: '.var_export($result,TRUE));
            return NULL;
        }
        $result=json_decode($result);
        if(isset($result->errcode) && $result->errcode!=0){
            log_message('error','template_send result: '.var_export($result,TRUE));
            return NULL;
        }
        return $result;
    }
    //模板消息-预发送模板消息
    public function template_pre_send($mchId,$openid,$formatMsg){
        $newMsg=clone $formatMsg;
        $newMsg->message=json_encode($newMsg->message,JSON_UNESCAPED_UNICODE);
        $newMsg->message=str_replace('\r\n','',$newMsg->message);
        $newMsg->message=trim($newMsg->message,'"');
        $this->CI->db->query("insert into user_template_msg(mchId,openid,formatMsg,createTime,updateTime) values($mchId,'$openid','".json_encode($newMsg,JSON_UNESCAPED_UNICODE)."',".time().",".time().")");
        return $this->CI->db->insert_id();
    }
    //模板消息-预警消息发送
    public function template_warning_send($mchId,$formatMsg){
        $warnAccount=$this->CI->db->query("select u.openid openid from warning_accounts a inner join users u on u.id=a.userId where u.mchId=$mchId and a.mchId=$mchId")->result();
        if(! $warnAccount) return;
        foreach($warnAccount as $k=>$v){
            $this->template_pre_send($mchId,$v->openid,$formatMsg);
        }
    }

    //摇一摇开通申请
    public function shakearound_register($mchId){
        $merchant=$this->CI->merchant_model->get($mchId);
        if(!$merchant) return NULL;
        if($merchant->wxAuthStatus!=1) return NULL;
        $url='https://api.weixin.qq.com/shakearound/account/register?access_token='.$merchant->wxAuthorizerAccessToken;
        if(empty($merchant->contact) || empty($merchant->phoneNum) || empty($merchant->mail) ){
            return NULL;
        }
        $postData="{
            \"name\": \"$merchant->contact\",
            \"phone_number\": \"$merchant->phoneNum\",
            \"email\": \"$merchant->mail\",
            \"industry_id\": \"0101\",
            \"qualification_cert_urls\": [],
            \"apply_reason\": \"用户扫商品二维码，参与交互活动，希望增加摇一摇的互动方式，提高用户体验、丰富交互方式。\"
        }";
        log_message('debug','shakearound_register data: '.var_export($postData,TRUE));
        $result=curl_post($url,$postData,TRUE);
        if(! $result) return NULL;
        $result=json_decode($result);
        if(isset($result->errcode) && $result->errcode!=0){
            log_message('error','shakearound_register: '.var_export($result,TRUE));
        }
        return $result;
    }
    //摇一摇审核状态查询
    public function shakearound_auditstatus($mchId){
        $merchant=$this->CI->merchant_model->get($mchId);
        if(!$merchant) return NULL;
        if($merchant->wxAuthStatus!=1) return NULL;
        $url='https://api.weixin.qq.com/shakearound/account/auditstatus?access_token='.$merchant->wxAuthorizerAccessToken;
        $postData=NULL;
        $result=curl_post($url,$postData,TRUE);
        if(! $result) return NULL;
        $result=json_decode($result);
        if(isset($result->errcode) && $result->errcode!=0){
            log_message('error','shakearound_auditstatus: '.var_export($result,TRUE));
        }
        return $result;
    }
    //公众号的所有api调用（包括第三方帮其调用）次数进行清零
    public function clear_quota($mchId){
        $merchant=$this->CI->merchant_model->get($mchId);
        if(!$merchant) return NULL;
        if($merchant->wxAuthStatus!=1) return NULL;
        $url='https://api.weixin.qq.com/cgi-bin/clear_quota?access_token='.$merchant->wxAuthorizerAccessToken;
        $postData="{\"appid\":\"$merchant->wxAppId\"}";
        $result=curl_post($url,$postData,TRUE);
        if(! $result) return NULL;
        $result=json_decode($result);
        if(isset($result->errcode) && $result->errcode!=0){
            log_message('error','clear_quota: '.var_export($result,TRUE));
        }
        return $result;
    }
    //平台业务处理函数======================================end===============================================
}
