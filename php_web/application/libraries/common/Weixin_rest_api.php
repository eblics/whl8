<?php
/**
* 微信功能类
*/
class Weixin_rest_api {

    const WX_URL_INFO = "https://open.weixin.qq.com/connect/oauth2/authorize?&appid=%s&redirect_uri=%s";
    const WX_URL_BASE = "https://open.weixin.qq.com/connect/oauth2/authorize?&appid=%s&redirect_uri=%s";

    const WX_URL_CODE = 'https://api.weixin.qq.com/sns/oauth2/';

    // 定义需要用户手动授权的企业编号
    const NEED_USER_INFO = [0, 173];

    var $CI;

    public function __construct() {
        $this->CI =& get_instance();
        $this->CI->load->model('user_model');
        $this->CI->load->model('merchant_model');
        $this->CI->load->model('variables_model');
    }

    // public function login($mchInfo, $from = FALSE) {
    //     if (empty($mchInfo->wxAppId)) {
    //         exit('<script>alert("微信公众号未配置...")</script>');
    //     }
    //     if ($mchInfo->id == -1) {
    //         $this->CI->session->unset_userdata('common_openid_-1');
    //     } else {
    //         $this->CI->session->unset_userdata('openid_' . $mchInfo->id);
    //     }
    //     $http_type = 'http://';
    //     if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') {
    //         $http_type = 'https://';
    //     }
    //     if (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https') {
    //         $http_type = 'https://';
    //     }
    //     if (! $from) {
    //         $from = $http_type . $_SERVER['HTTP_HOST'] . $_SERVER["REQUEST_URI"];
    //     }
    //     $from = urlencode($from);
    //     $currentUri = $http_type . $_SERVER['HTTP_HOST'];
    //     $redirect_uri = urlencode($currentUri . '/weixin/code/' . $mchInfo->id . '/' . $from);
    //     if ($mchInfo->wxAuthStatus == 1) {
    //         $component_appid = $this->CI->config->item('wx3rd_appid');
    //         if ($mchInfo->id == -1 || ! in_array($mchInfo->id, self::NEED_USER_INFO)) {
    //             $str = '&response_type=code&scope=snsapi_base&component_appid=%s#wechat_redirect';
    //             $url = self::WX_URL_BASE . $str;
    //         } else {
    //             $str = '&response_type=code&scope=snsapi_userinfo&component_appid=%s#wechat_redirect';
    //             $url = self::WX_URL_INFO . $str;
    //         }
    //         $url_base = sprintf($url, $mchInfo->wxAppId, $redirect_uri, $component_appid);
    //     } else {
    //         if ($mchInfo->id == -1 || ! in_array($mchInfo->id, self::NEED_USER_INFO)) {
    //             $url = self::WX_URL_BASE . '&response_type=code&scope=snsapi_base#wechat_redirect';
    //         } else {
    //             $url = self::WX_URL_INFO . '&response_type=code&scope=snsapi_userinfo#wechat_redirect';
    //         }
    //         $url_base = sprintf($url, $mchInfo->wxAppId, $redirect_uri);
    //     }
    //     redirect($url_base);
    //     exit();
    // }

    function base64_url_encode($input) {
        return strtr(base64_encode($input), '+\=', '-_:');
    }

    function base64_url_decode($input) {
        return base64_decode(strtr($input, '-_:', '+\='));
    }

    // public function base($mchInfo,$from){

    // }

