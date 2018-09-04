<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class User_model extends CI_Model {
    public function __construct()
    {
        // Call the CI_Model constructor
        parent::__construct();
    }
    function get_by_phone($phone){
        return $this->db->where('phoneNum',$phone)->get('mch_accounts')->row();
    }
    //企业端
    //这叫啥鸡巴破名字？简称？全拼？谁看了这个方法知道啥意思？
    function get_accm($account){
        return $this->db->where('mail',$account)->get('mch_accounts')->row_array();
    }
    //同上
    function get_accp($account){
        return $this->db->where('phoneNum',$account)->get('mch_accounts')->row_array();
    }
    function get($id){
        return $this->db->where('id',$id)->get('users')->row();
    }

    function get_by_openid($openid){
        return $this->db->where('openId',$openid)->get('users')->row();
    }

    function get_common_by_openid($openid){
        return $this->db->where('openId',$openid)->get('users_common')->row();
    }

    function get_by_openid_waiter($openid){
        return $this->db->where('openId',$openid)->get('waiters')->row();
    }

    function get_waiter($id) {
        return $this->db->where('id',$id)->get('waiters')->row();
    }

    function get_common_by_sub_openid($openid){
        return $this->db->query("select c.* from users_common c inner join users_common_sub s on s.parentId=c.id where s.openId='$openid' limit 0,1")->row();
    }

    function save($user){
        if(isset($user->id)){
            $this->db->where('id',$user->id)->update('users',$user);
            return $user->id;
        }
        else{
            if (property_exists($user, 'unionid')) {
                unset($user->unionid);
            }
            debug('user-model-save - insert users: '. json_encode($user));
            $this->db->insert('users',$user);
            return $this->db->insert_id();
        }

    }

    function save_common($user){
        if(isset($user->id)){
            $this->db->where('id',$user->id)->update('users_common',$user);
            return $user->id;
        }
        else{
            if (property_exists($user, 'unionid')) {
                unset($user->unionid);
            }
            $this->db->insert('users_common',$user);
            return $this->db->insert_id();
        }

    }

    function insert_user_update($wxYsId,$openid){
        debug('insert_user_update'. json_encode(func_get_args()));
        $theTime=time()-60;
        $record=$this->db->query("select * from user_update where wxYsId='$wxYsId' and openid='$openid' and createTime>$theTime limit 0,1")->row();
        if(!$record){
            $this->db->query("insert into user_update(wxYsId,openid,status,createTime) values('$wxYsId','$openid',0,".time().")");
        }
    }

    function get_amount($userId,$mchId,$moneyType){
        return $this->db
        ->where('userId',$userId)
        ->where('mchId',$mchId)
        ->where('moneyType',$moneyType)
        ->get('user_accounts')->row();
    }

    function get_point($userId,$mchId){
        return $this->db
        ->where('userId',$userId)
        ->where('mchId',$mchId)
        ->get('user_points_accounts')->row();
    }

    function withdraw($userId,$mchId,$moneyType,$amount,$payType,$payAccountType){
        $ret=(object)['errcode'=>0,'errmsg'=>''];
        $this->db->trans_begin();
        if($this->db->query("select id from user_accounts where userId=$userId and amount>=$amount and moneyType=$moneyType for update")->row()==null){
            $this->db->trans_rollback();
            $ret->errcode=2;
            $ret->errmsg='账户余额不足';
            $ret->notes = '';
            return $ret;
        }
        if($payAccountType==1){
            $balance=$this->db->query("select * from mch_balances where mchId=$mchId and amount>=$amount for update")->row();
            if(!$balance){
                $ret->errcode=3;
                $ret->errmsg='红包小助手今天忘记带钱包了。请明天再试吧。';
                $ret->notes = '';
                return $ret;
            }
        }
        $this->db->query("update user_accounts set amount=amount-$amount where userId=$userId and mchId=$mchId and moneyType=$moneyType and amount>0");
        $theTime=time();
        $this->db->query("insert into user_trans(userId,amount,theTime,mchId,isAuto,moneyType,payType,payAccountType) values($userId,$amount,$theTime,$mchId,0,$moneyType,$payType,$payAccountType)");
        if ($this->db->trans_status() === FALSE){
            $this->db->trans_rollback();
            $ret->errcode=1;
            $ret->errmsg='服务器忙，稍后重试';
            $ret->notes = '';
            return $ret;
        }else{
            $this->db->trans_commit();
        }
        return $ret;
    }

    // ----------------------------------
    // Added by shizq
    // 获取用户的红包记录列表
    function getRedpacketLogs($userId, $page = 0, $pageSize = 10) {
        $sql = "SELECT
                CASE fType
                    WHEN 0 THEN -amount
                    WHEN 1 THEN amount
                    WHEN 2 THEN amount
                    WHEN 3 THEN -amount
                    ELSE 0 END AS amount, createTime,
                CASE fType
                    WHEN 0 THEN '扔红包炸弹'
                    WHEN 1 THEN '炸弹被他人引爆'
                    WHEN 2 THEN '捞到红包'
                    WHEN 3 THEN '引爆了炸弹'
                    ELSE '发生了未知情况' END AS action, 1 AS wx_status, 1 AS wx_errcode
                FROM groups_fishing_log
                WHERE userId = ?
                UNION ALL
                SELECT -t1.amount AS amount, t1.createTime, '发起扫码PK' AS action, 1 AS wx_status, 1 AS wx_errcode
                FROM user_accounts_used t1
                WHERE userId = ? AND doTable = 'groups_scanpk_users'
                UNION ALL
                SELECT t1.amount AS amount, t1.getTime as createTime, '扫码PK中获胜' AS action, 1 AS wx_status, 1 AS wx_errcode
                FROM user_redpackets_get t1
                WHERE userId = ? AND doTable = 'groups_scanpk'
                UNION ALL
                SELECT -t1.amount AS amount, t1.theTime as createTime,
                CASE t1.isAuto
                    WHEN 0 THEN '提现'
                    WHEN 1 THEN '系统自动提现发放'
                    ELSE '未知操作' END AS action, t1.wxStatus AS wx_status, t1.wxErrCode AS wx_errcode
                FROM user_trans t1
                WHERE userId = ? AND role = 0 AND action = 0
                UNION ALL
                SELECT t1.amount AS amount, t1.getTime as createTime, '中奖红包' AS action, 1 AS wx_status, 1 AS wx_errcode
                FROM user_redpackets t1
                WHERE userId = ? AND role = 0
                ORDER BY createTime DESC LIMIT ?, ?";
        $query = [$userId, $userId, $userId, $userId, $userId, $page * $pageSize, $pageSize];
        $rows = $this->db->query($sql, $query)->result();
        foreach ($rows as $row) {
            $row->amount = sprintf("%.2f", $row->amount * 0.01);
            $row->createTime = date('Y-m-d H:i:s', $row->createTime);
        }
        return $rows;
    }

    // ----------------------------------
    // Added by shizq
    // 获取用户的积分记录列表
    function getPointLogs($userId, $role, $mchId, $page = 0, $pageSize = 10) {
        $sql = "SELECT CASE pointsId WHEN -1 THEN '乐券兑换' ELSE '中奖积分' END AS title, amount, getTime AS actionTime, null AS wxStatus, 1 AS wx_errcode
                FROM user_points
                WHERE userId = ? AND role = ?
                UNION ALL
                SELECT '商城兑换' AS title, -amount, createTime AS actionTime, null AS wxStatus, 1 AS wx_errcode
                FROM user_points_used
                WHERE userId = ? AND role = ? AND doTable = 'mall_orders'
                UNION ALL
                SELECT '现金兑换' AS title, -t1.amount, t1.createTime AS actionTime, t2.wxStatus AS wxStatus, t2.wxErrCode AS wx_errcode
                FROM user_points_used AS t1
                LEFT JOIN user_trans AS t2 ON t1.doId = t2.id
                WHERE t1.userId = ? AND t1.role = ? AND (t1.doTable = 'exchange_point' OR t1.doTable = 'user_trans')
                UNION ALL
                SELECT '发起扫码PK' AS title, -amount, createTime AS actionTime, null AS wxStatus, 1 AS wx_errcode
                FROM user_points_used
                WHERE userId = ? AND role = ? AND doTable = 'groups_scanpk_users'
                UNION ALL
                SELECT '扫码PK中获胜' AS title, amount, getTime AS actionTime, null AS wxStatus, 1 AS wx_errcode
                FROM user_points_get
                WHERE userId = ? AND role = ? AND doTable = 'groups_scanpk'
                ORDER BY actionTime DESC LIMIT ?, ?";
        $start = $page * $pageSize;
        $query = [$userId, $role, $userId, $role, $userId, $role, $userId, $role, $userId, $role, $start, $pageSize];
        $rows = $this->db->query($sql, $query)->result_array();
        foreach ($rows as &$row) {
            $row['actionTime'] = date('Y-m-d H:i:s', $row['actionTime']);
        }
        return $rows;
    }

    // ----------------------------------
    // Added by shizq
    // 获取用户的总积分
    function getTotalPoints($userId, $role) {
        $row = $this->db->where('userId', $userId)->where('role', $role)->get('user_points_accounts')->row();
        if (! isset($row)) {
            return 0;
        }
        return $row->amount;
    }

    // ----------------------------------
    // Added by shizq
    // 获取用户的积分的使用记录
    function getPointUsedLogs($userId, $role, $mchId, $page = 0, $pageSize = 10) {
        $sql = "SELECT '商城兑换' AS title, -amount AS amount, createTime AS actionTime, null AS wxStatus
                FROM user_points_used
                WHERE userId = ? AND role = ? AND mchId = ? AND doTable = 'mall_orders'
                UNION ALL
                SELECT '现金兑换' AS title, -t1.amount AS amount, t1.createTime AS actionTime, t2.wxStatus AS wxStatus
                FROM user_points_used AS t1
                LEFT JOIN user_trans AS t2 ON t2.id = t1.doId
                WHERE t1.userId = ? AND t1.role = ? AND t1.mchId = ? AND (t1.doTable = 'exchange_point' OR t1.doTable = 'user_trans')
                UNION ALL
                SELECT '发起扫码PK' AS title, -amount AS amount, createTime AS actionTime, null AS wxStatus
                FROM user_points_used
                WHERE userId = ? AND role = ? AND mchId = ? AND doTable = 'groups_scanpk_users'
                ORDER BY actionTime DESC LIMIT ?, ?";
        $start = $page * $pageSize;
        $query = [$userId, $role, $mchId, $userId, $role, $mchId, $userId, $role, $mchId, $start, $pageSize];
        $rows = $this->db->query($sql, $query)->result_array();
        foreach ($rows as &$row) {
            $row['actionTime'] = date('Y-m-d H:i:s', $row['actionTime']);
        }
        return $rows;
    }

    // ----------------------------------
    // Added by shizq
    // 获取用户的积分的获取记录
    function getPointGetLogs($userId, $role, $mchId, $page = 0, $pageSize = 10) {
        $sql = "SELECT '中奖积分' AS title, amount, getTime AS actionTime, null AS wxStatus
                FROM user_points
                WHERE userId = ? AND role = ? AND mchId = ?
                UNION ALL
                SELECT '扫码PK中获胜' AS title, amount, getTime AS actionTime, null AS wxStatus
                FROM user_points_get
                WHERE userId = ? AND role = ? AND mchId = ? AND doTable = 'groups_scanpk'
                ORDER BY actionTime DESC LIMIT ?, ?";
        $start = $page * $pageSize;
        $query = [$userId, $role, $mchId, $userId, $role, $mchId, $start, $pageSize];
        $rows = $this->db->query($sql, $query)->result_array();
        foreach ($rows as &$row) {
            $row['actionTime'] = date('Y-m-d H:i:s', $row['actionTime']);
        }
        return $rows;
    }

    /**
     * 用户兑换积分为现金
     *
     * @param  $user 用户
     * @param  $role 角色
     * @return void
     */
    function exchangePoint($user, $role = 0) {
        debug("exchange point - begin");
        debug("params: ". json_encode($user));

        try {
            // DB Session - begin
            $this->db->trans_begin();
            $sql = "SELECT amount FROM user_points_accounts WHERE userId = ? AND mchId = ? FOR UPDATE";
            $row = $this->db->query($sql, [$user->id, $user->mchId])->row();
            if (! isset($row)) {
                $this->db->trans_rollback();
                throw new Exception("用户积分账户不存在", 1);
            }

            $amount = $row->amount;
            if ($amount < 100) {
                $this->db->trans_rollback();
                debug("user's point is $amount which less than 100");
                throw new Exception("积分大于100才能兑换", 1);
            }

            $leftAmount = 0;
            if ($amount > 20000) {
                debug("user's point is $amount which more than 20000");
                $leftAmount = $amount - 20000;
                $amount = 20000;
            }

            $sql = "UPDATE user_points_accounts SET amount = ? WHERE userId = ? AND mchId = ?";
            $success = $this->db->query($sql, [$leftAmount, $user->id, $user->mchId]);
            if (! $success) {
                $this->db->trans_rollback();
                debug("update user_points_accounts fail");
                throw new Exception("兑换失败", 1);
            }
            $params = [
                'mchId'  => $user->mchId,
                'openid' => $user->openid,
                'amount' => $amount,
                'userId' => $user->id,
                'desc'   => '积分兑换现金'
            ];

            $this->load->model('Merchant_model', 'merchant');
            $merchant = $this->merchant->get($params['mchId']);
            if (count(config_item('sender')) <= intval($merchant->wxSendType)) {
                $this->db->trans_rollback();
                error("exchange point fail: wxSendType is ". $merchant->wxSendType);
                throw new Exception("未知的发放平台", 1);
            }
            $senderName = config_item('sender')[$merchant->wxSendType];
            $classz = new ReflectionClass($senderName);
            $sender = $classz->newInstance();
            try {
                $transId = $sender->requestThirdPlatform($params);
            } catch (Exception $e) {
                $this->db->trans_rollback();
                throw $e;
            }

            // 添加积分使用记录
            $data = [
                'userId'  => $user->id,
                'mchId'   => $user->mchId,
                'doTable' => 'user_trans',
                'doId'    => $transId,
                'amount'  => $amount,
                'role'    => $role,
            ];
            debug("create point used - begin");
            debug("params: ". json_encode($data));
            $this->db->set('createTime', 'unix_timestamp()', false);
            $this->db->insert('user_points_used', $data);
            debug("create point used - end");
            // 积分使用触发器 add by cw
            // 积分兑换现金成功以前不计算报表数据  mod by zht
            //$this->trigger_model->trigger_point_used((object)$data);
            // 积分使用触发器 end
            $this->db->trans_commit();
        } finally {
            debug("exchange point - end");
        }
    }

    /**
     * 获取某个活动的扫码次数
     * 
     * @param $mchId 商户编号
     * @param $wxOpenId 微信openid
     * @return array
     */
    public function getScanTimes($wxOpenId) {
        debug("Get ScanTimes - begin");
        debug("params: ". json_encode(func_get_args()));
        $user = $this->get_by_openid($wxOpenId);
        if (! isset($user)) {
            debug("User does not exists which openid is: ". $wxOpenId);
            throw new Exception("当前用户不存在", 1);
        }

        $redis = new Redis();
        $redisConfig = config_item('redis');
        $redis->pconnect($redisConfig['host'], $redisConfig['port']);
        if (isset($redisConfig['password'])) {
            $redis->auth($redisConfig['password']);
        }

        $expireTime = 60;

        $redisKeyUser = 'scan_times_' . $user->id . '_' . $user->mchId;
        $redisKeyTotal = 'scan_times_' . $user->mchId;
        $userScanTimes = $redis->get($redisKeyUser);
        $totalScanTimes = $redis->get($redisKeyTotal);
        if (! $userScanTimes) {
            debug("Get userScanTimes from db.");
            // 读取数据库
            $sql = "SELECT COUNT(*) count FROM scan_log t1 
                INNER JOIN sub_activities t2 ON t2.id = t1.activityId
                INNER JOIN activities t3 ON t3.id = t2.parentId AND t3.id = t1.activityId
                WHERE t1.mchId = ? AND t1.userId = ?";
            $userScanTimes = $this->db->query($sql, [$user->mchId, $user->id])->row();
            $redis->set($redisKeyUser, $userScanTimes->count);
            $redis->expire($redisKeyUser, $expireTime);
            $userScanTimes = $userScanTimes->count;
        } 
        
        if (! $totalScanTimes) {
            debug("Get totalScanTimes from db.");
            // 读取数据库
            $sql = "SELECT COUNT(*) count FROM scan_log t1 
                INNER JOIN sub_activities t2 ON t2.id = t1.activityId
                INNER JOIN activities t3 ON t3.id = t2.parentId AND t3.id = t1.activityId
                WHERE t1.mchId = ?";
            $totalScanTimes = $this->db->query($sql, [$user->mchId])->row();
            $redis->set($redisKeyTotal, $totalScanTimes->count);
            $redis->expire($redisKeyTotal, $expireTime);
            $totalScanTimes = $totalScanTimes->count;
        } 
        
        $data = ['total_scan_times' => $totalScanTimes, 'user_scan_times' => $userScanTimes];
        info('data is: '. json_encode($data));
        debug("Get ScanTimes - end");
        return $data;
    }

    //检查并发放未发放的奖品(临时方案)
    public function checkAndSend($type,$user){
        if(! isset($user->subscribe) || $user->subscribe!=1){
            return;
        }
        $this->load->library('common/wx3rd/wx3rd_lib');
        //红包
        if($type==0){
            
        }
        //乐券
        if($type==2){
            
        }
        //积分
        if($type==3){
            $points=$this->db->query("select * from user_points where userId=$user->id and sended=0")->result();
            foreach($points as $k=>$v){
                $this->wx3rd_lib->send_points_after_subscribe($v,$user);
            }
        }
    }


}
