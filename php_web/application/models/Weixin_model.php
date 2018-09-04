<?php 
class Weixin_model extends MY_Model {

    const WX1_GET_CODE_URL      = 'https://open.weixin.qq.com/connect/oauth2/authorize?appid=%s&redirect_uri=%s&response_type=code&scope=snsapi_userinfo&state=%s#wechat_redirect';
    
    const WX2_GET_CODE_URL      = 'https://open.weixin.qq.com/connect/oauth2/authorize?appid=%s&redirect_uri=%s&response_type=code&scope=snsapi_userinfo&component_appid=%s&state=%s#wechat_redirect';
    
    const WX1_GET_TOKEN_URL     = 'https://api.weixin.qq.com/sns/oauth2/access_token?appid=%s&secret=%s&code=%s&grant_type=authorization_code';
    
    const WX2_GET_TOKEN_URL     = 'https://api.weixin.qq.com/sns/oauth2/component/access_token?appid=%s&code=%s&grant_type=authorization_code&component_appid=%s&component_access_token=%s';

    const WX1_GET_USER_INFO     = 'https://api.weixin.qq.com/sns/userinfo?access_token=%s&openid=%s&lang=zh_CN';
    
    const WX2_GET_USER_INFO     = 'https://api.weixin.qq.com/cgi-bin/user/info?access_token=%s&openid=%s&lang=zh_CN';

    const WX_GET_JS_TICKET_URL  = 'https://api.weixin.qq.com/cgi-bin/ticket/getticket?access_token=%s&type=jsapi';
    
    const WX_GET_ACCESS_TOKEN_URL   = 'https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=%s&secret=%s';
    
    public function getWxUser($url, $merchant, $isConsumer = FALSE) {
        if ($isConsumer) {
            if ($merchant->manualAuth == BoolEnum::Yes) {
                $resp = $this->getWebToken($url, $merchant, $isConsumer);
                $url = sprintf(self::WX1_GET_USER_INFO, $resp->access_token, $resp->openid);
            } else {
                $resp = $this->getWebToken($url, $merchant, $isConsumer);
                // 尝试获取用户信息
                if ($merchant->wxAuthStatus == BoolEnum::Yes) {
                    $token = $merchant->wxAuthorizerAccessToken;
                } else {
                    $token = $merchant->baseToken;
                }
                $url = sprintf(self::WX2_GET_USER_INFO, $token, $resp->openid);
            }
            $resp = json_decode(file_get_contents($url));
            debug('weixin-model-get-wx-user - merchant: '. $merchant->id);
            debug('weixin-model-get-wx-user - result: '. json_encode($resp));
            return $resp;
        } else {
            $resp = $this->getWebToken($url, $merchant, $isConsumer);
            $url = sprintf(self::WX1_GET_USER_INFO, $resp->access_token, $resp->openid);
            $resp = json_decode(file_get_contents($url));
            return $resp;
        }
    }

    /**
     * 根据会员的编号获取会员详细信息
     * @param  int $memberId
     * @return object
     */
    public function getWxUserById($wxUserId) {
        $wxUser = $this->db->select('id, openid, nickname, sex, city, province, country, headimgurl')
            ->where('id', $wxUserId)->get('waiters')->row();
        return $wxUser;
    }

    /**
     * 根据微信用户openid获取数据库记录
     * @param  int $openid
     * @return object
     */
    public function getWxUserByOpenid($openid) {
        $wxUser = $this->db->select('id, openid, nickname, sex, city, province, country, headimgurl')
            ->where('openid', $openid)->get('waiters')->row();
        return $wxUser;
    }

