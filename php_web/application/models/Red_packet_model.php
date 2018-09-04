<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Red_packet_model extends CI_Model {
    var $redis;
    public function __construct()
    {
        // Call the CI_Model constructor
        parent::__construct();
        $this->load->model('user_model');
        $this->load->model('merchant_model');
        $this->load->model('card_model');
        $this->load->model('mixstrategy_model');
        $this->load->model('accumstrategy_model');
        $this->load->model('point_model');
        $this->load->model('multistrategy_model');
        $this->load->model('jokes_model');
        $this->load->model('scan_log_model');
        $this->load->model('trigger_model');
        $this->redis=new Redis();
        $this->redis->pconnect($this->config->item('redis')['host'],$this->config->item('redis')['port']);
        if(isset($this->config->item('redis')['password'])){
            $this->redis->auth($this->config->item('redis')['password']);
        }
    }

    function get($id){
        $red_packet=$this->db->where('id',$id)->where('rowStatus',0)->get('red_packets')->row();
        if (isset($red_packet)) {
            if ($red_packet->limitType == 0) { // 限制红包发放个数
                $key = "red_packets.remainNum.id.$red_packet->id";
                $v=$red_packet->remainNum;
            } else if($red_packet->limitType == 1) { // 限制红包发放金额
                $key = "red_packets.remainAmount.id.$red_packet->id";
                $v=$red_packet->remainAmount;
            }
            if ($this->dbhelper->cache_zscore("limit_zone", $key) === false) {
                $this->dbhelper->cache_zadd("limit_zone", $key, $v);
            }
        }
        return $red_packet;
    }
    //分级红包情况下，按优先级获取子项
    function get_subs_by_priority($id,$priority){
        $sql="select sub.* from red_packets_sub sub inner join red_packets rp
            on sub.parentId=rp.id where rp.id=$id and sub.remainNum>0";
        if($priority==0){
            $order=' order by rand()';
        }
        else if($priority==1){
            $order=' order by amount';
        }
        else if($priority==2){
            $order=' order by amount desc';
        }
        $sql=$sql.$order;
        $subs=$this->db->query($sql)->result();
        //[表名_字段名_主键_主键值]
        foreach($subs as $sub){
            $rp_sub_key="red_packets_sub.remainNum.id.$sub->id";
            if($this->dbhelper->cache_zscore("limit_zone",$rp_sub_key)===false){
                $this->dbhelper->cache_zadd("limit_zone",$rp_sub_key,$sub->remainNum);
            }
        }
        return $subs;
    }

    function send($red_packet, $amount, $activity, $scaninfo, $sub = NULL, $isapp = NULL) {
        $result=(object)['errcode'=>0,'errmsg'=>'ok'];
        $this->db->trans_start();
        $isWaiter=FALSE;
        if (intval($activity->role) === 1) {
            $isWaiter = TRUE;
        }
        $nowScanlog = $this->scan_log_model->get_by_code($scaninfo->code);
        if (! $isapp && ! $nowScanlog) {
            $result->errcode = 1;
            $result->errmsg = '查不到扫码记录';
            return $result;
        }
        if ($isWaiter) {
            $user = $this->user_model->get_waiter($scaninfo->userId);
        } else {
            $user = $this->user_model->get($scaninfo->userId);
        }
        $user_redpacket=(object)[
            'userId'=>$scaninfo->userId,
            'mchId'=>$scaninfo->mchId,
            'rpId'=>$red_packet->id,
            'amount'=>$amount,
            'getTime'=>time(),
            'scanId'=>isset($scaninfo->id)?$scaninfo->id:-1,
            'code'=>$scaninfo->code,
            'instId'=>-1,
            'role'=>$activity->role];
        //活动需要订阅时，将红包设为pending，待用户订阅后到账
        if($isapp){
            $user_redpacket->sended=0;
            $user_redpacket->instId=$scaninfo->instId;
        }else{
            if($isWaiter){
                $user_redpacket->sended=1;
            }else{
                $user_redpacket->sended=(!$activity->subscribeNeeded||$user->subscribe);
            }
        }
        //记录用户红包
        $this->db->insert('user_redpackets',$user_redpacket);
        $scaninfo->over=true;
        $scaninfo->rewardTable='user_redpackets';
        $scaninfo->rewardId=$this->db->insert_id();
        //修改为写入消息队列
        if(!$isapp){
            if($isWaiter){
                $this->scan_log_model->update_waiter($scaninfo);
                //$sql=$this->dbhelper->insert_update_string('scan_log_waiters',$scaninfo);
                //$this->dbhelper->set_cache_and_db('waiter_'.$scaninfo->code,$scaninfo,$sql);
                //$this->db->where('id',$scaninfo->id)->update('scan_log_waiters',$scaninfo);
            }else{
                $this->scan_log_model->update($scaninfo);
                //$sql=$this->dbhelper->insert_update_string('scan_log',$scaninfo);
                //$this->dbhelper->set_cache_and_db($scaninfo->code,$scaninfo,$sql);
                //$this->db->where('id',$scaninfo->id)->update('scan_log',$scaninfo);
            }
        }
        // user_packets 插入触发器 add by cw
        $this->trigger_model->trigger_user_redpacket_insert($scaninfo,$user_redpacket);
        // user_packets 插入触发器 end
        if (! $isapp && ! $user_redpacket->sended){
            log_message('debug','红包不满足发放条件 $user_redpacket id:'.var_export($user_redpacket,true));
            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE){
                log_message('error','红包入库事务失败:'.var_export($user_redpacket,true));
                return (object)['errcode'=>10,'errmsg'=>'红包发放失败'];
            }
            return (object)['errcode'=>0,'errmsg'=>'ok'];
        }
        if($isapp){
            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE){
                log_message('error','红包发放send事务失败:'.var_export($sendResult,true));
                return (object)['errcode'=>9,'errmsg'=>'红包发放失败'];
            }
            return (object)['errcode'=>0,'errmsg'=>'ok'];
        }
        $sendResult = $this->send_redpacket($red_packet,$scaninfo,$amount,$user_redpacket->role);
        if($sendResult->errcode!=0){
            log_message('error','红包发放失败 $sendResult:'.var_export($sendResult,true));
            return (object)['errcode'=>8,'errmsg'=>'红包发放失败'];
        }
        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE){
            log_message('error','红包发放失败 $事务失败:'.var_export($sendResult,true));
            return (object)['errcode'=>9,'errmsg'=>'红包发放失败'];
        }
        return (object)['errcode'=>0,'errmsg'=>'ok'];
    }

    //发放红包数据到数据库
    function send_redpacket($red_packet, $scaninfo, $amount, $role = 0) {
        //error("hls-red-packet-model-send-red-packet - begin");
        //error("hls-red-packet-model-send-red-packet - params：". json_encode(func_get_args()));
        $this->db->trans_start();
        //查询用户当前帐户余额
        $sql = "select * from user_accounts where userId = ? and mchId = ? and moneyType = ? limit 0,1 for update";
        $userAccount = $this->db->query($sql, [$scaninfo->userId, $scaninfo->mchId, $red_packet->rpType])->row();
        //用户帐户余额变量
        if ($userAccount) {
            $userCurrentAmount = $userAccount->amount;
        } else {
            $userCurrentAmount = 0;
        }
        /*
        @计入帐户余额的情况
        */
        if ($red_packet->isDirect == 0 ||
            $red_packet->rpType == 1 || // 裂变红包
            ($red_packet->rpType == 0 && $red_packet->isDirect == 1 && $red_packet->withBalance == 0 && $amount < 10) ||
            //普通微信红包合并余额，自动发放的，合并后大于200元或小于1元的红包
            ($red_packet->rpType == 0 && $red_packet->isDirect == 1 && $red_packet->withBalance == 1 && $red_packet->payment == 0 && ($amount + $userCurrentAmount > 20000 || $amount + $userCurrentAmount < 10)) ||
            //微信企业付款合并余额，自动发放的，合并后大于20000元或小于1元的红包
            ($red_packet->rpType == 0 && $red_packet->isDirect == 1 && $red_packet->withBalance == 1 && $red_packet->payment == 1 && ($amount + $userCurrentAmount > 2000000 || $amount+$userCurrentAmount < 10))
        ) {
            //error("hls-red-packet-model-send-red-packet - 计入账户余额");
            $user_account=(object)['userId'=>$scaninfo->userId,'mchId'=>$scaninfo->mchId,'moneyType'=>$red_packet->rpType,'amount'=>$amount];
            $sql = "INSERT INTO user_accounts (userId, mchId, moneyType, amount, role)
                VALUES ($user_account->userId, $user_account->mchId, $user_account->moneyType, IFNULL(amount, 0) + $user_account->amount, $role)
                ON DUPLICATE KEY UPDATE amount = IFNULL(amount, 0) + $user_account->amount";
            $this->db->query($sql);
            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE){
                //error("hls-red-packet-model-send-red-packet - fail: insert user_accounts fail");
                return (object)['errcode'=>4,'errmsg'=>'红包计入余额失败'];
            }
            //中奖通知模板消息写入数据库
            $activity=$this->db->where('id',$scaninfo->activityId)->get('sub_activities')->row();
            if($activity){
                $params = [
                    '恭喜您中奖啦！',
                    '中得“' . ($amount/100) . '元红包”',
                    $activity->name,
                    date('Y-m-d H:i:s', $activity->startTime) . ' 至 ' . date('Y-m-d H:i:s', $activity->endTime),
                    str_replace(PHP_EOL,'',$activity->content),
                    '点击查看'
                ];
                $formatMsg = $this->wx3rd_lib->template_format_data($scaninfo->mchId, 'get_redpacket', $params);
                $formatMsg->message=json_encode($formatMsg->message,JSON_UNESCAPED_UNICODE);
                $formatMsg->message=str_replace('\r\n','',$formatMsg->message);
                $formatMsg->message=trim($formatMsg->message,'"');
                $this->db->query("insert into user_template_msg(mchId,openid,formatMsg,createTime,updateTime) values($scaninfo->mchId,'$scaninfo->openId','".json_encode($formatMsg,JSON_UNESCAPED_UNICODE)."',".time().",".time().")");
            }
            return (object)['errcode'=>0,'errmsg'=>'ok'];

        }

        // 微信红包不合并余额自动发放
        if ($red_packet->rpType == 0 &&
            $red_packet->isDirect == 1 &&
            $red_packet->withBalance == 0 && $amount >= 10) {
            //error("hls-red-packet-model-send-red-packet: 普通红包不合并余额自动发放");
            $sql = "insert into user_trans(userId,mchId,amount,theTime,isAuto,moneyType,payType)
                    values (?, ?, ?, ?, ?, ?, ?)";
            $addParams = [
                $scaninfo->userId,
                $scaninfo->mchId,
                $amount,
                time(),
                1,
                $red_packet->rpType,
                $red_packet->payment
            ];
            $this->db->query($sql, $addParams);
            //提现成功以前不更新报表数据 mod by zht
            //$this->trigger_model->trigger_trans($scaninfo,$amount);// 提现触发器 add by cw
            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE){
                //error("hls-red-packet-model-send-red-packet - fail: insert user_trans fail");
                return (object)['errcode'=>5,'errmsg'=>'微信红包不合并余额自动发放失败'];
            }
            return (object)['errcode'=>0,'errmsg'=>'ok'];
        }

        // 微信红包合并余额自动发放
        if (
            ($red_packet->rpType==0 && $red_packet->isDirect==1 && $red_packet->payment==0 &&
            $red_packet->withBalance==1 && $amount+$userCurrentAmount>=10 && $amount+$userCurrentAmount<=20000) ||
            ($red_packet->rpType==0 && $red_packet->isDirect==1 && $red_packet->payment==1 &&
            $red_packet->withBalance==1 && $amount+$userCurrentAmount>=10 && $amount+$userCurrentAmount<=2000000)
        ) {
            //error("hls-red-packet-model-send-red-packet: 普通红包合并余额自动发放");

            // 增加用户提现申请
            $sql = "insert into user_trans(userId,mchId,amount,theTime,isAuto,moneyType,payType)
                    values(?, ?, ?, ?, ?, ?, ?)";
            $userTrans = [
                $scaninfo->userId,
                $scaninfo->mchId,
                $amount+$userCurrentAmount,
                time(),
                1,
                $red_packet->rpType,
                $red_packet->payment,
            ];
            $this->db->query($sql, $userTrans);

            // 用户账户更新为0
            $sql = "update user_accounts set amount = 0
                    where userId = ?  and mchId = ? and moneyType = 0 and amount > 0";
            $this->db->query($sql, [$scaninfo->userId, $scaninfo->mchId]);
            //提现成功以前不更新报表数据 mod by zht
            //$this->trigger_model->trigger_trans($scaninfo,$amount);// 提现触发器 add by cw
            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE){
                return (object)['errcode'=>6,'errmsg'=>'微信红包合并余额自动发放失败'];
            }
            return (object)['errcode'=>0,'errmsg'=>'ok'];
        }
        error("hls-red-packet-model-send-red-packet:未考虑的情况: ". json_encode($red_packet));
        return (object)['errcode'=>7,'errmsg'=>'红包发放失败'];
    }


    function get_by_mchid($mchId){
        return $this->db->where('mchId',$mchId)->where('rowStatus',0)->order_by('id','desc')->get('red_packets')->result();
    }
    function add_redpacket($data){
        $this->db->insert('red_packets',$data);
        return $this->db->insert_id();
    }
    function update_redpacket($id,$data){
        if($data->limitType==0){
            $key="red_packets.remainNum.id.$data->id";
            $v=$data->remainNum;
        }
        else if((int) $data->limitType==1){
            $key="red_packets.remainAmount.id.$data->id";
            $v=$data->remainAmount;
        }
        $this->redis->zAdd('limit_zone',$v,$key);
        return $this->db->where('id',$id)->update('red_packets',$data);
    }
    function del_redpacket($id){
        return $this->db->where('id',$id)->update('red_packets',['rowStatus'=>1]);
    }
    function get_sub($id){
        return $this->db->where('id',$id)->get('red_packets_sub')->row();
    }
    function add_redpacket_sub($data){
        $this->db->insert('red_packets_sub',$data);
        return $this->db->insert_id();
    }
    function update_redpacket_sub($id,$data){
        $rp_sub_key="red_packets_sub.remainNum.id.$data->id";
        $this->redis->zAdd('limit_zone',$data->remainNum,$rp_sub_key);
        return $this->db->where('id',$id)->update('red_packets_sub',$data);
    }
    function del_redpacket_sub($id){
        return $this->db->where('id',$id)->delete('red_packets_sub');
    }
    function get_sub_by_mchid($mchId){
        return $this->db->where('mchId',$mchId)->order_by('id','desc')->get('red_packets_sub')->result();
    }


    function try_red_packet($red_packet_id, $activity, $scaninfo, $isapp = NULL) {
        $id = $red_packet_id;

        $result = (object)([
            'errcode' => 0,
            'errmsg' => NULL,
            'datatype' => 0,
            'data' => NULL
        ]);

        // 随机获取一个笑话
        $joke = $this->jokes_model->get_joke();
        if ($joke) {
            $result->alt_text = $joke->text;
        }

        $red_packet = $this->get($id);
        if (! isset($red_packet)) {
            $result->errcode = 14;
            $result->errmsg = '没有这个红包策略';
            return $result; // 如果红包策略不存在，到此结束
        }

        // 如果是分级红包，走分级红包流程
        if ($red_packet->levelType == 1) {
            return $this->try_red_packet_level($red_packet,$activity,$scaninfo,$result,$isapp);
            // 如果是分级红包，到此结束
        }

        if (! $this->is_lucky($red_packet->probability)) {
            $result->errcode = 20;
            $result->errmsg = '运气不够好哦';
            return $result;
            // 如果没有抽中红包，到此结束
        }

        //如果是限制数量，检查红包是否发完
        if ($red_packet->limitType == 0) {
            $rp_cache_key = "red_packets.remainNum.id.$red_packet->id";
            //此处首先将数量或金额从缓存中减去，是为了避免高并发下的问题，因为此时如果从数据库
            //加锁处理，会引起大量阻塞。假设余额S=3，A用户提取4，B用户提取2，C用户提取。如果A
            //与B先后执行到此处，首先是A扣减，此时余额为-1，则A无法领取，B用户执行后为-3，B也
            //无法提取。A与B执行结束后，S再次恢复为3，此时C执行到此处，则有S=2，C可正常提取。
            //也就是说，在策略余额不足时，可能会出现先到者未得的情况。鉴于这些情况出现在抽奖临近
            //结束时，没必要为了这个业务的严谨性牺牲并发性能，因此不予加锁处理。
            $this->dbhelper->cache_zincrby("limit_zone",$rp_cache_key,-1);
            $remainNum = $this->dbhelper->cache_zscore('limit_zone',$rp_cache_key);
            debug("$rp_cache_key: $remainNum");
            if((int)$remainNum < 0) {
                $result->errcode=12;
                $result->errmsg='红包已发完';
                $this->dbhelper->cache_zincrby('limit_zone',$rp_cache_key,1);
                return $result;
                // 如果红包发放数量已超过，到此结束
            }
        }
        //固定金额
        if($red_packet->amtType==0){
            $amount=$red_packet->amount;
        }
        //随机金额
        if($red_packet->amtType==1){
            $amount=rand($red_packet->minAmount,$red_packet->maxAmount);
        }

        // Added by shizq - begin
        if ($red_packet->amtType == 2) {
            $settingStr = $red_packet->ruleStr; // '0.18:136,1.18:16,5.88:5,6.66:2,8.88:1';
            $settingArr = explode(',', $settingStr);
            $pool = [];
            foreach ($settingArr as $settingItem) {
                $settingItemArr = explode(':', $settingItem);
                for ($i = 0; $i < $settingItemArr[1]; $i++) {
                    $pool[] = $settingItemArr[0] * 100;
                }
            }
            $amount = $pool[mt_rand(0, count($pool) - 1)];
            if (! is_numeric($amount)) {
                $result->errcode = 19;
                $result->errmsg = '无法发放此红包';
                error("userId: ". $scaninfo->userId . "can not get red_packet: ". $amount);
                return $result;
            }
        }
        // Added by shizq - end

        //红包金额是否足够
        if($red_packet->limitType==1){
            $rp_cache_key="red_packets.remainAmount.id.$red_packet->id";
            $this->dbhelper->cache_zincrby('limit_zone',$rp_cache_key,-$amount);
            if((int)$this->dbhelper->cache_zscore('limit_zone',$rp_cache_key)<0){
                $result->errcode=13;
                $result->errmsg='红包已发完';
                $this->dbhelper->cache_zincrby('limit_zone',$rp_cache_key,$amount);
                return $result;
                // 如果红包发放金额已超过，到此结束
            }
        }

        $merchant = $this->merchant_model->get($activity->mchId);
        $send = $this->send($red_packet, $amount, $activity, $scaninfo, NULL, $isapp);
        if($send->errcode!=0){
            $result->errcode=21;
            $result->errmsg='红包发放失败';
            return $result;
        }
        $result->errcode = 0;
        $result->errmsg = '领到红包'.$amount.'分';
        $result->wx_qrcode_url = $this->config->item('base_url').'files/public/'.$merchant->id.'/'.$merchant->wxQrcodeUrl;
        if ($merchant->wxAuthStatus == 1) {
            $result->wx_qrcode_url = '/h5/get_qrcode/'. urlencode($merchant->wxQrcodeUrl);
        }
        $result->amount = $amount;
        if ($isapp) {
            $result->sType = $activity->finalStrategyType;
            $result->sId = $activity->finalStrategyId;
        }
        // Added by shizq begin ==============================
        if (isset($activity->finalStrategyId)) {
            $result->sId = $activity->finalStrategyId;
        }
        // Added by shizq end   ==============================
        return $result;
    }

    function try_red_packet_level($red_packet,$activity,$scaninfo,$result,$isapp=NULL){
        $subs=$this->get_subs_by_priority($red_packet->id,$red_packet->priority);
        $joke=$this->jokes_model->get_joke();
        if($joke){
            $result->alt_text=$joke->text;
        }
        if(!isset($subs)){
            $result->errcode=13;
            $result->errmsg='没有红包了哦';
            return $result;
        }
        $sub=NULL;
        foreach($subs as $s){
            if($this->is_lucky($s->probability)){
                $rp_sub_key="red_packets_sub.remainNum.id.$s->id";
                //判断数量是否足够，见注释，此处也有先到而未得红包的情况
                $this->dbhelper->cache_zincrby("limit_zone",$rp_sub_key,-1);
                if($this->dbhelper->cache_zscore("limit_zone",$rp_sub_key)<0){
                    $this->dbhelper->cache_zincrby("limit_zone",$rp_sub_key,1);
                    continue;
                }
                $result->amount=$s->amount;
                $sub=$s;
                break;
            }
        }
        if(!isset($sub)){
            $result->errcode=20;
            $result->errmsg='运气不够好哦';
            return $result;
        }

        $merchant=$this->merchant_model->get($activity->mchId);
        $this->send($red_packet,$sub->amount,$activity,$scaninfo,$sub,$isapp);
        $result->errcode=0;
        $result->errmsg='领到红包'.$sub->amount.'分';
        $result->wx_qrcode_url=$this->config->item('base_url').'files/public/'.$merchant->id.'/'.$merchant->wxQrcodeUrl;
        if($isapp){
            $result->sType=$activity->finalStrategyType;
            $result->sId=$activity->finalStrategyId;
        }
        if (isset($activity->finalStrategyId)) {
            $result->sId = $activity->finalStrategyId;
        }
        return $result;
    }

    function is_lucky($probability){
        $max_num=100000;
        $num=$probability*$max_num;
        $r=rand(1,$max_num);
        return $r<=$num;
    }

    //抽取乐券
    function try_card($id,$activity,$scaninfo,$isapp=NULL){
        $result=(object)[
                'errcode'=>0,
                'errmsg'=>'',
                'datatype'=>2,
                'data'=>NULL,
                'mchId'=>$scaninfo->mchId
        ];
        $cardGroup=$this->card_model->get_cardgroup($id);
        $joke=$this->jokes_model->get_joke();
        if($joke){
            $result->alt_text=$joke->text;
        }
        if(!isset($cardGroup)){
            $result->errcode=12;
            $result->errmsg='没有这个乐券策略';
            return $result;
        }
        $subs=$this->get_cards_by_priority($cardGroup->id,$cardGroup->priority);
        if(!isset($subs)){
            $result->errcode=1;
            $result->errmsg='没有乐券了哦';
            return $result;
        }
        $sub=NULL;
        foreach($subs as $s){
            if($this->is_lucky($s->probability/100)){//乐券表保存的40%的40整数部分
                //利用缓存限制数量
                $card_sub_key="cards.remainNum.id.$s->id";
                $this->dbhelper->cache_zincrby("limit_zone",$card_sub_key,-1);
                if($this->dbhelper->cache_zscore("limit_zone",$card_sub_key)<0){
                    $this->dbhelper->cache_zincrby("limit_zone",$card_sub_key,1);
                    continue;
                }
                $result->data=(object)['id'=>$s->id,'name'=>$s->title];
                $sub=$s;
                break;
            }
        }
        if(!isset($sub)){
            $result->errcode=20;
            $result->errmsg='运气不够好哦';
            return $result;
        }
        $merchant=$this->merchant_model->get($activity->mchId);
        $send_result = $this->send_card($sub->id,$activity,$scaninfo,$sub,$isapp);

        // ========================== Added by shizq start ==========================
        if ($send_result->errcode) {
            $result->errcode = $send_result->errcode;
            $result->errmsg = $send_result->errmsg;
            $result->wx_qrcode_url = $this->config->item('base_url') .
                'files/public/' . $merchant->id . '/' . $merchant->wxQrcodeUrl;
            return $result;
        }
        // ==========================  Added by shizq end  ==========================

        $result->errcode=0;
        if ($send_result->errmsg == 'youzan') {
            $result->card_type = 'youzan';
            $result->marcket_url = $merchant->marcketUrl or 'https://www.baidu.com/';
        }
        $result->errmsg='获得“'.$sub->title.'”';
        $result->wx_qrcode_url=$this->config->item('base_url').'files/public/'.$merchant->id.'/'.$merchant->wxQrcodeUrl;
        if($isapp){
            $result->sType=$activity->finalStrategyType;
            $result->sId=$activity->finalStrategyId;
        }
        // Added by shizq begin ==============================
        if (isset($activity->finalStrategyId)) {
            $result->sId = $activity->finalStrategyId;
        }
        // Added by shizq end   ==============================
        return $result;
    }
    //按优先级获取乐券
    function get_cards_by_priority($id,$priority){
        $sql="select c.* from cards c inner join cards_group cg
            on c.parentId=cg.id where cg.id=$id and cg.rowStatus=0 and c.remainNum>0 and c.rowStatus=0";
        if($priority==0){
            $order=' order by rand()';
        }
        else if($priority==1){
            $order=' order by probability';
        }
        else if($priority==2){
            $order=' order by probability desc';
        }
        $sql=$sql.$order;
        $subs=$this->db->query($sql)->result();
        foreach($subs as $sub){
            $card_sub_key="cards.remainNum.id.$sub->id";
            if($this->dbhelper->cache_zscore("limit_zone",$card_sub_key)===false){
                $this->dbhelper->cache_zadd("limit_zone",$card_sub_key,$sub->remainNum);
            }
        }
        return $subs;
    }

    //乐券发放
    function send_card($cardId,$activity,$scaninfo,$sub,$isapp=NULL){
        // ========================== Added by shizq start ==========================
        $mch_id = $scaninfo->mchId;
        $openid = $scaninfo->openId;
        $card = $this->db
            ->where('id', $cardId)
            ->get('cards')->row();
        if (! isset($card)) {
            return (object)['errcode' => 1, 'errmsg' => '发生未知错误'];
        }
        debug("Send card to weixin user: $openid");
        switch ($card->cardType) {
            case 1: // 有赞商户平台优惠券
                if ($activity->role == 1) {
                    // 服务员不参与有赞卡券中奖
                    return (object)['errcode' => 1, 'errmsg' => '真遗憾，你没有中奖'];
                }
                $this->load->helper('youzan');
                $params = [
                    'coupon_group_id' => $card->couponGroupId,
                    'weixin_openid' => $openid
                ];
                debug('request youzan_api, params is: '. json_encode($params));
                $result = youzan_api($scaninfo->mchId, 'kdt.ump.coupon.take', $params);
                if (isset($result['error_response'])) {
                    debug("receive card faild: " . $result['error_response']['msg'] . ' errcode:' . $result['error_response']['code']);
                    return (object)['errcode' => 90001, 'errmsg' => '（' . $card->title . '）' . $result['error_response']['msg']];
                } else {
                    debug("receive card success");
                    $sub->remainNum -= 1;
                    $this->db->where('id', $sub->id)->update('cards', $sub);
                    //中奖通知模板消息写入数据库
                    $formatMsg=$this->wx3rd_lib->template_format_data($scaninfo->mchId,'get_card_youzan',
                                ['恭喜您中奖啦！','中得“'.$sub->title.'”',$activity->name,date('Y-m-d H:i:s',$activity->startTime).' 至 '.date('Y-m-d H:i:s',$activity->endTime),str_replace(PHP_EOL,'',$activity->content),'请到【有赞商城】查看使用。']);
                    $formatMsg->message=json_encode($formatMsg->message,JSON_UNESCAPED_UNICODE);
                    $formatMsg->message=str_replace('\r\n','',$formatMsg->message);
                    $formatMsg->message=trim($formatMsg->message,'"');
                    $this->db->query("insert into user_template_msg(mchId,openid,formatMsg,createTime,updateTime) values($scaninfo->mchId,'$scaninfo->openId','".json_encode($formatMsg,JSON_UNESCAPED_UNICODE)."',".time().",".time().")");

                    return (object)['errcode' => 0, 'errmsg' => 'youzan'];
                }
        }
        // ==========================  Added by shizq end  ==========================
        $this->db->trans_start();
        $isWaiter=FALSE;
        if(intval($activity->role)===1){
            $isWaiter=TRUE;
        }
        debug('scaninfo: '. json_encode($scaninfo));
        if($isWaiter){
            $user=$this->user_model->get_waiter($scaninfo->userId);
        }else{
            $user=$this->user_model->get($scaninfo->userId);
        }
        $user_card=(object)[
            'userId'=>$scaninfo->userId,
            'role'=>$activity->role,
            'cardId'=>$cardId,
            'scanId'=>isset($scaninfo->id)?$scaninfo->id:-1,
            'code'=>$scaninfo->code,
            'instId'=>-1,
            'getTime'=>time(),
            'status'=>0
        ];
        //活动需要订阅时，将乐券设为pending，待用户订阅后到账
        if(!$isapp){
            if($isWaiter){
                $user_card->sended=1;
            }else{
                debug('activity subscribeNeeded: '. json_encode($activity));
                debug('current user: '. json_encode($user));
                $user_card->sended=(!$activity->subscribeNeeded||$user->subscribe);
            }
        }else{
            $user_card->sended=0;
            $user_card->instId=$scaninfo->instId;
        }
        //记录用户乐券
        $this->db->insert('user_cards',$user_card);
        $scaninfo->over=true;
        $scaninfo->rewardTable='user_cards';
        $scaninfo->rewardId=$this->db->insert_id();
        if(!$isapp){
            if($isWaiter){
                $this->scan_log_model->update_waiter($scaninfo);
                //$this->db->where('id',$scaninfo->id)->update('scan_log_waiters',$scaninfo);
            }else{
                $this->scan_log_model->update($scaninfo);
                //$this->db->where('id',$scaninfo->id)->update('scan_log_waiters',$scaninfo);
                //$this->db->where('id',$scaninfo->id)->update('scan_log',$scaninfo);
            }
        }
        if($user_card->sended){
            $user_cards_account=(object)['userId'=>$scaninfo->userId,'role'=>$user_card->role,'mchId'=>$scaninfo->mchId,'cardId'=>$cardId];
            $sql1 = "INSERT INTO user_cards_account (userId, role, mchId, cardId, num)
                    VALUES ($user_cards_account->userId, $user_cards_account->role, $user_cards_account->mchId, $user_cards_account->cardId, IFNULL(num, 0) + 1)
                    ON DUPLICATE KEY
                    UPDATE num = IFNULL(num, 0) + 1";
            // ---------------- Added by shizq begin ---------------
            debug("User cards account changed, data is: " . json_encode($user_cards_account));
            // ----------------  Added by shizq end  ---------------
            $this->db->query($sql1);
            //中奖通知模板消息写入数据库
            $formatMsg=$this->wx3rd_lib->template_format_data($scaninfo->mchId,'get_card',
                            ['恭喜您中奖啦！','中得“'.$sub->title.'”',$activity->name,date('Y-m-d H:i:s',$activity->startTime).' 至 '.date('Y-m-d H:i:s',$activity->endTime),str_replace(PHP_EOL,'',$activity->content),'已存入我的乐券，点击查看。']);
            $formatMsg->message=json_encode($formatMsg->message,JSON_UNESCAPED_UNICODE);
            $formatMsg->message=str_replace('\r\n','',$formatMsg->message);
            $formatMsg->message=trim($formatMsg->message,'"');
            $this->db->query("insert into user_template_msg(mchId,openid,formatMsg,createTime,updateTime) values($scaninfo->mchId,'$scaninfo->openId','".json_encode($formatMsg,JSON_UNESCAPED_UNICODE)."',".time().",".time().")");

        }
        //debug('乐券缓存写入');
        //$card_sub_key="card.$sub->parentId.sub.$sub->id.limit";
        //debug($card_sub_key);
        //debug($this->dbhelper->cache_get("$card_sub_key.expire"));
        //debug(time());
        //if((int)$this->dbhelper->cache_get("$card_sub_key.expire")<time()||(int)$this->dbhelper->cache_get($card_sub_key)<=0){
        //    debug('乐券缓存过期');
        //    $remainNum=$this->dbhelper->cache_get($card_sub_key);
        //    $sql="update cards set remainNum=$remainNum where id=$sub->id";
        //    $this->dbhelper->push('sql_master',$sql);
        //    $this->dbhelper->cache_set("$card_sub_key.expire",time()+3);
        //}
        //$sub->remainNum-=1;
        //$this->db->where('id',$sub->id)->update('cards',$sub);
        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE){
            return (object)['errcode'=>1,'errmsg'=>'发放失败'];
        }
        // user_cards 触发器 add by cw
        $this->trigger_model->trigger_user_cards($scaninfo,$user_card);
        // user_cards 插入触发器 end
        return (object)['errcode'=>0,'errmsg'=>'ok'];
    }

    //尝试抽取组合策略
    function try_mixstrategy($id,$activity,$scaninfo,$isapp=NULL){
        $result=(object)[
                'errcode'=>0,
                'errmsg'=>'',
                'mchId'=>$scaninfo->mchId
        ];
        $mix=$this->mixstrategy_model->get($id);
        if(!$mix){
            $result->errcode=50;
            $result->errmsg='组合策略不存在';
            return $result;
        }

        $mixSub=$this->mixstrategy_model->get_sub_by_pid($id);
        if(!$mixSub){
            $result->errcode=51;
            $result->errmsg='组合策略获取失败';
            return $result;
        }

        //-------Modify by wangcq begin-------
        $max_num=0;
        foreach ($mixSub as $k => $v) {
            //if($v->enabled==='1'){
                if($mix->hasEnabled==1){
                    if($v->strategyType==='0'){
                        if($this->exists_red_packet($v->strategyId,$v->id)->errcode!=0){
                            $v->enabled='0';
                            continue;
                        }
                    }
                    if($v->strategyType==='2'){
                        if($this->exists_card($v->strategyId,$v->id)->errcode!=0){
                            $v->enabled='0';
                            continue;
                        }
                    }
                    if($v->strategyType==='3'){
                        if($this->exists_point($v->strategyId,$v->id)->errcode!=0){
                            $v->enabled='0';
                            continue;
                        }
                    }
                    $v->enabled='1';
                }
                else{
                    $v->enabled='1';
                }

                $max_num+=intval($v->weight);
            //}
        }

        $r=rand(1,$max_num);

        $strategy=null;
        $current=0;
        foreach ($mixSub as $k => $v) {
            if($v->enabled==='1'){
                $weight=intval($v->weight);
                if($r>$current && $r<=$current+$weight){
                    $strategy=$v;
                }
                $current+=$weight;
            }
        }

        if($strategy==null){
            $result->errcode=53;
            $result->errmsg='奖品已发完';
            return $result;
        }
        //-------Modify by wangcq end-------

        if(!$strategy){
            $result->errcode=52;
            $result->errmsg='匹配活动策略失败';
            return $result;
        }
        //最终匹配策略
        // if($isapp){ removed by shizq
            $activity->finalStrategyType=$strategy->strategyType;
            $activity->finalStrategyId=$strategy->strategyId;
        // }

        if($strategy->strategyType==0){
            return $this->try_red_packet($strategy->strategyId,$activity,$scaninfo,$isapp);
        }
        if($strategy->strategyType==2){
            return $this->try_card($strategy->strategyId,$activity,$scaninfo,$isapp);
        }
        if($strategy->strategyType==3){

            return $this->try_point($strategy->strategyId,$activity,$scaninfo,$isapp);
        }
    }

    function set_disabled($msid){
        //$this->db->query("update mix_strategies_sub set enabled=0 where id=$msid");
    }

    function exists_red_packet($id,$msid){
        $red_packet=$this->get($id);
        if(!isset($red_packet)){
            return (object)['errcode'=>1,'errmsg'=>'没有这个红包策略'];
        }
        //如果是分级红包，走分级红包流程
        if($red_packet->levelType==1){
            $subs=$this->get_subs_by_priority($red_packet->id,$red_packet->priority);
            if(!isset($subs)){
                return (object)['errcode'=>1,'errmsg'=>'没有红包了'];
            }
            foreach($subs as $s){
                $rp_sub_key="red_packets_sub.remainNum.id.$s->id";
                //判断数量是否足够，见注释，此处也有先到而未得红包的情况
                if($this->dbhelper->cache_zscore("limit_zone",$rp_sub_key)>0){
                    return (object)['errcode'=>0,'errmsg'=>'ok'];
                }
            }
            $this->set_disabled($msid);
            return (object)['errcode'=>1,'errmsg'=>'奖品已发完'];
        }
        if(($red_packet->limitType==0)){
            $rp_cache_key="red_packets.remainNum.id.$red_packet->id";
            //此处首先将数量或金额从缓存中减去，是为了避免高并发下的问题，因为此时如果从数据库
            //加锁处理，会引起大量阻塞。假设余额S=3，A用户提取4，B用户提取2，C用户提取。如果A
            //与B先后执行到此处，首先是A扣减，此时余额为-1，则A无法领取，B用户执行后为-3，B也
            //无法提取。A与B执行结束后，S再次恢复为3，此时C执行到此处，则有S=2，C可正常提取。
            //也就是说，在策略余额不足时，可能会出现先到者未得的情况。鉴于这些情况出现在抽奖临近
            //结束时，没必要为了这个业务的严谨性牺牲并发性能，因此不予加锁处理。
            if((int)$this->dbhelper->cache_zscore('limit_zone',$rp_cache_key)<=0){
                $this->set_disabled($msid);
                return (object)['errcode'=>1,'errmsg'=>'奖品已发完'];
            }
        }
        return (object)['errcode'=>0,'errmsg'=>'ok'];
    }

    function exists_card($id,$msid){
        $cardGroup=$this->card_model->get_cardgroup($id);
        if(!isset($cardGroup)){
            return (object)['errcode'=>1,'errmsg'=>'没有这个乐券策略'];
        }
        $subs=$this->get_cards_by_priority($cardGroup->id,$cardGroup->priority);
        if(!isset($subs)){
            return (object)['errcode'=>1,'errmsg'=>'没有乐券了'];
        }
        foreach($subs as $s){
            $card_sub_key="cards.remainNum.id.$s->id";
            if($this->dbhelper->cache_zscore("limit_zone",$card_sub_key)>0){
                return (object)['errcode'=>0,'errmsg'=>'ok'];
            }
        }
        $this->set_disabled($msid);
        return (object)['errcode'=>1,'errmsg'=>'乐券已发完'];
    }

    function exists_point($id,$msid){
        $point=$this->point_model->get($id);
        if(!isset($point)){
            return (object)['errcode'=>1,'errmsg'=>'没有这个积分策略'];
        }
        $subs=$this->get_point_subs_by_priority($point->id,$point->priority);
        if(!isset($subs)){
            return (object)['errcode'=>1,'errmsg'=>'没有积分了'];
        }
        foreach($subs as $s){
            $point_sub_key="points_sub.remainNum.id.$s->id";
            if($this->dbhelper->cache_zscore("limit_zone",$point_sub_key)>0){
                return (object)['errcode'=>0,'errmsg'=>'ok'];
            }
        }
        $this->set_disabled($msid);
        return (object)['errcode'=>1,'errmsg'=>'积分已发完'];
    }

    /**
     * 尝试抽积分
     */
    function try_point($id,$activity,$scaninfo,$isapp=NULL){

        log_message('debug', 'function try_point');
        $result=(object)([
                'errcode'=>0,
                'errmsg'=>'',
                'datatype'=>3,
                'data'=>NULL]);
        $point=$this->point_model->get($id);
        $joke=$this->jokes_model->get_joke();
        if($joke){
            $result->alt_text=$joke->text;
        }
        if(!isset($point)){
            $result->errcode=14;
            $result->errmsg='没有这个积分策略';
            return $result;
        }
        return $this->try_point_level($point,$activity,$scaninfo,$result,$isapp);
    }

    /**
     * 尝试抽分级积分
     */
    function try_point_level($point,$activity,$scaninfo,$result,$isapp=NULL){
        log_message('debug', 'function try_point_level');
        $subs=$this->get_point_subs_by_priority($point->id,$point->priority);
        if(!isset($subs)){
            $result->errcode=13;
            $result->errmsg='没有积分了哦';
            return $result;
        }
        $sub=NULL;
        foreach($subs as $s){
            if($this->is_lucky($s->probability)){
                $point_sub_key="points_sub.remainNum.id.$s->id";
                $this->dbhelper->cache_zincrby("limit_zone",$point_sub_key,-1);
                if($this->dbhelper->cache_zscore("limit_zone",$point_sub_key)<0){
                    $this->dbhelper->cache_zincrby("limit_zone",$point_sub_key,1);
                    continue;
                }
                $result->data=(object)['id'=>$s->id,'amount'=>$s->amount];
                $result->amount=$s->amount;
                $sub=$s;
                break;
            }
        }
        if(!isset($sub)){
            $result->errcode=20;
            $result->errmsg='运气不够好哦';
            return $result;
        }
        $merchant=$this->merchant_model->get($activity->mchId);
        $send_point=$this->send_point($point,$sub->amount,$activity,$scaninfo,$sub,$isapp);
        if ($send_point->errcode === 6014002) {
            $result->errcode = $send_point->errcode;
            $result->errmsg = $send_point->errmsg;
            return $result;
        }
        if ($send_point->errcode === 6014004) {
            $result->errcode = $send_point->errcode;
            $result->errmsg = $send_point->errmsg;
            return $result;
        }
        debug("send piont fail: errcode is: $send_point->errcode");
        debug("errmsg: $send_point->errmsg");
        if($send_point->errcode!=0){
            $result->errcode=21;
            $result->errmsg='积分发放失败';
            return $result;
        }
        $result->errcode=0;
        $result->errmsg='获得'.$sub->amount.'积分';
        $result->wx_qrcode_url=$this->config->item('base_url').'files/public/'.$merchant->id.'/'.$merchant->wxQrcodeUrl;
        if($isapp){
            $result->sType=$activity->finalStrategyType;
            $result->sId=$activity->finalStrategyId;
        }
        if (isset($activity->finalStrategyId)) {
            $result->sId = $activity->finalStrategyId;
        }
        if ($activity->id == 4121) {
            error(json_encode($result));
        }
        return $result;
    }

    //尝试抽取累计策略
    function try_accumstrategy($id,$activity,$scaninfo,$isapp=NULL){
        //error('red-packet-model-try-accumstrategy - begin');
        $result=(object)[
                'errcode'=>0,
                'errmsg'=>'',
                'mchId'=>$scaninfo->mchId
        ];
        $accum=$this->accumstrategy_model->get($id);
        if(!$accum){
            $result->errcode=50;
            $result->errmsg='累计策略不存在';
            return $result;
        }

        $accumSub=$this->accumstrategy_model->get_sub_by_pid($id);
        if(!$accumSub){
            $result->errcode=51;
            $result->errmsg='累计策略获取失败';
            return $result;
        }
        $setKey = 'consumer_'. $scaninfo->userId .'_'. $activity->id;
        $setName = 'user_activity_count_set';
        $scanCount = $this->redis->zScore($setName, $setKey);
        
        // error('red-packet-model-try-accumstrategy - 活动参与次数: '. $scanCount);

        if ($this->accumstrategy_model->hasBonus($id)) {
            $bonus = $this->accumstrategy_model->tryBonus($id, $scaninfo->userId, $scanCount);
            if ($bonus) {
                error('red-packet-model-try-accumstrategy - 匹配大奖: '. json_encode($bonus));
                if($bonus->strategyType==0){
                    return $this->try_red_packet($bonus->strategyId,$activity,$scaninfo,$isapp);
                }
                if($bonus->strategyType==2){
                    return $this->try_card($bonus->strategyId,$activity,$scaninfo,$isapp);
                }
                if($bonus->strategyType==3){
                    return $this->try_point($bonus->strategyId,$activity,$scaninfo,$isapp);
                }
            }
        }

        $strategy=null;
        foreach ($accumSub as $k => $v) {
            if (! isProd()) {
                error('red-packet-model-try-accumstrategy - 策略'. $k .': '. json_encode($v));
            }
            if($v->start<=$scanCount && $v->end>=$scanCount){
                $strategy=$v;
                break;
            }
        }
        if(!$strategy){
            $result->errcode=52;
            $result->errmsg='没有适合您的策略';
            return $result;
        }
        if($strategy->strategyType==0){
            return $this->try_red_packet($strategy->strategyId,$activity,$scaninfo,$isapp);
        }
        if($strategy->strategyType==2){
            return $this->try_card($strategy->strategyId,$activity,$scaninfo,$isapp);
        }
        if($strategy->strategyType==3){
            return $this->try_point($strategy->strategyId,$activity,$scaninfo,$isapp);
        }
    }

    /**
     * 分级积分情况，按优先级获取子项
     */
    function get_point_subs_by_priority($id,$priority){
        $sql="select sub.* from points_sub sub inner join points p
            on sub.parentId=p.id where p.id=$id and sub.remainNum>0";
        if($priority==0){
            $order=' order by rand()';
        }
        else if($priority==1){
            $order=' order by amount';
        }
        else if($priority==2){
            $order=' order by amount desc';
        }
        $sql=$sql.$order;
        $subs=$this->db->query($sql)->result();
        foreach($subs as $sub){
            $point_sub_key="points_sub.remainNum.id.$sub->id";
            if($this->dbhelper->cache_zscore("limit_zone",$point_sub_key)===false){
                $this->dbhelper->cache_zadd("limit_zone",$point_sub_key,$sub->remainNum);
            }
        }
        return $subs;
    }

    /**
     * 发放积分
     */
    function send_point($point,$amount,$activity,$scaninfo,$sub=NULL,$isapp=NULL){
        $this->db->trans_start();
        $isWaiter=FALSE;
        if(intval($activity->role)===1){
            $isWaiter=TRUE;
        }
        if($isWaiter){
            $user=$this->user_model->get_waiter($scaninfo->userId);
        }else{
            $user=$this->user_model->get($scaninfo->userId);
        }

        // ------------------------------------------
        // Added by shizq - begin
        // 判断如果是第三方积分，调用第三方接口处理
        switch ($sub->third_number) {
            case 1: // 人人店积分
                // 获取企业人人店配置
                // $merchant = $this->merchant->get($scaninfo->mchId);
                // if (! isset($merchant->rrdAppId) || ! isset($merchant->rrdAppId)) {
                //     return (object)['errcode' => 1, 'errmsg' => '人人店未正确配置'];
                // }
                // if ($merchant->rrdAccessTokenExpiresd < time()) {
                //     return (object)['errcode' => 1, 'errmsg' => 'RrdAccessToken已过期'];
                // }
                // $this->load->library('common/weiba', ['appId' => $merchant->rrdAppId, 'secret' => $merchant->rrdSecret,
                //     'accessToken' => $merchant->rrdAccessToken]);
                // try {
                //     $resp = $this->weiba->sendCredit($scaninfo->openId, $amount);
                // } catch (Exception $e) {
                //     if ($e->getCode() == 6014002) {
                //         return (object)['errcode' => $e->getCode(), 'errmsg' => '您还没有注册人人店'];
                //     }
                //     if ($e->getCode() == 6014004) {
                //         return (object)['errcode' => $e->getCode(), 'errmsg' => '找不到会员信息'];
                //     }
                //     return (object)['errcode' => $e->getCode(), 'errmsg' => '人人店积分发送失败'];
                // }
                // debug('sendCredit: '. json_encode($resp));
                // if (isset($resp->errCode)) {
                //     return (object)['errcode' => $resp->errCode, 'errmsg' => '人人店积分发送失败'];
                // } else {
                //     log_message('debug', 'send rrd credit success');
                // }
            default:
                break;
        }
        // Added by shizq -end
        // ------------------------------------------

        $user_point=(object)[
            'userId'=>$scaninfo->userId,
            'mchId'=>$scaninfo->mchId,
            'pointsId'=>$point->id,
            'amount'=>$amount,
            'getTime'=>time(),
            'code'=>$scaninfo->code,
            'scanId'=>isset($scaninfo->id)?$scaninfo->id:-1,
            'code'=>$scaninfo->code,
            'instId'=>-1,
            'role'=>$activity->role];
        //活动需要订阅时，将红包设为pending，待用户订阅后到账
        if($isapp){
            $user_point->sended=0;
            $user_point->instId=$scaninfo->instId;
        }else{
            if($isWaiter){
                $user_point->sended=1;
            }else{
                $user_point->sended=(!$activity->subscribeNeeded||$user->subscribe);
            }
        }
        //记录用户积分
        if (! $sub->third_number) {
            $this->db->insert('user_points',$user_point);
        }
        $scaninfo->over=true;
        $scaninfo->rewardTable='user_points';
        $scaninfo->rewardId=$this->db->insert_id();
        if(!$isapp){
            if($isWaiter){
                $this->scan_log_model->update_waiter($scaninfo);
                //$this->db->where('id',$scaninfo->id)->update('scan_log_waiters',$scaninfo);
            }else{
                $this->scan_log_model->update($scaninfo);
                //$this->db->where('id',$scaninfo->id)->update('scan_log',$scaninfo);
            }
        }
        if($user_point->sended){

             if (! $sub->third_number) {
                $user_account=(object)['userId'=>$scaninfo->userId,'mchId'=>$scaninfo->mchId,'amount'=>$amount];
                    $sql="INSERT INTO user_points_accounts(userId,mchId,amount,role)
                        VALUES($user_account->userId,$user_account->mchId,IFNULL(amount,0)+$user_account->amount,$activity->role)
                        ON DUPLICATE KEY UPDATE amount=IFNULL(amount,0)+$user_account->amount";
                    $this->db->query($sql);
                }

                //$params = [
                //    'user_id'       => $scaninfo->userId,
                //    'mch_id'        => $scaninfo->mchId,
                //    'scan_id'       =>isset($scaninfo->id) ? $scaninfo->id : -1,
                //    'batch_id'      => $scaninfo->batchId,
                //    'point_amount'  => $amount,
                //    'event_time'    => $user_point->getTime,
                //];
                // 触发用户获取积分事件,服务员或业务员不参与报表统计
                if ($activity->role == 0) {
                    $this->trigger_model->trigger_after_get_point($scaninfo,$user_point);
                }
                //中奖通知模板消息写入数据库
                $formatMsg=$this->wx3rd_lib->template_format_data($scaninfo->mchId,'get_point',
                        ['恭喜您中奖啦！','中得“'.$amount.'积分”',$activity->name,date('Y-m-d H:i:s',$activity->startTime).' 至 '.date('Y-m-d H:i:s',$activity->endTime),str_replace(PHP_EOL,'',$activity->content),'点击查看']);
                $formatMsg->message=json_encode($formatMsg->message,JSON_UNESCAPED_UNICODE);
                $formatMsg->message=str_replace('\r\n','',$formatMsg->message);
                $formatMsg->message=trim($formatMsg->message,'"');
                $this->db->query("insert into user_template_msg(mchId,openid,formatMsg,createTime,updateTime) values($scaninfo->mchId,'$scaninfo->openId','".json_encode($formatMsg,JSON_UNESCAPED_UNICODE)."',".time().",".time().")");

        }
        //$point_sub_key="point.$sub->parentId.sub.$sub->id.limit";
        //if((int)$this->dbhelper->cache_get("$point_sub_key.expire")<time()||(int)$this->dbhelper->cache_get($point_sub_key)<=0){
        //    $remainNum=$this->dbhelper->cache_get($point_sub_key);
        //    $sql="update points_sub set remainNum=$remainNum where id=$sub->id";
        //    $this->dbhelper->push('sql_master',$sql);
        //    $this->dbhelper->cache_set("$point_sub_key.expire",time()+3);
        //}
        //$sub->remainNum-=1;
        //$this->db->where('id',$sub->id)->update('points_sub',$sub);
        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE){
            return (object)['errcode'=>1,'errmsg'=>'积分发放失败'];
        }
        return (object)['errcode'=>0,'errmsg'=>'ok'];
    }

    /**
     * 尝试抽取叠加策略
     */
    function try_multistrategy($id,$activity,$scaninfo,$isapp=NULL){
        $result=(object)[
                'errcode'=>0,
                'errmsg'=>'',
                'datatype'=>100,
                'mchId'=>$scaninfo->mchId
        ];
        $multi=$this->multistrategy_model->get($id);
        if(!$multi){
            $result->errcode=50;
            $result->errmsg='叠加策略不存在';
            return $result;
        }
        $multiSub=$this->multistrategy_model->get_sub_by_pid($id);
        if(!$multiSub){
            $result->errcode=51;
            $result->errmsg='叠加策略获取失败';
            return $result;
        }
        $result->multiData=[];
        foreach ($multiSub as $k => $v) {
            if($v->strategyType==0){
                $dataRp=$this->try_red_packet($v->strategyId,$activity,$scaninfo,$isapp);
                if($dataRp->errcode==0){
                    $resultRp=(object)['strategyType'=>0,'strategyId'=>$v->strategyId,'value'=>$dataRp->amount];
                    array_push($result->multiData,$resultRp);
                }
            }
            if($v->strategyType==2){
                $dataCard=$this->try_card($v->strategyId,$activity,$scaninfo,$isapp);
                if($dataCard->errcode==0){
                    $resultCard=(object)['strategyType'=>2,'strategyId'=>$v->strategyId,'value'=>$dataCard->data->name];
                    array_push($result->multiData,$resultCard);
                }
            }
            if($v->strategyType==3){
                $dataPoint=$this->try_point($v->strategyId,$activity,$scaninfo,$isapp);
                if($dataPoint->errcode==0){
                    $resultPoint=(object)['strategyType'=>3,'strategyId'=>$v->strategyId,'value'=>$dataPoint->amount];
                    array_push($result->multiData,$resultPoint);
                }
            }
        }
        return $result;
    }

}
