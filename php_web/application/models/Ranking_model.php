<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * 排行业务逻辑
 * 
 * @author shizq
 */
class Ranking_model extends CI_Model {
	function __construct() {
        // Call the CI_Model constructor
        parent::__construct();
    }

    // --------------------------------------------------------------------------
	// 根据城市名称查找数据库中对应的Code
	function address($city_str) {
		$city_code = $this->db
			->select('code, name')
			->like('name', $city_str)
			->where('level', 1)
			->get('areas')->row();
		if (! $city_code) {
			throw new Exception("没有找到城市对应的Code", 1);
		}
		return $city_code;
	}

	// --------------------------------------------------------------------------
	// 获取所有的省市数据
	function areas() {
		$provinces = $this->db
			->select('name, code')
			->where('level', 0)
			->where('code !=', '710000')
			->where('code !=', '810000')
			->where('code !=', '820000')
			->get('areas')->result();
		$cities = $this->db
			->select('name, code, parentCode')
			->where('level', 1)
			->get('areas')->result();
		foreach ($provinces as &$province) {
			$province->cities = [];
			foreach ($cities as $city) {
				if ($province->code == $city->parentCode) {
					array_push($province->cities, $city);
				}
			}
		}
		if (! $provinces) {
			throw new Exception("发生未知错误", 1);
		}
		return $provinces;
	}
	
	// --------------------------------------------------------------------------
	// 获取省份对应的城市
	function get_city_list($proCode){
		$cities = $this->db
			->select('name, code, parentCode')
			->where('parentCode', $proCode)
			->get('areas')->result();
		return $cities;
	}
	// --------------------------------------------------------------------------
	// 获取市下面对应的区县
	function get_dist_list($cityCode){
		$dists = $this->db->select('name,code,parentCode')->where('parentCode',$cityCode)->get('areas')->result();
		return $dists;
	}
	// --------------------------------------------------------------------------
	// 获取所有的省份
	function get_provinces(){
		$provinces = $this->db
			->select('code, name')
			->where('level', 0)
			->get('areas')->result();
		if (! $provinces) {
			throw new Exception("获取数据失败", 1);
		}
		return $provinces;
	}
	// --------------------------------------------------------------------------
	
