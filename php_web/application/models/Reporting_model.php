<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Reporting_model extends CI_Model {

    function __construct() {
        // Call the CI_Model constructor
        parent::__construct();
    }
	/**
	 *
	 * @param 商户标识 $mch_id
	 * @param 开始时间 $from
	 * @param 结束时间 $to
	 * @param 数据总数 $count
	 * @param 开始数据 $start
	 * @param 长度 $length
	 */
    function get_scan_dailylist($mch_id,$from,$to,&$count=0,$start=null,$length=null) {
        $count=$this->db->query('select count(*) cnt from (select theDate from rpt_user_daily rud left join users on rud.userId=users.id
            where users.mchId=? and rud.theDate>=? and rud.theDate<=?
            group by theDate) a',[$mch_id,$from,$to])->row()->cnt;
        $sql="select theDate date,sum(scanCount) scanCount,round(sum(rpAmount)/100,2) rpAmount,round(sum(transAmount)/100,2) transAmount
            from rpt_user_daily rud left join users on rud.userId=users.id
            where users.mchId=? and rud.theDate>=? and rud.theDate<=? group by theDate order by theDate desc";
        $data=[$mch_id,$from,$to];
        if(isset($start)&&isset($length)){
            $sql.=' limit ?,?';
            $data[]=intval($start);
            $data[]=intval($length);
        }
        $sql.=';';
        $sql=$this->db->compile_binds($sql,$data);
        return $this->dbhelper->serve($sql);
        //return $this->db->query($sql,$data)->result_array();
    }
    // 首页红包
    function get_mch_rp_used($mch_id) {
        $sql = "SELECT * FROM red_packets where mchId=$mch_id and rowStatus=0 order by id desc";
        //return $this->dbhelper->serve($sql);
        return $this->db->query($sql)->result();
    }
    // 首页卡券
    function get_mch_card_used($mch_id) {
        $sql = "select a.title name,sum(b.totalNum) total,sum(b.remainNum) remain,(sum(b.totalNum)-sum(b.remainNum)) used from cards_group a left join cards b on b.parentId=a.id where a.mchId=? and a.rowStatus=0 and b.rowStatus=0 group by a.id order by a.id desc,b.id desc";
        return $this->db->query($sql,[$mch_id])->result();
    }
    // 首页积分
    function get_mch_point_used($mch_id,$point,$name) {
        $sql = "select *,ifnull((total-used),'0') remain,? name,? table_model from (select ifnull(sum(amount),0) used,(select ifnull(sum(amount),'0') total from user_points where mchId=? and sended=1 and role=0) total from user_points_used where mchId=? and doTable=?)m";
        return $this->db->query($sql,[$name,$point,$mch_id,$mch_id,$point])->row_array();
    }
    function get_mch_indexdata($mchId){
        // 红包总额
        $sql="select round(sum(amount)/100,2) redNum from user_redpackets where mchId=? and sended=1 and role=0";
        $sql=$this->db->compile_binds($sql,[$mchId]);
        $redNum=$this->dbhelper->serverow($sql)->redNum;
        // 卡券总额
        $sql="select count(*) cardNum from(select id from cards where mchId=? and rowStatus=0 group by parentId)m";
        $sql=$this->db->compile_binds($sql,[$mchId]);
        $cardNum=$this->dbhelper->serverow($sql)->cardNum;
        // 积分总额
        $sql="select sum(amount) pointNum from user_points where mchId=? and sended=1 and role=0";
        $sql=$this->db->compile_binds($sql,[$mchId]);
        $pointNum=$this->dbhelper->serverow($sql)->pointNum;
        // 扫码总量
        $sql="select count(*) scanNum from scan_log where mchId=?";
        $sql=$this->db->compile_binds($sql,[$mchId]);
        $scanNum=$this->dbhelper->serverow($sql)->scanNum;
        // 扫码率
        $sql="select sum(len) batchNum from batchs where mchId=? and rowStatus=0";
        $sql=$this->db->compile_binds($sql,[$mchId]);
        $batchNum=$this->dbhelper->serverow($sql)->batchNum;

        return array('redNum'=>$redNum,'cardNum'=>$cardNum,'pointNum'=>$pointNum,'scanNum'=>$scanNum,'batchNum'=>$batchNum);
    }
    // 获取近7天用户新增数量
    function get_mch_user_xinzeng($mchId){
        $start=strtotime(date('Y-m-d 00:00:00',strtotime('-6 days')));
        $end=strtotime(date('Y-m-d 23:59:59'));
        $sql="select count(*) userNum,date(FROM_UNIXTIME(createTime)) date from users where mchId=? and createTime>=? and createTime<=? group by date;";
        return $this->db->query($sql,[$mchId,$start,$end])->result_array();
    }
    function get_rp_used($rp_id) {
        $sql = "SELECT name,ifnull(totalNum-remainNum,'0') used,ifnull(remainNum,'0') remain,ifnull(totalNum,'0') total,limitType
                from red_packets where id=$rp_id and levelType=0 and limitType=0 and rowStatus=0
            union select name,ifnull(totalAmount-remainAmount,'0') used,ifnull(remainAmount,'0') remain,ifnull(totalAmount,'0') total,limitType
                from red_packets where id=$rp_id and levelType=0 and limitType=1 and rowStatus=0
            union SELECT rp.name,ifnull(sum(sub.num)-sum(sub.remainNum),'0') used,ifnull(sum(sub.remainNum),'0') remain,ifnull(sum(num),'0') total,0 limitType
                FROM red_packets_sub sub inner join red_packets rp on rp.id=sub.parentId where rp.id=$rp_id and levelType=1 and rowStatus=0;";
        return $this->db->query($sql)->row();
        //return $this->dbhelper->serverow($sql);
    }

    function get_mch_daily_scanning($mch_id) {
        //$sql = "select count(*) times ,date(FROM_UNIXTIME(scanTime)) date from scan_log where mchId=$mch_id and DATEDIFF(FROM_UNIXTIME(scanTime),now())>=-31 group by date";
        //sql优化
        $startDate=date('Y-m-d',strtotime('-1 month'));
        $sql="select sum(scanCount) times ,theDate date from rpt_user_daily where mchId=$mch_id and theDate>='$startDate' group by theDate;";
        return $this->dbhelper->serve($sql);
    	//return $this->db->query($sql)->result();
    }
    function get_mch_daily_rp_amount($mch_id) {
        //$sql = "SELECT sum(amount) amount,date(FROM_UNIXTIME(getTime)) date from user_redpackets ur where mchId=$mch_id and DATEDIFF(FROM_UNIXTIME(getTime),now())>=-31 group by date";
        //sql优化
        $startDate=date('Y-m-d',strtotime('-1 month'));
        $sql="select sum(rpAmount) amount ,theDate date from rpt_user_daily where mchId=$mch_id and theDate>='$startDate' group by theDate;";
        return $this->dbhelper->serve($sql);
    	//return $this->db->query($sql)->result();
    }

    /**
     * 获取首页红包发放金额图表数据
     *
     * @param  int $mch_id     企业id
     * @param  int $start_date 开始时间
     * @param  int $end_date   结束时间
     * @param  boolean $export 是否为导出操作
     * @return array
     */
    function money_chart($mch_id, $start_date, $end_date, $export) {
        if (! $start_date || ! $end_date || ($end_date < $start_date)) {
            throw new Exception('params invalid');
        }
        $sql = "SELECT sum(amount) AS amount, FROM_UNIXTIME(getTime, '%Y-%m-%d')
            AS get_date FROM user_redpackets WHERE mchId = ? AND getTime >= ? AND
            getTime <= ? AND DATEDIFF(FROM_UNIXTIME(getTime), now()) >= -64 GROUP
            BY get_date;";
        if ($export) {
            $sql = "SELECT sum(amount) AS amount, FROM_UNIXTIME(getTime, '%Y-%m-%d %H:%i:%S')
            AS get_date FROM user_redpackets WHERE mchId = ? AND getTime >= ? AND getTime <= ?
            AND DATEDIFF(FROM_UNIXTIME(getTime), now()) >= -64 GROUP BY get_date;";
        }
        $params = [
            $mch_id,
            $start_date,
            $end_date
        ];
        $sql=$this->db->compile_binds($sql,$data);
        return $this->dbhelper->serve($sql);
        //$rows = $this->db->query($sql, $params)->result();
        //return $rows;
    }

    /**
     * 获取首页扫码记录图表数据
     *
     * @param  int $mch_id     企业id
     * @param  int $start_date 开始时间
     * @param  int $end_date   结束时间
     * @param  boolean $export 是否为导出操作
     * @return array
     */
    function scan_chart($mch_id, $start_date, $end_date, $export) {
        if (! $start_date || ! $end_date || ($end_date < $start_date)) {
            throw new Exception('params invalid');
        }
        $sql = "SELECT count(*) AS nums, FROM_UNIXTIME(scanTime, '%Y-%m-%d') AS
            scan_date FROM scan_log WHERE mchId = ? AND DATEDIFF(FROM_UNIXTIME(scanTime),
            now()) >= -64 AND scanTime >= ? AND scanTime <= ? GROUP BY scan_date;";
        if ($export) {
            $sql = "SELECT count(*) AS nums, FROM_UNIXTIME(scanTime, '%Y-%m-%d %H:%i:%S') AS
                scan_date FROM scan_log WHERE mchId = ? AND DATEDIFF(FROM_UNIXTIME(scanTime),
                now()) >= -64 AND scanTime >= ? AND scanTime <= ? GROUP BY scan_date;";
        }
        $params = [
            $mch_id,
            $start_date,
            $end_date
        ];
        $sql=$this->db->compile_binds($sql,$params);
        return $this->dbhelper->serve($sql);
        //$rows = $this->db->query($sql, $params)->result();
        //return $rows;
    }

    /**
     * 获取某个批次乐码总数和剩余数，任务167
     *
     * @param  int $batch_id 乐码批次
     * @return object {
     *         num_total 乐码批次总数
     *         num_left 乐码批次剩余数
     * }
     */
    function get_batch_left($batch_id) {
        $sql = "SELECT len AS num_total, (SELECT len FROM batchs WHERE id = ?) - (SELECT
            count(*) FROM scan_log WHERE batchId = ?) AS num_left FROM batchs WHERE id = ?;";
        $sql=$this->db->compile_binds($sql,[$batch_id, $batch_id, $batch_id]);
        return $this->dbhelper->serve($sql);
        //$row = $this->db->query($sql, [$batch_id, $batch_id, $batch_id])->row();
        //return $row;
    }

    /**
     * 子活动扫码数据信息导出，任务168
     *
     * @return array
     */
    function sub_activiey_export($activity_id) {
    	if (empty($activity_id)) {
    		throw new Exception('参数不正确！');
    	}

        $sql = "SELECT * ,count(*) as count_num FROM scan_log INNER JOIN user_redpackets ON user_redpackets.scanId
            = scan_log.id INNER JOIN users ON users.id = user_redpackets.userId WHERE
            activityId = ?  group by scanTime;";
        $sql=$this->db->compile_binds($sql,[$activity_id]);
        return $this->dbhelper->serve($sql);
        //$rows = $this->db->query($sql, [$activity_id])->result();
        //return $rows;
    }
    //获取用户扫码统计数据
    function get_user_scan_daylist($mch_id,$from,$to,&$count=0,$start=null,$length=null) {
        $count=$this->db->query('select count(*) cnt from rpt_user_daily rud left join users on rud.userId=users.id
            where users.mchId=? and rud.theDate>=? and rud.theDate<=?',[$mch_id,$from,$to])->row()->cnt;
        $sql="select userId,ifnull(nickName,'红码用户') nickName,theDate date,scanCount scan_num,round(rpAmount/100,2) red_amount,round(transAmount/100,2) trans_amount
            from rpt_user_daily rud left join users on rud.userId=users.id
            where users.mchId=? and rud.theDate>=? and rud.theDate<=? order by date desc,userId desc";
        $data=[$mch_id,$from,$to];
        if(isset($start)&&isset($length)){
            $sql.=' limit ?,?';
            $data[]=intval($start);
            $data[]=intval($length);
        }
        $sql.=';';
        $sql=$this->db->compile_binds($sql,$data);
        return $this->dbhelper->serve($sql);
        //return $this->db->query($sql,$data)->result_array();
    }
    public function get_user_info($mch_id,$id){
    	return $this->db->where('id',$id)->where('mchId',$mch_id)->get('users')->row();
    }
    //获取账户余额信息
    public function get_user_accounts($mch_id,$id,$type) {
    	return $this->db->where('userId',$id)->where('mchId',$mch_id)->where('moneyType',$type)->get('user_accounts')->row();
    }
    //获取用户乐券信息
    public function get_card_info($id,$mchId){
    	$sql="select a.userId,a.cardId,a.num Anum,ifnull(b.title,'欢乐券') title from user_cards_account a left join cards b on a.cardId=b.id where a.userId=? and a.mchId=? and a.role=0 and b.rowStatus=0 group by a.cardId order by Anum desc";

    	return $this->db->query($sql,[$id,$mchId])->result();

    }
    //获取用户积分余额
    public function get_points_info($id,$mch_id) {
        return $this->db->where('userId',$id)->where('mchId',$mch_id)->where('role','0')->get('user_points_accounts')->row();
    }
}