    // public function code($code, $mchInfo, $from){
    //     if ($mchInfo->wxAuthStatus == 1) {
    //         $component_appid = $this->CI->config->item('wx3rd_appid');
    //         $component_access_token = $this->CI->variables_model->get('component_access_token');
    //         if (! $component_access_token) {
    //             error('weixin-rest-api - fail: no component_access_token');
    //             return;
    //         }
    //         $str = "component/access_token?appid=%s&code=%s&grant_type=authorization_code&";
    //         $str .= 'component_appid=%s&component_access_token=%s';
    //         $url = self::WX_URL_CODE . $str;
    //         $url = sprintf($url, $mchInfo->wxAppId, $code, $component_appid, $component_access_token->val);
    //     } else {
    //         $str = "access_token?appid=%s&secret=%s&code=%s&grant_type=authorization_code";
    //         $url = self::WX_URL_CODE . $str;
    //         $url = sprintf($url, $mchInfo->wxAppId, $mchInfo->wxAppSecret, $code);
    //     }
    //     $result = $this->httpGet($url);
    //     $token_info = json_decode($result, 1);
    //     if (! isset($token_info)) {
    //         $token_info = [];
    //     }
    //     if (! array_key_exists('openid', $token_info)) {
    //         error('weixin-rest-api - get openid fail:'. json_encode($result));
    //         return (object)['errcode' => 1, 'errmsg' => '微信服务器连接失败，请重试'];
    //     }
    //     $openid = $token_info['openid'];
    //     if ($mchInfo->id == -1) {
    //         $wxUserInfo = $this->get_userinfo($mchInfo->wxAppId, $openid);
    //         $this->CI->session->set_userdata('common_mchId', -1);
    //         $this->CI->session->set_userdata('common_openid_-1', $openid);
    //         $sql = "SELECT * FROM users_common WHERE openid = ? AND mchId = ?";
    //         $user = $this->CI->db->query($sql, [$openid, -1])->row();
    //         if (! isset($user)) {
    //             $user = new stdClass();
    //             $user->openid = $openid;
    //             $user->createTime = time();
    //             $user->updateTime = time();
    //             if (! isset($wxUserInfo->errcode) && $wxUserInfo->subscribe == 1) {
    //                 $user->nickName = $wxUserInfo->nickname;
    //                 $user->sex      = $wxUserInfo->sex;
    //                 $user->city     = $wxUserInfo->city;
    //                 $user->province = $wxUserInfo->province;
    //                 $user->country  = $wxUserInfo->country;
    //                 $user->headimgurl = $wxUserInfo->headimgurl;
    //                 $user->subscribe  = $wxUserInfo->subscribe;
    //                 $user->subscribe_time = $wxUserInfo->subscribe_time;
    //             } else {
    //                 $user->nickName = '未知昵称';
    //                 $user->headimgurl = '/static/images/default_headimg.png';
    //                 $user->subscribe = 0;
    //             }
    //             $this->CI->db->insert('users_common', $user);
    //             $user->id = $this->CI->db->insert_id();
    //             $user->commonStatus = 0;
    //         } else {
    //             if (! isset($wxUserInfo->errcode) && $wxUserInfo->subscribe == 1) {
    //                 $updateData = [];
    //                 $updateData['headimgurl'] = $wxUserInfo->headimgurl;
    //                 $updateData['nickName'] = $wxUserInfo->nickname;
    //                 $updateData['subscribe'] = $wxUserInfo->subscribe;
    //                 $updateData['sex'] = $wxUserInfo->sex;
    //                 $updateData['city'] = $wxUserInfo->city;
    //                 $updateData['province'] = $wxUserInfo->province;
    //                 $updateData['country'] = $wxUserInfo->country;
    //                 $updateData['subscribe_time'] = $wxUserInfo->subscribe_time;
    //                 $updateData['updateTime'] = time();
    //                 $this->CI->db->where('id', $user->id)->update('users_common', $updateData);
    //             }
    //         }
    //     } else {
    //         if (in_array($mchInfo->id, self::NEED_USER_INFO)) {
    //             $wxUserInfo = $this->oauth2_get_userinfo($token_info['access_token'], $openid);
    //         } else {
    //             $wxUserInfo = $this->get_userinfo($mchInfo->wxAppId, $openid);
    //         }
    //         $this->CI->session->set_userdata('openid_' . $mchInfo->id,$openid);
    //         $this->CI->session->set_userdata('mchId', $mchInfo->id);
    //         $sql = "SELECT * FROM users WHERE openid = ? AND mchId = ?";
    //         $user = $this->CI->db->query($sql, [$openid, $mchInfo->id])->row();
    //         if (! isset($user)) {
    //             $user = new stdClass();
    //             $user->mchId        = $mchInfo->id;
    //             $user->openid       = $openid;
    //             $user->createTime   = time();
    //             $user->updateTime   = time();
    //             if (! isset($wxUserInfo->subscribe)) {
    //                 $wxUserInfo->subscribe = 0;
    //             }
    //             if (! isset($wxUserInfo->errcode) && ($wxUserInfo->subscribe == 1 || isset($wxUserInfo->nickname))) {
    //                 $user->nickName = $wxUserInfo->nickname;
    //                 $user->sex      = $wxUserInfo->sex;
    //                 $user->city     = $wxUserInfo->city;
    //                 $user->province = $wxUserInfo->province;
    //                 $user->country  = $wxUserInfo->country;
    //                 $user->headimgurl = $wxUserInfo->headimgurl;
    //                 if ($wxUserInfo->subscribe == 1) {
    //                     $user->subscribe  = $wxUserInfo->subscribe;
    //                     $user->subscribe_time = $wxUserInfo->subscribe_time;
    //                 }
    //             } else {
    //                 $user->nickName = '未知昵称';
    //                 $user->headimgurl = '/static/images/default_headimg.png';
    //                 $user->subscribe = 0;
    //             }
    //             $user->id = $this->CI->user_model->save($user);
    //         } else {
    //             // 更新用户信息
    //             if (! isset($wxUserInfo->subscribe)) {
    //                 $wxUserInfo->subscribe = 0;
    //             }
    //             if (! isset($wxUserInfo->errcode) && ($wxUserInfo->subscribe == 1 || isset($wxUserInfo->nickname))) {
    //                 $updateData = [];
    //                 $updateData['headimgurl'] = $wxUserInfo->headimgurl;
    //                 $updateData['nickName'] = $wxUserInfo->nickname;
    //                 $updateData['sex'] = $wxUserInfo->sex;
    //                 $updateData['city'] = $wxUserInfo->city;
    //                 $updateData['province'] = $wxUserInfo->province;
    //                 $updateData['country'] = $wxUserInfo->country;
    //                 $updateData['updateTime'] = time();
    //                 if ($wxUserInfo->subscribe == 1) {
    //                     $user->subscribe  = $wxUserInfo->subscribe;
    //                     $user->subscribe_time = $wxUserInfo->subscribe_time;
    //                 }
    //                 $this->CI->db->where('id', $user->id)->update('users', $updateData);
    //             }
    //         }
    //     }
    //     $this->CI->session->set_userdata('openid_' . $mchInfo->id, $openid);
    //     $this->CI->session->set_userdata('current_user_'. $mchInfo->id, $user);
    //     redirect(urldecode($from));
    //     exit();
    // }