	//获取用户排行榜数据
	/**
	 * 提供企业端排行数据
	 * @param 商户 $mch_id
	 * @param 参数 $param
	 */
	function get_table_data($mch_id,$param,&$count=0,$start=null,$length=null,$order=null){
		$time=get_time_screening($param);
		//全国数据排名 不区分省市区
		$sql="select m.userId,u.nickName,m.scanNum,m.transAmount,m.pointAmount from (select a.userId,sum(a.scanNum) scanNum,round(sum(a.transAmount)/100,2) transAmount,sum(a.pointAmount) pointAmount  from rpt_user_rank a
where a.mchId=? and a.theDate>=? and a.theDate<=? ";
		$sql_cnt="select count(m.id) cnt from (select id from rpt_user_rank where mchId=? and theDate>=? and theDate<=? ";
		//省份数据
		$sql2="select m.userId,u.nickName,m.scanNum,m.transAmount,m.pointAmount from (select a.userId,sum(a.scanNum) scanNum,round(sum(a.transAmount)/100,2) transAmount,sum(a.pointAmount) pointAmount  from rpt_user_rank a
where a.mchId=? and a.userId>0 and proCode=? and a.theDate>=? and a.theDate<=? ";
		$sql2_cnt="select count(m.id) cnt from (select id from rpt_user_rank where mchId=? and proCode=? and theDate>=? and theDate<=? ";
		//城市
		$sql3="select m.userId,u.nickName,m.scanNum,m.transAmount,m.pointAmount from (select a.userId,sum(a.scanNum) scanNum,round(sum(a.transAmount)/100,2) transAmount,sum(a.pointAmount) pointAmount  from rpt_user_rank a
where a.mchId=? and cityCode=? and a.theDate>=? and a.theDate<=? ";
		$sql3_cnt="select count(m.id) cnt from (select id from rpt_user_rank where mchId=? and cityCode=? and theDate>=? and theDate<=? ";

		$arr=array();
		if($param['pro']==0){//全国
			$data=[$mch_id,$time['start'],$time['end']];

			if(!$param['productid']==0){
              $sql.='and productId=? ';
              $sql_cnt.='and productId=? ';
              $data[]=$param['productid'];
            }
            if(!$param['batchid']==0){
              $sql.='and batchId=? ';
              $sql_cnt.='and batchId=? ';
              $data[]=$param['batchid'];
            }
            $sql.="group by a.userId ";
            // 这里定义排序
            if(isset($order)&&$order!=null){
            	$column=$order[0]['column'];
            	// $dir=$order[0]['dir']?$order[0]['dir']:'desc';
            	$dir='desc';
            	$sql.="order by %s %s,a.userId ";
            	if($column==3){
            		$column='scanNum';
            	}elseif($column==4){
            		$column='transAmount';
            	}elseif($column==5){
            		$column='pointAmount';
            	}else{
            		$column='scanNum';
            	}
            	$sql=sprintf($sql,$column,$dir);
            }else{
            	$sql.="order by scanNum desc,a.userId ";
            }
            // 这里定义排序

			$sql_cnt.="group by userId) m";

			$count=$this->db->compile_binds($sql_cnt,$data);
			$count=(array)$this->dbhelper->serverow($count);
			$count = 0;
			if (isset($count['cnt'])) {
				$count = $count['cnt'];
			}
			

			if(isset($start)&&isset($length)){
				$sql.=' limit ?,?';
				$data[]=intval($start);
				$data[]=intval($length);
			}


			$sql.=' ) m left join users u on m.userId=u.id;';


			$sql=$this->db->compile_binds($sql,$data);
        	$result=$this->dbhelper->serve_array($sql);
			//$result=$this->db->query($sql,$data)->result_array();
			for($i=0;$i<count($result);$i++){
				$arr[$i]=array(
					'rank_id'=>$i+$start+1,
					'userId'=>$result[$i]['userId'],
					'nickname'=>$result[$i]['nickName'] ? $result[$i]['nickName'] : '红码用户',
					'scanNum'=>$result[$i]['scanNum'],
					'transAmount'=>$result[$i]['transAmount'],
					'pointAmount'=>$result[$i]['pointAmount']
				);
			}
			return $arr;
		}else if($param['pro']!=0&&$param['city']==0){//省份
			$data=[$mch_id,$param['pro'],$time['start'],$time['end']];

			if(!$param['productid']==0){
              $sql2.='and productId=? ';
              $sql2_cnt.='and productId=? ';
              $data[]=$param['productid'];
            }
            if(!$param['batchid']==0){
              $sql2.='and batchId=? ';
              $sql2_cnt.='and batchId=? ';
              $data[]=$param['batchid'];
            }
            $sql2.="group by a.userId ";
            // 这里定义排序
            if(isset($order)&&$order!=null){
            	$column=$order[0]['column'];
            	// $dir=$order[0]['dir']?$order[0]['dir']:'desc';
            	$dir='desc';
            	$sql2.="order by %s %s,a.userId ";
            	if($column==3){
            		$column='scanNum';
            	}elseif($column==4){
            		$column='transAmount';
            	}elseif($column==5){
            		$column='pointAmount';
            	}else{
            		$column='scanNum';
            	}
            	$sql2=sprintf($sql2,$column,$dir);
            }else{
            	$sql2.="order by scanNum desc,a.userId ";
            }
            // 这里定义排序

			$sql2_cnt.="group by userId) m";


			$count=$this->db->compile_binds($sql2_cnt,$data);
			$count=(array)$this->dbhelper->serverow($count);
			$count=$count["cnt"];
			// $count=$this->db->query($sql2_cnt,$data)->row()->cnt;
				
			if(isset($start)&&isset($length)){
				$sql2.=' limit ?,?';
				$data[]=intval($start);
				$data[]=intval($length);
			}
			$sql2.=' ) m left join users u on m.userId=u.id;';
			$sql2=$this->db->compile_binds($sql2,$data);
        	$result=$this->dbhelper->serve_array($sql2);
			//$result=$this->db->query($sql2,$data)->result_array();
			for($i=0;$i<count($result);$i++){
				$arr[$i]=array(
						'rank_id'=>$i+$start+1,
						'userId'=>$result[$i]['userId'],
						'nickname'=>$result[$i]['nickName'] ? $result[$i]['nickName'] : '红码用户',
						'scanNum'=>$result[$i]['scanNum'],
						'transAmount'=>$result[$i]['transAmount'],
						'pointAmount'=>$result[$i]['pointAmount']
				);
			}
			return $arr;
		}else{//城市
			$data=[$mch_id,$param['city'],$time['start'],$time['end']];

			if(!$param['productid']==0){
              $sql3.='and productId=? ';
              $sql3_cnt.='and productId=? ';
              $data[]=$param['productid'];
            }
            if(!$param['batchid']==0){
              $sql3.='and batchId=? ';
              $sql3_cnt.='and batchId=? ';
              $data[]=$param['batchid'];
            }
            $sql3.="group by a.userId ";
            // 这里定义排序
            if(isset($order)&&$order!=null){
            	$column=$order[0]['column'];
            	// $dir=$order[0]['dir']?$order[0]['dir']:'desc';
            	$dir='desc';
            	$sql3.="order by %s %s,a.userId ";
            	if($column==3){
            		$column='scanNum';
            	}elseif($column==4){
            		$column='transAmount';
            	}elseif($column==5){
            		$column='pointAmount';
            	}else{
            		$column='scanNum';
            	}
            	$sql3=sprintf($sql3,$column,$dir);
            }else{
            	$sql3.="order by scanNum desc,a.userId ";
            }
            // 这里定义排序


			$sql3_cnt.="group by userId) m";

			$count=$this->db->compile_binds($sql3_cnt,$data);
			$count=(array)$this->dbhelper->serverow($count);
			$count=$count["cnt"];
			// $count=$this->db->query($sql3_cnt,$data)->row()->cnt;
			
			if(isset($start)&&isset($length)){
				$sql3.=' limit ?,?';
				$data[]=intval($start);
				$data[]=intval($length);
			}
			$sql3.=' ) m left join users u on m.userId=u.id;';
			$sql3=$this->db->compile_binds($sql3,$data);
        	$result=$this->dbhelper->serve_array($sql3);
			//$result=$this->db->query($sql3,$data)->result_array();
			for($i=0;$i<count($result);$i++){
				$arr[$i]=array(
						'rank_id'=>$i+$start+1,
						'userId'=>$result[$i]['userId'],
						'nickname'=>$result[$i]['nickName'] ? $result[$i]['nickName'] : '红码用户',
						'scanNum'=>$result[$i]['scanNum'],
						'transAmount'=>$result[$i]['transAmount'],
						'pointAmount'=>$result[$i]['pointAmount']
				);
			}
			return $arr;
		}
		
	}
	//userlist 排行
	public function get_userlist($userid,$mchid,$code,$page,$size){
		$myself_sql="select userId,scanNum,ifnull(nickName,'红码用户') nickname,headimgurl,rank_id from (";
		$myself_sql.="(select * from (select @rownum1:=@rownum1+1 as rank_id,userId,scanNum from (select userId,sum(scanNum) scanNum,@rownum1:=0 from rpt_user_rank where 1=1 ";
		if(isset($mchid)){
			$myself_sql.=" and mchId=$mchid";
		}
		if(isset($code)){
			$myself_sql.=" and cityCode=$code";
		}
		$myself_sql.=" group by userId order by scanNum desc) a ) b where userId=$userid)";
		$myself_sql.=") rank inner join users on rank.userId=users.id";
	    //---------------------------
	    $rank_sql="select userId,scanNum,ifnull(nickName,'红码用户') nickname,headimgurl from (";
	    $rank_sql.="select userId,sum(scanNum) scanNum from rpt_user_rank where 1=1 ";
	    if(isset($mchid)){
	        $rank_sql.=" and mchId=$mchid";
	    }
	    if(isset($code)&&$code!='0'){
	        $rank_sql.=" and cityCode=$code";
	    }
	    $rank_sql.=" group by userId order by scanNum desc limit $page,$size";
	    $rank_sql.=") rank left join users on rank.userId=users.id";
	    //---------------------------
	    $count_sql="select count(userId) total from (";
	    $count_sql.="select userId from rpt_user_rank where 1=1 ";
	    if(isset($mchid)){
	        $count_sql.=" and mchId=$mchid";
	    }
	    if(isset($code)&&$code!='0'){
	        $count_sql.=" and cityCode=$code";
	    }
	    $count_sql.=' group by userId';
	    $count_sql.=") a";
	    $myself=$this->dbhelper->serve($myself_sql);
	    $top99=$this->dbhelper->serve($rank_sql);
	    $count=$this->dbhelper->serve($count_sql);
	    for($i=0;$i<count($top99);$i++){
	    	 $top99[$i]->rank_id=($page - 1)*$size+$i+1;
	    }
	    $result=array(
	    	'errcode'=>0,
	    	'errmsg'=>'',
	    	'data'=>array(
	    			'myself'=>$myself,
	    			'data'=>$top99,
	    			'total'=>$count
	    		)
	    );
	    return $result;
	}

	//获取某个用户当前扫码排名
	public function get_rank_by_user_id($user,$type){
		if($type=='all'){
			$sql="select count(id)+1 as rank from rpt_user_rank_all where mchId=? and scanNum>ifnull((select scanNum from rpt_user_rank_all where mchId=? and userId=?),0)";
			$sql=$this->db->compile_binds($sql,[$user->mchId,$user->mchId,$user->id]);
			$sql2="select scanNum from rpt_user_rank_all where mchId=? and userId=?";
			$sql2=$this->db->compile_binds($sql2,[$user->mchId,$user->id]);
			$rank=$this->dbhelper->serverow($sql);
			if(!$rank){
				$rank=(object)['rank'=>''];
			}
			$scanNum=$this->dbhelper->serverow($sql2);
			if(!$scanNum){
				$scanNum=(object)['scanNum'=>''];
			}
			if (isset($scanNum->scanNum)) {
				return (object)['rank'=>$rank->rank,'scanNum'=>$scanNum->scanNum,'city'=>'全国'];
			} else {
				return (object)['rank'=>$rank->rank,'scanNum'=>0,'city'=>'全国'];
			}
		}
		
		if($type=='city'){ 
			
		}
	}
}