    /**
     * 更新一个微信用户信息
     * @param  int $memberId 
     * @param  object $member 
     * @return void 
     */
    public function updateWxUser($wxOpenid, $wxUser) {
        try {
            info("weixin-model-update-wx-user - begin");
            info("weixin-model-update-wx-user - params: ". json_encode(func_get_args()));
            $this->beginTransition();
            $wxUser->updateTime = time();
            unset($wxUser->unionid);
            $this->db->where('openid', $wxOpenid)->update('waiters', $wxUser);
            unset($wxUser->language);
            $this->db->where('openid', $wxOpenid)->update('salesman', $wxUser);
            if ($this->checkTransitionSuccess()) {
                $this->commitTransition();
            } else {
                throw new Exception("Unknown Error", 1);
            }
        } catch (Exception $e) {
            $this->rollbackTransition();
            error("weixin-model-update-wx-user - fail: ". $e->getMessage());
            throw $e;
        } finally {
            info("weixin-model-update-wx-user - end");
        }
    }

    /**
     * 添加一个新的会员到数据库
     * @param object $member 微信用户信息
     * @return int 返回该新增会员的会员编号
     */
    public function addWxUser($wxUser) {
        try {
            info("weixin-model-add-wx-user - begin");
            info("weixin-model-add-wx-user - params: ". json_encode(func_get_args()));
            $this->beginTransition();
            $wxUser->createTime = time();
            unset($wxUser->unionid);
            $this->db->insert('waiters', $wxUser);
            unset($wxUser->language);
            $this->db->insert('salesman', $wxUser);
            $memberId = $this->db->insert_id();
            if ($this->checkTransitionSuccess()) {
                $this->commitTransition();
            } else {
                throw new Exception("Unknown Error", 1);
            }
            return $memberId;
        } catch (Exception $e) {
            $this->rollbackTransition();
            error("weixin-model-add-wx-user - fail: ". $e->getMessage());
            throw $e;
        } finally {
            info("weixin-model-add-wx-user - end");
        }
    }

    private function getWebToken($redirectUri, $merchant, $isConsumer) {
        $code = $this->getCode($redirectUri, $merchant, $isConsumer);
        if ($isConsumer) {
            if ($merchant->wxAuthStatus == BoolEnum::Yes) {
                $componentAppId = config_item('wx3rd_appid');
                $this->load->model('Variables_model', 'variables');
                $componentToken = $this->variables->get('component_access_token');
                $url = sprintf(self::WX2_GET_TOKEN_URL, $merchant->wxAppId, 
                    $code, $componentAppId, $componentToken->val);
            } else {
                $url = sprintf(self::WX1_GET_TOKEN_URL, $merchant->wxAppId, 
                    $merchant->wxAppSecret, $code);
            }
            $resp = json_decode(file_get_contents($url));
            if (isset($resp->errcode)) {
                error('wx-model-get-web-token - fail: '. $resp->errmsg);
                throw new Exception($resp->errmsg, 1);
            }
        } else {
            if ($merchant->wxAuthStatus_shop == BoolEnum::Yes) {
                $componentAppId = config_item('wx3rd_appid');
                $this->load->model('Variables_model', 'variables');
                $componentToken = $this->variables->get('component_access_token');
                $url = sprintf(self::WX2_GET_TOKEN_URL, $merchant->wxAppId_shop, 
                    $code, $componentAppId, $componentToken->val);
            } else {
                $url = sprintf(self::WX1_GET_TOKEN_URL, $merchant->wxAppId_shop, 
                    $merchant->wxAppSecret_shop, $code);
            }
            $resp = json_decode(file_get_contents($url));
            if (isset($resp->errcode)) {
                error('wx-model-get-web-token - fail: '. $resp->errmsg);
                throw new Exception($resp->errmsg, 1);
            }
        }
        return $resp;
    }

