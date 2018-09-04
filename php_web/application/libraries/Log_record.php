<?php defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * 系统日志处理类
 * 
 * @author chencz
 */
class Log_record {

    private $ci;
    private $table;

    // 定义oprobject，可理解为枚举，对应数据库中oprobject字段
    public $all 			= null;
    public $Batch 			= 1;
    public $Activity 		= 2;
	public $Card 			= 3;
	public $Product 		= 4;
	public $Category 		= 5;
	public $RedPacket 		= 6;
	public $Point 			= 15; 
	public $Setting 		= 7;
	public $User 			= 8;
	public $Mixstrategy 	= 10;
	public $Multistrategy 	= 16;
	public $Accumstrategy 	= 17;
	public $Admin 			= 11; 
	public $Wechat 			= 12; 
	public $Mall 			= 13; 
	public $App 			= 14;
	public $Group 			= 21; 	
	public $Role 			= 22;
	public $Salesman 		= 23;
	
	// 定义操作类型，同枚举的意思是一样的，
	// 在数据库中的记录位置比较奇怪，在oprdetail（json字符串）中的op字段
	public $New 			= "new";
	public $Add 			= "add";
	public $Update 			= "update";
	public $Delete 			= "delete";
	public $Start 			= "start";
	public $Stop 			= "stop";
	public $Download 		= "download";
	public $Lock 			= "lock";
	public $Unlock 			= "unlock";
	public $Repassword 		= "repassword";
	public $Login 			= "login";
	public $AddInOrder 		= "addorder";
	public $DeleteInOrder 	= "deleteorder";
	public $AddOutOrder 	= "addoutorder";
	public $DeleteOutOrder 	= "deleteoutorder";
	public $DownloadOrder 	= "downloadorder";
	public $DownloadErr 	= "downloaderr";
	public $Confirm 		= "confirm";
	public $Send 			= "send";
	public $End 			= "end";
	public $Buy 			= "buy";
	public $Install 		= "install";
	public $Config 			= "config";
	
	function __construct() {
		$this->ci =& get_instance();
	}

	/**
	 * 判断某个日志是否存在
	 * @param $uid 用户
	 * @param $tid 东西
	 */
	function isLogExist($uid ,$tid,$option="",$table="")
	{
		$this->setTable($table);
		$sql = "select * from {$this->table} where mchid={$uid} and id={$tid}";
		if(!empty($option))
		{
			$sql .= " and option={$option}";
		}
		$result = $this->ci->db->query ( $sql );
		if ($result->num_rows () < 1)
		{
			return false;
		}
		else
		{
			return $result;
		}
	}

	/**
	 * 取得某个用户的日志
	 * @param $uid 用户
	 */
	function getUserLog($mchid,$table="")
	{
		$this->setTable($table);
		$sql = "select * from {$this->table} where mchid={$mchid}";
		return $this->ci->db->query ( $sql );
	}

	/**
	 * 取得日志
	 * @param $userids 当前用户id数组
	 * @param $start 开始位置（分页用）
	 * @param $len 页长度
	 * @param $count 查询到的记录数
	 * @param $obj 操作的具体实体
	 * @param $opr 操作类型
	 * @param $datestart 起始日期
	 * @param $dateend 结束日期
	 */
	function getLog($userids,$start,$len,&$count=0,$obj,$opr, $datestart,$dateend,$table="mch_opr_log")
	{
		$this->setTable($table);
		$sql = "select *,log.id as pid from mch_opr_log log LEFT JOIN mch_accounts mcha  on log.userid = mcha.id where log.userid in({$userids}) ";

		$sql_count="select count(*) cnt from {$table} where userid in({$userids}) ";
		if(isset($obj)){
		    $sql .= " and oprobject={$obj} ";
		    $sql_count .= " and oprobject={$obj}";
		}
		if(isset($opr)){
		    $sql .= " and oprdetail like '%\"op\":\"{$opr}\"%'";
		    $sql_count .= " and oprdetail like '%\"op\":\"{$opr}\"%'";
		}
		if(isset($datestart)){
		    $sql .= " and oprtime >= {$datestart}   and oprtime <= {$dateend}";
		    $sql_count .=  " and oprtime >= {$datestart}   and oprtime <= {$dateend}";
		}
		$count=$this->ci->db->query($sql_count)->row()->cnt;
		$sql .=" order by oprtime desc  limit {$start}, {$len}";
		$result = $this->ci->db->query($sql)->result();
		//echo $sql;
		return $result;
	}
	
	/**
	 * 取得某一项内容的日志
	 * @param $tid
	 */
	function getObjLog($mchid,$table="mch_opr_log")
	{
		$this->setTable($table);
		$sql = "select * from {$this->table} where userid={$mchid}";
		return $this->ci->db->query ( $sql );
	}

	/**
	 * 记录操作日志
	 * 
	 * @param $mchId 商户编号
	 * @param $oprdetail 操作详情
	 * @param $oprobject 上面定义的枚举值
	 */
	function addLog($mchid, $oprdetail, $oprobject, $table="mch_opr_log") {
		// 获取操作人
		if (isset($this->ci->session->userdata['userId'])) {
	    	$userId = $this->ci->session->userdata['userId'];
		} else {
			$userId = '游客';
		}

	    if (isset($oprdetail['description'])) {
	    	if (mb_strlen($oprdetail['description'], 'utf8') > 100) {
	    		$oprdetail['description'] = mb_substr($oprdetail['description'], 0, 100, 'utf-8');
	    	}
	    }

		$oprdetail = addslashes(json_encode($oprdetail));
		$this->setTable($table);
		$sql = "INSERT INTO {$this->table} (userid, oprdetail, oprtime, oprobject) 
				VALUES (?, '{$oprdetail}', ?, ?)";
		$success = $this->ci->db->query($sql, [$userId, time(), $oprobject]);
		if (! $success) {
			throw new Exception("发生未知错误", 1);
		}
	}

	/**
	 * 设置当前表，以防存在多个日志表的情况
	 * @param $table
	 */
	function setTable($table)
	{
		if(!empty($table))
		{
			$this->table = $table;
		}
	}
}