    /**
    * 红包方法 - 取指定长度随机字符串
    * @num number 长度
    */
    private function get_random_str($num){
        $char='A,B,C,D,E,F,G,H,I,J,K,L,M,N,O,P,Q,R,S,T,U,V,W,X,Y,Z,0,1,2,3,4,5,6,7,8,9';
        $charArr=explode(',',$char);
        shuffle($charArr);
        $newarr=array_rand($charArr,$num);
        $str = '';
        for($i = 0; $i < $num; $i++){
            $str .= $charArr[$newarr[$i]];
        }
        return $str;
    }

    /**
    * 红包方法 - 取当前日期、时间、随机数组成字符串
    */
    private function get_date_str(){
        $dataStr=date('Ymd');
        $timeStamp=substr((time().''),-5);
        $rand=rand(10000,99999);
        return $dataStr.$timeStamp.$rand;
    }

    /**
    * 红包方法 - 数组格式化为xml格式
    * @arr array 数组数据
    */
    private function array_to_xml($arr) {
        $xml = '<xml>';
        $fmt = '<%s><![CDATA[%s]]></%s>';
        // $fmt = '<%s>%s</%s>';
        foreach($arr as $key=>$val){
            $xml.=sprintf($fmt, $key, $val, $key);
        }
        $xml.='</xml>';
        return $xml;
    }

