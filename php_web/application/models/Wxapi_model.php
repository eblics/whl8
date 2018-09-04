<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Wxapi_model extends CI_Model {
    public function __construct()
    {
        parent::__construct();
        $this->load->model('trigger_model');
    }
    //取一些提现记录进行处理
    public function get_some_withdraw($num=1,$page=1,$payAccountType=0){
        $mchSql='';
        if($payAccountType==1){
            //代发红包企业余额需要大于100元
            $mchlist=$this->db->query("select m.id from merchants m inner join mch_balances a on a.mchId=m.id where m.payAccountType=1 and a.amount>0")->result();
            if($mchlist){
                $mchSql.='and (';
                $i=0;
                foreach ($mchlist as $key => $value) {
                    if($i>0){
                        $mchSql.=' or ';
                    }
                    $mchSql.='mchId='.$value->id;
                    $i++;
                }
                $mchSql.=')';
            }
        }
        $offset=($page-1)*$num;
        $sql="select * from user_trans where wxStatus=0 and payAccountType=$payAccountType $mchSql order by id asc limit $offset,$num";
        return $this->db->query($sql)->result();
    }

    //取一些模板消息记录进行处理
    public function get_some_templateMsg($num=1,$page=1){
        $offset=($page-1)*$num;
        return $this->db->query("select * from user_template_msg where status=0 order by id asc limit $offset,$num")->result();
    }

    //取一些用户信息进行处理
    public function get_some_userinfo($num=1,$page=1){
        $offset=($page-1)*$num;
        return $this->db->query("select * from user_update where status=0 order by id asc limit $offset,$num")->result();
    }

    //取一些提现记录进行处理(处理中状态)
    public function get_some_withdraw_processing($num=1,$page=1,$payAccountType=0){
        $mchSql='';
        if($payAccountType==1){
            //代发红包企业余额需要大于100元
            $mchlist=$this->db->query("select m.id from merchants m inner join mch_balances a on a.mchId=m.id where m.payAccountType=1 and a.amount>0")->result();
            if($mchlist){
                $mchSql.='and (';
                $i=0;
                foreach ($mchlist as $key => $value) {
                    if($i>0){
                        $mchSql.=' or ';
                    }
                    $mchSql.='mchId='.$value->id;
                    $i++;
                }
                $mchSql.=')';
            }
        }
        $offset=($page-1)*$num;
        $sql="select * from user_trans where wxStatus=3 $mchSql order by id asc limit $offset,$num";
        return $this->db->query($sql)->result();
    }

    //取一些企业信息
    public function get_some_merchants($num=1,$page=1){
        $offset=($page-1)*$num;
        return $this->db->query("select * from merchants order by id asc limit $offset,$num")->result();
    }

    //取一些用户标签更新进行处理
    public function get_some_usertag($num=1,$page=1){
        $offset=($page-1)*$num;
        return $this->db->query("select * from users_tags_update where status=0 order by id asc limit $offset,$num")->result();
    }

    //发送微信红包
    public function send_redpacket($transLog){
        if($transLog->payType!=0) {
            log_message('error','Wxapi_model/send_redpacket 红包类型不正确'.var_export($transLog,true).' in '.'file:'.__FILE__.' line:'.__LINE__);
            return;
        }
        $this->db->trans_begin();
        if($transLog->payAccountType==1) {//代发红包判断
            $mchAmount=$this->db->query("select * from mch_balances where mchId=$transLog->mchId for update")->row();
            if(! $mchAmount || $mchAmount->amount<$transLog->amount){
                log_message('error','Wxapi_model/send_redpacket 企业余额不足（无法代发）'.var_export($transLog,true).' in '.'file:'.__FILE__.' line:'.__LINE__);
                return;
            }
        }
        $confirmTransLog=$this->db->query("select * from user_trans where id=$transLog->id and wxStatus=0 for update")->row();
        if(! $confirmTransLog) {
            log_message('error','Wxapi_model/send_redpacket 本条记录无需处理'.var_export($transLog,true).' in '.'file:'.__FILE__.' line:'.__LINE__);
            return;
        }
        $merchant=$this->db->query("select * from merchants where id=$transLog->mchId")->row();
        if(! $merchant) {
            log_message('error','Wxapi_model/send_redpacket 查询企业信息失败'.var_export($transLog,true).' in '.'file:'.__FILE__.' line:'.__LINE__);
            return;
        }
        $user=$this->db->query("select * from users where id=$transLog->userId")->row();
        if(! $user) {
            error("wxapi-model-send-redpacket - fail: ID 为". $transLog->userId . '的用户不存在，请手动处理该提现');
            return;
        }
        if($merchant->id!=$user->mchId){
            log_message('error','Wxapi_model/send_redpacket 用户信息有误：$transLog=>'.var_export($transLog,true).' in '.'file:'.__FILE__.' line:'.__LINE__);
            return;
        }
        if($transLog->payAccountType==1) {//代发红包判断
            $commonUser=$this->db->query("select u.* from users_common_sub s inner join users_common u on u.id=s.parentId where s.openid='$user->openid'")->row();
            if(! $commonUser){
                log_message('error','Wxapi_model/send_redpacket 代发红包，common用户信息有误：$transLog=>'.var_export($transLog,true).' in '.'file:'.__FILE__.' line:'.__LINE__);
                return;
            }
            $commonMch=$this->db->query("select * from merchants where id=-1")->row();
            if(! $commonMch){
                log_message('error','Wxapi_model/send_redpacket 代发红包，commonMch信息有误：$transLog=>'.var_export($transLog,true).' in '.'file:'.__FILE__.' line:'.__LINE__);
                return;
            }
            $sendNowData=(object)[
                //红包类型
                'redpackType'=>$transLog->moneyType,//0普通 1裂变
                //商户id
                'mchId'=>$commonMch->id,
                //商户号
                'mch_id'=>$commonMch->wxMchId,
                //公众账号appid
                'wxappid'=>$commonMch->wxAppId,
                //商户名称
                'send_name'=>$merchant->wxSendName,
                //活动名称
                'act_name'=>$merchant->wxActName,
                //备注
                'remark'=>$merchant->wxRemark,
                //红包祝福语
                'wishing'=>str_replace('π', '', $merchant->wxWishing),
                //付款金额
                'total_amount'=>$transLog->amount,
                //红包发放总人数
                'total_num'=>$merchant->wxRpTotalNum,
                //用户openid
                're_openid'=>$commonUser->openid,
                //支付api密钥
                'key'=>$commonMch->wxPayKey,
                //支付证书cert
                'certPath'=>$commonMch->certPath,
                //支付证书key
                'keyPath'=>$commonMch->keyPath,
                //支付证书rootca
                'caPath'=>$commonMch->caPath,
            ];
        }else{
            $sendNowData=(object)[
                //红包类型
                'redpackType'=>$transLog->moneyType,//0普通 1裂变
                //商户id
                'mchId'=>$merchant->id,
                //商户号
                'mch_id'=>$merchant->wxMchId,
                //公众账号appid
                'wxappid'=>$merchant->wxAppId,
                //商户名称
                'send_name'=>$merchant->wxSendName,
                //活动名称
                'act_name'=>$merchant->wxActName,
                //备注
                'remark'=>$merchant->wxRemark,
                //红包祝福语
                'wishing'=>str_replace('π', '', $merchant->wxWishing),
                //付款金额
                'total_amount'=>$transLog->amount,
                //红包发放总人数
                'total_num'=>$merchant->wxRpTotalNum,
                //用户openid
                're_openid'=>$user->openid,
                //支付api密钥
                'key'=>$merchant->wxPayKey,
                //支付证书cert
                'certPath'=>$merchant->certPath,
                //支付证书key
                'keyPath'=>$merchant->keyPath,
                //支付证书rootca
                'caPath'=>$merchant->caPath,
            ];
        }
        $payOk=false;
        $wxStatus = 0;
        $sendNowResult=$this->weixin_rest_api->send_redpack($sendNowData);
        if(!$sendNowResult){
            log_message('error','Wxapi_model/send_redpacket 红包发放请求失败1 '.var_export($sendNowResult,TRUE).' in '.'file:'.__FILE__.' line:'.__LINE__);
            $this->db->trans_rollback();
            return;
        }
        if (! property_exists($sendNowResult, 'return_code')) {
            error('send-redpacket fail: '. json_encode($sendNowResult));
            $sql = "UPDATE user_trans SET wxStatus = 2, wxSendTime = ?, wxErrCode = ? WHERE id = ?";
            $this->db->query($sql, [time(), 'UN_KNOW', $transLog->id]);
            $this->db->trans_commit();
            return;
        }
        $return_code_key=property_exists($sendNowResult,'return_code');
        $result_code_key=property_exists($sendNowResult,'result_code');
        $mchBillnoSql='';
        if(property_exists($sendNowResult,'mch_billno')){
            $mchBillnoSql=",wxMchBillno='$sendNowResult->mch_billno'";
        }
        $wxSendListIdSql='';
        if(property_exists($sendNowResult,'send_listid')){
            $wxSendListIdSql=",wxSendListId='$sendNowResult->send_listid'";
        }
        if($return_code_key && $result_code_key && $sendNowResult->return_code=='SUCCESS' && $sendNowResult->result_code=='SUCCESS'){
            $payOk=true;
            if($transLog->payAccountType==1) {//代发红包判断
                $this->db->query("update mch_balances set amount=amount-$transLog->amount where mchId=$transLog->mchId");
            }
            $this->db->query("update user_trans set wxStatus=1,wxSendTime=".time()." $mchBillnoSql $wxSendListIdSql where id=$transLog->id");
            $wxStatus = 1;
        }else if($return_code_key && $result_code_key && $sendNowResult->return_code=='SUCCESS' && $sendNowResult->result_code!='SUCCESS' && $sendNowResult->err_code=='PROCESSING'){
            $payOk=true;
            $this->db->query("update user_trans set wxStatus=3,wxSendTime=".time().",wxErrCode='$sendNowResult->err_code' $mchBillnoSql $wxSendListIdSql where id=$transLog->id");
            log_message('error','Wxapi_model/send_redpacket 红包直接发放处理中 userId:'.$transLog->userId.' mchId:'.$transLog->mchId.' '.var_export($sendNowResult,TRUE).' in '.'file:'.__FILE__.' line:'.__LINE__);
        }else if($return_code_key && $result_code_key && $sendNowResult->return_code=='SUCCESS' && $sendNowResult->result_code!='SUCCESS'){
            $this->db->query("update user_trans set wxStatus=2,wxSendTime=".time().",wxErrCode='$sendNowResult->err_code' $mchBillnoSql $wxSendListIdSql where id=$transLog->id");
            log_message('error','Wxapi_model/send_redpacket 红包直接发放失败2 userId:'.$transLog->userId.' mchId:'.$transLog->mchId.' '.var_export($sendNowResult,TRUE).' in '.'file:'.__FILE__.' line:'.__LINE__);
            //通知企业帐户余额不足模板消息写入数据库
            if($sendNowResult->err_code=='NOTENOUGH'){
                $formatMsg=$this->wx3rd_lib->template_format_data($merchant->id,'kf_notice',['【消费者提现失败】\\n','重要提醒','已完成','欢乐扫系统','失败原因：微信支付商户平台帐户余额不足 '.date('Y-m-d H:i:s',time())]);
                $this->wx3rd_lib->template_warning_send($merchant->id,$formatMsg);
            }
        }else if($return_code_key && $sendNowResult->return_code!='SUCCESS'){
            $this->db->query("update user_trans set wxStatus=2,wxSendTime=".time().",wxErrCode='return_code_fail' $mchBillnoSql $wxSendListIdSql where id=$transLog->id");
            log_message('error','Wxapi_model/send_redpacket 红包直接发放失败3 userId:'.$transLog->userId.' mchId:'.$transLog->mchId.' '.var_export($sendNowResult,TRUE).' in '.'file:'.__FILE__.' line:'.__LINE__);
        }
        //红包提现失败处理
        if(! $payOk && $transLog->action==0){
            $this->db->query("INSERT INTO user_accounts(userId,mchId,moneyType,amount,role)
                VALUES($transLog->userId,$transLog->mchId,$transLog->moneyType,IFNULL(amount,0)+$transLog->amount,$transLog->role)
                ON DUPLICATE KEY UPDATE amount=IFNULL(amount,0)+$transLog->amount");
        }
        //积分兑换红包失败处理
        if(! $payOk && $transLog->action==1){
            $this->db->query("INSERT INTO user_points_accounts(userId,mchId,amount,role)
                VALUES($transLog->userId,$transLog->mchId,IFNULL(amount,0)+$transLog->amount,$transLog->role)
                ON DUPLICATE KEY UPDATE amount=IFNULL(amount,0)+$transLog->amount");
        }
        if ($this->db->trans_status() === FALSE){
            $this->db->trans_rollback();
            return;
        }else{
            $this->db->trans_commit();
            // 当红包提现失败退回账户余额的时候，减去触发的红包提现总额 add by cw
            // 提现成功以后，更新报表数据，以使当前的提现数据准确 mod by zht
            if(  $wxStatus == 1 && $transLog->action==0){
                $transInfo = clone $transLog;
                //$transInfo->amount = -$transInfo->amount;
                $this->trigger_model->trigger_trans($transInfo,$transInfo->amount);
            }
            // 当积分使用失败退回账户余额的时候，减去触发的积分使用总额 add by cw
            if(! $payOk && $transLog->action==1){
                $pointInfo = clone $transLog;
                //$pointInfo->amount = -$pointInfo->amount;
                $this->trigger_model->trigger_point_used($pointInfo);
            }
        }
    }

    //发送企业付款
    public function send_mchpay($transLog){
        if($transLog->payType!=1) return;
        $this->db->trans_begin();
        if($transLog->payAccountType==1) {//代发红包判断
            $mchAmount=$this->db->query("select * from mch_balances where mchId=$transLog->mchId for update")->row();
            if(! $mchAmount || $mchAmount->amount<$transLog->amount){
                log_message('error','Wxapi_model/send_redpacket 企业余额不足（无法代发）'.var_export($transLog,true).' in '.'file:'.__FILE__.' line:'.__LINE__);
                return;
            }
        }
        $confirmTransLog=$this->db->query("select * from user_trans where id=$transLog->id and wxStatus=0 for update")->row();
        if(! $confirmTransLog) return;
        $merchant=$this->db->query("select * from merchants where id=$transLog->mchId")->row();
        if(! $merchant) return;
        $user=$this->db->query("select * from users where id=$transLog->userId")->row();
        if(! $user) return;
        if($merchant->id!=$user->mchId){
            log_message('error','Wxapi_model/send_mchpay 用户信息有误：$transLog=>'.var_export($transLog,true).' in '.'file:'.__FILE__.' line:'.__LINE__);
            return;
        }
        if($transLog->payAccountType==1) {//代发红包判断
            $commonUser=$this->db->query("select u.* from users_common_sub s inner join users_common u on u.id=s.parentId where s.openid='$user->openid'")->row();
            if(! $commonUser){
                log_message('error','Wxapi_model/send_mchpay 代发红包，common用户信息有误：$transLog=>'.var_export($transLog,true).' in '.'file:'.__FILE__.' line:'.__LINE__);
                return;
            }
            $commonMch=$this->db->query("select * from merchants where id=-1")->row();
            if(! $commonMch){
                log_message('error','Wxapi_model/send_mchpay 代发红包，commonMch信息有误：$transLog=>'.var_export($transLog,true).' in '.'file:'.__FILE__.' line:'.__LINE__);
                return;
            }
            $sendAccountData=$commonMch;
            $sendAccountData->mchId=$commonMch->id;//企业id
            $sendAccountData->openid=$commonUser->openid;//支付对方openid
            $sendAccountData->amount=$transLog->amount;//支付金额（分）
            $sendAccountData->desc=$merchant->wxActName;//支付描述
        }else{
            $sendAccountData=$merchant;
            $sendAccountData->mchId=$merchant->id;//企业id
            $sendAccountData->openid=$user->openid;//支付对方openid
            $sendAccountData->amount=$transLog->amount;//支付金额（分）
            $sendAccountData->desc=$merchant->wxActName;//支付描述
        }
        $payOk=false;
        $sendAccountResult=$this->weixin_rest_api->mch_pay($sendAccountData);
        if(!$sendAccountResult){
            log_message('ERROR','企业付款发放请求失败 '.var_export($sendAccountResult,TRUE).' in '.'file:'.__FILE__.' line:'.__LINE__);
            $this->db->trans_rollback();
            return;
        }
        $return_code_key=property_exists($sendAccountResult,'return_code');
        $result_code_key=property_exists($sendAccountResult,'result_code');
        $mchBillnoSql='';
        if(property_exists($sendAccountResult,'partner_trade_no')){
            $mchBillnoSql=",wxMchBillno='$sendAccountResult->partner_trade_no'";
        }
        $wxSendListIdSql='';
        if(property_exists($sendAccountResult,'payment_no')){
            $wxSendListIdSql=",wxMchBillno='$sendAccountResult->payment_no'";
        }
        if($return_code_key && $result_code_key && $sendAccountResult->return_code=='SUCCESS' && $sendAccountResult->result_code=='SUCCESS'){
            $payOk=true;
            if($transLog->payAccountType==1) {//代发红包判断
                $this->db->query("update mch_balances set amount=amount-$transLog->amount where mchId=$transLog->mchId");
            }
            $this->db->query("update user_trans set wxStatus=1,wxSendTime=".time()." $mchBillnoSql $wxSendListIdSql where id=$transLog->id");
        }else if($return_code_key && $result_code_key && $sendAccountResult->return_code=='SUCCESS' && $sendAccountResult->result_code!='SUCCESS'){
            $this->db->query("update user_trans set wxStatus=2,wxSendTime=".time().",wxErrCode='$sendAccountResult->err_code' $mchBillnoSql $wxSendListIdSql where id=$transLog->id");
            log_message('error','Wxapi_model/send_mchpay 企业付款发放失败2 '.var_export($sendAccountResult,TRUE).' in '.'file:'.__FILE__.' line:'.__LINE__);
        }else if($return_code_key && $sendAccountResult->return_code!='SUCCESS'){
            $this->db->query("update user_trans set wxStatus=2,wxSendTime=".time().",wxErrCode='return_code_fail' $mchBillnoSql $wxSendListIdSql where id=$transLog->id");
            log_message('error','Wxapi_model/send_mchpay 企业付款发放失败3 '.var_export($sendAccountResult,TRUE).' in '.'file:'.__FILE__.' line:'.__LINE__);
        }
        //红包提现失败处理
        if(! $payOk && $transLog->action==0){
            $this->db->query("INSERT INTO user_accounts(userId,mchId,moneyType,amount,role)
                VALUES($transLog->userId,$transLog->mchId,$transLog->moneyType,IFNULL(amount,0)+$transLog->amount,$transLog->role)
                ON DUPLICATE KEY UPDATE amount=IFNULL(amount,0)+$transLog->amount");
        }
        //积分兑换红包失败处理
        if(! $payOk && $transLog->action==1){
            $this->db->query("INSERT INTO user_points_accounts(userId,mchId,amount,role)
                VALUES($transLog->userId,$transLog->mchId,IFNULL(amount,0)+$transLog->amount,$transLog->role)
                ON DUPLICATE KEY UPDATE amount=IFNULL(amount,0)+$transLog->amount");
        }
        if ($this->db->trans_status() === FALSE){
            $this->db->trans_rollback();
            return;
        }else{
            $this->db->trans_commit();

            // 当红包提现失败退回账户余额的时候，减去触发的红包提现总额 add by cw
            // 提现成功以后，更新报表数据，以使当前的提现数据准确 mod by zht
            if(! $payOk && $transLog->action==0){
                $transInfo = clone $transLog;
                //$transInfo->amount = -$transInfo->amount;
                $this->trigger_model->trigger_trans($transInfo,$transInfo->amount);
            }
            // 当积分使用失败退回账户余额的时候，减去触发的积分使用总额 add by cw
            if(! $payOk && $transLog->action==1){
                $pointInfo = clone $transLog;
                //$pointInfo->amount = -$pointInfo->amount;
                $this->trigger_model->trigger_point_used($pointInfo);
            }
        } 
    }

    //查询红包状态processing
    public function check_redpacket_processing($transLog){
        $this->db->trans_begin();
        $confirmTransLog=$this->db->query("select * from user_trans where id=$transLog->id and wxStatus=3 for update")->row();
        if(! $confirmTransLog) return;
        $merchant=$this->db->query("select * from merchants where id=$transLog->mchId")->row();
        if(! $merchant) return;
        if($transLog->payAccountType==1) {//代发红包判断
            $commonMch=$this->db->query("select * from merchants where id=-1")->row();
            if(! $commonMch){
                log_message('error','Wxapi_model/send_mchpay 代发红包，commonMch信息有误：$transLog=>'.var_export($transLog,true).' in '.'file:'.__FILE__.' line:'.__LINE__);
                return;
            }
            $checkData=$commonMch;
        }else{
            $checkData=$merchant;
        }
        $checkData->wxMchBillno=$confirmTransLog->wxMchBillno;
        $checkDataResult=$this->weixin_rest_api->check_redpacket($checkData);
        log_message('error','debug check_redpacket_processing/checkDataResult:'.var_export($checkDataResult,true));
        $return_code_key=property_exists($checkDataResult,'return_code');
        $result_code_key=property_exists($checkDataResult,'result_code');
        if(! ($return_code_key && $result_code_key && $checkDataResult->return_code=='SUCCESS' && $checkDataResult->result_code=='SUCCESS')){
            log_message('error','Wxapi_model/check_redpacket_processing 失败：$msg=>'.var_export($checkDataResult,true).' in '.'file:'.__FILE__.' line:'.__LINE__);
            if($checkDataResult->err_code=='NOT_FOUND'){
                $this->db->query("update user_trans set wxFinalStatus='$checkDataResult->err_code',wxStatus=4 where id=$transLog->id");
            }else{
                $this->db->trans_rollback();
                return;
            }
        }
        if($checkDataResult->status=='FAILED'){
            $this->db->query("update user_trans set wxFinalStatus='$checkDataResult->status',wxStatus=2 where id=$transLog->id");
            $this->db->query("INSERT INTO user_accounts(userId,mchId,moneyType,amount,role)
                VALUES($transLog->userId,$transLog->mchId,$transLog->moneyType,IFNULL(amount,0)+$transLog->amount,$transLog->role)
                ON DUPLICATE KEY UPDATE amount=IFNULL(amount,0)+$transLog->amount");
        }else if($checkDataResult->status=='PROCESSING'){
            $this->db->trans_rollback();
            return;
        }else{
            $this->db->query("update user_trans set wxFinalStatus='$checkDataResult->status',wxStatus=1 where id=$transLog->id");
        }
        if ($this->db->trans_status() === FALSE){
            $this->db->trans_rollback();
            return;
        }else{
            $this->db->trans_commit();
        }
        // 当红包提现失败退回账户余额的时候，减去触发的红包提现总额 add by cw
        // 提现成功以后，更新报表数据，以使当前的提现数据准确 mod by zht
        if($checkDataResult->status=='FAILED' && $transLog->action==0){
            $transInfo = clone $transLog;
            //$transInfo->amount = -$transInfo->amount;
            $this->trigger_model->trigger_trans($transInfo,$transInfo->amount);
        }
        // 当积分使用失败退回账户余额的时候，减去触发的积分使用总额 add by cw
        if($checkDataResult->status=='FAILED' && $transLog->action==1){
            $pointInfo = clone $transLog;
            //$pointInfo->amount = -$pointInfo->amount;
            $this->trigger_model->trigger_point_used($pointInfo);
        }
    }

    //查询红包状态
    public function check_redpacket($transLog){
        $this->db->trans_begin();
        $confirmTransLog=$this->db->query("select * from user_trans where id=$transLog->id for update")->row();
        if(! $confirmTransLog) return;
        $merchant=$this->db->query("select * from merchants where id=$transLog->mchId")->row();
        if(! $merchant) return;
        $checkData=$merchant;
        $checkData->wxMchBillno=$confirmTransLog->wxMchBillno;
        $checkDataResult=$this->weixin_rest_api->check_redpacket($checkData);
        $status_key=property_exists($checkDataResult,'status');
        if(! $status_key){
            $this->db->trans_rollback();
            return;
        }
        $this->db->query("update user_trans set wxFinalStatus='$checkDataResult->status' where id=$transLog->id");
        if ($this->db->trans_status() === FALSE){
            $this->db->trans_rollback();
            return;
        }else{
            $this->db->trans_commit();
        }
    }

    //发送模板消息
    public function send_template_msg($msg){
        if($msg->status!=0) return;
        $this->db->trans_begin();
        $confirmMsg=$this->db->query("select * from user_template_msg where id=$msg->id and status=0 for update")->row();
        if(! $confirmMsg) return;
        $merchant=$this->db->query("select * from merchants where id=$confirmMsg->mchId")->row();
        if(! $merchant) return;
        $user=$this->db->query("select * from users where openid='$confirmMsg->openid'")->row();
        if(! $user) return;
        if($merchant->id!=$user->mchId){
            $this->db->query("update user_template_msg set status = 3, updateTime = ? where id = ?", [time(), $confirmMsg->id]);
            error('Wxapi_model/send_template_msg 用户信息有误: msg->id = ' . $msg->id . ' merchant->id = ' . $merchant->id . ' user->mchId = '. $user->mchId);
            $this->db->trans_commit();
            return;
        }
        $objMsg=json_decode($confirmMsg->formatMsg);
        $sendResult=$this->wx3rd_lib->template_send($confirmMsg->mchId,$confirmMsg->openid,$objMsg);
        if(! $sendResult || $sendResult->errcode!=0){
            $this->db->query("update user_template_msg set status=2,updateTime=".time()." where id=$confirmMsg->id");
            error('Wxapi_model/send_template_msg 微信模板消息接口调用失败：userId:'.$user->id.' mchId:'.$confirmMsg->mchId.' '.var_export($sendResult,true));
            $this->db->trans_commit();
            return;
        }
        if($sendResult->errcode==0){
            $this->db->query("update user_template_msg set status=1,updateTime=".time()." where id=$confirmMsg->id");
        }
        if ($this->db->trans_status() === FALSE){
            $this->db->trans_rollback();
            return;
        }else{
            $this->db->trans_commit();
        }
    }

    //更新用户信息
    public function update_userinfo($info){
        $this->load->model('common_login_model');
        $this->load->model('user_model');
        $merchant=$this->merchant_model->get_by_wxid($info->wxYsId);
        if(!$merchant){
            $this->db->query("update user_update set status=2,updateTime=".time()." where id=$info->id");
            return;
        }
        $openid=$info->openid;
        $wx_userinfo=$this->weixin_rest_api->get_userinfo($merchant->wxAppId,$openid);
        if(isset($wx_userinfo->errcode) && $wx_userinfo->errcode!=0){
            log_message('error','微信获取用户信息失败：'. json_encode($wx_userinfo));
            $this->db->query("update user_update set status=2,updateTime=".time()." where id=$info->id");
            return;
        }
        if($merchant->id==-1){
            //common user 
            $userInfo=$this->common_login_model->get_user_by_openid($openid);
        }else{
        	$userInfo=$this->user_model->get_by_openid($openid);
        }
        if(! $userInfo){
            $wx_userinfo->createTime=time();
            $wx_userinfo->updateTime=time();
            $wx_userinfo->mchId=$merchant->id;
            if($wx_userinfo->subscribe==1){
                $wx_userinfo->nickName=addslashes(htmlspecialchars($wx_userinfo->nickname));
                $wx_userinfo->nickName=str_replace("'","",$wx_userinfo->nickName);
                $wx_userinfo->nickName=str_replace('"','',$wx_userinfo->nickName);
                $wx_userinfo->nickName=str_replace('\\','',$wx_userinfo->nickName);
                unset($wx_userinfo->nickname);
            }
            if($merchant->id==-1){
                //common user 
                $save=$this->common_login_model->save_user($wx_userinfo);
            }else{
            	$save=$this->user_model->save($wx_userinfo);//新增信息
            }
            $status=0;
            if($save){
                $status=1;
            }else{
                $status=2;
            }
            $this->db->query("update user_update set status=$status,updateTime=".time()." where id=$info->id");
            return;
        }
        if($wx_userinfo->subscribe==1){
            foreach($userInfo as $key => $value) {
                foreach($wx_userinfo as $k => $v) {
                    if($key==$k){
                        $userInfo->$key=$v;
                    }
                }
            }
            $userInfo->nickName=addslashes(htmlspecialchars($wx_userinfo->nickname));
            $userInfo->nickName=str_replace("'","",$userInfo->nickName);
            $userInfo->nickName=str_replace('"','',$userInfo->nickName);
            $userInfo->nickName=str_replace("\/","",$userInfo->nickName);
            $userInfo->subscribe=1;
        }else{
            $userInfo->subscribe=0;
        }
        $userInfo->updateTime=time();
        if($merchant->id==-1){
            //common user 
            $save=$this->common_login_model->save_user($userInfo);
        }else{
        	$save=$this->user_model->save($userInfo);//更新信息
        }
        $status=0;
        if($save){
            $status=1;
        }else{
            $status=2;
        }
        $this->db->query("update user_update set status=$status,updateTime=".time()." where id=$info->id");
    }

    //给用户打标签
    public function tagging_user($info){
        $openidArr=[$info->openid];
        $result=$this->weixin_rest_api->tagging($info->mchId,$info->tagId,$openidArr);
        $status=0;
        if($result->errcode!=0){
            $status=2;
        }else{
            $status=1;
        }
        $this->db->query("update users_tags_update set status=$status,updateTime=".time()." where id=$info->id");
    }
    
}