    private function getCode($redirectUri, $merchant, $isConsumer) {
        if (isset($_GET['code'])) {
            $code = $this->input->get('code');
            return $code;
        } else {
            if ($isConsumer) {
                if ($merchant->wxAuthStatus == BoolEnum::Yes) {
                    $url = sprintf(self::WX2_GET_CODE_URL, $merchant->wxAppId, $redirectUri, 
                        config_item('wx3rd_appid'), base64_encode($redirectUri));
                } else {
                    $url = sprintf(self::WX1_GET_CODE_URL, $merchant->wxAppId, $redirectUri, 
                        base64_encode($redirectUri));
                }
                // 消费者微信默认不需要手动同意授权
                if ($merchant->id == -1 || $merchant->manualAuth == BoolEnum::No) {
                    $url = str_replace('snsapi_userinfo', 'snsapi_base', $url);
                }
                redirect($url);
            } else {
                if ($merchant->wxAuthStatus_shop == BoolEnum::Yes) {
                    $url = sprintf(self::WX2_GET_CODE_URL, $merchant->wxAppId_shop, $redirectUri, 
                        config_item('wx3rd_appid'), base64_encode($redirectUri));
                } else {
                    $url = sprintf(self::WX1_GET_CODE_URL, $merchant->wxAppId_shop, $redirectUri, 
                        base64_encode($redirectUri));
                }
                // 供应链微信默认需要手动同意授权
                if ($merchant->id == -1) {
                    $url = str_replace('snsapi_userinfo', 'snsapi_base', $url);
                }
                redirect($url);
            }
            exit();
        }
    }

    /**
     * 获取微信jssdk签名，此方法只适用于供应链微信
     */
    public function getJssdkParams($merchant, $url) {
        $now = time();
        $nonceStr = $this->createNonceStr();
        $signature = $this->getJssdkSignature($merchant, $now, $nonceStr, $url);
        $wxJssdkParams = [
            'debug' => isDev() ? true: false,
            'appId' => $merchant->wxAppId_shop,
            'timestamp' => $now,
            'nonceStr' => $nonceStr,
            'signature' => $signature,
            'jsApiList' => [
                'getLocation', 'openLocation', 'chooseWXPay', 
                'scanQRCode', 'chooseImage', 'uploadImage', 'downloadImage',
                'onMenuShareTimeline', 'onMenuShareAppMessage', 'hideOptionMenu',
                'hideMenuItems']
        ];
        return json_encode($wxJssdkParams);
    }

    private function getJssdkSignature($merchant, $now, $nonceStr, $url) {
        $jssdkTicket = $this->getJssdkTicket($merchant);
        $params = [
            'noncestr' => $nonceStr,
            'jsapi_ticket' => $jssdkTicket,
            'timestamp' => $now,
            'url' => $url
        ];
        ksort($params);
        $params = http_build_query($params);
        $params = urldecode($params);
        $signature  = sha1($params);
        return $signature;
    }

    public function getJssdkTicket($merchant, $isFresh = FALSE, $isConsumer = FALSE) {
        if ($isFresh) { // 非开放平台刷新
            if ($isConsumer) {
                // 刷新消费者微信
                if ($merchant->jsapiTicketTime - 1000 > time()) {
                    print $merchant->name .' -> 无需刷新'. PHP_EOL;
                    return;
                }
                $token = $this->getAccessToken($merchant, $isFresh, $isConsumer);
                if (isset($token)) {
                    $url = sprintf(self::WX_GET_JS_TICKET_URL, $token);
                    $resp = json_decode(file_get_contents($url));
                    if (isset($resp->errcode) && $resp->errmsg !== 'ok') {
                        print('wx-model-get-jssdk-ticket-mobile - fail: '. $resp->errmsg. PHP_EOL);
                    } else {
                        $updateData = ['jsapiTicket' => $resp->ticket, 'jsapiTicketTime' => time() + 7000];
                        $this->db->where('id', $merchant->id)->update('merchants', $updateData);
                        print $merchant->name .' -> ticket 刷新成功'. PHP_EOL;
                    }
                }
                return;
            }
            // 刷新供应链微信
            if ($merchant->jsapiTicketTime_shop - 1000 > time()) {
                print $merchant->name .' -> 无需刷新'. PHP_EOL;
                return;
            }
            $token = $this->getAccessToken($merchant, $isFresh, $isConsumer);
            if (isset($token)) {
                $url = sprintf(self::WX_GET_JS_TICKET_URL, $token);
                $resp = json_decode(file_get_contents($url));
                if (isset($resp->errcode) && $resp->errmsg !== 'ok') {
                    print('wx-model-get-jssdk-ticket-shop - fail: '. $resp->errmsg. PHP_EOL);
                } else {
                    $updateData = ['jsapiTicket_shop' => $resp->ticket, 'jsapiTicketTime_shop' => time() + 7000];
                    $this->db->where('id', $merchant->id)->update('merchants', $updateData);
                    print $merchant->name .' -> ticket 刷新成功'. PHP_EOL;
                }
            }
        } else {
            if ($merchant->wxAuthStatus_shop == BoolEnum::Yes) {
                $jssdkTicket = $merchant->wxAuthorizerJsapiTicket_shop;
                if ($merchant->wxAuthorizerJsapiTicketTime_shop - time() < 0) {
                    error('wx-model-get-jssdk-ticket - fail:  value expired');
                    throw new Exception('微信jssdk ticket已过期', 1);
                }
                if (! $jssdkTicket) {
                    error('wx-model-get-jssdk-ticket - fail:  value is empty');
                    throw new Exception('微信jssdk ticket没有刷新', 1);
                }
            } else {
                $jssdkTicket = $merchant->jsapiTicket_shop;
                if ($merchant->jsapiTicketTime_shop - time() < 0) {
                    error('wx-model-get-jssdk-ticket - fail:  value expired');
                    throw new Exception('微信jssdk ticket已过期', 1);
                }
                if (empty($jssdkTicket)) {
                    error('wx-model-get-jssdk-ticket - fail:  value is empty');
                    throw new Exception('微信jssdk ticket没有刷新', 1);
                }
            }
            return $jssdkTicket;
        }
    }