    /**
    * 红包方法 - curl 带证书post请求
    * @url string 请求url
    * @arrayParam array 请求数据
    * @cert object 证书路径
    */
    function curl_post_ssl($url, $arrayParam,$cert) {
        $vars=$this->array_to_xml($arrayParam);
        // print_r($vars);exit;
        $ch = curl_init();
        //超时时间
        curl_setopt($ch,CURLOPT_TIMEOUT,30);
        curl_setopt($ch,CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch,CURLOPT_URL,$url);
        curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,false);
        curl_setopt($ch,CURLOPT_SSL_VERIFYHOST,false);
        //默认格式为PEM，可以注释
        curl_setopt($ch,CURLOPT_SSLCERT, config_item('ssl_file_path') . $cert->certPath);
        curl_setopt($ch,CURLOPT_SSLKEY, config_item('ssl_file_path') . $cert->keyPath);
        curl_setopt($ch,CURLOPT_CAINFO, config_item('ssl_file_path') . $cert->caPath);
        $aHeader=[];
        if( count($aHeader) >= 1 ){
            curl_setopt($ch, CURLOPT_HTTPHEADER, $aHeader);
        }
        curl_setopt($ch,CURLOPT_POST, 1);
        curl_setopt($ch,CURLOPT_POSTFIELDS,$vars);
        $data = curl_exec($ch);
        if($data){
            curl_close($ch);
            return $data;
        }else {
            $error = curl_errno($ch);
            log_message('ERROR','红包发放curl_post_ssl失败 '. json_encode($error));
            curl_close($ch);
            return false;
        }
    }

    /**
    * 发送普通红包
    * @data object  对象参数
    */
    private function send_normal_redpack($data){
        //微信服务api url
        $postUrl = 'https://api.mch.weixin.qq.com/mmpaymkttransfers/sendredpack';
        $postData = [
            // 随机字符串
            'nonce_str'     => time().$this->get_random_str(22),
            // 商户订单号
            'mch_billno'    => $data->mch_id.$this->get_date_str(),
            // 商户号
            'mch_id'        => $data->mch_id,
            // 公众账号appid
            'wxappid'       => $data->wxappid,
            // 商户名称
            'send_name'     => $data->send_name,
            // 用户openid
            're_openid'     => $data->re_openid,
            // 付款金额
            'total_amount'  => $data->total_amount,
            // 红包发放总人数
            'total_num'     => 1,
            // 红包祝福语
            'wishing'       => $data->wishing,
            // 调用接口的机器Ip地址
            'client_ip'     => $this->get_server_ip(),
            // 活动名称
            'act_name'      => $data->act_name,
            // 备注
            'remark'        => $data->remark,
            // 最小红包金额
            'min_value'     => $data->total_amount,
            // 最大红包金额
            'max_value'     => $data->total_amount,

        ];
        if (in_array(intval($data->mchId), [112, 119, 126, 167, 169, 171, 245])) {
            $postData['scene_id'] = 'PRODUCT_1';
        }
        if (config_item('mobile_url') === 'http://dev.m.lsa0.cn/') {
            $postData['total_amount'] = 60; // 一毛钱
        }
        ksort($postData);
        $urlStr = http_build_query($postData);
        $urlStr = urldecode($urlStr);
        $stringSignTemp = $urlStr . '&key=' . $data->key; // key 支付api密钥
        $sign = strtoupper(md5($stringSignTemp)); // 签名
        $postData['sign'] = $sign;
        $cert=(object)[
            //支付证书cert
            'certPath'=>$data->certPath,
            //支付证书key
            'keyPath'=>$data->keyPath,
            //支付证书rootca
            'caPath'=>$data->caPath
        ];
        $result=$this->curl_post_ssl($postUrl, $postData, $cert);
        $responseObj = simplexml_load_string($result, 'SimpleXMLElement', LIBXML_NOCDATA);
        if(! $responseObj){
            log_message('error','send_normal_redpack :'.var_export($result,true));
        } else {
            debug("hls-send-red-packet: ". json_encode($responseObj));
        }

        return $responseObj;
    }

    /**
    * 发送裂变红包
    * @data object  对象参数
    */
    private function send_group_redpack($data){
        //微信服务api url
        $postUrl='https://api.mch.weixin.qq.com/mmpaymkttransfers/sendgroupredpack';
        $postData=[
            //商户号
            'mch_id'=>$data->mch_id,
            //公众账号appid
            'wxappid'=>$data->wxappid,
            //商户名称
            'send_name'=>$data->send_name,
            //随机字符串
            'nonce_str'=>time().$this->get_random_str(22),
            //商户订单号
            'mch_billno'=>$data->mch_id.$this->get_date_str(),
            //活动名称
            'act_name'=>$data->act_name,
            //备注
            'remark'=>$data->remark,
            //红包祝福语
            'wishing'=>$data->wishing,
            //付款金额
            'total_amount'=>$data->total_amount,
            //红包金额设置方式
            'amt_type'=>'ALL_RAND',
            //红包发放总人数
            'total_num'=>$data->total_num,
            //用户openid
            're_openid'=>$data->re_openid
        ];
        ksort($postData);
        $urlStr=http_build_query($postData);
        $urlStr=urldecode($urlStr);
        $stringSignTemp=$urlStr.'&key='.$data->key;//key 支付api密钥
        $sign=strtoupper(md5($stringSignTemp));//签名
        $postData['sign']=$sign;
        $cert=(object)[
            //支付证书cert
            'certPath'=>$data->certPath,
            //支付证书key
            'keyPath'=>$data->keyPath,
            //支付证书rootca
            'caPath'=>$data->caPath
        ];
        $result=$this->curl_post_ssl($postUrl,$postData,$cert);
        $responseObj = simplexml_load_string($result, 'SimpleXMLElement', LIBXML_NOCDATA);
        return $responseObj;
    }

    /**
    * 发送红包
    * @param object $data
    */
    public function send_redpack($data){
        if((int)$data->redpackType===1){
            return $this->send_group_redpack($data);
        }else if((int)$data->redpackType===0){
            return $this->send_normal_redpack($data);
        }
    }

    /**
    * 从本地数据库获取 access_token
    */
    private function access_token($appId){
        $mchInfo=$this->CI->merchant_model->get_by_appid($appId);//商户信息
        if($mchInfo->wxAuthStatus==1){
            return $mchInfo->wxAuthorizerAccessToken;
        }
        return $mchInfo->baseToken;
    }

    // 获取用户信息，不需要用户授权，但需要用户关注公众号
    public function get_userinfo($appId, $openid) {
        $access_token=$this->access_token($appId);
        $url = "https://api.weixin.qq.com/cgi-bin/user/info?access_token=%s&openid=%s&lang=zh_CN";
        $url = sprintf($url, $access_token, $openid);
        $wxUser = json_decode($this->httpGet($url));
        return $wxUser;
    }

    // 获取用户信息，需要用户手动授权获取
    public function oauth2_get_userinfo($token, $openid) {
        $url = "https://api.weixin.qq.com/sns/userinfo?access_token=%s&openid=%s&lang=zh_CN";
        $url = sprintf($url, $token, $openid);
        $wxUser = json_decode($this->httpGet($url));
        return $wxUser;
    }

    /**
    * 设置消费者菜单方法
    */
    public function create_menu($mchId, $menu_data, $type = MchWxEnum::Mobile){
        $access_token = $this->getAccessToken($mchId, $type);
        $url="https://api.weixin.qq.com/cgi-bin/menu/create?access_token=".$access_token;
        $re=curl_post($url,$menu_data);
        return $re;
    }

    /**
    * 获取消费者菜单方法
    */
    public function get_menu($mchId, $type = MchWxEnum::Mobile) {
        $access_token = $this->getAccessToken($mchId, $type);
        $url = "https://api.weixin.qq.com/cgi-bin/menu/get?access_token=". $access_token;
        $resp = curl_post($url);
        return $resp;
    }

    /**
    *删除消费者菜单方法
    */
    public function delete_menu($mchId, $type = MchWxEnum::Mobile) {
        $access_token = $this->getAccessToken($mchId, $type);
        $url = "https://api.weixin.qq.com/cgi-bin/menu/delete?access_token=". $access_token;
        $re=curl_post($url);
        return $re;
    }

    /**
     * 获取企业微信AccessToken
     */
    private function getAccessToken($mchId, $type) {
        $merchant = $this->CI->merchant_model->get($mchId);
        if ($type == MchWxEnum::Mobile) {
            if ($merchant->wxAuthStatus == BoolEnum::Yes) {
                $access_token = $merchant->wxAuthorizerAccessToken;
            } else {
                $access_token = $merchant->baseToken;
            }
        } else {
            if ($merchant->wxAuthStatus_shop == BoolEnum::Yes) {
                $access_token = $merchant->wxAuthorizerAccessToken_shop;
            } else {
                $access_token = $merchant->baseToken_shop;
            }
        }
        return $access_token;
    }

    /**
    * 向微信服务器请求accessToken
    */
    // public function get_access_token($appId,$appSecret) {
    //     $url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=$appId&secret=$appSecret";
    //     $re=curl_get($url);
    //     $res = json_decode($re,true);
    //     return isset($res['access_token'])?$res['access_token']:NULL;
    //  }

    public function mch_pay($data){
        //微信服务api url
        $postUrl='https://api.mch.weixin.qq.com/mmpaymkttransfers/promotion/transfers';
        $postData=[
            //商户号
            'mchid'=>$data->wxMchId,
            //公众账号appid
            'mch_appid'=>$data->wxAppId,
            //设备号
            'device_info'=>'hls',
            //随机字符串
            'nonce_str'=>time().$this->get_random_str(22),
            //商户订单号
            'partner_trade_no'=>'mch'.$data->mchId.'pay'.$this->get_date_str(),
            //用户openid
            'openid'=>$data->openid,
            //校验用户姓名选项
            'check_name'=>'NO_CHECK',
            //收款用户姓名
            //'re_user_name'=>'',
            //付款金额(分)
            'amount'=>$data->amount,
            //企业付款描述信息
            'desc'=>mb_substr($data->desc,0,30),
            //调用接口的机器Ip地址
            'spbill_create_ip'=>$this->get_server_ip()
        ];
        ksort($postData);
        $urlStr=http_build_query($postData);
        $urlStr=urldecode($urlStr);
        $stringSignTemp=$urlStr.'&key='.$data->wxPayKey;//key 支付api密钥
        $sign=strtoupper(md5($stringSignTemp));//签名
        $postData['sign']=$sign;
        $cert=(object)[
            //支付证书cert
            'certPath'=>$data->certPath,
            //支付证书key
            'keyPath'=>$data->keyPath,
            //支付证书rootca
            'caPath'=>$data->caPath
        ];
        log_message('debug','mchpay data:'.var_export($postData,true));
        $result=$this->curl_post_ssl($postUrl,$postData,$cert);
        $responseObj = simplexml_load_string($result, 'SimpleXMLElement', LIBXML_NOCDATA);
        return $responseObj;
    }

    function get_server_ip() {
        if (isset($_SERVER)) {
            if(isset($_SERVER['SERVER_ADDR'])){
                $server_ip = $_SERVER['SERVER_ADDR'];
            }else{
                $server_ip = $this->CI->config->item('api3rd_server_ip');
            }
            return $server_ip;
        }
        $server_ip = $this->CI->config->item('api3rd_server_ip');
        return $server_ip;
    }

    /**
    * 查询红包状态
    */
    public function check_redpacket($data){
        //微信服务api url
        $postUrl='https://api.mch.weixin.qq.com/mmpaymkttransfers/gethbinfo';
        $postData=[
            //公众号appid
            'appid'=>$data->wxAppId,
            //商户号
            'mch_id'=>$data->wxMchId,
            //商户订单号
            'mch_billno'=>$data->wxMchBillno,
            //随机字符串
            'nonce_str'=>time().$this->get_random_str(22),
            //订单类型(MCHT:通过商户订单号获取红包信息)
            'bill_type'=>'MCHT'
        ];
        ksort($postData);
        $urlStr=http_build_query($postData);
        $urlStr=urldecode($urlStr);
        $stringSignTemp=$urlStr.'&key='.$data->wxPayKey;//key 支付api密钥
        $sign=strtoupper(md5($stringSignTemp));//签名
        $postData['sign']=$sign;
        $cert=(object)[
            //支付证书cert
            'certPath'=>$data->certPath,
            //支付证书key
            'keyPath'=>$data->keyPath,
            //支付证书rootca
            'caPath'=>$data->caPath
        ];
        log_message('debug','check_redpacket data:'.var_export($postData,true));
        $result=$this->curl_post_ssl($postUrl,$postData,$cert);
        $responseObj = simplexml_load_string($result, 'SimpleXMLElement', LIBXML_NOCDATA);
        return $responseObj;
    }

    /**
    * 获取公众号已创建的标签
    */
    public function get_tags($mchId,$type=1) {
        $merchant=$this->CI->merchant_model->get($mchId);
        if ($type === 1) {
            $access_token = $merchant->baseToken;
            if ($merchant->wxAuthStatus == BoolEnum::Yes) {
                $access_token = $merchant->wxAuthorizerAccessToken;
            }
        } else {
            $access_token = $merchant->wxAuthorizerAccessToken_shop;
        }
        $url = "https://api.weixin.qq.com/cgi-bin/tags/get?access_token=".$access_token;
        $re = curl_get($url);
        $res = json_decode($re);
        if (isset($res->errcode) && $res->errcode != 0) {
            error("weixin-rest-api-get-tags - fail: ". json_encode($res));
            throw new Exception("获取公众号已创建的标签失败", 1);
        }
        return $res;
     }

     /**
    * 向公众号创建标签
    */
    public function add_tags($mchId,$name,$type=1) {
        $merchant=$this->CI->merchant_model->get($mchId);
        if($type===1){
            $access_token=$merchant->baseToken;
            if($merchant->wxAuthStatus==1){
                $access_token = $merchant->wxAuthorizerAccessToken;
            }
        }else{
            $access_token=$merchant->wxAuthorizerAccessToken_shop;
        }
        $tag_data=json_encode(['tag'=>['name'=>$name]],JSON_UNESCAPED_UNICODE);
        $url = "https://api.weixin.qq.com/cgi-bin/tags/create?access_token=".$access_token;
        $re=curl_post($url,$tag_data);
        $res = json_decode($re);
        return $res;
     }

     /**
    * 向公众号更新标签
    */
    public function update_tags($mchId,$tagId,$name,$type=1) {
        $merchant=$this->CI->merchant_model->get($mchId);
        if($type===1){
            $access_token=$merchant->baseToken;
            if($merchant->wxAuthStatus==1){
                $access_token = $merchant->wxAuthorizerAccessToken;
            }
        }else{
            $access_token=$merchant->wxAuthorizerAccessToken_shop;
        }
        $tag_data=json_encode(['tag'=>['id'=>$tagId,'name'=>$name]],JSON_UNESCAPED_UNICODE);
        $url = "https://api.weixin.qq.com/cgi-bin/tags/update?access_token=".$access_token;
        $re=curl_post($url,$tag_data);
        $res = json_decode($re);
        return $res;
     }

     /**
    * 向公众号删除标签
    */
    public function delete_tags($mchId,$tagId,$type=1) {
        $merchant=$this->CI->merchant_model->get($mchId);
        if($type===1){
            $access_token=$merchant->baseToken;
            if($merchant->wxAuthStatus==1){
                $access_token = $merchant->wxAuthorizerAccessToken;
            }
        }else{
            $access_token=$merchant->wxAuthorizerAccessToken_shop;
        }
        $tag_data=json_encode(['tag'=>['id'=>$tagId]],JSON_UNESCAPED_UNICODE);
        $url = "https://api.weixin.qq.com/cgi-bin/tags/delete?access_token=".$access_token;
        $re=curl_post($url,$tag_data);
        $res = json_decode($re);
        return $res;
     }

     /**
    * 批量给公众号用户打标签
    */
    public function tagging($mchId,$tagId,$openidArr,$type=1) {
        $merchant=$this->CI->merchant_model->get($mchId);
        if($type===1){
            $access_token=$merchant->baseToken;
            if($merchant->wxAuthStatus==1){
                $access_token = $merchant->wxAuthorizerAccessToken;
            }
        }else{
            $access_token=$merchant->wxAuthorizerAccessToken_shop;
        }
        $tag_data=json_encode(['tagid'=>$tagId,'openid_list'=>$openidArr]);
        $url = "https://api.weixin.qq.com/cgi-bin/tags/members/batchtagging?access_token=".$access_token;
        $re=curl_post($url,$tag_data);
        $res = json_decode($re);
        return $res;
     }

     private function httpGet($url) {
        if (config_item('mch_url') === 'http://dev.www.lsa0.cn/') {
            return file_get_contents('http://dev.tools.lsa0.cn/api.php?path='. base64_encode($url));
        } else {
            return file_get_contents($url);
        }
     }
}
