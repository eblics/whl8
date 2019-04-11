<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Code extends Mobile_Controller {

    // 企业封禁错误码
    const MCH_FORBIDDEN_CODES = [110104, 110133];

    // 系统封禁错误码
    const SYS_FORBIDDEN_CODES = [];

    /**
     * 扫码入口
     * @param $code 乐码
     * @return view
     */
    public function scan($code = NULL) {
        $this->session->unset_userdata('last_scan_code');
        $this->session->unset_userdata('last_evil_level');
        $loadTestOpenid = $this->input->get('openid');

        if (! isset($code)) {
            $this->load->view('error_code_scan', ['errmsg' => '找不到乐码']);
        }

        $this->load->library('common/ipwall');
        if ($this->ipwall->is_prevent()) {
            $this->load->view('error_code_scan', ['errmsg' => '您扫码过于频繁，请稍后重试']);
        }

        $commonUser = $this->getCommonUser();
	//error('commonUser:'.var_export($commonUser,True));
        try {
            debug('scan - begin');
            debug('lecode is: '. $code);
            $this->load->model('Scan_model', 'scan');
            $this->load->model('Scan_log_model','scan_log');
            $scanLog = $this->scan_log->get_by_code($code);
            if (isset($scanLog)) {
                $scanResult = $this->reScan($scanLog, $loadTestOpenid);
            } else {
                $scanResult = $this->firstScan($code, $loadTestOpenid);
            }
            $this->load->model('Merchant_model', 'merchant');
            //$merchant = $this->merchant->getMerchantByScanLog($scanLog);
            //$currentUser = $this->getCurrentScanUser($merchant->id, $loadTestOpenid);
            $merchant = $scanResult[0];
            $currentUser = $scanResult[1];
            $params = ['appId' => $merchant->wxAppId, 'appSecret' => $merchant->wxAppSecret];
            $this->load->library('weixin_jssdk', $params);
            $signPackage = $this->weixin_jssdk->GetSignPackage();
            $data = [
                'mch_id' => $merchant->id, 
                'code' => $code, 
                'geoLocation' => intval($merchant->geoLocation)
            ];
            $viewData = array_merge($data, $signPackage);
            
            $this->session->set_userdata('last_scan_code', $code);
	    if($merchant->id==7){
		    $webappUrl='https://m.jr.jd.com/zc/drawSystem/hb/index.html?contentParam=100001277&actCode=C13D937C0D&actType=1';
		    header('location:'.$webappUrl);
		    return;
	    }
	    // 无广告的扫码加载界面
	    if ($merchant->id == config_item('gm_mch_id')) {
	      $viewData['gmLoading'] = BoolEnum::Yes;
	    }
	    $this->load->view('scan', $viewData);
        } catch (Exception $e) {
            $this->load->library('common/common_login');
            $subUserId = isset($currentUser) ? $currentUser->id : -1;
            $mchId = isset($currentUser) ? $currentUser->mchId : -1;
            $this->common_login->save_user_log(1, $e->getMessage(), $code, $mchId, $subUserId);
            if ($e->getCode() > 110100) {
                error('code-scan -> users_common: ' . json_encode($commonUser) . 
                    ' at: ' . get_real_ip() . 
                    ' scaned: ' . $code . ' ' . $e->getMessage() . ' 已做封号处理');
                if (in_array($e->getCode(), self::MCH_FORBIDDEN_CODES)) {
                    error('code-scan - 封号类型：企业封禁');
                    $currentUser = $this->getCurrentUser($this->getCurrentMchId());
                    $this->common_login->save_user_log(1, $e->getMessage(), $code, $currentUser->mchId, $currentUser->id);
                    $this->common_login->forbidden($commonUser, $currentUser);
                }
                error('code-scan - 封号类型：系统封禁');
                $this->common_login->forbidden($commonUser);
            } else {
                error("code-scan -> userId: $subUserId, lecode: $code, ". $e->getMessage());
                $this->load->view('error_code_scan', ['errmsg' => $e->getMessage()]);
            }
        } finally {
            debug('scan - end');
        }
    }

    private function firstScan($code, $loadTestOpenid) {
        debug('scan_log not exists');
        $code_ret = $this->scan->deLecode($code);

        $this->load->model('Merchant_model', 'merchant');
        $merchant = $this->merchant->getMerchantByMchCode($code_ret->mch_code);
	//error('firstscan:'.var_export($merchant,True));
        $currentUser = $this->getCurrentScanUser($merchant->id, $loadTestOpenid);

        $this->scan->checkScanLimit($merchant);
        $this->scan->checkScanDenyTimes($currentUser->mchId, $currentUser->id);
        //判断位置移动到检查扫描次数以后，以减小天御访问次数
        $this->load->library('common/tencent_api',array('wxAppId'=>$merchant->wxAppId));
        $evillevel = $this->tencent_api->checkCsec($currentUser,$code);
        
        $validateResp = $this->scan->checkValidate($merchant->withCaptcha);
        if (! $validateResp['pass']) {
            $this->load->view('captcha', ['errmsg' => $validateResp['msg']]);
        }
        $this->ipwall->correct_process();
        
        $this->load->model('Batch_model', 'batch');
        $batch = $this->batch->getBatchByValue($currentUser, $code_ret->value, $code);

        $scanLog = new stdClass();
        $scanLog->code = $code; 
        $scanLog->userId = $currentUser->id;
        $scanLog->openId = $currentUser->openid;
        $scanLog->mchId = $currentUser->mchId;
        $scanLog->ip = $_SERVER['REMOTE_ADDR'];
        $scanLog->scanTime = time();
        $scanLog->batchId = $batch->id;
        $scanLog->isFirst = 1;
        $scanLog->activityId = NULL;
        $scanLog->areaCode = NULL;
        $scanLog->over = 0;
        //$scanLog->evilLevel = $evillevel;
        $this->session->set_userdata('last_evil_level', $evillevel);
        $save = $this->scan_log->insert($scanLog);
        if (! $save) {
            error('save scan_log fail');
            throw new Exception("发生未知错误", 1);
        }
        $this->trigger_model->trigger_scan_log_insert($scanLog);
        return [$merchant, $currentUser];
    }

    private function reScan($scanLog, $loadTestOpenid) {
        error('scan_log exists: '. json_encode($scanLog));
        $this->load->model('Merchant_model', 'merchant');
        $merchant = $this->merchant->getMerchantByScanLog($scanLog);
        $currentUser = $this->getCurrentScanUser($merchant->id, $loadTestOpenid);
	error('rescan:'.var_export($currentUser,True));

        if ($merchant->id == config_item('gm_mch_id')) {
            if ($currentUser->id !== $scanLog->userId) {
                $this->load->view('error_code_scan', ['errmsg' => '此二维码已被他人扫过']);
            }
        }
        
        //mod by zht,17.11.28
        //如果不是第一次扫描，可不查询用户等级，防止接口访问次数超限。
        //虽然不能防护高恶意用户访问，但是重复扫码不能产生活动收益，因此防护意义不大
        //add by zht,17.11.30
        //如果不进行防护，不法分子可能收集瓶盖，通过该漏洞检测码是否被扫描，然后转卖以获取利益
        //↑↑↑上述不能成立，因为企业一般设定每日访问次数，不能无限检查瓶盖
        //$this->load->library('common/tencent_api',array('wxAppId'=>$merchant->wxAppId));
        //$evillevel = $this->tencent_api->checkCsec($currentUser,$scanLog->code);
        $this->scan->checkScanLimit($merchant);
        
        // 检查是否需要验证码
        $validateResp = $this->scan->checkValidate($merchant->withCaptcha);
        if (! $validateResp['pass']) {
            $this->load->view('captcha', ['errmsg' => $validateResp['msg']]);
        }
        if ($scanLog->openId != $currentUser->openid) {
            $this->scan->checkScanDenyTimes($merchant->id, $currentUser->id);
            $this->load->library('common/common_login');
            $this->common_login->save_user_log(6, '扫描他人的码', $scanLog->code, $currentUser->mchId, $currentUser->id);
            $this->scan->checkScanOtherTimes($merchant->id, $currentUser->id);
        }
        return [$merchant, $currentUser];
    }

    //扫码验证码输出
    public function captcha(){
        $commonUser = $this->getCommonUser();
        if(!isset($_SERVER['HTTP_REFERER'])){
            log_message('error','code/captcha '.$_SERVER['REMOTE_ADDR'].' commonOpenid:'.$commonUser->openid.
                ' no HTTP_REFERER');
            return;
        }
        $linkPage=$_SERVER['HTTP_REFERER'];
        $mobileUrl=config_item('mobile_url');
        if(strpos($linkPage,$mobileUrl)!==0){
            log_message('error','code/captcha '.$_SERVER['REMOTE_ADDR'].' commonOpenid:'.$commonUser->openid.
                ' not right HTTP_REFERER');
            return;
        }
        $this->load->library('common/captcha/cool_captcha');
        return $this->cool_captcha->CreateImage();
    }

}
