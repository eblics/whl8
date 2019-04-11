<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Scan_log_model extends MY_Model {
    public function __construct()
    {
        // Call the CI_Model constructor
        parent::__construct();
        $this->load->model('trigger_model');
    }
    /**
     * 返回第一次扫码的日志
     * @param  [string] $code [码文本]
     * @return [object]       [扫码日志]
     */
    function get($id){
        return $this->db->where('id',$id)->get('scan_log')->row();
    }
    function get_by_code($code){
        $sql=$this->db->compile_binds("select * from scan_log where code=?",$code);
        return $this->dbhelper->get_cache_or_db($code,$sql);
        // return $this->db->where('code',$code)->get('scan_log')->row();
    }
    function code_exist_in_write_server($code){
        $codestr=$code.'_'.time();
        return $this->db->query("update settings set val='$codestr' where name='code_in_write_server' and  EXISTS (select id from scan_log where code='$code')");
    }

    public function getWaiterScanLogByLecode($lecode) {
        $sql = "SELECT * FROM scan_log_waiters WHERE code = ?";
        $sql = $this->db->compile_binds($sql, [$lecode]);
        return $this->dbhelper->get_cache_or_db('waiter_'. $lecode, $sql);
    }

    public function updateWaiterScanLogByLecode($lecode, $scanLog) {
        $sql = $this->db->update_string('scan_log_waiters', $scanLog, "code=". $lecode);
        $this->dbhelper->set_cache_and_db('waiter_'. $lecode, $scanLog, $sql);
        return $scanLog;
    }

    function get_user_first_scaninfo($openid,$mch_id){
        return $this->db->query(
            'select * from scan_log where openId=? and mchId=? and isFirst=1 and over=0 order by scanTime desc limit 0,1',
            [$openid,$mch_id])->row();
    }
    function get_latest_one_by_openid($openid){
        return $this->db->query("select * from scan_log where openId='".$openid."' order by id desc limit 0,1")->row();
    }

    function get_by_user_activity($user,$activity){
        return $this->db
            ->where('userId',$user->id)
            ->where('activityId',$activity->id)
            ->get('scan_log')
            ->row();
    }
    function insert($scaninfo){
        $sql=$this->db->insert_string('scan_log',$scaninfo);
        $sql=str_replace('INSERT INTO', 'INSERT IGNORE INTO', $sql);
        $this->dbhelper->set_cache_and_db($scaninfo->code,$scaninfo,$sql);
        $this->dbhelper->push('sql_master',"update tts_orders_codes set isScan=1 where code='$scaninfo->code'");
        return $this->dbhelper->cache_get($scaninfo->code);
    }
    function update($scaninfo){
        $sql=$this->db->update_string('scan_log',$scaninfo,"code='$scaninfo->code'");
        $this->dbhelper->set_cache_and_db($scaninfo->code,$scaninfo,$sql);
        return $this->dbhelper->cache_get($scaninfo->code);
    }

    function delete($scaninfo){
        $sql="delete from scan_log where code='$scaninfo->code'";
        return $this->dbhelper->del_cache_and_db($scaninfo->code,$sql);
    }
    //去除save，改为insert和update
    //function save($scaninfo){
    //    //此处更改为首先写到缓存中，因此此时将取不到自增id，后续的业务处理均不能依赖id，而使用code
    //    $sql=$this->dbhelper->insert_update_string('scan_log',$scaninfo);
    //    $this->dbhelper->set_cache_and_db($scaninfo->code,$scaninfo,$sql);
    //    $this->dbhelper->push('sql_master',sprintf("update tts_orders_codes set isScan=1 where code='%s'",$scaninfo->code));
    //    return $this->dbhelper->cache_get($scaninfo->code);
    //    //if(isset($scaninfo->id)){
    //    //    $update=$this->db->where('id',$scaninfo->id)->update('scan_log',$scaninfo);
    //    //    // scan_log 更新触发器 add by cw
    //    //    $this->trigger_model->trigger_scan_log_update($scaninfo);
    //    //    // scan_log 更新触发器 end
    //    //    return $update;
    //    //}else{
    //    //    $this->db->insert('scan_log',$scaninfo);
    //    //    $insertId=$this->db->insert_id();
    //    //    // scan_log 插入触发器 add by cw
    //    //    $this->trigger_model->trigger_scan_log_insert($scaninfo);
    //    //    // scan_log 插入触发器 end
    //    //    //更新出入库单扫码状态 (add by whl)
    //    //    $this->db->query('update tts_orders_codes set isScan=1 where code=?',[$scaninfo['code']]);
    //    //    //更新出入库单扫码状态 end
    //    //    return $insertId;
    //    //}

    //}

    function insert_waiter($scaninfo){
        $sql=$this->db->insert_string('scan_log_waiters',$scaninfo);
        $this->dbhelper->set_cache_and_db('waiter_'.$scaninfo->code,$scaninfo,$sql);
        return $this->dbhelper->get_cache_or_db('waiter_'.$scaninfo->code);
    }

    function update_waiter($scaninfo){
        $sql=$this->db->update_string('scan_log_waiters',$scaninfo,"code='$scaninfo->code'");
        $this->dbhelper->set_cache_and_db('waiter_'.$scaninfo->code,$scaninfo,$sql);
        return $this->dbhelper->get_cache_or_db('waiter_'.$scaninfo->code, $sql);
    }
    //function save_waiter($scaninfo){
    //    //此处更改为首先写到缓存中，因此此时将取不到自增id，后续的业务处理均不能依赖id，而使用code
    //    $sql=$this->dbhelper->insert_update_string('scan_log_waiters',$scaninfo);
    //    return $this->dbhelper->set_cache_and_db('waiter_'.$scaninfo->code,json_encode($scaninfo));
    //    /*
    //     *if(isset($scaninfo->id)){
    //     *    return $this->db->where('id',$scaninfo->id)->update('scan_log_waiters',$scaninfo);
    //     *}else{
    //     *    $this->db->insert('scan_log_waiters',$scaninfo);
    //     *    return $this->db->insert_id();
    //     *}
    //     */
    //}

    function get_some_by_user_id($userId,$mchId,$rows){
        return $this->db->query(
            'select a.code as code,b.amount as amount,b.getTime as getTime from scan_log as a
            join user_redpackets as b
            where a.userId=? and a.mchId=? and a.isFirst=1 and b.userId=a.userId and b.scanId=a.id
            order by scanTime desc limit '.$rows,[$userId,$mchId]
            )->result();
    }

    function get_scaninfo_by_scan_rule($userId,$endTime){
        return $this->db->query(
            'select count(id) as count from scan_log where userId=? and scanTime>=?',
            [$userId,$endTime])->row();
    }
    function get_commonlog_by_scan_rule($userId,$endTime){
        return $this->db->query(
            'select count(*) as count from users_common_log where mchUserId=? and createTime>=? and logType=6',
            //'select count(1) as count from (select lecode from users_common_log where mchUserId=? and createTime>=? and logType=6 group by lecode) t',
            [$userId,$endTime])->row();
    }

    function get_data_no_geo($index,$size){
        $sql="select id,ip,lng,lat from scan_log where geoId=-1 limit $index,$size";
        return $this->db->query($sql)->result();
    }
    function get_ip_geo($ip){
        $sql="select ip,geoId from scan_log where ip='$ip' and geoId!=-1 limit 0,1";
        return $this->db->query($sql)->row();
    }
    function get_ip_subnet($ip){
        $sql="select ip,geoId from scan_log where CONCAT(SUBSTRING_INDEX(ip,'.',3),'.0')='$ip' and geoId!=-1 limit 0,1";
        return $this->db->query($sql)->row();
    }

    function getSubActivityFromScanLog($lecode) {
        $scan_history = $this->db->where('code', $lecode)->get('scan_log')->row();
        if (! $scan_history) {
            throw new Exception("没有找到扫码记录", 1);
        }
        $sub_activity = $this->db->where('id', $scan_history->activityId)->get('sub_activities')->row();

    }
    //扫码报警
    public function scan_warning($mchId,$code,$batchNo,$ip){
        $time = date('Y-m-d H:i:s',time());
        // $mes = '贵公司批次为：'.$batchNo.'的码：'.$code.'，在'.$time.'被IP为：'.$ip.'的用户所扫！！！';
        $mes = '\\n乐码：'.$code.'\\n批次：'.$batchNo.'\\n扫码IP：'.$ip.'\\n扫码时间：'.$time;
        $sql = "select a.userId userId,u.openid openid from warning_accounts a LEFT JOIN users u ON u.id=a.userId and a.mchId='$mchId'";
        $res = $this->db->query($sql)->result();
        $array = ['mchId'=>$mchId,'type'=>0,'createTime'=>time(),'desc'=>$mes];
        $this->db->insert('warning_log',$array);
        if(!empty($res)){
            foreach ($res as $key => $value) {
                $openid = $value->openid;
                $res = $this->wx3rd_lib->template_send($mchId,$openid,$this->wx3rd_lib->template_format_data($mchId,'kf_notice',['【未激活批次乐码被扫提醒】\\n','重要提醒','已完成','红码系统',$mes]));
            }
        }

    }

    // 加入黑名单
	public function add_black_list($ip) {
  		$expireTime=time()+3600*10;
        $this->db->query("insert into ip_blacklist(ip,expireTime) values('$ip',$expireTime) on duplicate key update expireTime=$expireTime;");
        //修改为由队列触发
        $commStr=sprintf("iptables -I INPUT -s %s -j DROP",$ip);
        $this->dbhelper->push('exec_cmd',$commStr);
        //$json=curl_post_text($this->config->item('rpt_svr_url').'/add_blacklist',$ip);
        //log_message('debug','加入黑名单结果：'.var_export($json,true));
        //return json_decode($json);
	}

    // 解除黑名单
	public function remove_black_list() {
        $time=time();
        $result=$this->db->query("select * from ip_blacklist where expireTime<$time limit 0,20")->result();
        if($result){
            foreach($result as $k=>$v){
                //修改由队列触发
                $commStr=sprintf("iptables -D INPUT -s %s -j DROP",$v->ip);
                $this->dbhelper->push('exec_cmd',$commStr);
//                curl_post_text($this->config->item('rpt_svr_url').'/remove_blacklist',$v->ip);
            }
        }
        $newTime=$time-3600*2;
        $this->db->query("delete from ip_blacklist where expireTime<$newTime");
        log_message('debug','解除黑名单结果：'.var_export($result,true));
        return 'ok';
	}

    // 更新扫描记录
    public function updateScanLogPosition($scanLog, $position, $isWaiter = FALSE) {
        debug("update-scan-log-position - begin");
        debug("update scan_log");
        debug("old data: ". json_encode($scanLog));
        if (! isset($position) || 
            ! array_key_exists('lng', $position) ||  
            ! array_key_exists('lat', $position)) {
            $scanLog->lng = 0;
            $scanLog->lat = 0;
        } else {
            $scanLog->lng = $position['lng'];
            $scanLog->lat = $position['lat'];
        }

        if ($scanLog->lng != 0 || $scanLog->lat != 0) {
            $this->load->library('common/geolocation');
            $gps = $this->geolocation->get_gps((object)$position);
        }
        if (isset($gps)) {
            $scanLog->geoId = $gps->id;
            $scanLog->areaCode = $gps->areaCode;
            $scanLog->geoLat = $gps->lat;
            $scanLog->geoLng = $gps->lng;
        } else {
            $scanLog->geoId = -1;
        }

        try {
            debug("new data: ". json_encode($scanLog));
            if ($isWaiter) {
                $this->update_waiter($scanLog);
            } else {
                $this->update($scanLog);
            }
        } catch (Exception $e) {
            error("update-scan-log-position - fail: ". $e->getMessage());
            throw $e;
        } finally {
            debug("update-scan-log-position - end");
        }
        
        return $scanLog;
    }

    public function matchSubActivity($scanLog, $isWaiter = FALSE) {
        $this->load->model('sub_activity_model');
        if (isset($scanLog->activityId)) {
            $subActivity = $this->sub_activity_model->get($scanLog->activityId);
        } else {
            try {
                debug("match-sub-activity - begin");
                $subActivity = $this->sub_activity_model->get_best_match($scanLog, $isWaiter);
                unset($scanLog->evilLevel);
                if (! isset($subActivity)) {
                    throw new Exception("没有适合你的活动", 1);
                }
                if ($subActivity->mainState == 0 || $subActivity->state == 0) {
                    throw new Exception("这个活动还未启动，敬请期待", 1);
                }
                if ($subActivity->mainState == 2 || $subActivity->state == 2) {
                    throw new Exception("这个活动已经停止", 1);
                }

                ////////////////////////////////////////////////
                if (! empty($subActivity->tagId)) {
                    $this->load->model('tag_model');
                    $tagArr = explode(',', $subActivity->tagId);
                    foreach ($tagArr as $value) {
                        $this->tag_model->update_user_tag($scanLog->mchId, $value, $scanLog->openId);
                    }
                }
                ////////////////////////////////////////////////
                debug("update scan_log");
                debug("old data: ". json_encode($scanLog));
                $scanLog->activityId = $subActivity->id;
                debug("new data: ". json_encode($scanLog));
                if ($isWaiter) {
                    $this->update_waiter($scanLog);
                } else {
                    $this->update($scanLog);
                }
                // 只有在第一次匹配活动时，才统计入活动
                $this->cacheUserJoinActivityTimes($scanLog->userId, $subActivity->id, $isWaiter);
            } catch (Exception $e) {
                error("match-sub-activity - fail: ". $e->getMessage());
                throw $e;
            } finally {
                debug("match-sub-activity - end");
            }
        }

        return $scanLog;
    }

    // 更新用户参与活动的次数
    private function cacheUserJoinActivityTimes($userId, $activityId, $isWaiter) {
        $redis = parent::getRedisClient();
        $setName = 'user_activity_count_set';
        if ($isWaiter) {
            $setKey = 'waiter_' . $userId . '_' . $activityId;
        } else {
            $setKey = 'consumer_' . $userId . '_' . $activityId;
        }
        
        if ($redis->zScore($setName, $setKey) > 0) {
            $redis->zIncrBy($setName, 1, $setKey);
        } else {
            $sql = "SELECT count(1) joinTimes FROM scan_log WHERE userId = ? AND activityId = ?";
            $result = $this->db->query($sql, [$userId, $activityId])->row();
            $joinTimes = $result->joinTimes + 1;
            $redis->zAdd($setName, $joinTimes, $setKey);
        }
        debug("user-join-activity-times: ". $setKey . ' ' . $redis->zScore($setName, $setKey));
    }
}