    private function getAccessToken($merchant, $isFresh, $isConsumer) {
        if ($isFresh) { // 非开放平台刷新
            if ($isConsumer) {
                // 刷新消费者微信
                $url = sprintf(self::WX_GET_ACCESS_TOKEN_URL, $merchant->wxAppId, $merchant->wxAppSecret);
                $resp = json_decode(file_get_contents($url));
                if (isset($resp->errcode)) {
                    print('wx-model-get-access-token-mobile - fail: '. $resp->errmsg. PHP_EOL);
                    return NULL;
                } else {
                    $updateData = ['baseToken' => $resp->access_token, 'baseTokenTime' => time() + 7000];
                    $this->db->where('id', $merchant->id)->update('merchants', $updateData);
                    print $merchant->name .' -> token 刷新成功'. PHP_EOL;
                    return $resp->access_token;
                }
                return;
            }
            // 刷新供应链微信
            $url = sprintf(self::WX_GET_ACCESS_TOKEN_URL, $merchant->wxAppId_shop, $merchant->wxAppSecret_shop);
            $resp = json_decode(file_get_contents($url));
            if (isset($resp->errcode)) {
                print('wx-model-get-access-token-shop - fail: '. $resp->errmsg. PHP_EOL);
                return NULL;
            } else {
                $updateData = ['baseToken_shop' => $resp->access_token, 'baseTokenTime_shop' => time() + 7000];
                $this->db->where('id', $merchant->id)->update('merchants', $updateData);
                print $merchant->name .' -> token 刷新成功'. PHP_EOL;
                return $resp->access_token;
            }
        } else {
            if ($merchant->wxAuthStatus_shop == BoolEnum::Yes) {
                $jssdkTicket = $merchant->wxAuthorizerAccessToken_shop;
            } else {
                $jssdkToken = $merchant->baseToken_shop;
                if (empty($jssdkToken)) {
                    error('wx-model-get-access-token - fail: value expired');
                    throw new Exception('微信jssdk token没有刷新', 1);
                }
            }
            return $jssdkToken;
        }
    }

    public function createNonceStr($length = 16) {
        $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
        $str = "";
        for ($i = 0; $i < $length; $i++) {
            $str .= substr($chars, mt_rand(0, strlen($chars) - 1), 1);
        }
        return $str;
    }

    private function arrayToXml($arr) {
        $xml = '<xml>';
        $fmt = '<%s><![CDATA[%s]]></%s>';
        foreach($arr as $key => $val){
            $xml .= sprintf($fmt, $key, $val, $key);
        }
        $xml .= '</xml>';
        return $xml;
    }

}